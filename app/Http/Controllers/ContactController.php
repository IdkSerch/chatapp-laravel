<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function add(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $contactUser = User::where('email', $request->email)->first();

        if ($contactUser->id === Auth::id()) {
            return back()->withErrors(['email' => 'No puedes agregarte a ti mismo.']);
        }

        $exists = Contact::where('user_id', Auth::id())
            ->where('contact_id', $contactUser->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Este contacto ya está agregado.']);
        }

        Contact::create([
            'user_id'    => Auth::id(),
            'contact_id' => $contactUser->id,
        ]);

        return redirect()->route('chat.index');
    }
}