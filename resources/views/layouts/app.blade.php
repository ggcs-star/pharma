<!DOCTYPE html>
<html>
<head>
    <title>Pharma ERP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Pharma ERP</a>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>
@stack('scripts')

</body>
</html>