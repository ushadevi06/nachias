@extends('layouts.common')
@section('title', 'Dashboard - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Card Border Shadow -->
    <div class="row g-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="dashboard-title">Total Orders</h6>
                    <div class="inner-wrapper">
                        <div class="icon-box">
                            <i class="ri ri-file-list-fill"></i>
                        </div>
                        <div class="content-box">
                            <h4>45</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card dashboard-card two">
                <div class="card-body">
                    <h6 class="dashboard-title">Total Stock Value</h6>
                    <div class="inner-wrapper">
                        <div class="icon-box">
                            <i class="ri ri-funds-line"></i>
                        </div>
                        <div class="content-box">
                            <h4>679</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card dashboard-card three">
                <div class="card-body">
                    <h6 class="dashboard-title">Profit Margin</h6>
                    <div class="inner-wrapper">
                        <div class="icon-box">
                            <i class="ri ri-wallet-3-line"></i>
                        </div>
                        <div class="content-box">
                            <h4>24.8%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card dashboard-card four">
                <div class="card-body">
                    <h6 class="dashboard-title">Total Sales</h6>
                    <div class="inner-wrapper">
                        <div class="icon-box">
                            <i class="ri ri-shopping-cart-2-line"></i>
                        </div>
                        <div class="content-box">
                            <h4>530</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Progress Indicator -->
        <div class="col-lg-12">
            <div class="row justify-content-start g-4">
                <div class="col-lg-6">
                    <div class="card text-center common-card">
                        <div class="card-body">
                            <h5 class="card-title">Orders in Progress</h5>
                            <div class="common-chart">
                                <canvas id="progressChart"></canvas>
                            </div>
                            <p class="mt-3 mb-0">204/300 Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card common-card">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Sales</h5>
                            <div class="common-chart">
                                <canvas id="salesChart" width="600" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-8 col-xl-9">
                            <h5 class="dashboard-card-title">Sales vs Expenses</h5>
                        </div>
                        <div class="col-lg-4 col-xl-3">
                            <select id="filterRange" class="form-select">
                                <option value="first">First 6 Months</option>
                                <option value="last">Last 6 Months</option>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                            <div class="common-chart">
                                <canvas id="salesExpensesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <!-- Data Source -->
                    <div class="mb-3">
                        <label for="dataSource" class="form-label">Data Source</label>
                        <select id="dataSource" class="form-select">
                            <option value="" disabled selected>-- Select Module --</option>
                            <option value="sales">Sales</option>
                            <option value="production">Production</option>
                            <option value="store">Store</option>
                            <option value="accounts">Accounts</option>
                        </select>
                    </div>

                    <!-- Display Type -->
                    <div class="mb-3">
                        <label for="displayType" class="form-label">Display Type</label>
                        <select id="displayType" class="form-select">
                            <option value="" disabled selected>-- Select Display Type --</option>
                            <option value="table">Table</option>
                            <option value="chart">Chart</option>
                            <option value="kpi">KPI</option>
                            <option value="number">Number Widget</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-3">
                        <label for="dateRange" class="form-label">Date Range</label>
                        <select id="dateRange" class="form-select">
                            <option value="" disabled selected>-- Select Range --</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <!-- Custom Date Picker (hidden unless Custom is selected) -->
                    <div class="row mb-3" id="customDateRow" style="display:none;">
                        <div class="col">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Metric</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card common-card">
                <div class="card-body">
                    <h5 class="card-title text-start">Pending Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S.No</th>
                                <th>Item(Code)</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Men’s Casual Denim Shirt(ITEM001)</td>
                                <td>Hero Mens Wear(CUS001)</td>
                                <td>3</td>
                                <td>₹50</td>
                                <td>₹150</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Men’s Formal Cotton Shirt(ITEM002)</td>
                                <td>Unlimited Fashion Store(CUS002)</td>
                                <td>1</td>
                                <td>₹25</td>
                                <td>₹25</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Subtotal</strong></td>
                                <td colspan="2"><strong>₹175.00</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Add New Widget</h5>
                    <hr>
                    <form class="common-form">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <label for="widgetName" class="form-label">Widget Name</label>
                                <input type="text" id="widgetName" class="form-control" placeholder="Enter Widget Name">
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label for="widgetType" class="form-label">Widget Type</label>
                                <select id="widgetType" class="form-select select2" data-placeholder="Select Widget Type">
                                    <option value="" disabled selected>-- Select Widget Type --</option>
                                    <option value="chart">Chart</option>
                                    <option value="graph">Graph</option>
                                    <option value="table">Table</option>
                                    <option value="kpi">KPI Card</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label for="dataSource" class="form-label">Data Source</label>
                                <select id="dataSource" class="form-select select2" data-placeholder="Select Data Source">
                                    <option value="" disabled selected>-- Select Data Source --</option>
                                    <option value="custom">Custom Report</option>
                                    <option value="predefined">Predefined Report</option>
                                    <option value="module">Module Data</option>
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h5>Filters</h5>
                                <div class="row g-2">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Data Range</label>
                                        <select class="form-select select2" data-placeholder="Select Data Range">
                                            <option value="">Date Range</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" class="form-control" placeholder="Category">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Department</label>
                                        <input type="text" class="form-control" placeholder="Department">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Customer</label>
                                        <input type="text" class="form-control" placeholder="Customer">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label for="position" class="form-label">Position on Dashboard</label>
                                <input type="number" id="position" class="form-control" placeholder="Enter sequence order (e.g., 1, 2, 3)">
                                <small class="text-muted">Use numbers to define sequence order. (Drag & Drop can be implemented in dashboard view)</small>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select id="visibility" class="form-select select2" data-placeholder="Select Visibility">
                                    <option value="" disabled selected>-- Select Visibility --</option>
                                    <option value="user">User Based</option>
                                    <option value="role">Role Based</option>
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <div class="button-box">
                                    <button type="submit" class="btn btn-primary">Save Widget</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [68, 32], // 68% complete, 32% remaining
                backgroundColor: ['#71a769', '#e9ecef'], // Blue for complete, gray for remaining
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%', // Makes it a ring/doughnut
            circumference: 360,
            rotation: -90,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            },
            animation: {
                animateRotate: true
            }
        }
    });

    const progressText = document.createElement('div');
    progressText.style.position = 'absolute';
    progressText.style.top = '52%';
    progressText.style.left = '50%';
    progressText.style.transform = 'translate(-50%, -50%)';
    progressText.innerHTML = '<h3>68%</h3><p>Complete</p>';
    document.getElementById('progressChart').parentElement.style.position = 'relative';
    document.getElementById('progressChart').parentElement.appendChild(progressText);

    // Chart configuration for Monthly Sales
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Sales (₹)',
                data: [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150], // Approximate values from the image
                borderColor: '#FFC69D',
                backgroundColor: '#ffc69d2d', // Light blue fill
                borderWidth: 1, // Reduced from 2 to 1
                fill: true,
                tension: 0.4,
                pointRadius: 3, // Reduced from 5 to 3
                pointHoverRadius: 5 // Reduced from 7 to 5
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value + 'K';
                        },
                        font: {
                            size: 10 // Reduced font size for y-axis
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 8 // Reduced font size for x-axis from 10 to 8
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y + 'K';
                        }
                    },
                    bodyFont: {
                        size: 10 // Reduced font size for tooltip
                    }
                }
            }
        }
    });
    const salesExpensesCtx = document.getElementById('salesExpensesChart').getContext('2d');

    // Full year data (in thousands)
    const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ],
        sales: [12, 15, 18, 17, 20, 23, 21, 25, 27, 30, 32, 34], // in K
        expenses: [10, 13, 14, 16, 18, 19, 20, 22, 23, 25, 27, 28] // in K
    };

    function getFilteredData(range) {
        if (range === 'first') {
            return {
                labels: chartData.labels.slice(0, 6),
                sales: chartData.sales.slice(0, 6),
                expenses: chartData.expenses.slice(0, 6)
            };
        } else {
            return {
                labels: chartData.labels.slice(6, 12),
                sales: chartData.sales.slice(6, 12),
                expenses: chartData.expenses.slice(6, 12)
            };
        }
    }

    // Initial load: First 6 months
    let filtered = getFilteredData('first');

    let salesExpensesChart = new Chart(salesExpensesCtx, {
        type: 'bar',
        data: {
            labels: filtered.labels,
            datasets: [{
                    label: 'Sales',
                    data: filtered.sales,
                    backgroundColor: '#16b1ff96',
                    borderColor: '#16b1ff',
                    borderWidth: 1
                },
                {
                    label: 'Expenses',
                    data: filtered.expenses,
                    backgroundColor: '#ff00008c',
                    borderColor: '#ff0000',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10, // Show 10K, 20K, 30K
                        callback: function(value) {
                            return value + 'K';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + 'K';
                        }
                    }
                }
            }
        }
    });

    // Handle filter change
    document.getElementById('filterRange').addEventListener('change', function() {
        let selected = this.value;
        let filtered = getFilteredData(selected);

        salesExpensesChart.data.labels = filtered.labels;
        salesExpensesChart.data.datasets[0].data = filtered.sales;
        salesExpensesChart.data.datasets[1].data = filtered.expenses;
        salesExpensesChart.update();
    });

    document.getElementById('dateRange').addEventListener('change', function() {
        if (this.value === 'custom') {
            document.getElementById('customDateRow').style.display = 'flex';
        } else {
            document.getElementById('customDateRow').style.display = 'none';
        }
    });
</script>
@endsection