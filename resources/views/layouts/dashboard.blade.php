<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #111827;
        }
        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: #1f2937;
            color: #fff;
        }
        .content {
            padding: 25px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h5 class="text-white text-center py-3">Admin Panel</h5>

        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.users.index') }}">Users</a>

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button class="btn btn-danger w-100">Logout</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="content">
        @yield('content')
    </main>
</div>

</body>
</html>
