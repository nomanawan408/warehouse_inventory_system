<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">LOGO</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                {{-- <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/welcome')}}">Home</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition btn btn-secondary text-white" href="{{ route('sales.create') }}">Create New Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/products')}}">Stocks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/sales')}}">Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/customers')}}">Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/companies')}}">Companies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/accounts')}}">Accounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/reports')}}">Reports</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    backdrop-filter: blur(10px);
}

.nav-link {
    position: relative;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.transition {
    transition: all 0.3s ease;
}
</style>