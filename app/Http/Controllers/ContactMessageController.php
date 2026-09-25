<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        return view('manage.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        return view('manage.contact-messages.show', compact('contactMessage'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return redirect()->route('manage.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
