return [
    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    |
    | You can adjust these settings to manage cross-origin resource sharing
    | for your Laravel application. Adjust the origins, headers, and methods
    | as needed based on your frontend's domain.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // CORS apply වෙන routes

    'allowed_methods' => ['*'], // All HTTP methods allow කරන්න (GET, POST, PUT, DELETE, OPTIONS)

    'allowed_origins' => ['http://localhost:3000'], // React frontend එකේ URL එක මෙතන දාන්න

    'allowed_origins_patterns' => [], // Regex patterns වලට allow කරන්න නම් මෙතන set කරන්න

    'allowed_headers' => ['*'], // All headers allow කරන්න

    'exposed_headers' => [], // Expose කරන headers specify කරන්න

    'max_age' => 0, // Pre-flight request එකක් browser එක cache කරන්නේ කොච්චර කාලයකටද?

    'supports_credentials' => true, // Cookies සහ authentication headers allow කරන්නද?
];
