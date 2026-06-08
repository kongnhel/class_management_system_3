<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Faculty;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\CourseOfferingPolicy;
use App\Policies\CoursePolicy;
use App\Policies\FacultyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Course::class => CoursePolicy::class,
        CourseOffering::class => CourseOfferingPolicy::class,
        Faculty::class => FacultyPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
