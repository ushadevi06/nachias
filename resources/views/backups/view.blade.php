@extends('layouts.common')
@section('title', 'Backup & Restore - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Backup & Restore</h4>
                <a class="btn btn-primary" href="javascript:void(0)" id="generateBackupBtn">
                    <i class="menu-icon icon-base ri ri-stack-line"></i> Generate System Backup
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="backups-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Backup ID</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                    <th>Location</th>
                                    <th>Size</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Restore Backup Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Restore Database</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i> <strong>Warning:</strong> This action will <strong>overwrite</strong> the current database. All unsaved data will be lost.
                </div>
                <form id="restoreForm">
                    <input type="hidden" id="restore_id" name="id">
                    <div class="mb-3">
                        <label for="password" class="form-label">Super Admin Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password to confirm">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRestoreBtn">Restore Database</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        let table = $('#backups-table').DataTable({
            processing: true,
            serverSide: false, // Client-side pagination for now as we don't have server-side pagination in controller
            ajax: "{{ url('backup_restore') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'backup_no', name: 'backup_no' },
                { data: 'backup_type', name: 'backup_type' },
                { data: 'created_at', name: 'created_at' },
                { data: 'location', name: 'location' },
                { data: 'file_size', name: 'file_size' },
                { data: 'created_by', name: 'created_by' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // Generate Backup
        $('#generateBackupBtn').click(function() {
            Swal.fire({
                title: 'Generate Backup?',
                text: "Are you sure you want to generate a new database backup?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Generating Backup...',
                        html: '<div class="loader"></div><p>Please wait while your backup is being created.</p>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('backup_restore/generate') }}",
                        type: "POST",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('✅ Success!', response.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('❌ Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('❌ Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                }
            });
        });

        // Restore Backup
        window.confirmRestore = function(id) {
            $('#restore_id').val(id);
            $('#restoreModal').modal('show');
        }

        $('#confirmRestoreBtn').click(function() {
            let btn = $(this);
            let password = $('#password').val();

            if (!password) {
                toastr.error('Please enter your password to confirm.');
                return;
            }

            if (!confirm('Are you absolutely sure you want to restore? This irreversible action will overwrite your database.')) {
                return;
            }

            btn.prop('disabled', true).html('<i class="ri-loader-4-line animation-spin"></i> Restoring...');

            $.ajax({
                url: "{{ url('backup_restore/restore') }}",
                type: "POST",
                data: { 
                    _token: "{{ csrf_token() }}",
                    id: $('#restore_id').val(),
                    password: password
                },
                success: function(response) {
                    $('#restoreModal').modal('hide');
                    btn.prop('disabled', false).text('Restore Database');
                    $('#restoreForm')[0].reset();
                    
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload(); 
                        }, 2000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    $('#restoreModal').modal('hide');
                    btn.prop('disabled', false).text('Restore Database');
                    toastr.error('An error occurred during restoration.');
                }
            });
        });

        setInterval(function() {
            let hasRunning = false;
            table.rows().data().each(function (value, index) {
                if (value.status.includes('Running')) {
                    hasRunning = true;
                }
            });
            if (hasRunning) {
                table.ajax.reload(null, false);
            }
        }, 5000);
    });
</script>
@endsection