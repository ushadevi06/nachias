@extends('layouts.common')
@section('title', 'Add Task Creation - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Task Creation</h4>
                        </div>

                        <div class="row g-4">
                            <!-- Task ID -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="task_id" placeholder="Enter Task ID" name="task_id" value="TASK-2025-001" readonly>
                                    <label for="task_id">Task ID</label>
                                </div>
                            </div>

                            <!-- Job Card No -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="job_card_no" name="job_card_no" data-placeholder="Select Job Card No">
                                        <option value="">Select Job Card No</option>
                                        <option value="JC20250924-001-K">JC20250924-001-K</option>
                                        <option value="JC20250924-002-K">JC20250924-002-K</option>
                                        <option value="JC20250924-003-K">JC20250924-003-K</option>
                                    </select>
                                    <label for="job_card_no">Job Card No *</label>
                                </div>
                            </div>

                            <!-- Task Title -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="task_title" placeholder="Enter Task Title" name="task_title">
                                    <label for="task_title">Task Title</label>
                                </div>
                            </div>

                            <!-- Task Description -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="task_description" placeholder="Enter Task Description"></textarea>
                                    <label for="task_description">Task Description</label>
                                </div>
                            </div>

                            <!-- Static Job Card Details -->
                            <div class="col-lg-12 mb-5 d-none" id="job_card_details_container">
                                <h6 class="fw-semibold mb-3">Job Card Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Item</th>
                                                <th>Ordered Qty</th>
                                                <th>Balanced Qty</th>
                                                <th>Size</th>
                                                <th>Sleeve Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Men’s Denim Shirt (ITEM001)</td>
                                                <td><input type="number" class="form-control text-center" value="100"></td>
                                                <td><input type="number" class="form-control text-center" value="40"></td>
                                                <td>38, 40, 42, 44 - 1, 2, 3, 1</td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_full_1">
                                                            <label class="form-check-label" for="checked_full_1">Checked Full Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_full_half_1">
                                                            <label class="form-check-label" for="checked_full_half_1">Checked Full & Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_half_1">
                                                            <label class="form-check-label" for="checked_half_1">Checked Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_full_1">
                                                            <label class="form-check-label" for="others_full_1">Others Full Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_full_half_1">
                                                            <label class="form-check-label" for="others_full_half_1">Others Full & Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_half_1">
                                                            <label class="form-check-label" for="others_half_1">Others Half Sleeve</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Men’s Cotton Shirt (ITEM002)</td>
                                                <td><input type="number" class="form-control text-center" value="50"></td>
                                                <td><input type="number" class="form-control text-center" value="20"></td>
                                                <td>38, 40, 42, 44 - 5, 6, 7, 8</td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_full_2">
                                                            <label class="form-check-label" for="checked_full_2">Checked Full Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_full_half_2">
                                                            <label class="form-check-label" for="checked_full_half_2">Checked Full & Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checked_half_2">
                                                            <label class="form-check-label" for="checked_half_2">Checked Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_full_2">
                                                            <label class="form-check-label" for="others_full_2">Others Full Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_full_half_2">
                                                            <label class="form-check-label" for="others_full_half_2">Others Full & Half Sleeve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="others_half_2">
                                                            <label class="form-check-label" for="others_half_2">Others Half Sleeve</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <!-- Task Type -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Task Type">
                                        <option value="">Select Task Type</option>
                                        <option value="Instant">Instant</option>
                                        <option value="Scheduled">Scheduled</option>
                                    </select>
                                    <label for="task_type">Task Type *</label>
                                </div>
                            </div>

                            <!-- Assigned Team -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Assigned Team">
                                        <option value="">Select Assigned Team</option>
                                        <option value="Cutting Department">Cutting Department</option>
                                        <option value="Quality Department">Quality Department</option>
                                        <option value="Stitching Department">Stitching Department</option>
                                    </select>
                                    <label for="assigned_team">Assigned Team *</label>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control start_date" placeholder="Enter Start Date">
                                    <label for="start_date">Start Date *</label>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="quantity" placeholder="Enter Quantity" name="quantity">
                                    <label for="quantity">Quantity *</label>
                                </div>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Priority">
                                        <option value="">Select Priority</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                    <label for="priority">Priority *</label>
                                </div>
                            </div>

                            <!-- Deadline Date -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control deadline_date" id="deadline_date" placeholder="Enter Deadline Date" name="deadline_date">
                                    <label for="deadline_date">Deadline Date *</label>
                                </div>
                            </div>

                            <!-- Related Department -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Related Department">
                                        <option value="">Select Related Department</option>
                                        <option value="Production">Productiong</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Store">Store</option>
                                        <option value="HR">HR</option>
                                        <option value="Accounts">Accounts</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                    <label for="related_department">Related Department </label>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Overdue">Overdue</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('task_creation') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.deadline_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.start_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#job_card_no').on('change', function() {
            const jcNo = $(this).val();
            if (jcNo) {
                $('#job_card_details_container').removeClass('d-none');
            } else {
                $('#job_card_details_container').addClass('d-none');
            }
        });
    });
</script>
@endsection