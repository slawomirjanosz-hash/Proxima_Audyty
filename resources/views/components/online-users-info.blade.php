<div style="display:flex; align-items:center; gap:14px; font-weight:600; font-size:15px;">
    <span title="Liczba aktywnych użytkowników w ostatnich 10 minutach">
        👥 {{ $onlineCount }} online
    </span>
    @if($user)
        @php
            $initials = collect(explode(' ', trim($user->name ?? $user->short_name ?? '')))
                ->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
            if ($initials === '') { $initials = mb_strtoupper(mb_substr($user->email ?? 'U', 0, 1)); }
        @endphp
        <div class="user-avatar-wrap" style="position:relative;">
            <button
                type="button"
                id="user-avatar-btn"
                onclick="toggleUserDropdown()"
                title="{{ $user->name }}"
                style="
                    width:36px; height:36px; border-radius:50%;
                    background:rgba(255,255,255,.25); border:2px solid rgba(255,255,255,.5);
                    color:#fff; font-weight:800; font-size:14px;
                    cursor:pointer; display:grid; place-items:center;
                    padding:0; line-height:1;
                "
            >{{ $initials }}</button>

            <div
                id="user-dropdown"
                style="
                    display:none; position:absolute; top:calc(100% + 8px); right:0;
                    background:#fff; border:1px solid #d0e0ec; border-radius:12px;
                    box-shadow:0 8px 24px rgba(10,50,80,.15);
                    min-width:220px; z-index:500; overflow:hidden;
                "
            >
                <div style="padding:12px 14px; border-bottom:1px solid #edf3f8; color:#2c4e67;">
                    <div style="font-weight:700; font-size:14px;">{{ $user->name }}</div>
                    <div style="font-size:12px; color:#6b8294; margin-top:2px;">{{ $user->email }}</div>
                </div>
                <div style="padding:6px 0;">
                    <a href="{{ route('profile.edit') }}" style="display:block; padding:9px 14px; font-size:13px; color:#1d4f73; text-decoration:none; font-weight:600;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background=''">&#9881;&#65039; Ustawienia profilu</a>
                    <a href="{{ route('profile.password') }}" style="display:block; padding:9px 14px; font-size:13px; color:#1d4f73; text-decoration:none; font-weight:600;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background=''">&#128274; Zmiana hasła</a>
                    <div style="border-top:1px solid #edf3f8; margin:4px 0;"></div>
                    <form method="POST" action="{{ route('logout', [], false) }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="
                            width:100%; text-align:left; padding:9px 14px; font-size:13px;
                            color:#b42318; font-weight:600; background:none; border:none;
                            cursor:pointer; border-radius:0;
                        " onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background=''">&#x2192; Wyloguj się</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function toggleUserDropdown() {
                const dd = document.getElementById('user-dropdown');
                if (!dd) return;
                const isOpen = dd.style.display === 'block';
                dd.style.display = isOpen ? 'none' : 'block';
            }
            document.addEventListener('click', function(e) {
                const btn = document.getElementById('user-avatar-btn');
                const dd  = document.getElementById('user-dropdown');
                if (!dd || !btn) return;
                if (!btn.contains(e.target) && !dd.contains(e.target)) {
                    dd.style.display = 'none';
                }
            });
        </script>
    @endif
</div>
