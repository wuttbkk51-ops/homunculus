<?php

declare(strict_types=1);

return [
    // ฝั่งผู้รับ Invite
    'invalidOrExpired'   => 'This invite link is invalid or has already expired.',
    'emailMismatch'      => 'The email address does not match the invitation.',
    'tenantInfoRequired' => 'Business name and slug are required to complete registration.',
    'registrationSuccess'=> 'Your account has been created successfully. Welcome!',
    'registrationFailed' => 'Registration failed. Please try again or contact support.',

    // ฝั่ง Admin จัดการ
    'createFailed'  => 'Could not create the invitation. Please try again.',
    'sendSuccess'   => 'Invitation sent successfully.',
    'revokeSuccess' => 'Invitation has been revoked.',
    'resendSuccess' => 'Invitation resent successfully.',

    // UI Labels
    'pageTitle'       => 'Invitations',
    'sendInvite'      => 'Send Invitation',
    'revokeInvite'    => 'Revoke',
    'resendInvite'    => 'Resend',
    'statusPending'   => 'Pending',
    'statusAccepted'  => 'Accepted',
    'statusExpired'   => 'Expired',

    // Form Labels
    'labelEmail'      => 'Email Address',
    'labelRole'       => 'Role',
    'labelInviteType' => 'Invite Type',
    'labelTenantId'   => 'Tenant',
    'labelTenantName' => 'Business Name',
    'labelTenantSlug' => 'Slug',
    'labelPassword'   => 'Password',
    'labelPasswordConfirm' => 'Confirm Password',
    'optionNewTenant'      => 'New Tenant (create new)',
    'optionExistingTenant' => 'Existing Tenant',
    'btnRegister'          => 'Create Account',
    'expiredTitle'         => 'Invitation Expired',
    'expiredMessage'       => 'This invite link has expired or has already been used. Please contact your administrator for a new invitation.',
    'backToLogin'          => 'Back to Login',
];
