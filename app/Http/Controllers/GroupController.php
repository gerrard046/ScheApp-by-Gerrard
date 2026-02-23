<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Administered groups with their resources
        $administeredGroups = Group::where('admin_id', $user->id)
            ->with(['resources.uploader', 'members'])
            ->get();
            
        // Joined groups with their resources
        $joinedGroups = $user->groups()->with(['resources.uploader', 'admin'])->get();
        
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

    public function storeResource(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        
        // Cek apakah user adalah admin grup atau anggota
        $isMember = $group->members()->where('users.id', auth()->id())->exists();
        $isAdmin = $group->admin_id === auth()->id();
        
        if (!$isAdmin && !$isMember) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('group_resources/' . $group->id, 'public');
            
            GroupResource::create([
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
            ]);

            return redirect()->back()->with('success', 'Materi berhasil diunggah!');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    public function downloadResource($id)
    {
        $resource = GroupResource::findOrFail($id);
        $group = $resource->group;
        
        // Cek akses
        $isMember = $group->members()->where('users.id', auth()->id())->exists();
        $isAdmin = $group->admin_id === auth()->id();
        
        if (!$isAdmin && !$isMember) {
            abort(403);
        }

        return Storage::disk('public')->download($resource->file_path, $resource->title . '.' . $resource->file_type);
    }
}
