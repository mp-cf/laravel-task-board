<?php

namespace App\Http\Controllers;

use App\Jobs\TaskCompletedJob;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Board $board): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $board->tasks()->create([
            'title' => $validated['title'],
            'status' => 'todo',
        ]);

        return redirect()->route('boards.show', $board)->with('success', 'Task created.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $wasNotDone = ! $task->isCompleted();
        $task->update($validated);

        if ($wasNotDone && $task->fresh()->isCompleted()) {
            TaskCompletedJob::dispatch($task);
        }

        return redirect()->route('boards.show', $task->board_id)->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $boardId = $task->board_id;
        $task->delete();

        return redirect()->route('boards.show', $boardId)->with('success', 'Task deleted.');
    }
}
