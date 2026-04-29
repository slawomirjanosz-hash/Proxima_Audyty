<x-layouts.app>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .aw-wrap {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 0 80px;
        }

        /* ── Header ── */
        .aw-header {
            background: #fff;
            border: 1px solid #d7e5f0;
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
        }
        .aw-header-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0e89d8, #0a5f9e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .aw-header-info { flex: 1; min-width: 0; }
        .aw-header-title { font-size: 17px; font-weight: 800; color: #0f2330; margin: 0 0 3px; }
        .aw-header-sub { font-size: 12px; color: #4c6373; display: flex; gap: 10px; flex-wrap: wrap; }
        .aw-header-sub .badge {
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 6px;
            padding: 2px 8px;
            font-weight: 700;
            font-size: 11px;
        }
        .aw-back {
            font-size: 13px;
            color: #0e89d8;
            text-decoration: none;
            font-weight: 600;
            white-space: nowrap;
            align-self: center;
        }
        .aw-back:hover { text-decoration: underline; }

        /* ── Q&A history ── */
        .aw-history { margin-bottom: 20px; }
        .aw-pair {
            border: 1px solid #e3eff8;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 10px;
            background: #fbfdff;
        }
        .aw-question {
            background: #f0f8ff;
            border-bottom: 1px solid #e3eff8;
            padding: 12px 16px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .aw-question-icon {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #0e89d8;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .aw-question-text {
            font-size: 14px;
            color: #0f2330;
            line-height: 1.55;
            flex: 1;
            white-space: pre-wrap;
        }
        .aw-answer {
            padding: 10px 16px 10px 52px;
            font-size: 14px;
            color: #2c4a5e;
            line-height: 1.55;
            white-space: pre-wrap;
        }
        .aw-answer-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b8aa3;
            margin-bottom: 3px;
        }

        /* ── Active question / waiting ── */
        .aw-active {
            border: 2px solid #0e89d8;
            border-radius: 14px;
            background: #f0f8ff;
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .aw-active-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #0e89d8;
            margin-bottom: 8px;
        }
        .aw-active-text {
            font-size: 15px;
            color: #0f2330;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* ── Input area ── */
        .aw-input-wrap {
            background: #fff;
            border: 1px solid #d7e5f0;
            border-radius: 14px;
            padding: 16px 18px;
        }
        .aw-input-label {
            font-size: 12px;
            font-weight: 700;
            color: #4c6373;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .aw-textarea {
            width: 100%;
            min-height: 90px;
            border: 1px solid #c8dce9;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            color: #0f2330;
            resize: vertical;
            background: #f9fbfd;
            font-family: inherit;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .aw-textarea:focus { outline: none; border-color: #0e89d8; background: #fff; }
        .aw-input-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 10px;
        }
        .aw-btn-send {
            background: #0e89d8;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
        }
        .aw-btn-send:hover:not(:disabled) { background: #0a6faf; }
        .aw-btn-send:disabled { opacity: .55; cursor: not-allowed; }

        /* ── Thinking indicator ── */
        .aw-thinking {
            display: none;
            padding: 14px 18px;
            background: #f0f8ff;
            border: 1px dashed #7ec5ef;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #0e89d8;
            font-weight: 600;
        }
        .aw-thinking.visible { display: block; }

        /* ── Status finished ── */
        .aw-finished {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            color: #166534;
            margin-bottom: 18px;
        }
        .aw-finished strong { font-weight: 800; }

        /* ── Finish button ── */
        .aw-finish-wrap {
            display: none;
            margin-top: 20px;
            text-align: center;
        }
        .aw-finish-wrap.visible { display: block; }
        .aw-btn-finish {
            padding: 14px 36px;
            background: linear-gradient(130deg, #059669, #0e89d8);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: .3px;
        }
        .aw-btn-finish:hover { opacity: .9; }
        .aw-finish-note {
            margin-top: 8px;
            font-size: 12px;
            color: #4c6373;
        }

        /* ── Dot animation ── */
        @keyframes blink { 0%,80%,100%{opacity:0} 40%{opacity:1} }
        .dot { animation: blink 1.4s infinite both; }
        .dot:nth-child(2) { animation-delay: .2s; }
        .dot:nth-child(3) { animation-delay: .4s; }
    </style>

    <section class="panel">
        <div class="aw-wrap">

            {{-- Header --}}
            <div class="aw-header">
                <div class="aw-header-icon">🔍</div>
                <div class="aw-header-info">
                    <div class="aw-header-title">{{ $audit->title }}</div>
                    <div class="aw-header-sub">
                        <span class="badge">{{ $agentLabel }}</span>
                        <span>{{ $audit->statusLabel() }}</span>
                        <span>Audytor ENESA</span>
                    </div>
                </div>
                <a href="{{ route('strefa-klienta') }}" class="aw-back">&larr; Strefa klienta</a>
            </div>

            {{-- Q&A pairs (history, excluding the last AI message which is "active") --}}
            @php
                $pairs = [];
                $pendingQ = null;
                foreach ($messages as $msg) {
                    if ($msg->role === 'assistant') {
                        $pendingQ = $msg->content;
                    } elseif ($msg->role === 'user' && $pendingQ !== null) {
                        $pairs[] = ['q' => $pendingQ, 'a' => $msg->content];
                        $pendingQ = null;
                    }
                }
                // Last assistant message (not yet answered) → active question
                $lastAssistant = $messages->last(fn($m) => $m->role === 'assistant');
                $lastUser      = $messages->last(fn($m) => $m->role === 'user');
                // Active question = last assistant message if it came after the last user message
                $activeQuestion = null;
                if ($lastAssistant) {
                    if (!$lastUser || $lastAssistant->id > $lastUser->id) {
                        $activeQuestion = $lastAssistant->content;
                    }
                }
            @endphp

            {{-- History --}}
            @if (count($pairs) > 0)
                <div class="aw-history" id="aw-history">
                    @foreach ($pairs as $i => $pair)
                        <div class="aw-pair">
                            <div class="aw-question">
                                <div class="aw-question-icon">AI</div>
                                <div class="aw-question-text">{{ $pair['q'] }}</div>
                            </div>
                            <div class="aw-answer">
                                <div class="aw-answer-label">Twoja odpowiedź</div>
                                {{ $pair['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div id="aw-history"></div>
            @endif

            {{-- Thinking indicator --}}
            <div class="aw-thinking" id="aw-thinking">
                System analizuje Twoją odpowiedź<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
            </div>

            {{-- Active question --}}
            @if ($activeQuestion)
                <div class="aw-active" id="aw-active-wrap">
                    <div class="aw-active-label">Pytanie audytora</div>
                    <div class="aw-active-text" id="aw-active-text">{{ $activeQuestion }}</div>
                </div>
            @else
                <div class="aw-active" id="aw-active-wrap" style="display:none;">
                    <div class="aw-active-label">Pytanie audytora</div>
                    <div class="aw-active-text" id="aw-active-text"></div>
                </div>
            @endif

            {{-- Input --}}
            <div class="aw-input-wrap" id="aw-input-section">
                <div class="aw-input-label">Twoja odpowiedź</div>
                <textarea
                    id="aw-answer"
                    class="aw-textarea"
                    placeholder="Wpisz swoją odpowiedź..."
                    rows="4"
                ></textarea>
                <div class="aw-input-actions">
                    <button type="button" id="aw-send-btn" class="aw-btn-send" onclick="sendAnswer()">
                        Wyślij odpowiedź
                    </button>
                </div>
            </div>

            {{-- Finish audit button --}}
            <div class="aw-finish-wrap" id="aw-finish-wrap">
                <form method="POST" action="{{ route('client.audit.finish', ['audit' => $audit->id, 'conversation' => $conversation->id]) }}">
                    @csrf
                    <button type="submit" class="aw-btn-finish">
                        ✅ Zakończ audyt i zapisz wyniki
                    </button>
                </form>
                <p class="aw-finish-note">Twoje odpowiedzi zostaną zapisane i przekazane do analizy specjalistom.</p>
            </div>

        </div>
    </section>

    <script>
    const conversationId = {{ $conversation->id }};
    const csrfToken      = document.querySelector('meta[name="csrf-token"]').content;

    const historyEl   = document.getElementById('aw-history');
    const activeWrap  = document.getElementById('aw-active-wrap');
    const activeText  = document.getElementById('aw-active-text');
    const answerEl    = document.getElementById('aw-answer');
    const sendBtn     = document.getElementById('aw-send-btn');
    const thinkingEl  = document.getElementById('aw-thinking');
    const inputSection = document.getElementById('aw-input-section');
    const finishWrap  = document.getElementById('aw-finish-wrap');

    // Phrases that signal the AI has finished collecting data
    const COMPLETION_PHRASES = [
        'mam teraz wystarczające dane',
        'czy mogę przygotować podsumowanie',
        'mam wszystkie niezbędne informacje',
        'zebrałem wystarczające informacje',
        'dziękuję za wszystkie informacje',
    ];

    function isCompletionMessage(text) {
        const lower = text.toLowerCase();
        return COMPLETION_PHRASES.some(phrase => lower.includes(phrase));
    }

    function showFinishButton() {
        inputSection.style.display = 'none';
        finishWrap.classList.add('visible');
    }

    // Check initial active question for completion (e.g. page reload after completion message)
    @if($activeQuestion ?? false)
        if (isCompletionMessage({!! json_encode($activeQuestion) !!})) {
            showFinishButton();
        }
    @endif

    // Send answer on Enter (Shift+Enter for newline)
    answerEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendAnswer();
        }
    });

    var _awRlPending = false;
    var _awRlTimer   = null;

    function awShowRateLimitCountdown(seconds, retryAnswer) {
        if (_awRlTimer) clearInterval(_awRlTimer);
        var rem = seconds;
        _awRlPending = true;
        sendBtn.disabled = true;
        answerEl.disabled = true;
        thinkingEl.textContent = 'Limit API \u2014 ponawianie za ' + rem + 's\u2026';
        thinkingEl.classList.add('visible');
        activeWrap.style.display = 'none';
        _awRlTimer = setInterval(async function () {
            rem--;
            if (rem > 0) {
                thinkingEl.textContent = 'Limit API \u2014 ponawianie za ' + rem + 's\u2026';
                return;
            }
            clearInterval(_awRlTimer); _awRlTimer = null;
            thinkingEl.textContent = 'Asystent AI \u2026';
            answerEl.disabled = false;
            try {
                const res2 = await fetch(`/ai/${conversationId}/wiadomosc`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ message: retryAnswer }),
                });
                const d2 = await res2.json();
                thinkingEl.classList.remove('visible');
                if (d2.response) {
                    activeText.textContent = d2.response;
                    activeWrap.style.display = '';
                    if (isCompletionMessage(d2.response)) showFinishButton();
                } else if (d2.rate_limited) {
                    awShowRateLimitCountdown(d2.retry_after || 30, retryAnswer);
                    return;
                } else {
                    activeText.textContent = d2.error || 'B\u0142\u0105d asystenta. Spr\u00f3buj ponownie.';
                    activeWrap.style.display = '';
                }
            } catch (err2) {
                thinkingEl.classList.remove('visible');
                activeText.textContent = 'B\u0142\u0105d po\u0142\u0105czenia. Spr\u00f3buj ponownie.';
                activeWrap.style.display = '';
            } finally {
                _awRlPending = false;
                sendBtn.disabled = false;
                answerEl.disabled = false;
                answerEl.focus();
            }
        }, 1000);
    }

    async function sendAnswer() {
        const answer = answerEl.value.trim();
        if (!answer) return;

        const currentQuestion = activeText.textContent.trim();

        // Disable UI
        sendBtn.disabled = true;
        answerEl.disabled = true;

        // Move current question + answer to history
        if (currentQuestion) {
            appendPairToHistory(currentQuestion, answer);
        }

        // Show thinking
        activeWrap.style.display = 'none';
        thinkingEl.classList.add('visible');
        answerEl.value = '';

        try {
            const res = await fetch(`/ai/${conversationId}/wiadomosc`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message: answer }),
            });

            const data = await res.json();

            thinkingEl.classList.remove('visible');

            if (data.response) {
                // Show new AI question
                activeText.textContent = data.response;
                activeWrap.style.display = '';

                // Check if AI signals completion
                if (isCompletionMessage(data.response)) {
                    showFinishButton();
                }
            } else if (data.rate_limited) {
                awShowRateLimitCountdown(data.retry_after || 30, answer);
            } else if (!data.success) {
                activeText.textContent = data.error || 'B\u0142\u0105d asystenta. Od\u015bwie\u017c stron\u0119 i spr\u00f3buj ponownie.';
                activeWrap.style.display = '';
            }

        } catch (err) {
            thinkingEl.classList.remove('visible');
            activeText.textContent = 'Wyst\u0105pi\u0142 b\u0142\u0105d komunikacji z asystentem. Od\u015bwie\u017c stron\u0119 i spr\u00f3buj ponownie.';
            activeWrap.style.display = '';
        } finally {
            if (!_awRlPending) {
                sendBtn.disabled = false;
                answerEl.disabled = false;
                answerEl.focus();
            }
        }
    }

    function appendPairToHistory(question, answer) {
        const pair = document.createElement('div');
        pair.className = 'aw-pair';
        pair.innerHTML =
            '<div class="aw-question">'
            + '<div class="aw-question-icon">EN</div>'
            + '<div class="aw-question-text">' + escHtml(question) + '</div>'
            + '</div>'
            + '<div class="aw-answer">'
            + '<div class="aw-answer-label">Twoja odpowiedź</div>'
            + escHtml(answer)
            + '</div>';
        historyEl.appendChild(pair);
        pair.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function escHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>');
    }
    </script>

</x-layouts.app>
