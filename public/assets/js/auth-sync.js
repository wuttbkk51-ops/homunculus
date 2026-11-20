/**
 * AuthSync Helper
 *
 * ระบบซิงค์สถานะการเปลี่ยนแปลงสิทธิ์ (Auth/Permissions) ข้ามแท็บเบราว์เซอร์ผ่าน BroadcastChannel API
 * ทำงานร่วมกับ Response Header "X-Auth-Refreshed" จาก TenantAuthFilter (App\Filters\TenantAuthFilter)
 *
 * @example
 * // 1. โหลดสคริปต์ใน HTML View
 * // <script src="<?= base_url('assets/js/auth-sync.js') ?>"></script>
 *
 * // 2. ดักฟัง Event เมื่อสิทธิ์เปลี่ยน (ใช้ได้ทั้งในหน้านี้และหน้าอื่นๆ ที่เปิดอยู่)
 * window.addEventListener('auth:refreshed', (event) => {
 *     console.log('สิทธิ์ถูกอัปเดตเมื่อ:', event.detail.timestamp);
 *     // สั่ง refresh UI หรือดึงข้อมูลใหม่
 * });
 *
 * // หรือสั่งให้ reload หน้าเว็บอัตโนมัติเมื่อแท็บอื่นสิทธิ์เปลี่ยน:
 * AuthSync.init({ autoReload: true });
 */
(function (global) {
    'use strict';

    // ชื่อ Channel สำหรับสื่อสารข้ามแท็บภายใต้ Origin เดียวกัน
    const CHANNEL_NAME = 'auth_sync';

    // Header ที่ส่งมาจาก TenantAuthFilter เมื่อ Cache สิทธิ์ถูกคำนวณใหม่
    const REFRESH_HEADER = 'X-Auth-Refreshed';

    // การตั้งค่าเริ่มต้น
    const config = {
        autoReload: false,     // หากเป็น true จะสั่ง window.location.reload() เมื่อแท็บอื่นสิทธิ์เปลี่ยน
        debug: false,          // พิมพ์ log ใน console สำหรับการ debug
        interceptFetch: true,  // ดักจับ window.fetch อัตโนมัติเพื่อตรวจจับ Header
    };

    let channel = null;

    /**
     * พิมพ์ข้อความ debug หากเปิดใช้งานโหมด debug
     */
    function log(...args) {
        if (config.debug) {
            console.log('[AuthSync]', ...args);
        }
    }

    /**
     * ส่ง Event แจ้งเตือนไปยังทุกส่วนในหน้าเว็บปัจจุบัน
     */
    function dispatchLocalEvent(detail) {
        const event = new CustomEvent('auth:refreshed', {
            detail: detail || { timestamp: Date.now(), source: 'remote' }
        });
        window.dispatchEvent(event);
    }

    /**
     * กระจายสัญญาณแจ้งเตือนไปยังแท็บอื่นๆ
     *
     * @param {Object} payload ข้อมูลเพิ่มเติมที่จะส่งไป
     */
    function notifyOtherTabs(payload = {}) {
        const message = {
            type: 'AUTH_REFRESHED',
            timestamp: Date.now(),
            ...payload
        };

        if (channel) {
            channel.postMessage(message);
            log('ส่งสัญญาณ AUTH_REFRESHED ไปยังแท็บอื่นแล้ว', message);
        } else {
            // Fallback กรณีบราวเซอร์ไม่รองรับ BroadcastChannel (ใช้ localStorage event)
            try {
                localStorage.setItem('auth_sync_event', JSON.stringify(message));
                localStorage.removeItem('auth_sync_event');
            } catch (e) {
                // ป้องกันกรณีเข้า Private Mode แล้วเข้าถึง storage ไม่ได้
            }
        }
    }

    /**
     * ประมวลผลเมื่อได้รับสัญญาณว่าสิทธิ์มีการเปลี่ยนแปลง
     */
    function handleRefreshSignal(data) {
        log('ได้รับสัญญาณสิทธิ์เปลี่ยนแปลงจากแท็บอื่น', data);

        // 1. Dispatch custom event ให้ script อื่นๆ ในหน้านี้ดักจับไปใช้งาน
        dispatchLocalEvent(data);

        // 2. หากตั้งค่า autoReload ไว้ ให้โหลดหน้าเว็บใหม่
        if (config.autoReload) {
            log('กำลังโหลดหน้าเว็บใหม่ตามการตั้งค่า autoReload...');
            window.location.reload();
        }
    }

    /**
     * เริ่มต้นการทำงานของ BroadcastChannel และ Event Listener
     */
    function setupChannel() {
        if ('BroadcastChannel' in window) {
            channel = new BroadcastChannel(CHANNEL_NAME);
            channel.onmessage = (event) => {
                if (event.data && event.data.type === 'AUTH_REFRESHED') {
                    handleRefreshSignal(event.data);
                }
            };
        } else {
            // Fallback สำหรับเบราว์เซอร์ที่ไม่รองรับ BroadcastChannel
            window.addEventListener('storage', (event) => {
                if (event.key === 'auth_sync_event' && event.newValue) {
                    try {
                        const data = JSON.parse(event.newValue);
                        if (data.type === 'AUTH_REFRESHED') {
                            handleRefreshSignal(data);
                        }
                    } catch (e) {}
                }
            });
        }
    }

    /**
     * ดักจับ window.fetch อัตโนมัติเพื่อตรวจสอบ Header X-Auth-Refreshed
     */
    function setupFetchInterceptor() {
        if (!window.fetch) {
            return;
        }

        const originalFetch = window.fetch;
        window.fetch = async function (...args) {
            const response = await originalFetch.apply(this, args);

            try {
                // ตรวจสอบว่า Response มี Header X-Auth-Refreshed หรือไม่
                const refreshedHeader = response.headers.get(REFRESH_HEADER);
                if (refreshedHeader === 'true') {
                    log(`ตรวจพบ ${REFRESH_HEADER} ใน Response จาก Fetch`);
                    
                    // แจ้งเตือนหน้านี้เอง
                    dispatchLocalEvent({ timestamp: Date.now(), source: 'fetch' });

                    // ส่งสัญญาณบอกแท็บอื่น
                    notifyOtherTabs({ source: 'fetch', url: response.url });
                }
            } catch (err) {
                // หลีกเลี่ยงข้อผิดพลาดหากไม่สามารถอ่าน Header ได้ (เช่น Opaque Response ข้ามโดเมน)
            }

            return response;
        };
    }

    /**
     * Public API
     */
    const AuthSync = {
        /**
         * ตั้งค่าและเริ่มต้นระบบ
         *
         * @param {Object} options ตัวเลือกการตั้งค่า
         * @param {boolean} [options.autoReload=false] สั่ง reload หน้านี้เมื่อแท็บอื่นสิทธิ์เปลี่ยน
         * @param {boolean} [options.debug=false] เปิดโหมด debug log
         * @param {boolean} [options.interceptFetch=true] ดักจับ fetch อัตโนมัติ
         */
        init(options = {}) {
            Object.assign(config, options);
            log('AuthSync เริ่มต้นทำงานพร้อม config:', config);
        },

        /**
         * ส่งสัญญาณแจ้งเตือนไปยังแท็บอื่นด้วยตนเอง
         *
         * @param {Object} payload ข้อมูลเพิ่มเติม
         */
        notify(payload = {}) {
            notifyOtherTabs(payload);
        },

        /**
         * ตรวจสอบ HTTP Response Header ด้วยตนเอง (เหมาะสำหรับ Axios หรือ XMLHttpRequest)
         *
         * @param {Headers|Object} headers Header จาก response
         */
        checkResponseHeaders(headers) {
            if (!headers) return;

            let isRefreshed = false;
            if (typeof headers.get === 'function') {
                isRefreshed = headers.get(REFRESH_HEADER) === 'true';
            } else if (typeof headers[REFRESH_HEADER.toLowerCase()] !== 'undefined') {
                isRefreshed = String(headers[REFRESH_HEADER.toLowerCase()]) === 'true';
            } else if (typeof headers[REFRESH_HEADER] !== 'undefined') {
                isRefreshed = String(headers[REFRESH_HEADER]) === 'true';
            }

            if (isRefreshed) {
                log(`ตรวจพบ ${REFRESH_HEADER} ผ่าน checkResponseHeaders()`);
                dispatchLocalEvent({ timestamp: Date.now(), source: 'manual' });
                notifyOtherTabs({ source: 'manual' });
            }
        },

        /**
         * ฟังก์ชันช่วยติดตั้ง Interceptor สำหรับ Axios
         *
         * @param {Object} axiosInstance Instance ของ Axios (เช่น axios หรือ instance ที่สร้างจาก axios.create())
         */
        attachToAxios(axiosInstance) {
            if (!axiosInstance || !axiosInstance.interceptors) {
                console.error('[AuthSync] กรุณาส่ง axios instance ที่ถูกต้อง');
                return;
            }

            axiosInstance.interceptors.response.use(
                (response) => {
                    this.checkResponseHeaders(response.headers);
                    return response;
                },
                (error) => Promise.reject(error)
            );

            log('ติดตั้ง Interceptor เข้ากับ Axios เรียบร้อยแล้ว');
        },

        /**
         * ลงทะเบียน Callback ดักฟังเมื่อสิทธิ์ถูกรีเฟรช
         *
         * @param {Function} callback ฟังก์ชันที่จะทำงานเมื่อสิทธิ์เปลี่ยน
         * @returns {Function} ฟังก์ชันสำหรับ Unsubscribe
         */
        onRefreshed(callback) {
            const listener = (event) => callback(event.detail);
            window.addEventListener('auth:refreshed', listener);
            return () => window.removeEventListener('auth:refreshed', listener);
        }
    };

    // เริ่มต้นระบบ Channel และ Fetch Interceptor ทันทีเมื่อโหลดไฟล์
    setupChannel();
    if (config.interceptFetch) {
        setupFetchInterceptor();
    }

    // Export เข้าสู่ Global Namespace
    global.AuthSync = AuthSync;

})(typeof window !== 'undefined' ? window : this);
