<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BrightSteps Learning') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

        <style>
            body {
                font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }

            .brand-link {
                border-bottom: 1px solid rgba(255, 255, 255, .08);
            }

            .content-wrapper {
                background: #f4f6f9;
            }

            .task-priority-dot {
                border-radius: 999px;
                display: inline-block;
                height: .65rem;
                margin-right: .4rem;
                width: .65rem;
            }

            .task-progress {
                height: .55rem;
            }
        </style>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('profile.edit') }}" class="nav-link">Profile</a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item d-none d-md-block mr-3 text-muted">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-right-from-bracket mr-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <i class="fas fa-list-check brand-image mt-1 ml-3"></i>
                    <span class="brand-text font-weight-light ml-2">BrightSteps Learning</span>
                </a>

                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <span class="img-circle elevation-2 d-inline-flex align-items-center justify-content-center bg-info" style="height: 34px; width: 34px;">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <div class="info">
                            <a href="{{ route('profile.edit') }}" class="d-block">{{ Auth::user()->name ?? 'Task Admin' }}</a>
                        </div>
                    </div>

                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-gauge-high"></i>
                                    <p>Parent Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-shapes"></i>
                                    <p>Curriculum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('worksheets.index') }}" class="nav-link {{ request()->routeIs('worksheets.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-lines"></i>
                                    <p>Worksheets</p>
                                </a>
                            </li>
                            @if (Auth::user()?->isAdmin())
                                <li class="nav-header">ADMIN</li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.worksheets.index') }}" class="nav-link {{ request()->routeIs('admin.worksheets.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-pdf"></i>
                                        <p>Worksheet Uploads</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.accounts.index') }}" class="nav-link {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users-gear"></i>
                                        <p>Accounts & ACL</p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-gear"></i>
                                    <p>Account Settings</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <div class="content-wrapper">
                @isset($header)
                    <section class="content-header">
                        <div class="container-fluid">
                            {{ $header }}
                        </div>
                    </section>
                @endisset

                <section class="content">
                    <div class="container-fluid pb-4">
                        {{ $slot }}
                    </div>
                </section>
            </div>

            <footer class="main-footer">
                <strong>{{ config('app.name', 'BrightSteps Learning') }}</strong>
                <span class="float-right d-none d-sm-inline">Parent-guided learning</span>
            </footer>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    </body>
</html>
