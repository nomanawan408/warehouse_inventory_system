<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">LOGO</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">OVERVIEW</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">SALES</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">STOCK</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">REPORTS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">CUSTOMERS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">COMPANIES</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-8">
                <div class="container-box">
                    <input type="text" class="search-bar" placeholder="Search Item here....">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>PRODUCT NAME</th>
                                <th>QTY</th>
                                <th>PRICE</th>
                                <th>Discount</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Lays Sneaks 100gm</td>
                                <td>100</td>
                                <td>05</td>
                                <td>05</td>
                                <td>550</td>
                            </tr>
                            <tr>
                                <td>Lays Sneaks 100gm</td>
                                <td>100</td>
                                <td>05</td>
                                <td>05</td>
                                <td>550</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="container-box">
                    <h5><strong>STOCK</strong></h5>
                    <button class="btn btn-green w-100 mb-2">REPORTS</button>
                    <h6>Select Customer</h6>
                    <!-- <div class="border p-2 mb-3">TOTAL: 3,000</div>
                    <div class="border p-2 mb-3">Discounts: 50</div>
                    <div class="border p-2 total-box">Net Total: 5,550</div>
                    <button class="btn btn-green w-100 mb-2">RESET</button> -->
                    <button class="btn btn-green w-100">PAY NOW</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
