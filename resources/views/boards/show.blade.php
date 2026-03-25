@extends('layouts.app')

@section('title', $board->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('boards.index') }}" class="text-muted text-decoration-none">&larr; All Boards</a>
        <h1 class="mb-0 mt-1">{{ $board->name }}</h1>
    </div>
</div>

{{-- Add Task Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.store', $board) }}" class="d-flex gap-2">
            @csrf
            <input type="text" class="form-control @error('title') is-invalid @enderror"
                   name="title" value="{{ old('title') }}" placeholder="New task title..." required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <button type="submit" class="btn btn-primary text-nowrap">Add Task</button>
        </form>
    </div>
</div>

{{-- Kanban Columns --}}
<div class="row g-3">
    @foreach ([
        'todo' => ['label' => 'To Do', 'color' => 'secondary', 'tasks' => $todo],
        'in_progress' => ['label' => 'In Progress', 'color' => 'warning', 'tasks' => $in_progress],
        'done' => ['label' => 'Done', 'color' => 'success', 'tasks' => $done],
    ] as $status => $column)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ $column['label'] }}</span>
                    <span class="badge bg-{{ $column['color'] }}">{{ $column['tasks']->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @forelse ($column['tasks'] as $task)
                        <div class="card task-card mb-2">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <span class="flex-grow-1">{{ $task->title }}</span>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                          onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"
                                                title="Delete">&times;</button>
                                    </form>
                                </div>

                                {{-- Move Buttons --}}
                                <div class="d-flex gap-1 mt-2">
                                    @if ($status !== 'todo')
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status === 'done' ? 'in_progress' : 'todo' }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0">&larr;</button>
                                        </form>
                                    @endif
                                    @if ($status !== 'done')
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status === 'todo' ? 'in_progress' : 'done' }}">
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $column['color'] }} py-0">&rarr;</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center small py-3 mb-0">No tasks here.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
