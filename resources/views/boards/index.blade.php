@extends('layouts.app')

@section('title', 'Boards')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Boards</h1>

        @if ($boards->isEmpty())
            <p class="text-muted">No boards yet. Create one below.</p>
        @else
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                @foreach ($boards as $board)
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('boards.show', $board) }}" class="text-decoration-none">
                                            {{ $board->name }}
                                        </a>
                                    </h5>
                                    <small class="text-muted">{{ $board->tasks_count }} task{{ $board->tasks_count !== 1 ? 's' : '' }}</small>
                                </div>
                                <form method="POST" action="{{ route('boards.destroy', $board) }}"
                                      onsubmit="return confirm('Delete board and all its tasks?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">New Board</div>
            <div class="card-body">
                <form method="POST" action="{{ route('boards.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Board Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Board</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
