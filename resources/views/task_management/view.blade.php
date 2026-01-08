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
                <a class="btn btn-primary" href="{{ url('add_task_management') }}">
                    <i class="icon-base ri ri-add-line me-1"></i> <span class="align-middle">Add new</span>
                </a>
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

    <!-- Kanban Wrapper with Integrated Add List -->
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
                        <option value="Production">Production</option>
                        <option value="Research">Research</option>
                        <option value="QC">QC</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3" id="btn-add-board-static">Add to board</button>
                    <button class="btn btn-label-secondary btn-sm px-3" id="btn-cancel-board-static">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task/Task & Activities -->
    <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body pt-2">
        <ul class="nav nav-tabs mb-2 border-bottom">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
              <i class="icon-base ri ri-edit-box-line icon-sm me-1_5"></i>
              <span class="align-middle">Edit</span>
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
              <i class="icon-base ri ri-pie-chart-line icon-sm me-1_5"></i>
              <span class="align-middle">Activity</span>
            </button>
          </li>
        </ul>
        <div class="tab-content px-0 pb-0 pt-4  ">
          <!-- Update item/tasks -->
          <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
            <form>
              <div class="form-floating form-floating-outline mb-5">
                <input type="text" id="title" class="form-control" placeholder="Enter Title" />
                <label for="title">Title</label>
              </div>
              <div class="form-floating form-floating-outline mb-5">
                <input type="text" id="due-date" class="form-control" placeholder="Enter Due Date" />
                <label for="due-date">Due Date</label>
              </div>
              <div class="form-floating form-floating-outline mb-5">
                <select class="select2 select2-label form-select" id="label">
                  <option data-color="bg-label-info" value="Production">Production</option>
                  <option data-color="bg-label-secondary" value="Not Started">Not Started</option>
                </select>
                <label for="label"> label</label>
              </div>
              <div class="form-floating form-floating-outline mb-5">
                <select class="select2 select2-label form-select" id="priority">
                  <option value="High">High</option>
                  <option value="Medium">Medium</option>
                  <option value="Low">Low</option>
                </select>
                <label for="priority">Priority</label>
              </div>
              <div class="mb-5">
                <label class="form-label">Assigned</label>
                <div class="assigned d-flex flex-wrap"></div>
              </div>
              <div class="mb-5">
                <label class="form-label" for="attachments">Attachments</label>
                <div>
                  <input type="file" class="form-control" id="attachments" />
                </div>
              </div>
              <div class="mb-5">
                <label class="form-label">Comment</label>
                <div class="comment-editor"></div>
                <div class="d-flex justify-content-end">
                  <div class="comment-toolbar">
                    <span class="ql-formats me-0">
                      <button class="ql-bold"></button>
                      <button class="ql-italic"></button>
                      <button class="ql-underline"></button>
                      <button class="ql-link"></button>
                      <button class="ql-image"></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="mb-5">
                <div class="d-flex flex-wrap">
                  <button type="button" class="btn btn-primary me-4" data-bs-dismiss="offcanvas">Update</button>
                  <button type="button" class="btn btn-outline-danger" data-bs-dismiss="offcanvas">Delete</button>
                </div>
              </div>
            </form>
          </div>
          <!-- Activities -->
          <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Jordan Left the board.</p>
                <small class="text-body-secondary">Today 11:00 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Dianna mentioned <span class="text-primary">@bruce</span> in a comment.</p>
                <small class="text-body-secondary">Today 10:20 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/2.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Martian added mopd Charts & Maps task to the done board.</p>
                <small class="text-body-secondary">Today 10:00 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Barry Commented on pp review task.</p>
                <small class="text-body-secondary">Today 8:32 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Bruce was assigpd task of code review.</p>
                <small class="text-body-secondary">Today 8:30 PM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Clark assigned taskpX Research to <span class="text-primary">@martian</span></p>
                <small class="text-body-secondary">Today 8:00 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Ray Added mopd <span class="text-heading">Forms & Tables</span> task from in progress to done.</p>
                <small class="text-body-secondary">Today 7:45 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Barry Complete all pe tasks assigned to him.</p>
                <small class="text-body-secondary">Today 7:17 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Jordan added taskpo update new images.</p>
                <small class="text-body-secondary">Today 7:00 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <img src="../../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Dianna moved tpk <span class="fw-medium text-heading">FAQ UX</span> from in progress to done board.</p>
                <small class="text-body-secondary">Today 7:00 AM</small>
              </div>
            </div>
            <div class="media mb-4 d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Clark added new bopd with name <span class="fw-medium text-heading">Done</span></p>
                <small class="text-body-secondary">Yesterday 3:00 PM</small>
              </div>
            </div>
            <div class="media d-flex align-items-center">
              <div class="avatar me-3 flex-shrink-0">
                <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
              </div>
              <div class="media-body ms-1">
                <p class="mb-0">Bruce added new tpk in progress board.</p>
                <small class="text-body-secondary">Yesterday 12:00 PM</small>
              </div>
            </div>
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
  window.kanbanAddUrl = "{{ url('add_task_management') }}";
  window.kanbanViewUrl = "{{ url('view_task_details') }}";
  window.kanbanListViewUrl = "{{ url('view_task_management') }}";
</script>
<script src="{{ url('assets/js/app-kanban.js') }}"></script>
<script>
  // ➕ Static "+ New list" Logic
  $(document).ready(function() {
      const $trigger = $('#add-list-trigger');
      const $form = $('#add-list-form');
      const $btnAdd = $('#btn-add-board-static');
      const $btnCancel = $('#btn-cancel-board-static');
      const $addListCol = $('.kanban-add-new-board-column');

      // 🔄 Initial positioning: Move "+ New list" inside the scrollable container
      const repositionNewList = () => {
          const $container = $('.kanban-container');
          if ($container.length) {
              $container.append($addListCol);
          } else {
              // Try again if jKanban hasn't finished rendering
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

              // 🔄 Reposition the "Add List" column to the end
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
