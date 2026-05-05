<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowe zadanie CRM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #0e89d8 0%, #1ba84a 100%); padding: 28px 36px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.82); font-size: 13px; margin-top: 4px; }
        .body { padding: 32px 36px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #0f2330; }
        .intro { color: #3b5567; line-height: 1.6; margin-bottom: 22px; }
        .task-box { background: #f3f8fc; border: 1px solid #c9dcea; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; }
        .task-box .task-title { font-size: 16px; font-weight: 800; color: #0f2330; margin-bottom: 14px; }
        .meta-row { display: flex; gap: 10px; align-items: baseline; margin-bottom: 8px; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-label { font-size: 11px; font-weight: 700; color: #6b8aa3; text-transform: uppercase; letter-spacing: .5px; min-width: 90px; }
        .meta-value { font-size: 13px; color: #1a2e3d; font-weight: 600; }
        .meta-value.priority-pilna  { color: #dc2626; }
        .meta-value.priority-wysoka { color: #d97706; }
        .desc-box { background: #fafcff; border-left: 3px solid #0e89d8; padding: 10px 14px; border-radius: 0 8px 8px 0; font-size: 13px; color: #3b5567; line-height: 1.6; margin-bottom: 20px; }
        .cta-wrap { text-align: center; margin: 20px 0 24px; }
        .cta-btn { display: inline-block; padding: 12px 32px; background: linear-gradient(130deg, #0e89d8, #1ba84a); color: #fff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 8px; }
        .footer { background: #1a2e3d; padding: 18px 36px; text-align: center; }
        .footer p { color: #7fa3be; font-size: 11px; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>ENESA CRM</h1>
        <p>Nowe zadanie do wykonania</p>
    </div>

    <div class="body">
        <div class="greeting">Cześć, {{ $assignee->first_name ?? $assignee->name }}!</div>

        <p class="intro">
            Zostało Ci przydzielone nowe zadanie w systemie ENESA CRM.
            Zapoznaj się ze szczegółami poniżej.
        </p>

        <div class="task-box">
            <div class="task-title">{{ $task->title }}</div>

            <div class="meta-row">
                <span class="meta-label">Typ</span>
                <span class="meta-value">{{ ucfirst(str_replace('_', ' ', $task->type)) }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Priorytet</span>
                <span class="meta-value priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Status</span>
                <span class="meta-value">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
            </div>
            @if($task->due_date)
            <div class="meta-row">
                <span class="meta-label">Termin</span>
                <span class="meta-value">{{ $task->due_date->format('d.m.Y') }}</span>
            </div>
            @endif
            @if($task->company)
            <div class="meta-row">
                <span class="meta-label">Firma</span>
                <span class="meta-value">{{ $task->company->name }}</span>
            </div>
            @endif
            @if($task->deal)
            <div class="meta-row">
                <span class="meta-label">Szansa</span>
                <span class="meta-value">{{ $task->deal->name }}</span>
            </div>
            @endif
        </div>

        @if($task->description)
        <div class="desc-box">{{ $task->description }}</div>
        @endif

        <div class="cta-wrap">
            <a href="{{ url('/crm') }}" class="cta-btn">Przejdź do CRM →</a>
        </div>
    </div>

    <div class="footer">
        <p>Wiadomość wygenerowana automatycznie przez <strong>ENESA System</strong>.<br>
           Nie odpowiadaj na tego maila.</p>
    </div>
</div>
</body>
</html>
