<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Supplier Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Supplier Panel</span>

    <div class="d-flex align-items-center text-white">
        <span class="me-3">
            {{ auth('supplier')->user()->name ?? '' }}
        </span>

        <form method="POST" action="{{ route('supplier.logout') }}">
            @csrf
            <button class="btn btn-sm btn-danger">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-light vh-100 p-3">
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="{{ route('supplier.dashboard') }}" class="nav-link">Dashboard</a>
                </li>

                <li class="nav-item mb-2">
                   <a href="{{ route('supplier.catalogs.index') }}" class="nav-link">catalogs</a>
                </li>

                 <li class="nav-item mb-2">
                   <a href="{{ route('supplier.stocks.index') }}" class="nav-link">Stocks</a>
                </li>


                <li class="nav-item mb-2">
                    <a href="#" class="nav-link">Ledger</a>
                </li>
            </ul>
        </div>

        <!-- Content -->
        <div class="col-md-10 p-4">
            @yield('content')
        </div>

    </div>
</div>

</body>
</html>