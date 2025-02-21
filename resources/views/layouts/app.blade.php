<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('index.css') }}">
</head>
<body>
    
    @include('layouts.header')
    
    @if(session()->has('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show text-center mx-auto" style="max-width: 600px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" role="alert">
                <strong><i class="fas fa-check-circle me-2"></i></strong>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    
    @if(session()->has('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show text-center mx-auto" style="max-width: 600px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" role="alert">
                <strong><i class="fas fa-exclamation-circle me-2"></i></strong>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    
    @yield('content')    

    @include('layouts.footer')

</body>
</html>