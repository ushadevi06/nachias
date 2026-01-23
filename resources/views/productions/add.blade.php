@extends('layouts.common')
@section('title', 'Production Planning - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-11">
            <form action="{{ url('productions/add' . ($production ? '/' . $production->id : '')) }}" method="POST" class="common-form">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-error-warning-line fs-4 me-2"></i>
                            <h6 class="mb-0 fw-bold">Please correct the following errors:</h6>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success shadow-sm border-0 mb-4 d-flex align-items-center">
                        <i class="ri ri-checkbox-circle-line fs-4 me-2"></i>
                        <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger shadow-sm border-0 mb-4 d-flex align-items-center">
                        <i class="ri-error-warning-line fs-4 me-2"></i>
                        <h6 class="mb-0 fw-bold">{{ session('error') }}</h6>
                    </div>
                @endif

                <div class="card mb-4 border-0 shadow-sm erp-header-card">
                    <div class="card-header border-bottom py-3 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-file-list-3-line fs-4 me-2 text-primary"></i>
                            <h5 class="mb-0 fw-bold">Module 1: Production (Planning & Control)</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="production_id" value="PROD-2026-001" readonly>
                                    <label>Production ID</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="job_card_entry_id" name="job_card_entry_id" class="select2 form-select @error('job_card_entry_id') is-invalid @enderror" data-placeholder="Select Job Card">
                                        <option value="">Select Job Card</option>
                                        @foreach($jobCards as $jc)
                                            <option value="{{ $jc->id }}" {{ old('job_card_entry_id', optional($production)->job_card_entry_id) == $jc->id ? 'selected' : '' }}>{{ $jc->job_card_no }}</option>
                                        @endforeach
                                    </select>
                                    <label>Job Card No <span class="text-danger">*</span></label>
                                    @error('job_card_entry_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" name="job_card_no" id="job_card_no_hidden" value="{{ old('job_card_no', optional($production)->job_card_no) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="po_no_display" value="{{ optional($production)->purchase_order_no }}" readonly>
                                    <input type="hidden" name="purchase_order_id" id="purchase_order_id" value="{{ old('purchase_order_id', optional($production)->purchase_order_id) }}">
                                    <input type="hidden" name="purchase_order_no" id="purchase_order_no" value="{{ old('purchase_order_no', optional($production)->purchase_order_no) }}">
                                    <label>Purchase Order No</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="plant_id" name="plant_id" class="select2 form-select @error('plant_id') is-invalid @enderror" data-placeholder="Select Plant">
                                        <option value="">Select Plant</option>
                                        @foreach($plants as $plant)
                                            <option value="{{ $plant->id }}" {{ old('plant_id', optional($production)->plant_id) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                        @endforeach
                                    </select>
                                    <label>Plant</label>
                                    @error('plant_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="process_group_display" value="{{ optional($production)->processGroup ? $production->processGroup->name : '' }}" readonly>
                                    <input type="hidden" name="process_group_id" id="process_group_id" value="{{ old('process_group_id', optional($production)->process_group_id) }}">
                                    <label>Process Group</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" name="full_sleeve_qty" id="planned_qty_fs" value="{{ old('full_sleeve_qty', optional($production)->full_sleeve_qty ?? 0) }}" readonly>
                                    <label>Full Sleeve Qty (FS)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" name="half_sleeve_qty" id="planned_qty_hs" value="{{ old('half_sleeve_qty', optional($production)->half_sleeve_qty ?? 0) }}" readonly>
                                    <label>Half Sleeve Qty (HS)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control bg-light" name="total_planned_qty" id="planned_qty" value="{{ old('total_planned_qty', optional($production)->total_planned_qty ?? 0) }}" readonly>
                                    <label>Total Planned Qty</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker @error('planned_start_date') is-invalid @enderror" name="planned_start_date" id="start_date" value="{{ old('planned_start_date', (isset($production->planned_start_date) ? date('d-m-Y', strtotime($production->planned_start_date)) : '')) }}" placeholder="Planned Start Date">
                                    <label>Planned Start Date <span class="text-danger">*</span></label>
                                    @error('planned_start_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker @error('planned_end_date') is-invalid @enderror" name="planned_end_date" id="end_date" value="{{ old('planned_end_date', (isset($production->planned_end_date) ? date('d-m-Y', strtotime($production->planned_end_date)) : '')) }}" placeholder="Planned End Date">
                                    <label>Planned End Date <span class="text-danger">*</span></label>
                                    @error('planned_end_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker @error('expected_completion_date') is-invalid @enderror" name="expected_completion_date" id="completion_date" value="{{ old('expected_completion_date', (isset($production->expected_completion_date) ? date('d-m-Y', strtotime($production->expected_completion_date)) : '')) }}" placeholder="Exp. Completion Date">
                                    <label>Exp. Completion Date <span class="text-danger">*</span></label>
                                    @error('expected_completion_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value=""></option>
                                        <option value="Draft" {{ old('status', optional($production)->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="Confirmed" {{ old('status', optional($production)->status) == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="Closed" {{ old('status', optional($production)->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <label>Status <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" id="remarks">{{ old('remarks', optional($production)->remarks) }}</textarea>
                                    <label>Remarks</label>
                                    @error('remarks')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm erp-header-card">
                    <div class="card-header border-bottom py-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="ri ri-node-tree me-2 text-info"></i> Module 2: Process Schedule
                            </h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="bs-stepper wizard-numbered mt-2">
                            <div class="bs-stepper-header border-bottom">
                                @foreach($operationStages as $index => $stage)
                                    <div class="step {{ $index === 0 ? 'active' : '' }}" data-target="#stage-content-{{ $stage->id }}" data-id="{{ $stage->id }}" data-name="{{ $stage->operation_stage_name }}">
                                        <button type="button" class="step-trigger" disabled>
                                            <span class="bs-stepper-circle">
                                                @if($index === 0)
                                                    <i class="ri ri-check-line d-none"></i>
                                                @endif
                                                <span class="step-number">{{ $index + 1 }}</span>
                                            </span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">{{ $stage->operation_stage_name }}</span>
                                            </span>
                                        </button>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="line"></div>
                                    @endif
                                @endforeach
                            </div>
                            
                            <div class="bs-stepper-content pt-4">
                                @foreach($operationStages as $index => $stage)
                                    <div id="stage-content-{{ $stage->id }}" class="content {{ $index === 0 ? 'active d-block' : 'd-none' }}">
                                        <div class="row g-3">
                                            <div class="col-12 mb-2">
                                                <h6 class="fw-bold text-primary">{{ $stage->operation_stage_name }} Details</h6>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control schedule-input" name="schedules[{{ $stage->id }}][planned_qty]" data-field="planned_qty" placeholder="Planned Qty">
                                                    <label>Planned Qty</label>
                                                    @error('schedules.' . $stage->id . '.planned_qty')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control date-picker schedule-input" name="schedules[{{ $stage->id }}][start_date]" data-field="start_date" placeholder="Start Date">
                                                    <label>Start Date <span class="text-danger">*</span></label>
                                                    @error('schedules.' . $stage->id . '.start_date')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control date-picker schedule-input" name="schedules[{{ $stage->id }}][end_date]" data-field="end_date" placeholder="End Date">
                                                    <label>End Date <span class="text-danger">*</span></label>
                                                    @error('schedules.' . $stage->id . '.end_date')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control date-picker schedule-input" name="schedules[{{ $stage->id }}][due_date]" data-field="due_date" placeholder="Due Date">
                                                    <label>Due Date <span class="text-danger">*</span></label>
                                                    @error('schedules.' . $stage->id . '.due_date')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating form-floating-outline">
                                                    <select class="form-select select2 schedule-input" name="schedules[{{ $stage->id }}][scheduled_to]" data-field="scheduled_to" data-plac>
                                                        <option value="">Select Unit</option>
                                                        @foreach($plants as $plant)
                                                            <option value="{{ $plant->name }}">{{ $plant->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Scheduled To <span class="text-danger">*</span></label>
                                                    @error('schedules.' . $stage->id . '.scheduled_to')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12 mt-4">
                                                <h6 class="fw-bold text-secondary mb-3"><i class="ri ri-list-settings-line me-1"></i> {{ $stage->operation_stage_name }} Services</h6>
                                                <div class="table-responsive border rounded bg-white">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th style="width: 50px;">Check</th>
                                                                <th>Service Name</th>
                                                                <th>Applies To</th>
                                                                <th class="text-end">Qty</th>
                                                                <th>UOM</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="services-tbody-{{ $stage->id }}">
                                                            <tr><td colspan="5" class="text-center small text-muted">Select a Job Card to load services</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-4 d-flex justify-content-between">
                                                @if(!$loop->first)
                                                    <button type="button" class="btn btn-outline-secondary btn-prev" data-target="{{ $index - 1 }}">
                                                        <i class="ri ri-arrow-left-line me-1"></i> Previous
                                                    </button>
                                                @else
                                                    <div></div>
                                                @endif
                                                
                                                @if(!$loop->last)
                                                    <button type="button" class="btn btn-primary btn-next" data-target="{{ $index + 1 }}">
                                                        Next <i class="ri ri-arrow-right-line ms-1"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="mt-4">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 me-2">
                            <i class="ri ri-save-line me-1"></i> Submit
                        </button>
                        <a href="{{ url('productions') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let schedulesState = {};

    @if(isset($production) && $production->processSchedules)
        @foreach($production->processSchedules as $schedule)
            @php
                $sObj = \App\Models\OperationStage::where('operation_stage_name', $schedule->stage)->first();
                $sId = $sObj ? $sObj->id : 0;
            @endphp
            @if($sId)
            schedulesState[{{ $sId }}] = {
                planned_qty: "{{ $schedule->planned_qty }}",
                start_date: "{{ $schedule->start_date ? date('d-m-Y', strtotime($schedule->start_date)) : '' }}",
                end_date: "{{ $schedule->end_date ? date('d-m-Y', strtotime($schedule->end_date)) : '' }}",
                due_date: "{{ $schedule->due_date ? date('d-m-Y', strtotime($schedule->due_date)) : '' }}",
                scheduled_to: "{{ $schedule->scheduled_to }}",
                services: @json($schedule->services->map(function($s){ return ['service_id' => $s->service_id, 'selected' => 1]; }))
            };
            @endif
        @endforeach
    @endif
    
    @if(old('schedules'))
        @foreach(old('schedules') as $sId => $oldData)
            schedulesState[{{ $sId }}] = {
                planned_qty: "{{ $oldData['planned_qty'] ?? '' }}",
                start_date: "{{ $oldData['start_date'] ?? '' }}",
                end_date: "{{ $oldData['end_date'] ?? '' }}",
                due_date: "{{ $oldData['due_date'] ?? '' }}",
                scheduled_to: "{{ $oldData['scheduled_to'] ?? '' }}",
                services: []
            };
             @if(isset($oldData['services']))
                 schedulesState[{{ $sId }}].services = [
                     @foreach($oldData['services'] as $oldSvc)
                        @if(isset($oldSvc['selected']))
                            { service_id: "{{ $oldSvc['service_id'] }}", selected: 1 },
                        @endif
                     @endforeach
                 ];
             @endif
        @endforeach
    @endif

    $(document).ready(function() {
        $('.date-picker').flatpickr({ dateFormat: 'd-m-Y' });
        $('.select2').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({ placeholder: "Select", allowClear: true, width: '100%' });
            }
        });

        const firstStep = $('.step.active');
        if (firstStep.length && $('#job_card_entry_id').val()) {
            loadStepData(firstStep.data('id'), firstStep.data('name'));
        }


        $('.btn-next').on('click', function() {
            const currentContent = $(this).closest('.content');
            const nextContent = currentContent.next('.content');
            if (nextContent.length) {
                const nextStepIndex = nextContent.index('.bs-stepper-content .content');
                const nextStep = $('.bs-stepper-header .step').eq(nextStepIndex);
                activateStep(nextStep);
            }
        });

        $('.btn-prev').on('click', function() {
            const currentContent = $(this).closest('.content');
            const prevContent = currentContent.prev('.content');
            if (prevContent.length) {
                const prevStepIndex = prevContent.index('.bs-stepper-content .content');
                const prevStep = $('.bs-stepper-header .step').eq(prevStepIndex);
                activateStep(prevStep);
            }
        });

        function activateStep(stepEl) {
            $('.step').removeClass('active');
            stepEl.addClass('active');
            
            $('.step').removeClass('completed');
            stepEl.prevAll('.step').addClass('completed');
            
            $('.step').each(function() {
                if ($(this).hasClass('completed')) {
                    $(this).find('.bs-stepper-circle').html('<i class="ri-check-line"></i>');
                    $(this).find('.bs-stepper-circle').html('<i class="ri-check-line"></i>');
                } else if($(this).hasClass('active')) {
                    const num = $(this).index('.step') + 1;
                    $(this).find('.bs-stepper-circle').html(num);
                    $(this).find('.bs-stepper-circle').html(num);
                } else {
                    const num = $(this).index('.step') + 1;
                    $(this).find('.bs-stepper-circle').html(num);
                    $(this).find('.bs-stepper-circle').html(num);
                }
            });

            $('.content').removeClass('active d-block').addClass('d-none');
            const target = $(stepEl.data('target'));
            target.removeClass('d-none').addClass('active d-block');
            
            const id = stepEl.data('id');
            const name = stepEl.data('name');
            
            if ($('#job_card_entry_id').val()) {
                loadStepData(id, name);
            }
        }

        function loadStepData(stageId, stageName) {
            const jobCardId = $('#job_card_entry_id').val();
            if (schedulesState[stageId]) {
                const data = schedulesState[stageId];
                $(`input[name="schedules[${stageId}][planned_qty]"]`).val(data.planned_qty);
                $(`input[name="schedules[${stageId}][start_date]"]`).val(data.start_date);
                $(`input[name="schedules[${stageId}][end_date]"]`).val(data.end_date);
                $(`input[name="schedules[${stageId}][due_date]"]`).val(data.due_date);
                $(`select[name="schedules[${stageId}][scheduled_to]"]`).val(data.scheduled_to).trigger('change');
            } else {
                const inputQty = $(`input[name="schedules[${stageId}][planned_qty]"]`);
                if(inputQty.val() === '' && $('#planned_qty').val()) {
                    inputQty.val($('#planned_qty').val());
                }
            }

            fetchServicesForStage(stageId, stageName, jobCardId);
        }

        function fetchServicesForStage(stageId, stageName, jobCardId) {
            const tbody = $(`#services-tbody-${stageId}`);
             
            if (tbody.find('tr').length > 1 && !tbody.find('td').text().includes('No services')) {
                return;
            }

            tbody.html('<tr><td colspan="5" class="text-center small">Loading services...</td></tr>');
             
            $.ajax({
                url: "{{ url('productions/get-services') }}/" + stageName + "/" + jobCardId,
                method: 'GET',
                success: function(response) {
                    tbody.empty();
                    if (response.success && response.services.length > 0) {
                        const savedState = schedulesState[stageId] ? schedulesState[stageId].services : [];
                        const savedIds = savedState.map(s => String(s.service_id));
                        
                        const isNewRecord = !schedulesState[stageId];

                        response.services.forEach((service, index) => {
                            let isChecked = isNewRecord ? true : savedIds.includes(String(service.id));
                            
                            tbody.append(`
                                <tr>
                                    <td>
                                        <input type="checkbox" name="schedules[${stageId}][services][${index}][selected]" value="1" class="form-check-input" ${isChecked ? 'checked' : ''}>
                                        <input type="hidden" name="schedules[${stageId}][services][${index}][service_id]" value="${service.id}">
                                        <input type="hidden" name="schedules[${stageId}][services][${index}][applies_to]" value="${service.applies_to}">
                                        <input type="hidden" name="schedules[${stageId}][services][${index}][qty]" value="${service.qty}">
                                        <input type="hidden" name="schedules[${stageId}][services][${index}][uom]" value="${service.uom}">
                                    </td>
                                    <td>${service.service_name}</td>
                                    <td><span class="badge bg-label-info">${service.applies_to}</span></td>
                                    <td class="text-end fw-bold">${service.qty}</td>
                                    <td>${service.uom}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.html('<tr><td colspan="5" class="text-center small text-muted">No services found for this stage.</td></tr>');
                    }
                },
                error: function() {
                    tbody.html('<tr><td colspan="5" class="text-center small text-danger">Error loading services.</td></tr>');
                }
             });
        }

        $('#job_card_entry_id').on('change', function() {
            const id = $(this).val();
            if(!id) return;

            $('tbody[id^="services-tbody-"]').empty();
            schedulesState = {}; 

            $.ajax({
                url: "{{ url('productions/get-job-card-details') }}/" + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#job_card_no_hidden').val($('#job_card_entry_id option:selected').text());
                        $('#po_no_display').val(data.purchase_order_no);
                        $('#purchase_order_id').val(data.purchase_order_id);
                        $('#purchase_order_no').val(data.purchase_order_no);
                        $('#plant_id').val(data.plant_id).trigger('change');
                        $('#process_group_display').val(data.process_group_name);
                        $('#process_group_id').val(data.process_group_id);
                        $('#planned_qty_fs').val(data.fs_qty);
                        $('#planned_qty_hs').val(data.hs_qty);
                        $('#planned_qty').val(data.total_qty);
                        
                        const activeStep = $('.step.active');
                        if(activeStep.length) {
                            loadStepData(activeStep.data('id'), activeStep.data('name'));
                        }
                    }
                }
            });
        });
        

    });
</script>

<style>
    .bs-stepper-header {
        display: flex;
        align-items: center;
        flex-wrap: nowrap; 
        overflow-x: auto; 
        padding-bottom: 1rem;
    }
    
    .step {
        flex: 0 0 auto; 
        position: relative;
        padding: 0 1rem;
    }

    .step-trigger {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        padding: 0;
        cursor: default;
        outline: none !important;
    }

    .bs-stepper-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #697a8d;
        background-color: #f5f5f9;
        margin-right: 0.75rem;
        transition: all 0.3s ease;
    }

    .step.active .bs-stepper-circle {
        background-color: var(--bs-primary); 
        color: #fff;
        box-shadow: 0 3px 6px rgba(105, 108, 255, 0.4);
    }
    
    .step.completed .bs-stepper-circle {
        background-color: var(--bs-primary); 
        color: #fff;
    }

    .bs-stepper-label {
        display: flex;
        flex-direction: column;
        align-items: start;
    }

    .bs-stepper-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #566a7f;
        white-space: nowrap;
    }

    .bs-stepper-subtitle {
        font-size: 0.75rem;
        color: #b4bdc6;
    }

    .line {
        flex: 1;
        height: 2px;
        background-color: #e9ecef;
        min-width: 50px;
        margin: 0 0.5rem;
    }
</style>
@endsection
