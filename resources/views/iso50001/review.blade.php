<x-layouts.app>

    <style>
        .review-header {
            background: linear-gradient(135deg, #102838 0%, #0f5f8f 55%, #23a75a 100%);
            color: #fff;
            border-radius: 15px;
            padding: 22px;
            display: grid;
            gap: 7px;
        }
        .review-grid {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .answer-table td:first-child {
            width: 30%;
            font-weight: 700;
            color: #355267;
        }
        .status-chip {
            display: inline-flex;
            align-items: center;
            background: #f2f7fb;
            border: 1px solid #d7e5f0;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #2b4f67;
        }
    </style>

    <section class="review-header">
        <h2 style="margin:0;">Kontrola audytu ISO 50001</h2>
        <div>Audyt: <strong>{{ $audit->title }}</strong></div>
        <div>Firma: <strong>{{ $audit->company?->name ?? '—' }}</strong></div>
        <div>Klient: <strong>{{ $audit->creator?->name ?? '—' }}</strong></div>
        <div>Data zakonczenia: <strong>{{ $audit->due_date?->format('d.m.Y') ?? '—' }}</strong></div>
        <div>
            Aktualny status:
            <span class="status-chip">{{ $statusOptions[$audit->status] ?? $audit->status }}</span>
        </div>
    </section>

    <div class="review-grid">
        @if(!empty($audit->reviewer_notes))
            <section class="panel" style="background:#fff8e9; border-color:#f0d6a5;">
                <h3 style="margin-top:0;">Uwagi audytora</h3>
                <p style="margin:0; color:#7a5315; white-space:pre-wrap;">{{ $audit->reviewer_notes }}</p>
            </section>
        @endif

        @foreach($steps as $step)
            @php($sectionAnswers = (array) ($audit->answers[$step['key']] ?? []))
            <section class="panel">
                <h3 style="margin-top:0;">{{ $step['title'] }}</h3>
                <p class="muted">{{ $step['description'] }}</p>

                <table class="answer-table">
                    <tbody>
                    @foreach($step['fields'] as $field)
                        <tr>
                            <td>{{ $field['label'] }}</td>
                            <td>{{ $sectionAnswers[$field['name']] ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach

        @if($canReview)
            <section class="panel" style="background:#f3f9ff; border-color:#cee2f4;">
                <h3 style="margin-top:0;">Panel audytora</h3>
                <form method="POST" action="{{ route('iso50001.review.update', $audit) }}" style="display:grid; gap:10px;">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="review-status" style="display:block; margin-bottom:6px; font-weight:700;">Status</label>
                        <select id="review-status" name="status" required>
                            <option value="in_review" @selected($audit->status === 'in_review')>{{ $statusOptions['in_review'] }}</option>
                            <option value="changes_required" @selected($audit->status === 'changes_required')>{{ $statusOptions['changes_required'] }}</option>
                            <option value="approved" @selected($audit->status === 'approved')>{{ $statusOptions['approved'] }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="review-notes" style="display:block; margin-bottom:6px; font-weight:700;">Uwagi do klienta</label>
                        <textarea id="review-notes" name="reviewer_notes" rows="5" placeholder="Np. W kroku 2 doprecyzuj obszary SEU i dodaj dane bazowe.">{{ old('reviewer_notes', $audit->reviewer_notes) }}</textarea>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit">Zapisz ocenę</button>
                        <a href="{{ route('iso50001.index') }}" style="display:inline-block; text-decoration:none; background:#eff6fb; color:#204a66; padding:8px 10px; border-radius:9px; border:1px solid #d2e4f1;">Powrót do listy</a>
                    </div>
                </form>
            </section>
        @else
            <section class="panel">
                <a href="{{ route('iso50001.step', ['isoAudit' => $audit, 'step' => max(1, (int) $audit->current_step)]) }}" style="text-decoration:none; color:#0e89d8; font-weight:700;">Wróć do formularza krokowego</a>
            </section>
        @endif
    </div>

</x-layouts.app>
