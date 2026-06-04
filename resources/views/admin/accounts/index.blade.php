<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0">Accounts & ACL</h1>
                <p class="text-muted mb-0">Create accounts, edit roles, and assign permissions.</p>
            </div>
            <div class="col-sm-5 text-sm-right">
                <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Add Account
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-info' }}">{{ ucfirst($user->role) }}</span></td>
                            <td>
                                @forelse ($user->permissions as $permission)
                                    <span class="badge badge-secondary">{{ $permission->label }}</span>
                                @empty
                                    <span class="text-muted">Role defaults</span>
                                @endforelse
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.accounts.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-pen mr-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
