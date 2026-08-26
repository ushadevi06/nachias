<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between py-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="ri-bar-chart-box-line text-primary me-2"></i>Sales, Returns & Net Sales Summary
            </h5>
        </div>
        
        <!-- Interactive Period Selector Pills -->
        <div class="btn-group mt-2 mt-sm-0" role="group" aria-label="Sales Period Filter">
            <button type="button" class="btn btn-sm btn-outline-primary active sales-period-btn" data-target="period-all">
                <i class="ri-layout-grid-line me-1"></i> ALL CARDS
            </button>
            <button type="button" class="btn btn-sm btn-outline-success sales-period-btn" data-target="period-today">
                <i class="ri-sun-line me-1"></i> TODAY
            </button>
            <button type="button" class="btn btn-sm btn-outline-info sales-period-btn" data-target="period-monthly">
                <i class="ri-calendar-line me-1"></i> MONTHLY
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning sales-period-btn" data-target="period-yearly">
                <i class="ri-calendar-2-line me-1"></i> YEARLY
            </button>
        </div>
    </div>
    
    <div class="card-body py-4">
        <div class="row g-4">
            <!-- 1. TODAY SALES CARD -->
            <div class="col-lg-4 col-md-6 period-card" id="period-today">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden kpi-hover-card" style="background: linear-gradient(145deg, #f3fdf5 0%, #e2efda 100%); border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-success text-white px-3 py-2 fs-6 fw-bold">
                                <i class="ri-sun-line me-1"></i> TODAY SALES
                            </span>
                            <span class="text-muted small fw-bold">{{ date('d M Y') }}</span>
                        </div>

                        <!-- Net Sales Highlight -->
                        <div class="bg-white p-3 rounded-3 shadow-sm mb-3 border">
                            <span class="text-muted small fw-bold d-block text-uppercase">Net Sales (Sales - Returns)</span>
                            <h3 class="fw-bold text-success mb-1">₹{{ number_format($today_net_wot, 2) }}</h3>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-label-success fw-bold">{{ number_format($today_net_qty) }} Items</span>
                                <span class="text-muted small" style="font-size:11px;">WOT (Without Tax)</span>
                            </div>
                        </div>

                        <!-- Breakdown Grid -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Gross Sales</span>
                                    <strong class="text-dark d-block">₹{{ number_format($today_sales_wot, 2) }}</strong>
                                    <small class="text-muted" style="font-size:10px;">{{ number_format($today_sales_qty) }} Items</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Sales Return</span>
                                    <strong class="text-danger d-block">₹{{ number_format($today_return_wot, 2) }}</strong>
                                    <small class="text-danger" style="font-size:10px;">{{ number_format($today_return_qty) }} Items</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. MONTHLY SALES CARD -->
            <div class="col-lg-4 col-md-6 period-card" id="period-monthly">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden kpi-hover-card" style="background: linear-gradient(145deg, #f0f4fd 0%, #d9e1f2 100%); border-left: 5px solid #007bff !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary text-white px-3 py-2 fs-6 fw-bold">
                                <i class="ri-calendar-line me-1"></i> MONTHLY SALES
                            </span>
                            <span class="text-muted small fw-bold">{{ date('M Y') }}</span>
                        </div>

                        <!-- Net Sales Highlight -->
                        <div class="bg-white p-3 rounded-3 shadow-sm mb-3 border">
                            <span class="text-muted small fw-bold d-block text-uppercase">Net Sales (Sales - Returns)</span>
                            <h3 class="fw-bold text-primary mb-1">₹{{ number_format($month_net_wot, 2) }}</h3>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-label-primary fw-bold">{{ number_format($month_net_qty) }} Items</span>
                                <span class="text-muted small" style="font-size:11px;">WOT (Without Tax)</span>
                            </div>
                        </div>

                        <!-- Breakdown Grid -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Gross Sales</span>
                                    <strong class="text-dark d-block">₹{{ number_format($month_sales_wot, 2) }}</strong>
                                    <small class="text-muted" style="font-size:10px;">{{ number_format($month_sales_qty) }} Items</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Sales Return</span>
                                    <strong class="text-danger d-block">₹{{ number_format($month_return_wot, 2) }}</strong>
                                    <small class="text-danger" style="font-size:10px;">{{ number_format($month_return_qty) }} Items</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. YEARLY SALES CARD -->
            <div class="col-lg-4 col-md-6 period-card" id="period-yearly">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden kpi-hover-card" style="background: linear-gradient(145deg, #fffdf0 0%, #fff2cc 100%); border-left: 5px solid #ffc107 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">
                                <i class="ri-calendar-2-line me-1"></i> YEARLY SALES
                            </span>
                            <span class="text-muted small fw-bold">{{ date('Y') }}</span>
                        </div>

                        <!-- Net Sales Highlight -->
                        <div class="bg-white p-3 rounded-3 shadow-sm mb-3 border">
                            <span class="text-muted small fw-bold d-block text-uppercase">Net Sales (Sales - Returns)</span>
                            <h3 class="fw-bold text-dark mb-1">₹{{ number_format($year_net_wot, 2) }}</h3>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-label-warning text-dark fw-bold">{{ number_format($year_net_qty) }} Items</span>
                                <span class="text-muted small" style="font-size:11px;">WOT (Without Tax)</span>
                            </div>
                        </div>

                        <!-- Breakdown Grid -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Gross Sales</span>
                                    <strong class="text-dark d-block">₹{{ number_format($year_sales_wot, 2) }}</strong>
                                    <small class="text-muted" style="font-size:10px;">{{ number_format($year_sales_qty) }} Items</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-3 border text-start">
                                    <span class="text-muted small d-block" style="font-size:11px;">Sales Return</span>
                                    <strong class="text-danger d-block">₹{{ number_format($year_return_wot, 2) }}</strong>
                                    <small class="text-danger" style="font-size:10px;">{{ number_format($year_return_qty) }} Items</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .kpi-hover-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .kpi-hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<script>
$(document).ready(function() {
    $('.sales-period-btn').on('click', function() {
        $('.sales-period-btn').removeClass('active');
        $(this).addClass('active');

        let target = $(this).data('target');
        if (target === 'period-all') {
            $('.period-card').fadeIn(200);
        } else {
            $('.period-card').hide();
            $('#' + target).fadeIn(300);
        }
    });
});
</script>
