namespace App\Http\Controllers;

use App\Models\SubTask;
use App\Models\Schedule;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
    public function store(Request $request, $scheduleId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $schedule = Schedule::findOrFail($scheduleId);

        // Security check
        if ($schedule->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $schedule->subTasks()->create([
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return redirect()->back()->with('success', 'Sub-tugas berhasil ditambahkan!');
    }

    public function toggle($id)
    {
        $subTask = SubTask::findOrFail($id);
        $schedule = $subTask->schedule;

        if ($schedule->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $subTask->is_completed = !$subTask->is_completed;
        $subTask->save();

        return redirect()->back()->with('success', 'Status sub-tugas diperbarui.');
    }

    public function destroy($id)
    {
        $subTask = SubTask::findOrFail($id);
        $schedule = $subTask->schedule;

        if ($schedule->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $subTask->delete();

        return redirect()->back()->with('success', 'Sub-tugas berhasil dihapus.');
    }
}
