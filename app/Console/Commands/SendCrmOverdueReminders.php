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
        $now     = now()->startOfDay();
        $dayNum  = (int) now()->format('N'); // 1=Mon … 7=Sun
        $dayOfMonth = (int) now()->format('j');

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

        // Group by assignee, then filter by per-task notify_frequency
        $grouped = $overdueTasks->groupBy('assigned_to');

        $sent = 0;
        foreach ($grouped as $userId => $tasks) {
            $assignee = User::find($userId);
            if (! $assignee || ! $assignee->email) {
                continue;
            }

            // Filter tasks that should be notified today based on their frequency setting
            $tasksToNotify = $tasks->filter(function (CrmTask $task) use ($dayNum, $dayOfMonth): bool {
                return match ($task->notify_frequency ?? 'codziennie') {
                    'wylaczone' => false,
                    'co_2_dni'  => ($dayOfMonth % 2 === 1), // odd days
                    'co_tydzien'=> ($dayNum === 1),          // every Monday
                    default     => true,                     // codziennie
                };
            });

            if ($tasksToNotify->isEmpty()) {
                continue;
            }

            Mail::to($assignee->email)->send(new CrmTaskOverdueMail($assignee, $tasksToNotify));
            $sent++;

            $this->line("Sent reminder to {$assignee->email} ({$tasksToNotify->count()} tasks)");
        }

        $this->info("Done. Sent {$sent} reminder email(s).");

        return self::SUCCESS;
    }
}
