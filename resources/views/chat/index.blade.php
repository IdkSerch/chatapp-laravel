@extends('layouts.chat')

@section('content')
<style>
    .chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; color: var(--muted); text-align: center; padding: 32px; }
    .chat-empty-icon { width: 80px; height: 80px; }
    .chat-empty h2 { font-size: 1.2rem; color: var(--text); font-weight: 600; }
    .chat-empty p { font-size: 0.85rem; }
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
</div>
@endsection