<x-layouts.app>
    <section class="panel">
        <h1 style="margin:0 0 6px;">{{ __('ui.dashboard.title') }}</h1>
        <p class="muted" style="margin:0 0 14px;">{{ __('ui.dashboard.subtitle') }}</p>

        <div style="display:inline-block; padding:8px 12px; border-radius:999px; background:#eaf5ff; color:#145086; border:1px solid #cfe2f5; font-weight:700; font-size:13px; margin-bottom:14px;">
            {{ __('ui.dashboard.count') }}: {{ $activeAudits->count() }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('ui.dashboard.columns.title') }}</th>
                    <th>{{ __('ui.dashboard.columns.company') }}</th>
                    <th>{{ __('ui.dashboard.columns.auditor') }}</th>
                    <th>{{ __('ui.dashboard.columns.status') }}</th>
                    <th>{{ __('ui.dashboard.columns.updated') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeAudits as $audit)
                    <tr>
                        <td>{{ $audit->title }}</td>
                        <td>{{ $audit->company?->name ?? '-' }}</td>
                        <td>{{ $audit->auditor?->name ?? '-' }}</td>
                        <td>{{ $audit->status }}</td>
                        <td>{{ $audit->updated_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">{{ __('ui.dashboard.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
