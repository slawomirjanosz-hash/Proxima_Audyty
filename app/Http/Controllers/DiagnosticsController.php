<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use App\Models\AuditType;
use App\Models\Company;
use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\CrmTask;
use App\Models\CrmTaskChange;
use App\Models\EnergyAudit;
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
                'energy_audits'       => ['id', 'title', 'audit_type', 'audit_type_id', 'status', 'completed_at', 'data_payload', 'company_id', 'auditor_id'],
                'audit_types'         => ['id', 'name', 'formulas', 'variables'],
                'audit_type_sections' => ['id', 'audit_type_id', 'name', 'position', 'tasks', 'data_fields', 'formulas'],
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
                'Company::count()' => static fn () => Company::count() . ' firm systemowych',

                'CrmStage::get()' => static fn () => CrmStage::orderBy('order')->get()->count() . ' etapów',

                'CrmCompany query' => static fn () => CrmCompany::with(['owner'])->limit(1)->get()->count() . ' (próbka)',

                'CrmTask query (full)' => static fn () => CrmTask::with(['assignedTo', 'deal', 'company'])
                    ->where('status', '!=', 'zakonczone')
                    ->orderBy('due_date')->get()->count() . ' zadań łącznie',

                'CrmTask – iteracja priority + due_date' => static function () {
                    $tasks = CrmTask::with(['assignedTo', 'deal'])
                        ->where('status', '!=', 'zakonczone')
                        ->orderBy('due_date')->get();
                    $problems = [];
                    foreach ($tasks as $task) {
                        $class = match((string) $task->priority) {
                            'pilna'    => 'urgent',
                            'wysoka'   => 'high',
                            'normalna' => 'normal',
                            default    => 'low',
                        };
                        if ($task->due_date !== null) {
                            try {
                                $past = $task->due_date->isPast();
                            } catch (Throwable $ex) {
                                $problems[] = "task#{$task->id} due_date='" . $task->getRawOriginal('due_date') . "': " . $ex->getMessage();
                            }
                        }
                    }
                    return $problems
                        ? '❌ ' . implode('; ', $problems)
                        : "OK, przeszło {$tasks->count()} zadań (priority+due_date)";
                },

                'User::whereIn role' => static fn () => User::whereIn('role', ['admin', 'auditor'])
                    ->orderBy('name')->get()->count() . ' użytkowników (admin+auditor)',

                'User role enum values' => static function () {
                    $roles = DB::table('users')->distinct()->pluck('role')->toArray();
                    return 'role values: ' . implode(', ', $roles);
                },

                'CrmDeal query (full)' => static fn () => CrmDeal::with(['company', 'assignedUsers', 'owner', 'user'])
                    ->orderBy('created_at', 'desc')->get()->count() . ' szans',

                'CrmDeal user-filtered (admin)' => static function () {
                    $admin = User::where('role', 'admin')->first();
                    if (! $admin) { return 'Brak użytkownika admin'; }
                    $uid = $admin->id;
                    $count = CrmDeal::where(function ($q) use ($uid): void {
                        $q->where('user_id', $uid)
                            ->orWhereHas('assignedUsers', fn ($q2) => $q2->where('user_id', $uid));
                    })->count();
                    return "OK, {$count} szans dla admin #{$uid}";
                },

                'CrmActivity query' => static fn () => CrmActivity::with(['company', 'deal', 'user'])->latest()->limit(1)->get()->count() . ' (próbka)',

                'Overdue tasks count' => static fn () => CrmTask::where('status', '!=', 'zakonczone')
                    ->whereNotNull('due_date')->where('due_date', '<', now())->count() . ' po terminie',

                'syncSystemCompanies (full read)' => static function () {
                    $companies = Company::query()->orderBy('name')->get();
                    $errors = [];
                    foreach ($companies as $c) {
                        $nip = ! empty($c->nip) ? preg_replace('/\D+/', '', (string) $c->nip) : null;
                        try {
                            CrmCompany::query()
                                ->where(function ($q) use ($c, $nip): void {
                                    $q->where('system_company_id', $c->id);
                                    if ($nip) { $q->orWhere('nip', $nip); }
                                    $q->orWhere('name', (string) $c->name);
                                })->first();
                        } catch (Throwable $ex) {
                            $errors[] = "firma#{$c->id}: " . $ex->getMessage();
                        }
                    }
                    return $errors
                        ? '❌ ' . implode('; ', $errors)
                        : "OK, {$companies->count()} firm przetworzono";
                },

                'CRM compiled view' => static fn () => file_exists(resource_path('views/crm/index.blade.php'))
                    ? 'plik OK (' . round(filesize(resource_path('views/crm/index.blade.php')) / 1024, 1) . ' KB)'
                    : 'BRAK pliku widoku',

                'CRM cached view exists' => static function () {
                    $path = storage_path('framework/views');
                    if (! is_dir($path)) { return 'brak katalogu cache widoków'; }
                    $files = glob($path . '/*.php');
                    return ($files !== false ? count($files) : 0) . ' widoków w cache';
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

        // ─── Audits Probe ─────────────────────────────────────────────────
        $auditsProbe      = [];
        $auditsProbeError = null;
        if ($dbOk) {
            $auditSteps = [
                'AuditType::count()' => static fn () => AuditType::count() . ' rodzajów audytów',

                'EnergyAudit::count()' => static fn () => EnergyAudit::count() . ' audytów w tabeli',

                'energy_audits company_id nullable?' => static function () {
                    $col = DB::selectOne(
                        "SELECT IS_NULLABLE, COLUMN_TYPE FROM information_schema.COLUMNS
                         WHERE TABLE_SCHEMA = DATABASE()
                           AND TABLE_NAME = 'energy_audits'
                           AND COLUMN_NAME = 'company_id'"
                    );
                    if (! $col) {
                        throw new \RuntimeException('Kolumna company_id nie istnieje w energy_audits');
                    }
                    $nullable = ($col->IS_NULLABLE === 'YES');
                    if (! $nullable) {
                        throw new \RuntimeException(
                            'company_id IS NOT NULL – formularz "Nowy audyt" z opcją "Brak" firmy spowoduje błąd 500! '
                            . 'Uruchom migrację 2026_03_31_120000_make_company_id_nullable_in_energy_audits_table.php'
                        );
                    }
                    return 'OK – company_id jest nullable (' . $col->COLUMN_TYPE . ')';
                },

                'EnergyAudit create dry-run (bez firmy)' => static function () {
                    $type = AuditType::first();
                    if (! $type) {
                        return 'Pominięto – brak rodzajów audytów w bazie';
                    }
                    DB::beginTransaction();
                    try {
                        EnergyAudit::create([
                            'title'         => '__diag_test__',
                            'audit_type_id' => $type->id,
                            'audit_type'    => $type->name,
                            'status'        => 'in_progress',
                            'company_id'    => null,
                            'auditor_id'    => null,
                            'completed_at'  => null,
                            'data_payload'  => [],
                        ]);
                        DB::rollBack();
                        return 'OK – INSERT bez firmy powiódł się (rollback)';
                    } catch (Throwable $e) {
                        DB::rollBack();
                        throw $e;
                    }
                },

                'EnergyAudit::with relations (próbka)' => static fn () =>
                    EnergyAudit::with(['company', 'auditor', 'auditType'])->limit(3)->get()->count() . ' (próbka)',

                'AuditType sections (próbka)' => static fn () =>
                    AuditType::with('sections')->limit(3)->get()
                        ->map(fn ($t) => $t->name . ' (' . $t->sections->count() . ' sekcji)')
                        ->implode(', ') ?: 'brak typów',
            ];
            foreach ($auditSteps as $label => $fn) {
                try {
                    $detail = $fn();
                    $auditsProbe[] = ['label' => $label, 'ok' => true, 'detail' => $detail];
                } catch (Throwable $e) {
                    $auditsProbe[] = ['label' => $label, 'ok' => false, 'detail' => $e->getMessage()];
                    if ($auditsProbeError === null) {
                        $auditsProbeError = [
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

        // ─── Mail configuration info ───────────────────────────────────────
        $mailConfig = [
            'mailer'       => config('mail.default', 'n/a'),
            'host'         => config('mail.mailers.smtp.host', 'n/a'),
            'port'         => config('mail.mailers.smtp.port', 'n/a'),
            'scheme'       => config('mail.mailers.smtp.scheme', config('mail.mailers.smtp.encryption', 'n/a')),
            'username'     => config('mail.mailers.smtp.username', 'n/a'),
            'from_address' => config('mail.from.address', 'n/a'),
            'from_name'    => config('mail.from.name', 'n/a'),
            'password_set' => ! empty(config('mail.mailers.smtp.password')),
        ];

        return view('diagnostics.index', compact(
            'dbOk', 'dbError', 'dbDriver',
            'tableStatus', 'migrationOutput', 'migrationError', 'pendingCount',
            'recentErrors', 'sysInfo', 'columnChecks', 'crmProbe', 'crmProbeError',
            'auditsProbe', 'auditsProbeError',
            'mailConfig'
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

    /**
     * Test mail connectivity and optionally send a test message (POST).
     * Steps: 1) verify config, 2) TCP socket test on configured port,
     *         3) TCP socket test on fallback port 587, 4) send via Laravel Mail.
     */
    public function testMail(Request $request)
    {
        $steps   = [];
        $target  = filter_var($request->input('to_email', ''), FILTER_VALIDATE_EMAIL)
            ? $request->input('to_email')
            : null;

        // 1. Read effective config
        $mailer   = config('mail.default', 'log');
        $host     = config('mail.mailers.smtp.host', '');
        $port     = (int) config('mail.mailers.smtp.port', 465);
        $username = config('mail.mailers.smtp.username', '');
        $password = config('mail.mailers.smtp.password', '');
        $scheme   = config('mail.mailers.smtp.scheme', config('mail.mailers.smtp.encryption', ''));

        $steps[] = [
            'label' => 'Aktywny mailer (MAIL_MAILER)',
            'ok'    => $mailer !== 'log',
            'detail'=> $mailer . ($mailer === 'log' ? ' — ⚠ maile idą TYLKO do logów, nie są wysyłane!' : ' — OK'),
        ];

        // Helper: TCP socket test
        $socketTest = function (string $h, int $p, string $sc): array {
            $prefix = ($sc === 'ssl' || $sc === 'smtps') ? 'ssl://' : '';
            $errno = 0; $errstr = '';
            $t0 = microtime(true);
            $fp = @fsockopen($prefix . $h, $p, $errno, $errstr, 8);
            $ms = round((microtime(true) - $t0) * 1000);
            if ($fp) { fclose($fp); return ['ok' => true, 'ms' => $ms, 'error' => '']; }
            return ['ok' => false, 'ms' => $ms, 'error' => "errno={$errno}: {$errstr}"];
        };

        // 2. Socket test on configured port
        if ($mailer === 'smtp' && $host) {
            $r = $socketTest($host, $port, $scheme);
            $steps[] = [
                'label'  => "Połączenie TCP z {$host}:{$port} (skonfigurowany)",
                'ok'     => $r['ok'],
                'detail' => $r['ok']
                    ? "✅ Połączono ({$r['ms']} ms)"
                    : "❌ Zablokowane ({$r['ms']} ms): {$r['error']}" . ($r['ms'] > 7000 ? ' — port zablokowany przez firewall!' : ''),
            ];

            // 3. Fallback test: port 587 (STARTTLS)
            if (!$r['ok'] && $port !== 587) {
                $r587 = $socketTest($host, 587, 'tls');
                $steps[] = [
                    'label'  => "Połączenie TCP z {$host}:587 (fallback STARTTLS)",
                    'ok'     => $r587['ok'],
                    'detail' => $r587['ok']
                        ? "✅ Port 587 działa! Zmień MAIL_PORT=587 i MAIL_SCHEME=tls w zmiennych Railway."
                        : "❌ Port 587 też zablokowany ({$r587['ms']} ms). Railway blokuje wychodzące SMTP — wymagany Resend lub inny HTTP API.",
                ];
            }

            // 4. If both ports blocked, recommend Resend
            if (!$r['ok']) {
                $port587Works = isset($r587) && $r587['ok'];
                if (!$port587Works) {
                    $steps[] = [
                        'label'  => '💡 Zalecenie',
                        'ok'     => false,
                        'detail' => 'Railway blokuje SMTP (porty 465 i 587). Wymagane przejście na serwis oparty na HTTP API: Resend (resend.com) — darmowy do 3000 maili/mc, Laravel ma wbudowany driver. Instrukcja poniżej na tej stronie.',
                    ];
                }
            }
        }

        // 5. Send test mail (only if target provided)
        if ($target) {
            $sendOk    = false;
            $sendError = '';
            $elapsed   = 0;
            $t0        = microtime(true);
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    'To jest testowa wiadomość z systemu ENESA. Jeśli ją widzisz — konfiguracja e-mail działa poprawnie.',
                    function ($msg) use ($target) {
                        $msg->to($target)
                            ->subject('[ENESA] Test połączenia e-mail — ' . now()->format('Y-m-d H:i:s'));
                    }
                );
                $elapsed = round((microtime(true) - $t0) * 1000);
                $sendOk  = true;
            } catch (Throwable $e) {
                $elapsed   = round((microtime(true) - $t0) * 1000);
                $sendError = $e->getMessage();
            }
            $steps[] = [
                'label'  => "Wysyłka testowego maila na {$target}",
                'ok'     => $sendOk,
                'detail' => $sendOk
                    ? "✅ Wysłano pomyślnie ({$elapsed} ms) — sprawdź skrzynkę i folder SPAM"
                    : "❌ Błąd ({$elapsed} ms): {$sendError}",
            ];
        }

        return response()->json(['steps' => $steps]);
    }
}
