<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Create Account</h1>
    </x-slot>

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-primary">
                <form method="POST" action="{{ route('admin.accounts.store') }}">
                    @include('admin.accounts._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
