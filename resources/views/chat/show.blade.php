@extends('layouts.chat')

@section('content')
<style>
    .chat-header { display: flex; align-items: center; gap: 14px; padding: 12px 20px; background: var(--sidebar-bg); border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .chat-contact-name { display: block; font-weight: 600; font-size: 0.95rem; }
    .chat-contact-status { display: block; font-size: 0.75rem; color: var(--green); }
    .messages-wrap { flex: 1; overflow-y: auto; padding: 16px 20px; display: flex; flex-direction: column; gap: 6px; }
    .messages-wrap::-webkit-scrollbar { width: 4px; }
    .messages-wrap::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
    .msg { max-width: 68%; padding: 9px 13px; border-radius: 14px; font-size: 0.9rem; line-height: 1.5; word-break: break-word; }
    .msg.out { align-self: flex-end; background: var(--bubble-out); border-bottom-right-radius: 4px; }
    .msg.in { align-self: flex-start; background: var(--bubble-in); border-bottom-left-radius: 4px; }
    .msg-time { font-size: 0.68rem; color: rgba(255,255,255,0.4); margin-top: 4px; text-align: right; }
    .msg.in .msg-time { text-align: left; }
    .date-divider { text-align: center; font-size: 0.75rem; color: var(--muted); background: rgba(0,0,0,0.3); padding: 4px 12px; border-radius: 10px; margin: 10px auto; width: fit-content; }
    .chat-input-area { padding: 12px 20px 16px; background: var(--sidebar-bg); border-top: 1px solid var(--border); flex-shrink: 0; }
    .chat-input-wrap { display: flex; gap: 10px; align-items: flex-end; background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 14px; padding: 8px 8px 8px 16px; }
    .chat-input-wrap textarea { flex: 1; background: none; border: none; outline: none; color: var(--text); font-family: 'Sora', sans-serif; font-size: 0.9rem; resize: none; max-height: 120px; line-height: 1.5; }
    .chat-input-wrap textarea::placeholder { color: var(--muted); }
    .send-btn { width: 40px; height: 40px; flex-shrink: 0; background: linear-gradient(135deg, var(--green), var(--teal)); border: none; border-radius: 10px; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .send-btn svg { width: 18px; height: 18px; }
</style>

<div class="chat-header">
    @php
    $esContacto = \App\Models\Contact::where('user_id', Auth::id())
        ->where('contact_id', $contact->id)->exists();
@endphp

@if(!$esContacto)
<div style="background:rgba(0,200,83,0.07); border-bottom:1px solid rgba(0,200,83,0.14); padding:10px 20px; display:flex; align-items:center; justify-content:space-between;">
    <span style="font-size:0.82rem; color:var(--muted);">
        ⚠️ <strong style="color:var(--text);">{{ $contact->name }}</strong> no está en tus contactos
    </span>
    <form method="POST" action="{{ route('contacts.add') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $contact->email }}"/>
        <button type="submit" style="padding:6px 14px; background:linear-gradient(135deg,#00C853,#00897B); border:none; border-radius:8px; color:white; font-size:0.78rem; font-weight:600; cursor:pointer;">
            + Agregar
        </button>
    </form>
</div>
@endif
    <div class="avatar sm">
        {{ strtoupper(substr($contact->name, 0, 1)) }}
    </div>
    <div>
        <span class="chat-contact-name">{{ $contact->name }}</span>
        <span class="chat-contact-status">en línea</span>
    </div>
</div>

<div class="messages-wrap" id="messagesWrap">
    @php $lastDate = ''; @endphp
    @forelse($messages as $msg)
        @php
            $date = $msg->created_at->locale('es')->isoFormat('D [de] MMMM');
        @endphp
        @if($date !== $lastDate)
            <div class="date-divider">{{ $date }}</div>
            @php $lastDate = $date; @endphp
        @endif
        <div class="msg {{ $msg->sender_id === Auth::id() ? 'out' : 'in' }}">
            {{ $msg->body }}
            <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
        </div>
    @empty
        <div style="text-align:center; color:var(--muted); margin-top:40px; font-size:0.85rem;">
            Aún no hay mensajes. ¡Saluda!
        </div>
    @endforelse
</div>

<div class="chat-input-area">
    <form method="POST" action="{{ route('chat.send') }}" id="msgForm">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $contact->id }}"/>
        <div class="chat-input-wrap">
            <textarea name="body" id="msgInput" placeholder="Escribe un mensaje..." rows="1" onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
            <button type="submit" class="send-btn">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    // Scroll al fondo
    const wrap = document.getElementById('messagesWrap');
    wrap.scrollTop = wrap.scrollHeight;

    function handleKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('msgForm').submit();
        }
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    setInterval(() => {
    const input = document.getElementById('msgInput');
    const saved = input ? input.value : '';
    location.reload();
    sessionStorage.setItem('draft', saved);
}, 3000);

// Restaurar el mensaje al recargar
window.addEventListener('load', () => {
    const draft = sessionStorage.getItem('draft');
    const input = document.getElementById('msgInput');
    if (draft && input) {
        input.value = draft;
        sessionStorage.removeItem('draft');
    }
});
</script>
@endsection