<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * GeneralNotification already used the Queueable trait but never implemented
 * ShouldQueue, so every delivery ran inside the request even though this app
 * ships a queue worker (NMU-Queue-Worker) and runs on QUEUE_CONNECTION=database
 * in both .env and .env.production.
 *
 * A professor picking a whole class runs one notify() per recipient, so that
 * loop was N synchronous inserts before the response could be sent.
 */
class QueuedNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function sendTo(User $user): void
    {
        $user->notify(new GeneralNotification([
            'title' => 'Notice',
            'message' => 'Body',
            'from_user_name' => 'System',
        ]));
    }

    public function test_the_notification_is_queued_instead_of_written_inline(): void
    {
        config(['queue.default' => 'database']);

        $this->sendTo(User::factory()->create());

        $this->assertSame(1, DB::table('jobs')->count(), 'The delivery should be sitting on the queue.');
        $this->assertSame(0, DB::table('notifications')->count(), 'Nothing should have been written yet.');
    }

    public function test_the_worker_delivers_it_later(): void
    {
        config(['queue.default' => 'database']);

        $user = User::factory()->create();
        $this->sendTo($user);

        Artisan::call('queue:work', ['--once' => true]);

        $this->assertSame(0, DB::table('jobs')->count(), 'The job should have been consumed.');
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame('Notice', $user->notifications()->first()->data['title']);
    }
}
