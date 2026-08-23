<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schedule Settings
    |--------------------------------------------------------------------------
    |
    | Operating windows and session length used by the course-offering
    | schedule builder, the live room-availability checker, and any
    | slot-based UI. Change these values here when the school adjusts
    | its daily timetable (e.g. starting at 07:00 next year).
    |
    */

    'schedule' => [
        // Length of one teaching session in minutes.
        'session_minutes' => 90,

        // Daily operating windows (24h H:i). Sessions are generated
        // back-to-back inside each window: 07:30-12:00 => 3 x 90min,
        // 13:00-17:30 => 3 x 90min (6 sessions per day).
        'windows' => [
            ['start' => '07:30', 'end' => '12:00'],
            ['start' => '13:00', 'end' => '17:30'],
        ],

        // Days classes may be scheduled. Saturday and Sunday use the
        // same windows as weekdays.
        'days' => [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ],
    ],

];
