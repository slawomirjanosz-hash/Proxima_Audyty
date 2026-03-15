<div style="display: flex; align-items: center; gap: 14px; font-weight: 600; font-size: 15px;">
    <span title="Liczba aktywnych użytkowników w ostatnich 10 minutach">
        👥 {{ $onlineCount }} online
    </span>
    @if($user)
        <span title="Jesteś zalogowany jako">{{ __('ui.logged_in_as') }}: <strong>{{ $user->short_name }}</strong></span>
    @endif
</div>
