@extends('layouts.common')
@section('title', 'Production Stage Consumables - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="table-header-box">
                        <h4 class="card-title">Production Stage Consumables</h4>
                        <a href="{{ url('production_stage_consumables/add') }}" class="btn btn-primary"><i class="ri-add-line"></i> Add New</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="consumablesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Stage</th>
                                    <th>Raw Material</th>
                                    <th>Qty Per Unit</th>
                                    <th>UOM</th>
                                    <th>Status</th>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#consumablesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('production_stage_consumables') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'stage', name: 'stage' },
                { data: 'raw_material_name', name: 'raw_material.name' },
                { data: 'quantity_per_unit', name: 'quantity_per_unit' },
                { data: 'uom_code', name: 'uom.name' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endsection
