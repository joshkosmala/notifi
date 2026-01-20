<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all scheduled notifications that are due';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $notifications = Notification::query()
            ->whereNull('sent_at')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', now())
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No scheduled notifications to send.');

            return self::SUCCESS;
        }

        foreach ($notifications as $notification) {
            // TODO: Actually dispatch the push notification here
            $notification->markAsSent();
            $this->line("Sent: {$notification->title}");
        }

        $this->info("Sent {$notifications->count()} scheduled notification(s).");

        return self::SUCCESS;
    }
}
