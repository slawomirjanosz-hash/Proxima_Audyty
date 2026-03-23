<x-layouts.app>
    <section class="panel">
        <style>
            .audit-info-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; }
            .audit-info-card { border:1px solid #dbe8f3; border-radius:12px; background:#f9fcff; padding:10px; }
            .audit-info-card strong { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:4px; }
            .audit-section { border:1px solid #d7e5f0; border-left:5px solid #7fb4e1; border-radius:12px; padding:12px; background:#f8fbff; margin-top:12px; }
            .audit-section h4 { margin:0 0 8px; font-size:16px; color:#10344c; }
            .audit-data-table { width:100%; border-collapse:collapse; margin-top:10px; }
            .audit-data-table th, .audit-data-table td { border:1px solid #e0ecf5; padding:8px; font-size:13px; text-align:left; }
            .audit-data-table th { background:#eef5fb; color:#2c4e67; font-weight:700; }
            .audit-data-table-wrap { position:relative; }
            .dependency-tree-overlay { position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:2; }
            .dependency-tree-overlay .dependency-path { fill:none; stroke:#9ec2df; stroke-width:1.5; stroke-linecap:round; stroke-linejoin:round; opacity:.9; transition:stroke .15s, stroke-width .15s, opacity .15s; }
            .dependency-tree-overlay .dependency-path.dependency-path-active { stroke:#2f78af; stroke-width:2.3; opacity:1; }
            .audit-data-table tr.dependency-branch-active td:first-child { background:#eef6ff; }
            .audit-data-table tr.is-dependent-row td:first-child { position:relative; padding-left:calc(30px + (var(--dependency-depth, 1) - 1) * 16px); color:#264e6b; }
            .audit-data-table tr.is-dependent-row td:first-child::before { content:attr(data-dependency-label); position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#1d4f73; font-weight:800; font-size:14px; line-height:1; letter-spacing:-1px; min-width:18px; text-align:center; }
            .audit-formulas { margin-top:12px; border:1px solid #d7e5f0; border-radius:12px; padding:10px; background:#f7fbff; }
            .formula-line { display:flex; gap:8px; align-items:baseline; font-size:13px; color:#2c4e67; padding:6px 0; border-bottom:1px solid #e0ecf5; }
            .formula-line:last-child { border-bottom:none; }
            .formula-label { font-weight:700; }
            .btn-secondary { background:#dbe9f5; color:#1d4f73; }
            @media (max-width: 900px) {
                .audit-info-grid { grid-template-columns:1fr; }
            }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <h1 style="margin:0;">Info audytu</h1>
                <p class="muted" style="margin:4px 0 0;">Podgląd danych bez możliwości edycji.</p>
            </div>
            <a href="{{ route('audits.index', ['tab' => 'in-progress']) }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">← Wróć do audytów</a>
        </div>

        <div class="audit-info-grid">
            <div class="audit-info-card">
                <strong>Nazwa audytu</strong>
                {{ $audit->title }}
            </div>
            <div class="audit-info-card">
                <strong>Rodzaj audytu</strong>
                {{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Status</strong>
                {{ $audit->status }}
            </div>
            <div class="audit-info-card">
                <strong>Firma</strong>
                {{ $audit->company?->name ?? '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Audytor</strong>
                {{ $audit->auditor?->name ?? '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Ostatnia aktualizacja</strong>
                {{ $audit->updated_at?->format('Y-m-d H:i') ?? '—' }}
            </div>
        </div>

        @php
            $payload = is_array($audit->data_payload) ? $audit->data_payload : [];
            $sections = $audit->auditType?->sections ?? collect();
        @endphp

        <div style="margin-top:14px;">
            <?php if ($sections->isNotEmpty()): ?>
                <?php foreach ($sections as $sectionIndex => $section): ?>
                    @php
                        $sectionPayload = $payload[(string) $section->id] ?? [];
                        $taskValues = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
                        $sectionTasks = is_array($section->tasks ?? null) ? $section->tasks : [];
                        $sectionFormulas = is_array($section->formulas ?? null) ? $section->formulas : [];
                        $fieldRows = collect($section->data_fields ?? [])->map(function ($field) {
                            if (is_array($field)) {
                                return [
                                    'key' => trim((string) ($field['key'] ?? \Illuminate\Support\Str::slug((string) ($field['name'] ?? ''), '_'))),
                                    'name' => trim((string) ($field['name'] ?? '')),
                                    'unit' => trim((string) ($field['unit'] ?? '')),
                                    'kind' => trim((string) ($field['kind'] ?? 'number')),
                                    'parent_token' => trim((string) ($field['parent_token'] ?? '')),
                                    'show_when' => trim((string) ($field['show_when'] ?? '')),
                                ];
                            }

                            return [
                                'key' => \Illuminate\Support\Str::slug((string) $field, '_'),
                                'name' => trim((string) $field),
                                'unit' => '',
                                'kind' => 'number',
                                'parent_token' => '',
                                'show_when' => '',
                            ];
                        })->filter(fn ($field) => $field['name'] !== '')->values();

                        $payloadRows = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
                    @endphp
                    <div class="audit-section" data-audit-section="{{ $section->id }}">
                        <h4>{{ $sectionIndex + 1 }}. {{ $section->name }}</h4>

                        <?php if (!empty($sectionTasks)): ?>
                            <div style="font-size:12px; color:#4c6373; margin-bottom:6px;"><strong>Zadania:</strong></div>
                            <ul style="margin:0 0 8px 18px; padding:0; display:grid; gap:4px; color:#355468;">
                                <?php foreach ($sectionTasks as $task): ?>
                                    <li>
                                        {{ $task }}
                                        <?php if (!empty($taskValues[$task])): ?>
                                            <span style="color:#0c5f28; font-weight:700;">(wykonane)</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($fieldRows->isNotEmpty()): ?>
                            <table class="audit-data-table">
                                <thead>
                                <tr>
                                    <th>Dana</th>
                                    <th>Jednostka</th>
                                    <th>Wartość</th>
                                    <th>Uwagi</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($fieldRows as $rowIndex => $field): ?>
                                    @php
                                        $rowPayload = is_array($payloadRows[$rowIndex] ?? null) ? $payloadRows[$rowIndex] : [];
                                        $legacyValue = is_array($payloadRows) ? ($payloadRows[$field['name']] ?? '') : '';
                                        $value = (string) ($rowPayload['value'] ?? $legacyValue ?? '');
                                        $notes = (string) ($rowPayload['notes'] ?? '');
                                    @endphp
                                    <tr data-field-row="1" data-parent-token="{{ $field['parent_token'] }}" data-show-when="{{ $field['show_when'] }}" class="{{ $field['parent_token'] !== '' ? 'is-dependent-row' : '' }}">
                                        <td>{{ $field['name'] }}</td>
                                        <td>{{ $field['unit'] !== '' ? $field['unit'] : '—' }}</td>
                                        <td>
                                            <span
                                                class="formula-source"
                                                data-audit-section="{{ $section->id }}"
                                                data-field-token="{{ $field['key'] }}"
                                                data-field-kind="{{ $field['kind'] }}"
                                                data-field-value="{{ $value }}"
                                            >{{ $value !== '' ? $value : '—' }}</span>
                                        </td>
                                        <td>{{ $notes !== '' ? $notes : '—' }}</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php if (!empty($sectionFormulas)): ?>
                            <div class="audit-formulas">
                                <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:6px;">Wyniki obliczeń</div>
                                <?php foreach ($sectionFormulas as $formula): ?>
                                    <?php if (!empty($formula['label']) && !empty($formula['expression'])): ?>
                                        <div class="formula-line">
                                            <span class="formula-label">{{ $formula['label'] }}</span>
                                            <span>=</span>
                                            <strong data-audit-section="{{ $section->id }}" data-formula-expression="{{ $formula['expression'] }}" data-formula-unit="{{ (string) ($formula['unit'] ?? '') }}">—</strong>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($sections->isEmpty()): ?>
                <?php if (!empty($payload)): ?>
                    <div class="audit-section">
                        <h4>Zapisane dane audytu</h4>
                        <div class="muted" style="margin-bottom:8px;">Nie znaleziono aktualnych sekcji rodzaju audytu, ale zapisane dane są nadal dostępne.</div>
                        <pre style="white-space:pre-wrap; margin:0; font-size:12px; color:#2c4e67;">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                <?php else: ?>
                    <div class="muted">Ten audyt nie ma zdefiniowanych sekcji w rodzaju audytu.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function normalizeValueForDependency(rawValue, kind) {
            const text = String(rawValue ?? '').trim();
            if (text === '') {
                return '';
            }

            if (kind === 'boolean') {
                const normalized = text.toLowerCase();
                if (['tak', 'true', '1', 'yes'].includes(normalized)) {
                    return 'tak';
                }

                if (['nie', 'false', '0', 'no'].includes(normalized)) {
                    return 'nie';
                }

                return normalized;
            }

            if (kind === 'number') {
                const normalized = text.replace(',', '.');
                const number = Number(normalized);
                return Number.isFinite(number) ? String(number) : normalized;
            }

            return text.toLowerCase();
        }

        function computeDependencyDepth(token, parentByToken, cache = {}, trail = []) {
            if (!token) {
                return 0;
            }

            if (typeof cache[token] === 'number') {
                return cache[token];
            }

            if (trail.includes(token)) {
                return 1;
            }

            const parent = parentByToken[token] || '';
            if (parent === '') {
                cache[token] = 0;
                return 0;
            }

            const depth = 1 + computeDependencyDepth(parent, parentByToken, cache, trail.concat(token));
            cache[token] = depth;

            return depth;
        }

        function ensureAuditDependencyOverlaySvg(table) {
            if (!table) {
                return null;
            }

            let host = table.closest('.audit-data-table-wrap');
            if (!host) {
                host = document.createElement('div');
                host.className = 'audit-data-table-wrap';
                table.parentNode.insertBefore(host, table);
                host.appendChild(table);
            }

            let svg = host.querySelector(':scope > .dependency-tree-overlay');
            if (!svg) {
                svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.classList.add('dependency-tree-overlay');
                host.appendChild(svg);
            }

            return svg;
        }

        function clearAuditDependencyBranch(section) {
            if (!section) {
                return;
            }

            section.querySelectorAll('tr[data-field-row].dependency-branch-active').forEach((row) => {
                row.classList.remove('dependency-branch-active');
            });

            const svg = section.querySelector('.audit-data-table-wrap > .dependency-tree-overlay');
            if (!svg) {
                return;
            }

            svg.querySelectorAll('.dependency-path.dependency-path-active').forEach((path) => {
                path.classList.remove('dependency-path-active');
            });
        }

        function highlightAuditDependencyBranch(section, token, parentByToken, childrenByToken) {
            if (!section) {
                return;
            }

            if (!token) {
                clearAuditDependencyBranch(section);
                return;
            }

            const branchTokens = new Set([token]);
            let cursor = token;
            const seenParents = new Set();
            while (parentByToken[cursor] && !seenParents.has(cursor)) {
                seenParents.add(cursor);
                cursor = parentByToken[cursor];
                branchTokens.add(cursor);
            }

            const queue = [token];
            const seenChildren = new Set(queue);
            while (queue.length > 0) {
                const current = queue.shift();
                (childrenByToken[current] || []).forEach((child) => {
                    if (!seenChildren.has(child)) {
                        seenChildren.add(child);
                        branchTokens.add(child);
                        queue.push(child);
                    }
                });
            }

            section.querySelectorAll('tr[data-field-row]').forEach((row) => {
                const rowToken = String(row.getAttribute('data-field-token') ?? '').trim();
                row.classList.toggle('dependency-branch-active', rowToken !== '' && branchTokens.has(rowToken));
            });

            const svg = section.querySelector('.audit-data-table-wrap > .dependency-tree-overlay');
            if (!svg) {
                return;
            }

            svg.querySelectorAll('.dependency-path').forEach((path) => {
                const from = String(path.getAttribute('data-from') ?? '').trim();
                const to = String(path.getAttribute('data-to') ?? '').trim();
                const isActive = from !== '' && to !== '' && branchTokens.has(from) && branchTokens.has(to);
                path.classList.toggle('dependency-path-active', isActive);
            });
        }

        function renderAuditDependencyTree(section, parentByToken, childrenByToken) {
            if (!section) {
                return;
            }

            const table = section.querySelector('.audit-data-table');
            if (!table) {
                return;
            }

            const svg = ensureAuditDependencyOverlaySvg(table);
            if (!svg) {
                return;
            }

            if (table.offsetParent === null) {
                svg.innerHTML = '';
                return;
            }

            const host = table.closest('.audit-data-table-wrap');
            if (!host) {
                return;
            }

            const hostRect = host.getBoundingClientRect();
            svg.setAttribute('viewBox', `0 0 ${Math.max(1, hostRect.width)} ${Math.max(1, hostRect.height)}`);
            svg.setAttribute('preserveAspectRatio', 'none');
            svg.innerHTML = '';

            const tokenToRow = {};
            table.querySelectorAll('tr[data-field-row]').forEach((row) => {
                const token = String(row.getAttribute('data-field-token') ?? '').trim();
                if (token !== '') {
                    tokenToRow[token] = row;
                }
            });

            const depthCache = {};
            Object.entries(parentByToken).forEach(([token, parentToken]) => {
                if (!parentToken || !tokenToRow[token] || !tokenToRow[parentToken]) {
                    return;
                }

                const row = tokenToRow[token];
                const parentRow = tokenToRow[parentToken];
                if (row.style.display === 'none' || parentRow.style.display === 'none') {
                    return;
                }

                const rowCell = row.children[0];
                const parentCell = parentRow.children[0];
                if (!rowCell || !parentCell) {
                    return;
                }

                const childRect = rowCell.getBoundingClientRect();
                const parentRect = parentCell.getBoundingClientRect();
                const depth = computeDependencyDepth(token, parentByToken, depthCache);

                const startX = parentRect.left - hostRect.left + 12;
                const endX = childRect.left - hostRect.left + 12 + Math.max(0, depth - 1) * 12;
                const startY = parentRect.top - hostRect.top + parentRect.height / 2;
                const endY = childRect.top - hostRect.top + childRect.height / 2;
                const laneX = Math.min(startX, endX) - 10;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.classList.add('dependency-path');
                path.setAttribute('data-from', parentToken);
                path.setAttribute('data-to', token);
                path.setAttribute('d', `M ${startX} ${startY} H ${laneX} V ${endY} H ${endX}`);
                svg.appendChild(path);
            });

            const tbody = table.querySelector('tbody');
            if (tbody && tbody.dataset.dependencyTreeReady !== '1') {
                tbody.dataset.dependencyTreeReady = '1';
                tbody.addEventListener('mouseover', function (event) {
                    const row = event.target.closest('tr[data-field-row]');
                    if (!row || row.parentElement !== tbody) {
                        return;
                    }

                    const rowToken = String(row.getAttribute('data-field-token') ?? '').trim();
                    highlightAuditDependencyBranch(section, rowToken, parentByToken, childrenByToken);
                });

                tbody.addEventListener('mouseleave', function () {
                    clearAuditDependencyBranch(section);
                });
            }
        }

        function applyFieldDependenciesForSection(sectionId) {
            const section = document.querySelector(`.audit-section[data-audit-section="${sectionId}"]`);
            if (!section) {
                return;
            }

            const fieldByToken = {};
            const rowByToken = {};
            const parentByToken = {};
            const childrenByToken = {};
            section.querySelectorAll(`.formula-source[data-audit-section="${sectionId}"]`).forEach((input) => {
                const token = String(input.getAttribute('data-field-token') ?? '').trim();
                if (token === '') {
                    return;
                }

                fieldByToken[token] = input;

                const row = input.closest('tr[data-field-row]');
                if (!row) {
                    return;
                }

                rowByToken[token] = row;
                row.setAttribute('data-field-token', token);
                parentByToken[token] = String(row.getAttribute('data-parent-token') ?? '').trim();

                if (!childrenByToken[token]) {
                    childrenByToken[token] = [];
                }
            });

            Object.entries(parentByToken).forEach(([token, parentToken]) => {
                if (!parentToken || !rowByToken[parentToken]) {
                    return;
                }

                if (!childrenByToken[parentToken]) {
                    childrenByToken[parentToken] = [];
                }

                childrenByToken[parentToken].push(token);
            });

            const cache = {};
            Object.keys(rowByToken).forEach((token) => {
                const row = rowByToken[token];
                const depth = computeDependencyDepth(token, parentByToken, cache);
                if (depth > 0) {
                    row.classList.add('is-dependent-row');
                    row.style.setProperty('--dependency-depth', String(depth));
                    row.setAttribute('data-dependency-depth', String(depth));
                    row.setAttribute('data-dependency-label', '⤷'.repeat(Math.min(depth, 4)));
                } else {
                    row.classList.remove('is-dependent-row');
                    row.style.removeProperty('--dependency-depth');
                    row.removeAttribute('data-dependency-depth');
                    row.removeAttribute('data-dependency-label');
                }
            });

            section.querySelectorAll('tr[data-field-row]').forEach((row) => {
                const parentToken = String(row.getAttribute('data-parent-token') ?? '').trim();
                const showWhen = String(row.getAttribute('data-show-when') ?? '').trim();

                if (parentToken === '') {
                    row.style.display = '';
                    return;
                }

                const parentField = fieldByToken[parentToken];
                if (!parentField) {
                    row.style.display = 'none';
                    return;
                }

                const parentKind = String(parentField.getAttribute('data-field-kind') ?? 'text').trim();
                const normalizedParent = normalizeValueForDependency(parentField.getAttribute('data-field-value'), parentKind);
                const normalizedExpected = normalizeValueForDependency(showWhen, parentKind);

                const shouldShow = normalizedExpected === ''
                    ? normalizedParent !== ''
                    : normalizedParent === normalizedExpected;

                row.style.display = shouldShow ? '' : 'none';
            });

            renderAuditDependencyTree(section, parentByToken, childrenByToken);
        }

        function recalculateFormulasForSection(sectionId) {
            const section = document.querySelector(`.audit-section[data-audit-section="${sectionId}"]`);
            if (!section) {
                return;
            }

            applyFieldDependenciesForSection(sectionId);

            const values = {};
            section.querySelectorAll(`.formula-source[data-audit-section="${sectionId}"]`).forEach((input) => {
                const token = input.getAttribute('data-field-token');
                if (!token) {
                    return;
                }

                const row = input.closest('tr[data-field-row]');
                if (row && row.style.display === 'none') {
                    values[token] = null;
                    return;
                }

                let raw = String(input.getAttribute('data-field-value') ?? '').trim();
                if (raw === '') {
                    values[token] = null;
                    return;
                }

                if (raw.toLowerCase() === 'tak') {
                    values[token] = 1;
                    return;
                }

                if (raw.toLowerCase() === 'nie') {
                    values[token] = 0;
                    return;
                }

                raw = raw.replace(',', '.');

                const number = Number(raw);
                values[token] = Number.isFinite(number) ? number : null;
            });

            section.querySelectorAll(`[data-formula-expression][data-audit-section="${sectionId}"]`).forEach((output) => {
                const expression = String(output.getAttribute('data-formula-expression') ?? '').trim();
                const unit = String(output.getAttribute('data-formula-unit') ?? '').trim();
                const usedTokens = Array.from(new Set(Array.from(expression.matchAll(/\{([a-zA-Z0-9_]+)\}/g)).map((match) => match[1])));

                if (usedTokens.some((token) => values[token] === null || values[token] === undefined)) {
                    output.textContent = '—';
                    return;
                }

                const replaced = expression.replace(/\{([a-zA-Z0-9_]+)\}/g, (_, token) => String(values[token] ?? 0));
                const normalized = replaced.replace(/,/g, '.');

                if (!/^[0-9+\-*/().\s]+$/.test(normalized)) {
                    output.textContent = '—';
                    return;
                }

                try {
                    const result = Function('return (' + normalized + ')')();
                    if (Number.isFinite(result)) {
                        const rounded = String(Math.round((result + Number.EPSILON) * 1000000) / 1000000);
                        output.textContent = unit !== '' ? `${rounded} ${unit}` : rounded;
                    } else {
                        output.textContent = '—';
                    }
                } catch (error) {
                    output.textContent = '—';
                }
            });
        }

        function recalculateFormulas() {
            document.querySelectorAll('.audit-section[data-audit-section]').forEach((section) => {
                const sectionId = section.getAttribute('data-audit-section');
                if (!sectionId) {
                    return;
                }

                recalculateFormulasForSection(sectionId);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            recalculateFormulas();

            let dependencyResizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(dependencyResizeTimer);
                dependencyResizeTimer = setTimeout(function () {
                    recalculateFormulas();
                }, 100);
            });
        });
    </script>
</x-layouts.app>
