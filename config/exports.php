<?php

return [
    // Allowed Blade views for export-from-view endpoints.
    // Keep this list explicit to avoid exposing unintended views.
    'view_allowlist' => [
        'exports.test-report',
    ],
    // Disk for stored/queued exports.
    'disk' => env('EXPORTS_DISK', 'exports'),
];
