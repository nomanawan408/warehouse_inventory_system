<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-lg sticky-top" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); backdrop-filter: blur(15px);">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/sales/create') }}">
            <i class="ti ti-building-store me-2" style="font-size: 1.8rem;"></i>
            <span class="fs-4">M.Traders</span>
        </a>
        
        <!-- Mobile Toggler -->
        <div class="d-flex align-items-center">
            <div class="me-3">
                <span class="badge bg-light text-dark fw-bold" id="clock">{{ \Carbon\Carbon::now()->format('h:i:s A') }}</span>
            </div>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Sales Section -->
                <li class="nav-item mx-1">
                    <a class="nav-link rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="{{ route('sales.create') }}">
                        <i class="ti ti-plus me-2"></i> New Sale
                    </a>
                </li>
                
                <!-- Inventory Section -->
                <li class="nav-item dropdown mx-1">
                    <a class="nav-link dropdown-toggle rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-box me-2"></i> Inventory
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark mt-2 border-0 shadow-lg" style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(15px); min-width: 220px;">
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/products')}}">
                                <i class="ti ti-packages fs-5 me-3 text-primary menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Stock Management</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/sales')}}">
                                <i class="ti ti-receipt fs-5 me-3 text-success menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Sales History</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        {{-- <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-file-import fs-5 me-3 text-info menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Import Products</div>
                                </div>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                
                <!-- People Section -->
                <li class="nav-item dropdown mx-1">
                    <a class="nav-link dropdown-toggle rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="#" id="peopleDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-users me-2"></i> People
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark mt-2 border-0 shadow-lg" style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(15px); min-width: 220px;">
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/customers')}}">
                                <i class="ti ti-user fs-5 me-3 text-warning menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Customers</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/companies')}}">
                                <i class="ti ti-building fs-5 me-3 text-danger menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Companies</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        {{-- <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-user-plus fs-5 me-3 text-success menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Add New</div>
                                </div>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                
                <!-- Accounts Section -->
                <li class="nav-item dropdown mx-1">
                    <a class="nav-link dropdown-toggle rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="#" id="accountsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-wallet me-2"></i> Accounts
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark mt-2 border-0 shadow-lg" style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(15px); min-width: 220px;">
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/accounts')}}">
                                <i class="ti ti-user-circle fs-5 me-3 text-info menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Customer Accounts</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{ route('companies.accounts') }}">
                                <i class="ti ti-building-bank fs-5 me-3 text-primary menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Company Accounts</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        {{-- <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-report-money fs-5 me-3 text-success menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Payment History</div>
                                </div>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                
                <!-- Reports Section -->
                <li class="nav-item dropdown mx-1">
                    <a class="nav-link dropdown-toggle rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-chart-bar me-2"></i> Reports
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark mt-2 border-0 shadow-lg" style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(15px); min-width: 220px;">
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="{{url('/reports')}}">
                                <i class="ti ti-file-analytics fs-5 me-3 text-warning menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Sales Report</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-file-report fs-5 me-3 text-danger menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Inventory Report</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-file-dollar fs-5 me-3 text-success menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Financial Report</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            
            <!-- User Section -->
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle rounded-pill px-4 py-2 text-white fw-medium d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-user-circle me-2"></i> User
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark mt-2 border-0 shadow-lg" style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(15px); min-width: 200px;">
                        {{-- <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-settings fs-5 me-3 text-info menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Settings</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center" href="#">
                                <i class="ti ti-help fs-5 me-3 text-warning menu-icon"></i>
                                <div class="menu-text">
                                    <div class="fw-medium">Help Center</div>
                                </div>
                            </a>
                        </li> --}}
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 d-flex align-items-center w-100 text-start border-0 bg-transparent text-white">
                                    <i class="ti ti-logout fs-5 me-3 text-danger menu-icon"></i>
                                    <div class="menu-text">
                                        <div class="fw-medium">Logout</div>
                                    </div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('.navbar-nav .nav-item .dropdown-toggle').on('click', function() {
            // Close other open submenus
            $('.navbar-nav .nav-item .dropdown-menu').not($(this).next()).removeClass('show');
        });
        
        $('.dropdown-menu small').remove();
    });
</script>

<style>
.navbar {
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

.nav-link {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-link:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
    z-index: -1;
    transition: all 0.3s ease;
    opacity: 0;
}

.nav-link:hover:before {
    opacity: 1;
}

.nav-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.2);
}

.dropdown-menu {
    border-radius: 15px;
    overflow: hidden;
    transform-origin: top;
    animation: dropdownAnimation 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.5rem; /* tighter inner spacing */
}

@keyframes dropdownAnimation {
    from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.dropdown-item {
    transition: all 0.25s ease;
    border-radius: 10px;
    margin: 0; /* remove extra outer gaps */
    padding: 0.6rem 0.9rem; /* consistent height */
}

.dropdown-item:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
    transform: translateX(4px); /* smaller shift to avoid visual gaps */
}

/* Consistent icon and text alignment */
.dropdown-item .menu-icon {
    width: 24px;
    flex: 0 0 24px;
    text-align: center;
}

.dropdown-item .menu-text .fw-medium { line-height: 1.1; }

/* Tighter divider spacing */
.dropdown-divider { margin: 0.25rem 0; opacity: 0.2; }

.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.8em;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Scrollbar styling for dropdowns */
.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>