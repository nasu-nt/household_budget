<?php

return [
    'password_reset' => [
        'subject' => '[:app] Reset your password',
        'greeting' => 'Hello, :name!',
        'greeting_without_name' => 'Hello!',
        'request_received' => 'We received a request to reset the password for your account.',
        'instructions' => 'Use the button below to set a new password.',
        'action' => 'Reset Password',
        'expires' => 'This password reset link will expire in :count minute.|This password reset link will expire in :count minutes.',
        'ignore' => 'If you did not request a password reset, no further action is required.',
        'salutation' => 'Regards, :app',
    ],
];