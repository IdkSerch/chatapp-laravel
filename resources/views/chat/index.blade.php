@extends('layouts.chat')

@section('content')
<style>
    .chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; color: var(--muted); text-align: center; padding: 32px; }
    .chat-empty-icon { width: 80px; height: 80px; }
    .chat-empty h2 { font-size: 1.2rem; color: var(--text); font-weight: 600; }
    .chat-empty p { font-size: 0.85rem; }
    .suggestions { width: 100%; max-width: 400px; margin-top: 8px; }
    .suggestions-title { font-size: 0.78rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .suggestion-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 8px; }
    .suggestion-info { display: flex; align-items: center; gap: 10px; }
    .suggestion-name { font-weight: 600; font-size: 0.88rem; }
    .suggestion-email { font-size: 0.75rem; color: var(--muted); }
    .add-btn { padding: 6px 14px; background: linear-gradient(135deg, var(--green), var(--teal)); border: none; border-radius: 8px; color: white; font-family: 'Sora', sans-serif; font-size: 0.78rem; font-weight: 600; cursor: pointer; white-space: nowrap; }
    .add-btn:hover { opacity: 0.85; }
</style>

<div class="chat-empty">
    <div class="chat-empty-icon">
        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="40" fill="rgba(0,200,83,0.07)"/>
            <path d="M40 18C28.95 18 20 26.95 20 38c0 3.78.99 7.33 2.73 10.4L20 60l11.87-2.67A21.94 21.94 0 0040 58c11.05 0 20-8.95 20-20S51.05 18 40 18z" stroke="#00C853" stroke-width="1.5" fill="none"/>
        </svg>
    </div>
    <h2>Bienvenido a ChatApp</h2>
    <p>Selecciona un contacto para comenzar a chatear.</p>

    @if(isset($suggestions) && $suggestions->isNotEmpty())
    <div class="suggestions">
        <p class="suggestions-title">👥 Personas que puedes agregar</p>
        @foreach($suggestions as $user)
        <div class="suggestion-item">
            <div class="suggestion-info">
                <div class="avatar sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div>
                    <div class="suggestion-name">{{ $user->name }}</div>
                    <div class="suggestion-email">{{ $user->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('contacts.add') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $user->email }}"/>
                <button type="submit" class="add-btn">+ Agregar</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection