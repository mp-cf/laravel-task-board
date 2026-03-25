<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::withCount('tasks')->orderBy('name')->get();

        return view('boards.index', compact('boards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Board::create($validated);

        return redirect()->route('boards.index')->with('success', 'Board created.');
    }

    public function show(Board $board): View
    {
        return view('boards.show', [
            'board' => $board,
            'todo' => $board->tasksByStatus('todo'),
            'in_progress' => $board->tasksByStatus('in_progress'),
            'done' => $board->tasksByStatus('done'),
        ]);
    }

    public function destroy(Board $board): RedirectResponse
    {
        $board->delete();

        return redirect()->route('boards.index')->with('success', 'Board deleted.');
    }
}
