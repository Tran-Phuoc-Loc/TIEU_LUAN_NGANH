<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Chat;

class ChatController extends Controller
{
    public function store(Request $request, $groupId)
{
    $request->validate([
        'message' => 'required|string|max:255',
    ]);

    Chat::create([
        'group_id' => $groupId,
        'user_id' => Auth::id(),
        'message' => $request->message,
    ]);

    return redirect()->back();
}

public function index($groupId)
{
    $group = Group::with('chats.user')->findOrFail($groupId);
    return view('users.groups.chat', compact('group'));
}

}
