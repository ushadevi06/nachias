@extends('layouts.common')
@section('title', 'Dashboard - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Card Border Shadow -->
    <div class="row g-6">
        <div class="col-sm-6 col-lg-3 mb-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <h6 class="mb-0 fw-normal mb-3">Total Orders</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ri ri-file-list-fill icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">42</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <h6 class="mb-0 fw-normal mb-3">Total Stock Value</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ri ri-funds-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">679</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <h6 class="mb-0 fw-normal mb-3">Profit Margin</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ri ri-wallet-3-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">24.8%</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <h6 class="mb-0 fw-normal mb-3">Total Sales</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ri ri-shopping-cart-2-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">530</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Progress Indicator -->
        <div class="row justify-content-start mb-6">
            <div class="col-lg-6">
                <div class="card text-center p-4">
                    <h5 class="card-title">Orders in Progress</h5>
                    <canvas id="progressChart" width="150" height="150" style="margin: 0 auto;"></canvas>
                    <p class="mt-3 mb-0">204/300 Orders</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-4">
                    <h5 class="card-title">Monthly Sales</h5>
                    <canvas id="salesChart" width="600" height="300"></canvas>
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
                backgroundColor: ['#007bff', '#e9ecef'], // Blue for complete, gray for remaining
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
                label: 'Sales ($)',
                data: [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150], // Approximate values from the image
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.2)', // Light blue fill
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
                            return '$' + value + 'K';
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
</script>
@endsection