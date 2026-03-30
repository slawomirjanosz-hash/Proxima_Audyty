<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use App\Models\Company;
use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\CrmTask;
use App\Models\CrmTaskChange;
use App\Models\User;
use Throwable;

class DiagnosticsController extends Controller
{
    /** Expected tables in this application. */
    private const EXPECTED_TABLES = [
        'users',
        'sessions',
        'cache',
        'jobs',
        'companies',
        'offers',
        'energy_audits',
        'audit_types',
        'audit_type_sections',
        'audit_units',
        'company_user',
        'company_contacts',
        'crm_companies',
        'crm_customer_types',
        'crm_deals',
        'crm_stages',
        'crm_tasks',
        'crm_task_changes',
        'crm_activities',
        'crm_deal_user',
        'iso50001_audits',
        'iso50001_templates',
        'system_settings',
        'co2_indicators_history',
    ];

    public function index(Request $request): View
    {
        // DB connection check
        $dbOk = false;
        $dbError = null;
        $dbDriver = config('database.default', 'unknown');
        try {
            DB::statement('SELECT 1');
            $dbOk = true;
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        // Table checks
        $tableStatus = [];
        foreach (self::EXPECTED_TABLES as $table) {
            $exists = false;
            $error = null;
            if ($dbOk) {
                try {
                    $exists = Schema::hasTable($table);
                } catch (Throwable $e) {
                    $error = $e->getMessage();
                }
            }
            $tableStatus[$table] = ['exists' => $exists, 'error' => $error];
        }

        // Migration status
        $migrationOutput = '';
        $migrationError = null;
        try {
            Artisan::call('migrate:status');
            $migrationOutput = Artisan::output();
        } catch (Throwable $e) {
            $migrationError = $e->getMessage();
        }

        // Pending migrations check
        $pendingCount = 0;
        try {
            Artisan::call('migrate:status', ['--pending' => true]);
            $pendingOutput = Artisan::output();
            $pendingCount = substr_count($pendingOutput, 'Pending');
        } catch (Throwable) {
        }

        // Recent log errors
        $recentErrors = [];
        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                // Get last ~8000 chars
                if (strlen($logContent) > 8000) {
                    $logContent = '...[truncated]...' . substr($logContent, -8000);
                }
                $lines = explode("\n", $logContent);
                // Keep lines with ERROR or CRITICAL
                foreach ($lines as $line) {
                    if (str_contains($line, '.ERROR') || str_contains($line, '.CRITICAL') || str_contains($line, 'SQLSTATE')) {
                        $recentErrors[] = htmlspecialchars(substr($line, 0, 300));
                        if (count($recentErrors) >= 20) {
                            break;
                        }
                    }
                }
                $recentErrors = array_reverse($recentErrors);
            }
        } catch (Throwable) {
        }

        // System info
        $sysInfo = [
            'PHP'         => PHP_VERSION,
            'Laravel'     => app()->version(),
            'Environment' => app()->environment(),
            'DB Driver'   => $dbDriver,
            'DB Host'     => substr((string) config('database.connections.' . $dbDriver . '.host', 'n/a'), 0, 40),
            'Cache Driver' => config('cache.default', 'unknown'),
            'Queue Driver' => config('queue.default', 'unknown'),
        ];

        // ─── Critical column checks ───────────────────────────────────────
        $columnChecks = [];
        if ($dbOk) {
            $criticalColumns = [
                'crm_tasks'     => ['id', 'title', 'type', 'priority', 'status', 'due_date', 'assigned_to', 'company_id', 'deal_id'],
                'crm_deals'     => ['id', 'name', 'company_id', 'value', 'stage', 'user_id', 'owner_id'],
                'crm_companies' => ['id', 'name', 'nip', 'system_company_id', 'owner_id'],
                'companies'     => ['id', 'name', 'email', 'phone', 'street', 'city', 'postal_code'],
            ];
            foreach ($criticalColumns as $table => $cols) {
                foreach ($cols as $col) {
                    try {
                        $exists = Schema::hasColumn($table, $col);
                        if (! $exists) {
                            $columnChecks[] = ['table' => $table, 'column' => $col, 'ok' => false, 'error' => 'kolumna nie istnieje'];
                        }
                    } catch (Throwable $e) {
                        $columnChecks[] = ['table' => $table, 'column' => $col, 'ok' => false, 'error' => $e->getMessage()];
                    }
                }
            }
        }

        // ─── CRM Probe ────────────────────────────────────────────────────
        $crmProbe      = [];
        $crmProbeError = null;
        if ($dbOk) {
            $steps = [
                'Company::count()'      => static fn () => Company::count() . ' firm systemowych',
                'CrmStage::get()'       => static fn () => CrmStage::orderBy('order')->get()->count() . ' etapów',
                'CrmCompany query'      => static fn () => CrmCompany::with(['owner'])->limit(1)->get()->count() . ' (próbka)',
                'CrmTask query'         => static fn () => CrmTask::with(['assignedTo', 'deal'])
                                            ->where('status', '!=', 'zakonczone')
                                            ->orderBy('due_date')->limit(5)->get()->count() . ' zadań (próbka)',
                'CrmTask due_date cast' => static function () {
                    $t = CrmTask::whereNotNull('due_date')->latest()->first();
                    if (! $t) { return 'brak zadań z terminem'; }
                    return 'isPast()=' . ($t->due_date->isPast() ? 'true' : 'false');
                },
                'CrmDeal query'         => static fn () => CrmDeal::with(['company', 'owner'])->limit(1)->get()->count() . ' (próbka)',
                'CrmActivity query'     => static fn () => CrmActivity::with(['company', 'deal'])->latest()->limit(1)->get()->count() . ' (próbka)',
                'Overdue tasks count'   => static fn () => CrmTask::where('status', '!=', 'zakonczone')
                                            ->whereNotNull('due_date')->where('due_date', '<', now())->count() . ' po terminie',
                'CRM view file'         => static fn () => file_exists(resource_path('views/crm/index.blade.php')) ? 'istnieje' : 'BRAK',
                'syncSystemCompanies (dry)' => static function () {
                    $c = Company::query()->orderBy('name')->first();
                    if (! $c) { return 'brak firm systemowych'; }
                    $nip = ! empty($c->nip) ? preg_replace('/\D+/', '', (string) $c->nip) : null;
                    return 'OK, pierwsza firma: ' . mb_substr((string) $c->name, 0, 40)
                        . ', nip=' . ($nip ?: 'brak');
                },
            ];
            foreach ($steps as $label => $fn) {
                try {
                    $detail = $fn();
                    $crmProbe[] = ['label' => $label, 'ok' => true, 'detail' => $detail];
                } catch (Throwable $e) {
                    $crmProbe[] = ['label' => $label, 'ok' => false, 'detail' => $e->getMessage()];
                    if ($crmProbeError === null) {
                        $crmProbeError = [
                            'step'    => $label,
                            'message' => $e->getMessage(),
                            'class'   => get_class($e),
                            'file'    => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $e->getFile()),
                            'line'    => $e->getLine(),
                            'trace'   => implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 12)),
                        ];
                    }
                }
            }
        }

        return view('diagnostics.index', compact(
            'dbOk', 'dbError', 'dbDriver',
            'tableStatus', 'migrationOutput', 'migrationError', 'pendingCount',
            'recentErrors', 'sysInfo', 'columnChecks', 'crmProbe', 'crmProbeError'
        ));
    }

    /** Run pending migrations (POST). */
    public function migrate(Request $request)
    {
        if (! $request->user()?->canManageEverything()) {
            abort(403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
        } catch (Throwable $e) {
            $output = 'Error: ' . $e->getMessage();
        }

        return back()->with('migrate_output', $output);
    }

    /** Clear all caches (POST). */
    public function clearCache(Request $request)
    {
        if (! $request->user()?->canManageEverything()) {
            abort(403);
        }

        $output = [];
        foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $cmd) {
            try {
                Artisan::call($cmd);
                $output[] = $cmd . ': OK';
            } catch (Throwable $e) {
                $output[] = $cmd . ': ' . $e->getMessage();
            }
        }

        return back()->with('cache_output', implode("\n", $output));
    }
}
