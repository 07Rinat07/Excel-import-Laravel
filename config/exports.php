<?php

return [
    // Allowed Blade views for export-from-view endpoints.
    // Keep this list explicit to avoid exposing unintended views.
    'view_allowlist' => [
        'exports.test-report',
    ],
];
