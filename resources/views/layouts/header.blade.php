<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/sales/create') }}">M.Traders</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="d-flex justify-content-end">
            <p class="text-white-50 mt-2" id="clock"></p>
        </div>
        <script>
            function updateClock() {
                let now = new Date();
                const time = new Intl.DateTimeFormat('en-GB', { hour: 'numeric', minute: '2-digit', second: '2-digit' }).format(now);
                document.getElementById("clock").innerText = time;
            }
            updateClock();
            setInterval(updateClock, 1000);
        </script>
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
                <li class="nav-item dropdown">
                    <a class="nav-link px-3 rounded-pill mx-1 transition dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Accounts
                    </a>
                    <ul class="dropdown-menu p-2">
                        <li><a class="dropdown-item py-2" href="{{url('/accounts')}}">Customer Accounts</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('companies.accounts') }}">Company Accounts</a></li>
                    </ul>
                </li>
              
                
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill mx-1 transition" href="{{url('/reports')}}">Reports</a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary nav-link px-3 rounded-pill mx-1 transition">
                            <i class="ti ti-logout"></i> Logout
                        </button>
                    </form>
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