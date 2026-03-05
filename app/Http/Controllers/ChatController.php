<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
{
    $contacts = Contact::where('user_id', Auth::id())
        ->with('contactUser')
        ->get();

    $contactIds = $contacts->pluck('contact_id')->toArray();

    // Usuarios que te enviaron mensajes aunque no los tengas agregados
    $receivedFrom = Message::where('receiver_id', Auth::id())
        ->whereNotIn('sender_id', $contactIds)
        ->where('sender_id', '!=', Auth::id())
        ->pluck('sender_id')
        ->unique();

    $extraContacts = User::whereIn('id', $receivedFrom)->get();

    $allContactIds = array_merge($contactIds, $receivedFrom->toArray(), [Auth::id()]);
    $suggestions = User::whereNotIn('id', $allContactIds)->get();

    return view('chat.index', compact('contacts', 'extraContacts', 'suggestions'));
}

    public function show($userId)
    {
        $contact = User::findOrFail($userId);
        $contacts = Contact::where('user_id', Auth::id())
            ->with('contactUser')
            ->get();
        $messages = Message::where(function($q) use ($userId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
            })->orWhere(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
            })->orderBy('created_at')->get();

        return view('chat.show', compact('contact', 'contacts', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate(['receiver_id' => 'required|exists:users,id', 'body' => 'required|string']);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body'        => $request->body,
        ]);

        return redirect()->route('chat.show', $request->receiver_id);
    }
}