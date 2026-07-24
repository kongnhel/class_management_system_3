<?php

return [
    'default' => 'app',
    'projects' => [
        'app' => [
            'credentials' => storage_path('app/firebase/classmanagementsystem.json'),
            'project_id' => env('FIREBASE_PROJECT_ID', 'classmanagementsystem-cd57f'),
            'database_url' => env('FIREBASE_DATABASE_URL', 'https://classmanagementsystem-cd57f-default-rtdb.firebaseio.com/'),
        ],
    ],
];
