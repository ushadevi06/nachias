@extends('layouts.common')
@section('title', 'Logs & Audit Log - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Logs & Audit Log</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="logs-table table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action Type</th>
                                    <th>Employee</th>
                                    <th>Module</th>
                                    <th>Record</th>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="logDetailModalLabel">
                    <i class="icon-base ri ri-file-list-3-line me-2"></i>Log Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="text-muted small mb-1"><i class="icon-base ri ri-shield-check-line me-1"></i>Action</div>
                            <span id="modalActionType" class="badge bg-primary fs-6"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="text-muted small mb-1"><i class="icon-base ri ri-user-line me-1"></i>Employee</div>
                            <div class="fw-semibold" id="modalEmployee"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="text-muted small mb-1"><i class="icon-base ri ri-time-line me-1"></i>Date & Time</div>
                            <div class="fw-semibold" id="modalDate"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1"><i class="icon-base ri ri-apps-line me-1"></i>Module</div>
                            <div class="fw-semibold" id="modalModule"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1"><i class="icon-base ri ri-file-text-line me-1"></i>Record</div>
                            <div class="fw-semibold" id="modalRecord"></div>
                        </div>
                    </div>
                </div>

                <!-- Changed Fields Section -->
                <div class="border rounded">
                    <div class="bg-light border-bottom px-3 py-2">
                        <h6 class="mb-0"><i class="icon-base ri ri-exchange-line me-2"></i>Changed Fields</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="changedFieldsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3" style="width:30%">Field</th>
                                    <th class="px-3" style="width:35%">Old Value</th>
                                    <th class="px-3" style="width:35%">New Value</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        let table = $('.logs-table').DataTable({
            processing: true,
            responsive: true,
            serverSide: false,
            ajax: {
                url: "{{ url('logs') }}",
                data: function(d) {
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action_type'
                },
                {
                    data: 'user_name'
                },
                {
                    data: 'module'
                },
                {
                    data: 'record'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });

    function viewLogDetails(id) {
        $.ajax({
            url: "{{ url('logs/details') }}/" + id,
            type: "GET",
            success: function(response) {
                if (response.success) {
                    $('#modalModule').text(response.module);
                    $('#modalEmployee').text(response.user_name);
                    $('#modalRecord').text(response.record);
                    $('#modalDate').text(response.created_at);

                    // Dynamic badge color based on action type
                    let badgeClass = 'bg-secondary';
                    if (response.action_type.toLowerCase() === 'create') badgeClass = 'bg-success';
                    else if (response.action_type.toLowerCase() === 'update') badgeClass = 'bg-info';
                    else if (response.action_type.toLowerCase() === 'delete') badgeClass = 'bg-danger';
                    
                    $('#modalActionType')
                        .removeClass('bg-primary bg-success bg-info bg-danger bg-secondary')
                        .addClass(badgeClass)
                        .text(response.action_type);

                    let changedBody = '';
                    if (response.changed_fields && response.changed_fields.length > 0) {
                        $.each(response.changed_fields, function(index, item) {
                            changedBody += `<tr>
                                <td class="px-3"><strong>${item.field}</strong></td>
                                <td class="px-3"><span class="text-danger">${item.old !== '-' ? '<del>' + item.old + '</del>' : '-'}</span></td>
                                <td class="px-3"><span class="text-success fw-semibold">${item.new}</span></td>
                            </tr>`;
                        });
                    } else {
                        changedBody = '<tr><td colspan="3" class="text-center text-muted py-3">No changes recorded</td></tr>';
                    }
                    $('#changedFieldsTable tbody').html(changedBody);

                    $('#logDetailModal').modal('show');
                }
            }
        });
    }
</script>
@endsection