@csrf

<div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="form-group">
        <label for="name">Name</label>
        <input id="name" name="name" class="form-control" value="{{ old('name', $account->name ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $account->email ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" class="form-control" required>
            @foreach (['parent' => 'Parent', 'admin' => 'Admin'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $account->role ?? 'parent') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="password">Password {{ isset($account) ? '(leave blank to keep current)' : '' }}</label>
        <input id="password" name="password" type="password" class="form-control" @required(! isset($account))>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" @required(! isset($account))>
    </div>

    <div class="form-group mb-0">
        <label>ACL Permissions</label>
        @foreach ($permissions as $permission)
            <div class="form-check">
                <input
                    id="permission-{{ $permission->id }}"
                    name="permissions[]"
                    value="{{ $permission->id }}"
                    type="checkbox"
                    class="form-check-input"
                    @checked(in_array($permission->id, old('permissions', isset($account) ? $account->permissions->pluck('id')->all() : []), true))
                >
                <label for="permission-{{ $permission->id }}" class="form-check-label">{{ $permission->label }}</label>
            </div>
        @endforeach
        <p class="text-muted small mb-0 mt-2">Admin role always has every permission.</p>
    </div>
</div>

<div class="card-footer d-flex justify-content-between">
    <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit">
        <i class="fas fa-floppy-disk mr-1"></i> Save Account
    </button>
</div>
