<?php

namespace App\Console\Commands;

use App\Mail\CrmTaskOverdueMail;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCrmOverdueReminders extends Command
{
    protected $signature   = 'crm:send-overdue-reminders';
    protected $description = 'Send daily email reminders to users with overdue CRM tasks';

    public function handle(): int
    {
        $now = now()->startOfDay();

        // Fetch all overdue, unfinished tasks with an assignee
        $overdueTasks = CrmTask::whereNotNull('assigned_to')
            ->whereNotIn('status', ['zakonczone', 'anulowane'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->with(['assignedTo', 'company', 'deal'])
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('No overdue tasks found.');
            return self::SUCCESS;
        }

        // Group by assignee
        $grouped = $overdueTasks->groupBy('assigned_to');

        $sent = 0;
        foreach ($grouped as $userId => $tasks) {
            $assignee = User::find($userId);
            if (! $assignee || ! $assignee->email) {
                continue;
            }

            Mail::to($assignee->email)->send(new CrmTaskOverdueMail($assignee, $tasks));
            $sent++;

            $this->line("Sent reminder to {$assignee->email} ({$tasks->count()} tasks)");
        }

        $this->info("Done. Sent {$sent} reminder email(s).");

        return self::SUCCESS;
    }
}
