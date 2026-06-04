<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0">Worksheet Library</h1>
                <p class="text-muted mb-0">
                    @if ($canManageWorksheets)
                        Upload PDF worksheets for each age group.
                    @else
                        Select an age group to view admin-uploaded PDF worksheets.
                    @endif
                </p>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Worksheets</li>
                </ol>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row">
        <section class="col-lg-8">
            @if (! $canManageWorksheets)
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Select Age Group</h3>
                    </div>
                    <form method="GET" action="{{ route('worksheets.index') }}">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="age_group">Age Group</label>
                                <select id="age_group" name="age_group" class="form-control" onchange="this.form.submit()">
                                    <option value="">All age groups</option>
                                    @foreach ($ageGroups as $value => $label)
                                        <option value="{{ $value }}" @selected($selectedAgeGroup === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Uploaded Worksheets
                        @if ($selectedAgeGroup)
                            <span class="text-muted">- {{ $ageGroups[$selectedAgeGroup] }}</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Worksheet</th>
                                <th>Age Group</th>
                                <th>Subject</th>
                                <th>Assigned</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($worksheets as $worksheet)
                                <tr>
                                    <td>
                                        <strong>{{ $worksheet->title }}</strong>
                                        <div class="text-muted small">{{ $worksheet->original_filename }}</div>
                                        @if ($worksheet->description)
                                            <div class="text-muted small">{{ $worksheet->description }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-primary">{{ $ageGroups[$worksheet->age_group] ?? $worksheet->age_group }}</span></td>
                                    <td><span class="badge badge-info">{{ $subjectLabels[$worksheet->subject] }}</span></td>
                                    <td>{{ $worksheet->assignments_count }} child{{ $worksheet->assignments_count === 1 ? '' : 'ren' }}</td>
                                    <td class="text-right">
                                        @if ($canManageWorksheets)
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('worksheets.view', $worksheet) }}" target="_blank" rel="noopener">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('worksheets.download', $worksheet) }}">
                                                <i class="fas fa-download mr-1"></i> Download
                                            </a>
                                            <form method="POST" action="{{ route('admin.worksheets.destroy', $worksheet) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('worksheets.view', $worksheet) }}" target="_blank" rel="noopener">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('worksheets.download', $worksheet) }}">
                                                <i class="fas fa-download mr-1"></i> PDF
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">No admin worksheets are available for this age group yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (! $isAdmin)
                @foreach ($children as $child)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <span class="task-priority-dot" style="background: {{ $child->avatar_color }}"></span>
                                {{ $child->name }}'s Assigned Worksheets
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Worksheet</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th class="text-right">Track</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($child->worksheetAssignments as $assignment)
                                        <tr>
                                            <td>
                                                <strong>{{ $assignment->worksheet->title }}</strong>
                                                <div class="text-muted small">{{ $assignment->worksheet->original_filename }}</div>
                                            </td>
                                            <td><span class="badge badge-info">{{ $subjectLabels[$assignment->worksheet->subject] }}</span></td>
                                            <td>
                                                <span class="badge {{ $assignment->status === 'completed' ? 'badge-success' : ($assignment->status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}">
                                                    {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                @if ($assignment->status === 'assigned')
                                                    <form method="POST" action="{{ route('worksheet-assignments.start', $assignment) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-primary" type="submit">
                                                            <i class="fas fa-play mr-1"></i> Start
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($assignment->status !== 'completed')
                                                    <form method="POST" action="{{ route('worksheet-assignments.complete', $assignment) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-success" type="submit">
                                                            <i class="fas fa-check mr-1"></i> Done
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">No worksheets assigned to this child yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </section>

        <section class="col-lg-4">
            @if ($canManageWorksheets)
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Upload Worksheets</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.worksheets.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" name="title" class="form-control" placeholder="Optional title, otherwise file names are used">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <select id="subject" name="subject" class="form-control">
                                    @foreach ($subjectLabels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="age_group">Age Group</label>
                                <select id="age_group" name="age_group" class="form-control" required>
                                    @foreach ($ageGroups as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="worksheet_files">Worksheet Files</label>
                                <input id="worksheet_files" name="worksheet_files[]" type="file" class="form-control" accept="application/pdf,.pdf" multiple required>
                                <small class="form-text text-muted">Select one or more PDF files for the chosen age group and subject.</small>
                            </div>
                            <div class="form-group mb-0">
                                <label for="description">Notes</label>
                                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Optional parent notes."></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fas fa-upload mr-1"></i> Upload Worksheets
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if (! $isAdmin)
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Assign to Child</h3>
                    </div>
                    <form method="POST" action="{{ route('worksheets.assign') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="child_profile_id">Child</label>
                                <select id="child_profile_id" name="child_profile_id" class="form-control" required>
                                    @foreach ($children as $child)
                                        <option value="{{ $child->id }}">{{ $child->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label for="worksheet_id">Worksheet</label>
                                <select id="worksheet_id" name="worksheet_id" class="form-control" required>
                                    @foreach ($worksheets as $worksheet)
                                        <option value="{{ $worksheet->id }}">{{ $worksheet->title }} - {{ $ageGroups[$worksheet->age_group] ?? $worksheet->age_group }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success btn-block" type="submit" @disabled($children->isEmpty() || $worksheets->isEmpty())>
                                <i class="fas fa-user-check mr-1"></i> Assign Worksheet
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
