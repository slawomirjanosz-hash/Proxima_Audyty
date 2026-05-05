<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zadania po terminie – CRM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #dc2626 0%, #b91c1c 100%); padding: 28px 36px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.82); font-size: 13px; margin-top: 4px; }
        .body { padding: 32px 36px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #0f2330; }
        .intro { color: #3b5567; line-height: 1.6; margin-bottom: 22px; }
        .alert-box { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #991b1b; }
        .task-item { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 12px; background: #fff; }
        .task-item:last-child { margin-bottom: 0; }
        .task-name { font-size: 14px; font-weight: 800; color: #0f2330; margin-bottom: 8px; }
        .meta-row { display: flex; gap: 10px; align-items: baseline; margin-bottom: 5px; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-label { font-size: 11px; font-weight: 700; color: #6b8aa3; text-transform: uppercase; letter-spacing: .5px; min-width: 80px; }
        .meta-value { font-size: 12px; color: #1a2e3d; font-weight: 600; }
        .overdue-days { display: inline-block; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; margin-left: 6px; }
        .priority-pilna  { color: #dc2626; }
        .priority-wysoka { color: #d97706; }
        .cta-wrap { text-align: center; margin: 24px 0; }
        .cta-btn { display: inline-block; padding: 12px 32px; background: linear-gradient(130deg, #dc2626, #b91c1c); color: #fff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 8px; }
        .footer { background: #1a2e3d; padding: 18px 36px; text-align: center; }
        .footer p { color: #7fa3be; font-size: 11px; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>⚠ Zadania po terminie</h1>
        <p>ENESA CRM – Przypomnienie</p>
    </div>

    <div class="body">
        <div class="greeting">Cześć, {{ $assignee->first_name ?? $assignee->name }}!</div>

        <p class="intro">
            Masz {{ $tasks->count() === 1 ? '1 zadanie' : $tasks->count() . ' zadania' }}
            w systemie CRM, {{ $tasks->count() === 1 ? 'które jest po terminie' : 'które są po terminie' }}.
            Prosimy o jak najszybsze zajęcie się {{ $tasks->count() === 1 ? 'nim' : 'nimi' }}.
        </p>

        <div class="alert-box">
            Będziesz otrzymywać to przypomnienie codziennie aż do zakończenia zadań.
        </div>

        @foreach($tasks as $task)
        @php
            $daysOverdue = (int) now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false) * -1;
        @endphp
        <div class="task-item">
            <div class="task-name">
                {{ $task->title }}
                <span class="overdue-days">+{{ $daysOverdue }} {{ $daysOverdue === 1 ? 'dzień' : 'dni' }} po terminie</span>
            </div>

            <div class="meta-row">
                <span class="meta-label">Typ</span>
                <span class="meta-value">{{ ucfirst(str_replace('_', ' ', $task->type)) }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Priorytet</span>
                <span class="meta-value priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Termin był</span>
                <span class="meta-value">{{ $task->due_date->format('d.m.Y') }}</span>
            </div>
            @if($task->company)
            <div class="meta-row">
                <span class="meta-label">Firma</span>
                <span class="meta-value">{{ $task->company->name }}</span>
            </div>
            @endif
        </div>
        @endforeach

        <div class="cta-wrap">
            <a href="{{ url('/crm') }}" class="cta-btn">Przejdź do CRM i zakończ zadania →</a>
        </div>
    </div>

    <div class="footer">
        <p>Wiadomość wygenerowana automatycznie przez <strong>ENESA System</strong>.<br>
           Nie odpowiadaj na tego maila.</p>
    </div>
</div>
</body>
</html>
