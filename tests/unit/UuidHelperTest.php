<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class UuidHelperTest extends CIUnitTestCase
{
    private string $uuid7Pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    public function testUuidGeneratesValidRfc9562Format(): void
    {
        $id = uuid();

        $this->assertSame(36, strlen($id));
        $this->assertMatchesRegularExpression($this->uuid7Pattern, $id);
    }

    public function testUuidAliasUuid7(): void
    {
        $id = uuid7();

        $this->assertSame(36, strlen($id));
        $this->assertMatchesRegularExpression($this->uuid7Pattern, $id);
    }

    public function testUuidBinaryMode(): void
    {
        $bin = uuid(true);

        $this->assertSame(16, strlen($bin));
        $hex = bin2hex($bin);
        $formatted = substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);

        $this->assertMatchesRegularExpression($this->uuid7Pattern, $formatted);
    }

    public function testUuidIsMonotonicallyOrdered(): void
    {
        $uuids = [];
        for ($i = 0; $i < 50; $i++) {
            $uuids[] = uuid();
        }

        $sorted = $uuids;
        sort($sorted);

        $this->assertSame($sorted, $uuids);
        $this->assertCount(50, array_unique($uuids));
    }
}
