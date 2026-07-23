<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for Livewire component classes in
    | your application. This value will be used by the framework to
    | find your components when they are rendered by a browser.
    |
    */

    'class_namespace' => 'App\\Livewire',

    

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path for Livewire component views in your
    | application. This value will be used by the framework to
    | find your components when they are rendered by a browser.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | This value sets the default layout view that will be used when
    | rendering a Livewire component as a full-page component.
    |
    */

    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    |
    | This value sets the default placeholder view that will be used when
    | a component is lazy loaded.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire supports native file uploads via progressive enhancement.
    | Here you may configure the storage disk, temporary directory,
    | maximum file size, and middleware for handling uploads.
    |
    */

    'temporary_file_uploads' => [
        'disk' => null,        // Default: 'local'
        'rules' => null,       // Example: ['required', 'file', 'max:12288'] (12MB)
        'directory' => null,   // Default: 'livewire-tmp'
        'middleware' => null,  // Default: 'throttle:60,1'
        'preview_mimes' => [   // Supported file types for temporary previews.
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, // Max duration (in minutes) before an upload expires.
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Hydrate
    |--------------------------------------------------------------------------
    |
    | This value sets whether Livewire should render the component on
    | every hydrate request. If set to false, the component will
    | only be rendered on the initial request.
    |
    */

    'render_on_hydrate' => false,

    /*
    |--------------------------------------------------------------------------
    | Application Update Monitoring
    |--------------------------------------------------------------------------
    |
    | Livewire can monitor your application for updates and notify
    | the user when a new version of your application has been
    | deployed. You can enable this feature here.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inject Alpine
    |--------------------------------------------------------------------------
    |
    | Livewire v3 automatically injects Alpine.js into the frontend.
    | Since you are including Alpine in your app.js, you might want
    | to set this to "false" to avoid the "Multiple Instances" error.
    |
    */

    'inject_alpine' => false,

    /*
    |--------------------------------------------------------------------------
    | Inject Livewire Scripts & Styles
    |--------------------------------------------------------------------------
    |
    | In Livewire v3, scripts and styles are injected automatically.
    | You can disable this if you prefer manual control.
    |
    */

    'inject_assets' => false,

    /*
    |--------------------------------------------------------------------------
    | Pagination Theme
    |--------------------------------------------------------------------------
    |
    | When yielding pagination links from your components, you can
    | specify which theme you wish to use. The default is "tailwind".
    |
    */

    'pagination_theme' => 'tailwind',

    

];
