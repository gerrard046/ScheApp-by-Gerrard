<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $administeredGroups = $user->administeredGroups;
        $joinedGroups = $user->groups;
        $allUsers = User::where('id', '!=', $user->id)->get();

        return view('schedules.groups', compact('administeredGroups', 'joinedGroups', 'allUsers'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|min:3']);
        Group::create([
            'name' => $request->name,
            'admin_id' => auth()->id()
        ]);
        return redirect()->back()->with('success', 'Grup berhasil dibuat!');
    }

    public function addMember(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        if ($group->admin_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Hanya admin grup yang bisa menambah anggota.');
        }

        $request->validate(['user_id' => 'required|exists:users,id']);
        $group->members()->syncWithoutDetaching([$request->user_id]);

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan ke grup!');
    }
}
