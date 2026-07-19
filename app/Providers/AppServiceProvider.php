<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(
            Login::class,
            function ($event) {
                ActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'Login',
                    'description' => "User {$event->user->name} logged in successfully.",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        );

        Event::listen(
            Logout::class,
            function ($event) {
                if ($event->user) {
                    ActivityLog::create([
                        'user_id' => $event->user->id,
                        'action' => 'Logout',
                        'description' => "User {$event->user->name} logged out.",
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        );

        Password::defaults(function () {
            return Password::min(5);
        });
    }
}
