<x-layouts.app>

    <style>
        .iso-step-header {
            background: linear-gradient(140deg, #0f2330 0%, #0d4f77 50%, #1a8f4b 100%);
            border-radius: 16px;
            color: #fff;
            padding: 20px;
            display: grid;
            gap: 8px;
        }
        .iso-step-header h2 { margin: 0; }
        .iso-progress {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat({{ $totalSteps }}, minmax(0, 1fr));
            gap: 8px;
        }
        .iso-progress-item {
            border: 1px solid #d7e5f0;
            border-radius: 10px;
            padding: 10px;
            font-size: 12px;
            line-height: 1.35;
            background: #f7fbff;
            color: #315066;
            font-weight: 700;
        }
        .iso-progress-item.active {
            background: linear-gradient(135deg, #0e89d8 0%, #1ba84a 100%);
            border-color: transparent;
            color: #fff;
        }
        .iso-progress-item.done {
            background: #e9f7ef;
            border-color: #c8ead5;
            color: #1d6a39;
        }
        .iso-field-grid {
            display: grid;
            gap: 14px;
        }
        .iso-field {
            border: 1px solid #dbe7f0;
            border-radius: 12px;
            padding: 12px;
            background: #fbfeff;
        }
        .iso-field label {
            display: flex;
            gap: 6px;
            align-items: center;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .iso-help {
            width: 19px;
            height: 19px;
            border-radius: 50%;
            border: 0;
            background: #dff0ff;
            color: #0f4e7a;
            font-size: 12px;
            cursor: pointer;
            font-weight: 800;
            line-height: 19px;
            text-align: center;
            padding: 0;
        }
        .iso-actions {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .iso-actions button {
            min-width: 130px;
        }
        .btn-subtle {
            background: #eff6fb;
            color: #204a66;
            border: 1px solid #d2e4f1;
        }
        .help-modal {
            position: fixed;
            inset: 0;
            background: rgba(5, 18, 28, .45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 300;
            padding: 20px;
        }
        .help-modal.open { display: flex; }
        .help-box {
            width: min(560px, 100%);
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            border: 1px solid #d5e0ea;
            box-shadow: 0 14px 34px rgba(14,55,85,.2);
        }
        .help-box h4 { margin: 0 0 8px; }
    </style>

    <section class="iso-step-header">
        <h2>{{ $audit->title }}</h2>
        <div>Firma: <strong>{{ $audit->company?->name ?? '—' }}</strong></div>
        <div>Data zakonczenia: <strong>{{ $audit->due_date?->format('d.m.Y') ?? '—' }}</strong></div>
        <div>Status: <strong>{{ $statusOptions[$audit->status] ?? $audit->status }}</strong></div>
    </section>

    <div class="iso-progress">
        @foreach($steps as $stepIndex => $stepItem)
            @php($displayStep = $stepIndex + 1)
            @php($stateClass = $displayStep < $step ? 'done' : ($displayStep === $step ? 'active' : ''))
            <div class="iso-progress-item {{ $stateClass }}">
                {{ $displayStep }} / {{ $totalSteps }}<br>{{ $stepItem['title'] }}
            </div>
        @endforeach
    </div>

    <section class="panel">
        <h3 style="margin-top:0;">{{ $stepDefinition['title'] }}</h3>
        <p class="muted">{{ $stepDefinition['description'] }}</p>

        <form method="POST" action="{{ route('iso50001.step.update', ['isoAudit' => $audit, 'step' => $step]) }}">
            @csrf
            @method('PATCH')

            <div class="iso-field-grid">
                @foreach($stepDefinition['fields'] as $field)
                    @php($fieldValue = old($field['name'], $answers[$field['name']] ?? ''))
                    @php($dependsOn = $field['depends_on'] ?? '')
                    @php($dependsValue = $field['depends_value'] ?? '')

                    <div class="iso-field" data-depends-on="{{ $dependsOn }}" data-depends-value="{{ $dependsValue }}" @if($dependsOn !== '') style="display:none;" @endif>
                        <label for="field-{{ $field['name'] }}">
                            {{ $field['label'] }}
                            @if(!empty($field['required']))
                                <span style="color:#d33;">*</span>
                            @endif
                            @if(!empty($field['help']))
                                <button type="button" class="iso-help" data-help="{{ $field['help'] }}">?</button>
                            @endif
                        </label>

                        @if($field['type'] === 'textarea')
                            <textarea id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" rows="4">{{ $fieldValue }}</textarea>
                        @elseif($field['type'] === 'select')
                            <select id="field-{{ $field['name'] }}" name="{{ $field['name'] }}" data-field-name="{{ $field['name'] }}">
                                <option value="">Wybierz</option>
                                @foreach($field['options'] ?? [] as $option)
                                    <option value="{{ $option }}" @selected((string) $fieldValue === (string) $option)>{{ ucfirst($option) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="field-{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ $fieldValue }}" data-field-name="{{ $field['name'] }}">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="iso-actions">
                @if($step > 1)
                    <button name="action" value="previous" class="btn-subtle">Poprzedni krok</button>
                @endif

                <button name="action" value="save" class="btn-subtle">Zapisz roboczo</button>

                @if($step < $totalSteps)
                    <button name="action" value="next">Następny krok</button>
                @else
                    <button name="action" value="next">Zapisz podsumowanie</button>
                @endif
            </div>
        </form>
    </section>

    @if($step === $totalSteps)
        <section class="panel" style="background:#f2f8ff; border-color:#c8ddf2;">
            <h3 style="margin-top:0;">Finalizacja audytu</h3>
            <p class="muted">Po kliknięciu audyt trafi do audytora i otrzyma status „Przesłany do audytora”.</p>
            <form method="POST" action="{{ route('iso50001.submit', $audit) }}">
                @csrf
                @method('PATCH')
                <button type="submit">Wyślij do audytora</button>
            </form>
        </section>
    @endif

    <section class="panel">
        <a href="{{ route('iso50001.review', $audit) }}" style="text-decoration:none; color:#0e89d8; font-weight:700;">Podgląd wszystkich odpowiedzi i statusu kontroli</a>
    </section>

    <div class="help-modal" id="help-modal">
        <div class="help-box">
            <h4>Podpowiedź</h4>
            <p id="help-modal-text" style="margin:0; color:#315066;"></p>
            <div style="margin-top:14px;">
                <button type="button" onclick="closeHelpModal()">Zamknij</button>
            </div>
        </div>
    </div>

    <script>
        function updateDependentFields() {
            document.querySelectorAll('[data-depends-on]').forEach(function (wrapper) {
                const dependsOn = wrapper.getAttribute('data-depends-on');
                const dependsValue = wrapper.getAttribute('data-depends-value');

                if (!dependsOn) {
                    return;
                }

                const parentField = document.querySelector('[name="' + dependsOn + '"]');
                if (!parentField) {
                    return;
                }

                const currentValue = (parentField.value || '').toString().toLowerCase().trim();
                const expectedValue = (dependsValue || '').toString().toLowerCase().trim();

                wrapper.style.display = currentValue === expectedValue ? 'block' : 'none';
            });
        }

        function openHelpModal(text) {
            const modal = document.getElementById('help-modal');
            const textBox = document.getElementById('help-modal-text');
            if (!modal || !textBox) {
                return;
            }

            textBox.textContent = text;
            modal.classList.add('open');
        }

        function closeHelpModal() {
            const modal = document.getElementById('help-modal');
            if (modal) {
                modal.classList.remove('open');
            }
        }

        document.querySelectorAll('.iso-help').forEach(function (button) {
            button.addEventListener('click', function () {
                openHelpModal(button.getAttribute('data-help') || 'Brak dodatkowej podpowiedzi.');
            });
        });

        document.querySelectorAll('select[data-field-name], input[data-field-name]').forEach(function (field) {
            field.addEventListener('change', updateDependentFields);
            field.addEventListener('input', updateDependentFields);
        });

        document.getElementById('help-modal')?.addEventListener('click', function (event) {
            if (event.target === this) {
                closeHelpModal();
            }
        });

        updateDependentFields();
    </script>

</x-layouts.app>
