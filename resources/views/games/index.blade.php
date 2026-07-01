<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Games</h1>
    </x-slot>

    @foreach ($games as $game)
        <div class="card">
            <div class="card-body">
                <h3>{{ $game->title }}</h3>
                <p>{{ $game->description }}</p>

                @foreach ($children as $child)
                    @php
                        $allowed = $child->gamePermissions
                            ->where('game_id', $game->id)
                            ->first()?->is_allowed;
                    @endphp

                    <form method="POST" action="{{ route('children.games.permission', [$child, $game]) }}">
                        @csrf
                        @method('PATCH')

                        <button class="btn {{ $allowed ? 'btn-success' : 'btn-outline-secondary' }}">
                            {{ $allowed ? 'Allowed' : 'Allow' }} for {{ $child->name }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endforeach
</x-app-layout>