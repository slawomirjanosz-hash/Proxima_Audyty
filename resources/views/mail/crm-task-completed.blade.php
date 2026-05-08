<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zadanie zakończone</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #1A4D3A 0%, #2E7D5C 100%); padding: 28px 36px; text-align: center; }
        .header h1 { color: #F5EFE0; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(245,239,224,.82); font-size: 13px; margin-top: 4px; }
        .body { padding: 32px 36px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #0f2330; }
        .intro { color: #3b5567; line-height: 1.6; margin-bottom: 22px; }
        .task-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; }
        .task-box .task-title { font-size: 16px; font-weight: 800; color: #1A4D3A; margin-bottom: 14px; }
        .meta-row { display: flex; gap: 10px; align-items: baseline; margin-bottom: 8px; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-label { font-size: 11px; font-weight: 700; color: #2E7D5C; text-transform: uppercase; letter-spacing: .5px; min-width: 90px; }
        .meta-value { font-size: 13px; color: #1a2e3d; font-weight: 600; }
        .footer { background: #1A4D3A; padding: 18px 36px; text-align: center; }
        .footer p { color: #A4C2A8; font-size: 11px; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>ENESA CRM</h1>
        <p>✅ Zadanie zostało zakończone</p>
    </div>

    <div class="body">
        <div class="greeting">Cześć, {{ $recipient->first_name ?? $recipient->name }}!</div>

        <p class="intro">
            Poniższe zadanie zostało oznaczone jako <strong>zakończone</strong> w systemie ENESA CRM.
        </p>

        <div class="task-box">
            <div class="task-title">✅ {{ $task->title }}</div>

            <div class="meta-row">
                <span class="meta-label">Typ</span>
                <span class="meta-value">{{ ucfirst(str_replace('_', ' ', $task->type)) }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Priorytet</span>
                <span class="meta-value">{{ ucfirst($task->priority) }}</span>
            </div>
            @if($task->company)
            <div class="meta-row">
                <span class="meta-label">Firma</span>
                <span class="meta-value">{{ $task->company->name }}</span>
            </div>
            @endif
            @if($task->due_date)
            <div class="meta-row">
                <span class="meta-label">Termin</span>
                <span class="meta-value">{{ $task->due_date->format('d.m.Y H:i') }}</span>
            </div>
            @endif
            @if($task->completed_at)
            <div class="meta-row">
                <span class="meta-label">Zakończono</span>
                <span class="meta-value">{{ $task->completed_at->format('d.m.Y H:i') }}</span>
            </div>
            @endif
            @if($task->assignedTo)
            <div class="meta-row">
                <span class="meta-label">Wykonał(a)</span>
                <span class="meta-value">{{ $task->assignedTo->name }}</span>
            </div>
            @endif
        </div>

        @if($task->description)
        <div style="background:#f8fafc; border-left:3px solid #2E7D5C; padding:10px 14px; border-radius:0 8px 8px 0; font-size:13px; color:#3b5567; line-height:1.6; margin-bottom:20px;">
            {{ $task->description }}
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Wiadomość wygenerowana automatycznie przez system ENESA.<br>
        © {{ date('Y') }} ENESA sp. z o. o.</p>
    </div>
</div>
</body>
</html>
