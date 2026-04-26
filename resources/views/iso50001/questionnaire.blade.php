<x-layouts.app>

    <style>
        .qst-header {
            background: linear-gradient(135deg, #163347 0%, #0e89d8 55%, #1ba84a 100%);
            color: #fff;
            border-radius: 16px;
            padding: 26px;
            display: grid;
            gap: 8px;
            box-shadow: 0 12px 34px rgba(14, 55, 85, .2);
        }
        .qst-header h2 { margin: 0; font-size: 28px; }
        .qst-header p { margin: 0; color: rgba(255,255,255,.88); max-width: 760px; font-size: 15px; }
        .qst-block {
            margin-top: 16px;
            border: 1px solid #d2e3f1;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(18, 72, 110, 0.06);
        }
        .qst-block-header {
            width: 100%;
            text-align: left;
            border: none;
            background: #f0f7fc;
            padding: 14px 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 16px;
            color: #0e344e;
            border-bottom: 1px solid #d2e3f1;
        }
        .qst-block-header:hover { background: #e4f0fa; }
        .qst-block-body { padding: 16px; }
        .qst-row {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 10px 14px;
            align-items: start;
            padding: 10px 0;
            border-bottom: 1px solid #edf3f9;
        }
        .qst-row:last-child { border-bottom: none; padding-bottom: 0; }
        .qst-code {
            font-weight: 800;
            font-size: 13px;
            color: #0e89d8;
            background: #e8f4ff;
            border-radius: 8px;
            padding: 4px 8px;
            text-align: center;
            align-self: center;
        }
        .qst-question {
            font-size: 14px;
            font-weight: 600;
            color: #1c3a4e;
            margin-bottom: 5px;
        }
        .qst-hint {
            font-size: 12px;
            color: #6b8aa3;
            margin-bottom: 6px;
        }
        .qst-answer-area {
            width: 100%;
            border: 1px solid #c8d9e8;
            border-radius: 9px;
            padding: 8px 10px;
            font-size: 14px;
            background: #fafcff;
            resize: vertical;
            min-height: 40px;
            box-sizing: border-box;
        }
        .qst-answer-area:focus {
            outline: none;
            border-color: #0e89d8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(14, 137, 216, .12);
        }
        .qst-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #dce9f5;
        }
        .qst-progress {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #355c77;
            margin-bottom: 6px;
        }
        .qst-progress-bar {
            flex: 1;
            height: 8px;
            background: #dce9f4;
            border-radius: 999px;
            overflow: hidden;
        }
        .qst-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0e89d8, #1ba84a);
            border-radius: 999px;
            transition: width .4s ease;
        }
        .chevron-icon { font-size: 14px; color: #6b8aa3; transition: transform .2s; }
        .qst-block.open .chevron-icon { transform: rotate(180deg); }
        .qst-block-body { display: none; }
        .qst-block.open .qst-block-body { display: block; }
    </style>

    <section class="qst-header">
        <h2>Kwestionariusz wstępny ISO 50001</h2>
        <p>Wypełnienie kwestionariusza zajmuje ok. 15–20 minut. Odpowiedzi pomogą nam przygotować precyzyjną ofertę i skonfigurować asystenta AI do Twojego audytu.</p>
    </section>

    <div style="margin-top:14px; background:#fff; border:1px solid #d2e3f1; border-radius:14px; padding:14px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
            <div>
                <strong style="font-size:15px;">{{ $audit->title }}</strong>
                <span class="muted" style="font-size:13px;"> — {{ $audit->company?->name ?? '—' }}</span>
            </div>
            @if($audit->questionnaire_completed)
                <span style="background:#d9f5e8; border:1px solid #b3eacb; color:#1a6b3c; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700;">✓ Kwestionariusz wypełniony</span>
            @endif
        </div>

        <div class="qst-progress" style="margin-top:10px;">
            <span>Wypełniono:</span>
            <div class="qst-progress-bar">
                <div class="qst-progress-fill" id="progress-fill" style="width:0%"></div>
            </div>
            <span id="progress-text">0%</span>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-top:12px; padding:10px 14px; border:1px solid #b7dcb5; border-radius:10px; background:#f0faf0; color:#155724; font-size:13px;">
            {{ session('status') }}
        </div>
    @endif

    @if(session('draft_saved'))
        <div style="margin-top:12px; padding:10px 14px; border:1px solid #b7dcb5; border-radius:10px; background:#f0faf0; color:#155724; font-size:13px;">
            ✓ Kopia robocza zapisana.
        </div>
    @endif

    @if($errors->any())
        <div style="margin-top:12px; padding:10px 14px; border:1px solid #f5c2c7; border-radius:10px; background:#fff5f5; color:#842029; font-size:13px;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ $saveRoute ?? route('iso50001.questionnaire.save', $audit) }}" id="questionnaire-form">
        @csrf
        <input type="hidden" name="save_as_draft" id="draft-flag" value="0">

        @php
            $totalCount = 0;
            foreach ($questions as $block) { $totalCount += $block->count(); }
            $prefilled = $prefilled ?? [];
        @endphp

        @foreach($questions as $blockKey => $blockQuestions)
            <div class="qst-block open" id="block-{{ $blockKey }}">
                <button type="button" class="qst-block-header" onclick="toggleBlock('block-{{ $blockKey }}')">
                    <span>{{ $blockLabels[$blockKey] ?? 'Blok '.$blockKey }}</span>
                    <span class="chevron-icon">▼</span>
                </button>
                <div class="qst-block-body">
                    @foreach($blockQuestions as $question)
                        @php
                            $savedVal = old('answers.'.$question->question_code, $answers[$question->question_code] ?? null);
                            $displayVal = $savedVal ?? ($prefilled[$question->question_code] ?? '');
                            $isPrefilled = ($savedVal === null && isset($prefilled[$question->question_code]) && ($answers[$question->question_code] ?? null) === null);
                        @endphp
                        <div class="qst-row">
                            <div class="qst-code">{{ $question->question_code }}</div>
                            <div>
                                <div class="qst-question">{{ $question->question_text }}</div>
                                @if($question->answer_hint)
                                    <div class="qst-hint">np. {{ $question->answer_hint }}</div>
                                @endif
                                <textarea
                                    class="qst-answer-area{{ $isPrefilled ? ' prefilled-value' : '' }}"
                                    name="answers[{{ $question->question_code }}]"
                                    rows="2"
                                    placeholder="Wpisz odpowiedź…"
                                    oninput="markChanged()"
                                >{{ $displayVal }}</textarea>
                                @if($isPrefilled)
                                    <div style="font-size:11px; color:#0e89d8; margin-top:3px;">✦ Uzupełniono automatycznie na podstawie danych firmy — sprawdź i popraw jeśli potrzeba.</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="qst-actions">
            <button type="submit" style="background:linear-gradient(130deg,#0e89d8,#1ba84a); color:#fff; font-weight:700; font-size:15px; padding:10px 28px; border-radius:10px; border:none; cursor:pointer;">
                Zapisz i przejdź do audytu →
            </button>
            <a href="{{ $backRoute ?? route('iso50001.index') }}" style="display:inline-flex; align-items:center; padding:10px 18px; background:#eff6fb; color:#204a66; border:1px solid #d2e4f1; border-radius:10px; text-decoration:none; font-weight:600;">
                Wróć do listy
            </a>
        </div>
    </form>

    <script>
        const QST_STORAGE_KEY = 'qst_{{ $audit->id }}_{{ str_contains(get_class($audit), "Iso50001Audit") ? "iso" : "energy" }}';

        function toggleBlock(blockId) {
            const block = document.getElementById(blockId);
            if (block) block.classList.toggle('open');
        }

        function updateProgress() {
            const textareas = document.querySelectorAll('.qst-answer-area');
            let filled = 0;
            textareas.forEach(function(ta) {
                if (ta.value.trim().length > 0) filled++;
            });
            const total = textareas.length;
            const pct = total > 0 ? Math.round((filled / total) * 100) : 0;
            document.getElementById('progress-fill').style.width = pct + '%';
            document.getElementById('progress-text').textContent = pct + '%';
        }

        // Track unsaved changes
        let hasChanges = false;
        const draftBtn = document.getElementById('draft-btn');

        function markChanged() {
            updateProgress();
            saveToLocalStorage();
            if (!hasChanges) {
                hasChanges = true;
                draftBtn.style.background = '#dc2626';
                draftBtn.title = 'Masz niezapisane zmiany — kliknij aby zapisać kopię roboczą';
            }
        }

        function saveDraft() {
            document.getElementById('draft-flag').value = '1';
            clearLocalStorage();
            document.getElementById('questionnaire-form').submit();
        }

        // ---------- localStorage auto-save ----------

        function saveToLocalStorage() {
            const data = {};
            document.querySelectorAll('.qst-answer-area').forEach(function(ta) {
                const m = ta.name.match(/answers\[([^\]]+)\]/);
                if (m) data[m[1]] = ta.value;
            });
            try {
                localStorage.setItem(QST_STORAGE_KEY, JSON.stringify({ data, ts: Date.now() }));
            } catch(e) {}
        }

        function clearLocalStorage() {
            try { localStorage.removeItem(QST_STORAGE_KEY); } catch(e) {}
        }

        function restoreFromLocalStorage() {
            try {
                const raw = localStorage.getItem(QST_STORAGE_KEY);
                if (!raw) return;
                const saved = JSON.parse(raw);
                if (!saved || !saved.data) return;

                // Check if localStorage has any non-empty answer that's missing in the current form
                let hasExtra = false;
                for (const [code, val] of Object.entries(saved.data)) {
                    if (!val) continue;
                    const ta = document.querySelector('textarea[name="answers[' + code + ']"]');
                    if (ta && ta.value.trim() === '' && val.trim() !== '') {
                        hasExtra = true;
                        break;
                    }
                }
                if (!hasExtra) return;

                const banner = document.createElement('div');
                banner.id = 'restore-banner';
                banner.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:10000;background:#1d4f73;color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:14px;box-shadow:0 4px 14px rgba(0,0,0,.25);';
                banner.innerHTML = '<span>💾 Masz niezapisaną wersję roboczą z poprzedniej sesji. Czy chcesz ją przywrócić?</span>'
                    + '<div style="display:flex;gap:8px;">'
                    + '<button onclick="applyLocalDraft()" style="background:#16a34a;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-weight:700;cursor:pointer;">Przywróć</button>'
                    + '<button onclick="discardLocalDraft()" style="background:#6b8aa3;color:#fff;border:none;padding:7px 16px;border-radius:8px;cursor:pointer;">Odrzuć</button>'
                    + '</div>';
                document.body.prepend(banner);
                window._localDraft = saved.data;
            } catch(e) {}
        }

        function applyLocalDraft() {
            const data = window._localDraft || {};
            for (const [code, val] of Object.entries(data)) {
                const ta = document.querySelector('textarea[name="answers[' + code + ']"]');
                if (ta && val) ta.value = val;
            }
            document.getElementById('restore-banner')?.remove();
            hasChanges = true;
            draftBtn.style.background = '#dc2626';
            updateProgress();
        }

        function discardLocalDraft() {
            clearLocalStorage();
            document.getElementById('restore-banner')?.remove();
        }

        // Run on load
        updateProgress();
        restoreFromLocalStorage();

        // Clear localStorage when the final "save and continue" form is submitted
        document.getElementById('questionnaire-form').addEventListener('submit', function(e) {
            if (document.getElementById('draft-flag').value !== '1') {
                clearLocalStorage();
            }
        });
    </script>

    {{-- Floating draft save button --}}
    <button id="draft-btn"
            type="button"
            onclick="saveDraft()"
            title="Zapisz kopię roboczą"
            style="position:fixed; bottom:28px; right:28px; z-index:9999;
                   background:#16a34a; color:#fff; border:none; border-radius:50px;
                   padding:13px 22px; font-size:14px; font-weight:700; cursor:pointer;
                   box-shadow:0 6px 20px rgba(0,0,0,.25); display:flex; align-items:center; gap:8px;
                   transition: background .25s, box-shadow .25s;">
        <span style="font-size:18px;">💾</span> Zapisz kopię roboczą
    </button>

</x-layouts.app>
