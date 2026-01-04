<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Users Management System</h4>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm fw-bold">Add New User</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User Info</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">{{ $user->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_banned == 1 || $user->is_banned === true)
                                <span class="badge bg-danger text-white">Banned</span>
                            @else
                                <span class="badge bg-success text-white">Active</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                
                                @if($user->is_banned == 1 || $user->is_banned === true)
                                    <form action="{{ route('admin.users.unban', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Unban</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">Ban</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3 text-center">
            {{ $users->links() }}
        </div>
    </div>
</div>

</body>
</html>