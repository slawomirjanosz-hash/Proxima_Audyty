<div style="display: flex; align-items: center; gap: 14px; font-weight: 600; font-size: 15px;">
    <span title="Liczba aktywnych użytkowników w ostatnich 10 minutach">
        👥 {{ $onlineCount }} {{ __('ui.online_users') }}
    </span>
    @if($user)
        <span title="Jesteś zalogowany jako">{{ __('ui.logged_in_as') }}: <strong>{{ $user->name ?? ($user->first_name . ' ' . $user->last_name) }}</strong> ({{ $user->role->value }})</span>
    @endif
</div>
