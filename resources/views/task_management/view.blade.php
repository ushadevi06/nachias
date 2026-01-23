@extends('layouts.common')
@section('title', 'Task Management Board - ' . env('WEBSITE_NAME'))
@section('content')
<link rel="stylesheet" href="{{ url('assets/css/jkanban.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/app-kanban.css') }}">
<!-- Content -->
<div class="container-xxl section-padding container-p-y" style="margin-top: 50px !important;">
  <div class="app-kanban">
    <!-- Add new board -->
    <div class="row mt-5">
      <div class="col-lg-12"> 
        <div class="table-header-box">
            <h4 class="mb-0 text-primary fw-bold">Task Management</h4>
              <form class="kanban-add-new-board m-0 p-0" style="float: right !important;">
              <label class="kanban-add-board-btn mb-0" style="cursor: pointer;" for="kanban-add-board-input">
                {{-- <a class="btn btn-primary" href="{{ url('task_management/add') }}">
                    <i class="icon-base ri ri-add-line me-1"></i> <span class="align-middle">Add new</span>
                </a> --}}
              </label>
              <input type="text" class="form-control w-px-250 kanban-add-board-input d-none" placeholder="Add Board Title" id="kanban-add-board-input" required />
              <div class="kanban-add-board-input d-none">
                <button class="btn btn-primary btn-sm me-3">Add</button>
                <button type="button" class="btn btn-outline-secondary btn-sm kanban-add-board-cancel-btn">Cancel</button>
              </div>
            </form>
        </div>
      </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="kanban-wrapper">
        <div class="kanban-add-new-board-column">
            <div class="kanban-add-board-trigger" id="add-list-trigger">
                <i class="ri-add-line fs-4 me-1"></i> New list
            </div>
            
            <div class="kanban-new-list-form d-none" id="add-list-form">
                <h5 class="mb-3 fw-bold">New list</h5>
                <div class="mb-4">
                    <label class="form-label-bold small text-uppercase" style="font-weight: 700; display: block; margin-bottom: 0.25rem;">Scope</label>
                    <small class="form-text-muted mb-2 d-block" style="font-size: 0.8rem; color: #718096;">Issues must match this scope to appear in this list.</small>
                    <select class="form-select form-select-sm" id="new-list-label">
                        <option value="">Select a label</option>
                        @if(isset($allStatuses))
                            @foreach($allStatuses as $status)
                                <option value="{{ $status->name }}">{{ $status->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3" id="btn-add-board-static">Add to board</button>
                    <button class="btn btn-label-secondary btn-sm px-3" id="btn-cancel-board-static">Cancel</button>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ url('assets/js/jkanban.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
  window.csrfToken = "{{ csrf_token() }}";
  window.kanbanUpdateStatusUrl = "{{ route('task_management.update_status') }}";
  window.kanbanAddUrl = "{{ url('task_management/add') }}";
  window.kanbanListViewUrl = "{{ url('task_management') }}";
  window.kanbanViewUrl = "{{ url('task_management') }}";
</script>
<script src="{{ url('assets/js/app-kanban.js') }}?v={{ time() }}"></script>
<script>
$(document).ready(function() {
  const $trigger = $('#add-list-trigger');
  const $form = $('#add-list-form');
  const $btnAdd = $('#btn-add-board-static');
  const $btnCancel = $('#btn-cancel-board-static');
  const $addListCol = $('.kanban-add-new-board-column');

    const repositionNewList = () => {
        const $container = $('.kanban-container');
        if ($container.length) {
          $container.append($addListCol);
        } else {
          setTimeout(repositionNewList, 100);
        }
    };
    repositionNewList();

    $trigger.on('click', function() {
      $(this).addClass('d-none');
      $form.removeClass('d-none');
    });

    $btnCancel.on('click', function() {
      $form.addClass('d-none');
      $trigger.removeClass('d-none');
    });

    $btnAdd.on('click', function() {
      const labelValue = $('#new-list-label').val() || 'New List';
      const boardId = 'board-' + Date.now();
        
      if (window.kanban) {
        window.kanban.addBoards([{
          id: boardId,
          title: labelValue,
          item: []
        }]);

        setTimeout(() => {
          const $container = $('.kanban-container');
          const $addListCol = $('.kanban-add-new-board-column');
          $container.append($addListCol);
        }, 50);
      }

      $form.addClass('d-none');
      $trigger.removeClass('d-none');
      $('#new-list-label').val(''); 
    });
});
</script>
@endsection
