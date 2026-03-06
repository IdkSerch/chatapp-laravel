<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ChatApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --green: #00C853; --teal: #00897B; --dark: #0a0f0d; --sidebar-bg: #111a14; --chat-bg: #0d1510; --bubble-out: #005c4b; --bubble-in: #1e2d22; --border: rgba(0,200,83,0.14); --text: #e8f5e9; --muted: #6b8f71; --error: #ff5252; }
        body { font-family: 'Sora', sans-serif; background: var(--dark); color: var(--text); height: 100vh; display: flex; overflow: hidden; }
        .sidebar { width: 320px; min-width: 320px; background: var(--sidebar-bg); border-right: 1px solid var(--border); display: flex; flex-direction: column; height: 100vh; }
        .sidebar-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--border); }
        .my-profile { display: flex; align-items: center; gap: 10px; flex: 1; }
        .avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--green), var(--teal)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 1rem; overflow: hidden; flex-shrink: 0; }
        .avatar.sm { width: 38px; height: 38px; font-size: 0.85rem; }
        .my-info { display: flex; flex-direction: column; }
        .my-name { font-weight: 600; font-size: 0.9rem; }
        .my-email { font-size: 0.72rem; color: var(--muted); }
        .header-actions { display: flex; gap: 6px; }
        .icon-btn { width: 36px; height: 36px; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--muted); text-decoration: none; transition: all 0.2s; }
        .icon-btn:hover { background: rgba(0,200,83,0.1); color: var(--green); }
        .icon-btn.danger:hover { background: rgba(255,82,82,0.1); color: var(--error); }
        .icon-btn svg { width: 18px; height: 18px; }
        .contacts-list { flex: 1; overflow-y: auto; }
        .contacts-list::-webkit-scrollbar { width: 4px; }
        .contacts-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        .contact-item { display: flex; align-items: center; gap: 12px; padding: 12px 18px; cursor: pointer; border-bottom: 1px solid rgba(0,200,83,0.05); text-decoration: none; color: var(--text); transition: background 0.2s; }
        .contact-item:hover, .contact-item.active { background: rgba(0,200,83,0.08); }
        .contact-name { font-weight: 600; font-size: 0.9rem; }
        .contact-preview { font-size: 0.75rem; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .empty-contacts { display: flex; flex-direction: column; align-items: center; padding: 48px 24px; gap: 12px; color: var(--muted); text-align: center; }
        .empty-contacts svg { width: 48px; height: 48px; opacity: 0.4; }
        .empty-contacts p { font-size: 0.85rem; line-height: 1.6; }
        .chat-area { flex: 1; display: flex; flex-direction: column; height: 100vh; background: var(--chat-bg); }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(4px); }
        .modal { background: var(--sidebar-bg); border: 1px solid var(--border); border-radius: 16px; padding: 28px; width: 340px; }
        .modal h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 6px; }
        .modal p { font-size: 0.82rem; color: var(--muted); margin-bottom: 20px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; }
        .form-group input { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-family: 'Sora', sans-serif; font-size: 0.9rem; outline: none; }
        .form-group input:focus { border-color: var(--green); }
        .form-group input::placeholder { color: var(--muted); }
        .modal-actions { display: flex; gap: 10px; margin-top: 4px; }
        .btn-primary { flex: 1; padding: 11px; background: linear-gradient(135deg, var(--green), var(--teal)); border: none; border-radius: 10px; color: white; font-family: 'Sora', sans-serif; font-size: 0.88rem; font-weight: 600; cursor: pointer; }
        .btn-secondary { flex: 1; padding: 11px; background: rgba(255,255,255,0.06); border: none; border-radius: 10px; color: var(--muted); font-family: 'Sora', sans-serif; font-size: 0.88rem; cursor: pointer; }
        .error-msg { color: var(--error); font-size: 0.8rem; margin-bottom: 10px; }
        .hidden { display: none !important; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="my-profile">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="my-info">
                <span class="my-name">{{ Auth::user()->name }}</span>
                <span class="my-email">{{ Auth::user()->email }}</span>
            </div>
        </div>
        <div class="header-actions">
            <button class="icon-btn" onclick="document.getElementById('modalContacto').classList.remove('hidden')" title="Agregar contacto">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn danger" title="Cerrar sesión">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="contacts-list">
        @php $allContacts = $contacts ?? collect(); $extraContacts = $extraContacts ?? collect(); @endphp

        @if($allContacts->isEmpty() && $extraContacts->isEmpty())
            <div class="empty-contacts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <p>Sin contactos aún.<br/>Agrega a alguien para chatear.</p>
            </div>
        @else
            @foreach($allContacts as $contact)
                @php
                    $lastMsg = \App\Models\Message::where(function($q) use ($contact) {
                        $q->where('sender_id', $contact->user_id)->where('receiver_id', $contact->contact_id);
                    })->orWhere(function($q) use ($contact) {
                        $q->where('sender_id', $contact->contact_id)->where('receiver_id', $contact->user_id);
                    })->latest()->first();
                    $preview = $lastMsg ? (strlen($lastMsg->body) > 30 ? substr($lastMsg->body, 0, 30).'...' : $lastMsg->body) : 'Sin mensajes aún';
                @endphp
                <a href="{{ route('chat.show', $contact->contactUser->id) }}"
                   class="contact-item {{ isset($activeContact) && $activeContact->id === $contact->contactUser->id ? 'active' : '' }}">
                    <div class="avatar sm">{{ strtoupper(substr($contact->contactUser->name, 0, 1)) }}</div>
                    <div>
                        <div class="contact-name">{{ $contact->contactUser->name }}</div>
                        <div class="contact-preview">{{ $preview }}</div>
                    </div>
                </a>
            @endforeach

            @foreach($extraContacts as $extra)
                @php
                    $lastMsg = \App\Models\Message::where(function($q) use ($extra) {
                        $q->where('sender_id', Auth::id())->where('receiver_id', $extra->id);
                    })->orWhere(function($q) use ($extra) {
                        $q->where('sender_id', $extra->id)->where('receiver_id', Auth::id());
                    })->latest()->first();
                    $preview = $lastMsg ? (strlen($lastMsg->body) > 30 ? substr($lastMsg->body, 0, 30).'...' : $lastMsg->body) : 'Te envió un mensaje';
                @endphp
                <a href="{{ route('chat.show', $extra->id) }}"
                   class="contact-item {{ isset($activeContact) && $activeContact->id === $extra->id ? 'active' : '' }}">
                    <div class="avatar sm">{{ strtoupper(substr($extra->name, 0, 1)) }}</div>
                    <div>
                        <div class="contact-name">{{ $extra->name }}</div>
                        <div class="contact-preview">{{ $preview }}</div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</aside>

<main class="chat-area">
    @yield('content')
</main>

{{-- Modal agregar contacto --}}
<div class="modal-overlay hidden" id="modalContacto">
    <div class="modal">
        <h3>Agregar contacto</h3>
        <p>Ingresa el correo del usuario que quieres agregar.</p>
        @if($errors->has('email'))
            <p class="error-msg">{{ $errors->first('email') }}</p>
        @endif
        <form method="POST" action="{{ route('contacts.add') }}">
            @csrf
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="email" placeholder="usuario@email.com" required/>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="document.getElementById('modalContacto').classList.add('hidden')">Cancelar</button>
                <button type="submit" class="btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>

@if($errors->has('email'))
<script>document.getElementById('modalContacto').classList.remove('hidden');</script>
@endif
</body>
</html>