@php
    $domainLabels = [
        'literacy' => 'Literacy',
        'numeracy' => 'Numeracy',
        'motor' => 'Motor Skills',
        'social_emotional' => 'Social-Emotional',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0">Parent Learning Dashboard</h1>
                <p class="text-muted mb-0">Manage children, curriculum, and early learning progress.</p>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Learning</li>
                </ol>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $children->count() }}</h3>
                    <p>Child Profiles</p>
                </div>
                <div class="icon"><i class="fas fa-child-reaching"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $activities->count() }}</h3>
                    <p>Activities</p>
                </div>
                <div class="icon"><i class="fas fa-shapes"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $activities->where('is_active', true)->count() }}</h3>
                    <p>Active Lessons</p>
                </div>
                <div class="icon"><i class="fas fa-volume-high"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $children->sum(fn ($child) => $child->progressRecords->where('status', 'completed')->count()) }}</h3>
                    <p>Completions</p>
                </div>
                <div class="icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <section class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Children</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Audio</th>
                                <th>Progress</th>
                                <th class="text-right">Child Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($children as $child)
                                <tr>
                                    <td>
                                        <span class="task-priority-dot" style="background: {{ $child->avatar_color }}"></span>
                                        {{ $child->name }}
                                    </td>
                                    <td>{{ $child->audio_guidance_enabled ? 'On' : 'Off' }}</td>
                                    <td>{{ $child->progressRecords->where('status', 'completed')->count() }} completed</td>
                                    <td class="text-right">
                                        <a class="btn btn-sm btn-primary" href="{{ route('children.play', $child) }}">
                                            <i class="fas fa-play mr-1"></i> Open
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Add a child profile to unlock child mode.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Curriculum</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Domain</th>
                                <th>Ages</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->title }}</td>
                                    <td><span class="badge badge-info">{{ $domainLabels[$activity->domain] }}</span></td>
                                    <td>{{ $activity->age_min }}-{{ $activity->age_max }}</td>
                                    <td>{{ $activity->completions_count }} completions</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Create tap-friendly activities for the four early-childhood domains.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Add Child</h3></div>
                <form method="POST" action="{{ route('children.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="child-name">Name</label>
                            <input id="child-name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Birthdate</label>
                            <input id="birthdate" name="birthdate" type="date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="avatar-color">Avatar Color</label>
                            <input id="avatar-color" name="avatar_color" type="color" class="form-control" value="#38bdf8">
                        </div>
                        <div class="form-check">
                            <input id="audio-guidance" name="audio_guidance_enabled" value="1" type="checkbox" class="form-check-input" checked>
                            <label for="audio-guidance" class="form-check-label">Audio guidance</label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-plus mr-1"></i> Add Child</button>
                    </div>
                </form>
            </div>

            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">Add Activity</h3></div>
                <form method="POST" action="{{ route('activities.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input id="title" name="title" class="form-control" required placeholder="Tap the letter A">
                        </div>
                        <div class="form-group">
                            <label for="domain">Domain</label>
                            <select id="domain" name="domain" class="form-control">
                                @foreach ($domainLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prompt">Child Prompt</label>
                            <textarea id="prompt" name="prompt" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="age-min">Age Min</label>
                                <input id="age-min" name="age_min" type="number" min="2" max="8" value="2" class="form-control">
                            </div>
                            <div class="form-group col">
                                <label for="age-max">Age Max</label>
                                <input id="age-max" name="age_max" type="number" min="2" max="8" value="8" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label for="button-color">Button Color</label>
                            <input id="button-color" name="button_color" type="color" class="form-control" value="#f97316">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success btn-block" type="submit"><i class="fas fa-shapes mr-1"></i> Save Activity</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>
