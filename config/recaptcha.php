<?php

return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
];
