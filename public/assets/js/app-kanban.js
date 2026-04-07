'use strict';

(async function () {
    let kanbanWrapper = document.querySelector('.kanban-wrapper');
    if (!kanbanWrapper) return;

    let kanbanSidebar = document.querySelector('.kanban-update-item-sidebar'),
        assetsPath = document.querySelector('html').getAttribute('data-assets-path');

    let kanbanData;
    try {
        const response = await fetch(window.kanbanViewUrl || (assetsPath + 'json/kanban.json'), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        if (!response.ok) throw new Error('Failed to fetch kanban data');
        kanbanData = await response.json();
    } catch (error) {
        console.error('Error loading Kanban data:', error);
        kanbanData = [];
    }

    function renderDropdown(id, viewId) {
        const editUrl = window.kanbanAddUrl ? `${window.kanbanAddUrl}/${id}` : 'javascript:void(0)';
        const viewUrl = window.kanbanListViewUrl ? `${window.kanbanListViewUrl}/view/${id}` : 'javascript:void(0)';

        return `
      <div class="dropdown kanban-tasks-item-dropdown">
        <i class="dropdown-toggle icon-base ri ri-more-2-fill cursor-pointer" id="kanban-tasks-item-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="kanban-tasks-item-dropdown">
          <a class="dropdown-item" href="${editUrl}"> <i class="icon-base ri ri-edit-box-line me-2"></i> Edit</a>
          <a class="dropdown-item" href="${viewUrl}"><i class="icon-base ri ri-eye-line me-2"></i> View</a>
        </div>
      </div>`;
    }

    function renderBoardDropdown() {
        return `
      <div class="dropdown">
        <i class="dropdown-toggle icon-base ri ri-more-2-fill cursor-pointer" id="board-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="board-dropdown">
          <a class="dropdown-item delete-board" href="javascript:void(0)"><i class="ri ri-delete-bin-line me-1"></i> Delete</a>
          <a class="dropdown-item" href="javascript:void(0)"><i class="ri ri-edit-2-line me-1"></i> Rename</a>
        </div>
      </div>`;
    }

    function updateTaskProgress(el, status) {
        const badge = el.querySelector('.status-dropdown-toggle');
        if (badge) badge.textContent = status;

        const progressText = el.querySelector('.progress-label span:last-child');
        const progressBar = el.querySelector('.progress-bar');

        let percentage = null;
        let colorClass = 'bg-secondary';
        let labelColorClass = 'bg-label-secondary';

        if (status === 'Completed') {
            percentage = 100;
            colorClass = 'bg-success';
            labelColorClass = 'bg-label-success';
        } else if (status === 'In Progress') {
            colorClass = 'bg-secondary';
            labelColorClass = 'bg-label-secondary';
        } else if (status === 'Hold') {
            colorClass = 'bg-warning';
            labelColorClass = 'bg-label-warning';
        } else if (status === 'Planned') {
            colorClass = 'bg-info';
            labelColorClass = 'bg-label-info';
        } else {
            colorClass = 'bg-info';
            labelColorClass = 'bg-label-info';
        }

        if (percentage !== null && progressText) {
            if (percentage === 100) {
                const target = el.getAttribute('data-target-qty') || '0';
                progressText.textContent = `${target} / ${target} PCS (100%)`;
            } else {
                progressText.textContent = percentage + '%';
            }
        }

        if (progressBar) {
            if (percentage !== null) {
                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
            }
            progressBar.classList.remove('bg-success', 'bg-secondary', 'bg-warning', 'bg-info');
            progressBar.classList.add(colorClass);
        }

        if (badge) {
            badge.classList.remove('bg-label-success', 'bg-label-secondary', 'bg-label-warning', 'bg-label-info');
            badge.classList.add(labelColorClass);
        }
    }

    function renderStatusDropdown(currentStatus) {
        let statusColor = 'secondary';
        if (currentStatus === 'Completed') statusColor = 'success';
        else if (currentStatus === 'Hold') statusColor = 'warning';
        else if (currentStatus === 'In Progress') statusColor = 'secondary';
        else if (currentStatus === 'Planned') statusColor = 'secondary';
        else statusColor = 'info';

        let boards = [];
        if (window.kanban) {
            boards = window.kanban.options.boards.map(b => b.id);
        } else {
            boards = ['Planned', 'In Progress', 'Completed', 'Hold'];
        }

        let boardOptions = boards.map(b => `<li><a class="dropdown-item change-task-status" href="javascript:void(0)" data-status="${b}">${b}</a></li>`).join('');

        return `
            <div class="dropdown mb-2">
                <button class="badge rounded-pill bg-label-${statusColor} border-0 dropdown-toggle status-dropdown-toggle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    ${currentStatus}
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">Change Status</li>
                    ${boardOptions}
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item add-custom-task-status" href="javascript:void(0)">+ Custom Status</a></li>
                </ul>
            </div>
        `;
    }

    const kanban = new jKanban({
        element: '.kanban-wrapper',
        gutter: '12px',
        widthBoard: '250px',
        dragItems: true,
        boards: kanbanData,
        dragBoards: true,
        addItemButton: false,
        onBoardAdd: function (el) {
            window.kanban = kanban;
            const header = el.querySelector('.kanban-title-board');
            if (header && !header.nextElementSibling?.classList.contains('dropdown')) {
                header.insertAdjacentHTML('afterend', renderBoardDropdown());
            }
        },
        dropEl: function (el, target, source, sibling) {
            const taskId = el.getAttribute('data-eid');
            const boardEl = target.closest('.kanban-board');
            const newStatus = boardEl ? boardEl.getAttribute('data-id') : null;

            console.log('Kanban Move:', { taskId, newStatus });

            if (taskId && newStatus) {
                // If moving to Completed, it will be validated on the server.
                // We keep track of the source status to revert if needed.
                const sourceStatus = source ? (source.getAttribute('data-id') || source.closest('.kanban-board').getAttribute('data-id')) : null;

                $.ajax({
                    url: window.kanbanUpdateStatusUrl,
                    method: 'POST',
                    data: {
                        _token: window.csrfToken,
                        task_id: taskId,
                        status: newStatus
                    },
                    success: function (response) {
                        // Debug log to console
                        console.log('Kanban API Response:', response);

                        if (response && response.success === true) {
                            updateTaskProgress(el, newStatus);
                        } else {
                            const errorMsg = response.message || 'Action Blocked: Please complete all assignments first.';
                            
                            // 1. Show the error first (ensures user sees it even if revert fails)
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Action Blocked',
                                    text: errorMsg,
                                    icon: 'warning',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } else {
                                alert(errorMsg);
                            }

                            // 2. Revert movement with safety check
                            if (window.kanban && sourceStatus) {
                                try {
                                    window.kanban.moveElement(taskId, sourceStatus);
                                } catch (e) {
                                    console.error('Failed to revert Kanban move:', e);
                                    // Manual fallback if moveElement fails
                                    if (source && el) {
                                        source.appendChild(el);
                                    }
                                }
                            }
                        }
                    },
                    error: function (xhr) {
                        console.error('AJAX Error:', xhr.responseText);
                        
                        // Revert on server error too
                        if (window.kanban && sourceStatus) {
                            window.kanban.moveElement(taskId, sourceStatus);
                        }
                        
                        let errorMessage = 'An error occurred while updating task status.';
                        try {
                            const resObj = JSON.parse(xhr.responseText);
                            if (resObj && resObj.message) errorMessage = resObj.message;
                        } catch(e) {}
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            }
        },
        click: function (el) {
        }
    });

    window.kanban = kanban;

    const kanbanItems = Array.from(document.querySelectorAll('.kanban-item'));
    if (kanbanItems.length) {
        kanbanItems.forEach(function (el) {
            const eid = el.getAttribute('data-eid');
            let itemData = null;
            if (kanbanData && Array.isArray(kanbanData)) {
                kanbanData.forEach(board => {
                    if (board.item && Array.isArray(board.item)) {
                        const found = board.item.find(item => String(item.id) === String(eid));
                        if (found) itemData = found;
                    }
                });
            }
            if (!itemData) return;

            const taskId = itemData.task_no || 'TASK-000';
            const title = itemData.title || el.textContent || '';
            const badgeText = itemData['badge-text'] || '';
            const jcStart = itemData['jc-start'] || 'N/A';
            const jcEnd = itemData['jc-end'] || 'N/A';

            el.innerHTML = '';

            const headerHtml = `
                <div class="kanban-item-header">
                    <span class="kanban-item-id">${taskId}</span>
                    ${renderDropdown(eid, itemData.id)}
                </div>
            `;

            const badgeHtml = renderStatusDropdown(badgeText);
            const titleHtml = `<span class="kanban-text">${title}</span>`;
            const datesHtml = `
                <div class="kanban-item-dates">
                    <div class="date-box start-date">
                        <small style="color: #696cff; font-weight: 600;"><i class="ri-calendar-line"></i> START</small>
                        <div class="date-value">${jcStart}</div>
                    </div>
                    <div class="date-box due-date">
                        <small style="color: #696cff; font-weight: 600;"><i class="ri-flag-2-line"></i> END</small>
                        <div class="date-value">${jcEnd}</div>
                    </div>
                </div>
            `;

            const workingLevel = itemData.working_level || '0 / 0 PCS';
            const totalReceived = itemData.total_received || 0;
            const targetQty = itemData.target_qty || 0;

            el.setAttribute('data-working-level', workingLevel);
            el.setAttribute('data-target-qty', targetQty);
            el.setAttribute('data-received-qty', totalReceived);

            const progressColor = (badgeText === 'Completed') ? 'bg-success' : 'bg-secondary';

            // Commented out working level and progress display
            /*
            const progressHtml = `
                <div class="kanban-progress-wrapper">
                    <div class="progress-label">
                        <span>Working Level</span>
                        <span>${workingLevel} (${progress}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar ${progressColor}" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div >
                `;
            */
            const progressHtml = ''; // Working level and progress hidden

            const stageName = itemData.stage_name || 'No S+ badgeHtmltage';
            const stageHtml = `<div class="text-xs mb-2 text-dark bg-label-warning p-1 rounded fw-bold text-center">${stageName}</div>`;

            el.insertAdjacentHTML('beforeend', headerHtml + titleHtml + stageHtml + datesHtml + progressHtml);
        });
    }

    $(document).on('click', '.kanban-title-board', function () {
        this.contentEditable = 'true';
        this.focus();
    });

    $(document).on('click', '.kanban-tasks-item-dropdown', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '.kanban-tasks-item-dropdown .dropdown-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const url = $(this).attr('href');
        if (url && url !== 'javascript:void(0)') {
            window.location.href = url;
        }
    });

    $(document).on('click', '.change-task-status', function (e) {
        e.preventDefault();
        const newStatus = $(this).data('status');
        const taskItem = $(this).closest('.kanban-item')[0];
        const taskId = taskItem.getAttribute('data-eid');
        const currentBoard = $(this).closest('.kanban-board').data('id');

        if (window.kanban && taskId && newStatus) {
            // We use the same AJAX here instead of moveElement immediately
            // to ensure validation passes before UI update
             $.ajax({
                    url: window.kanbanUpdateStatusUrl,
                    method: 'POST',
                    data: {
                        _token: window.csrfToken,
                        task_id: taskId,
                        status: newStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            window.kanban.moveElement(taskId, newStatus);
                            updateTaskProgress(taskItem, newStatus);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Action Blocked',
                                    text: response.message,
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function (xhr) {
                         alert('An error occurred while updating task status.');
                    }
                });
        }
    });

    $(document).on('click', '.add-custom-task-status', function (e) {
        e.preventDefault();
        const taskItem = $(this).closest('.kanban-item')[0];
        const taskId = taskItem.getAttribute('data-eid');
        const newStatus = prompt('Enter custom status name:');

        if (newStatus && window.kanban && taskId) {
            const existingBoard = window.kanban.findBoard(newStatus);
            if (!existingBoard) {
                // Save to database first
                $.ajax({
                    url: window.location.origin + '/task_management/add_custom_status',
                    method: 'POST',
                    data: {
                        _token: window.csrfToken,
                        status_name: newStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            // Add to Kanban board
                            window.kanban.addBoards([{
                                id: newStatus,
                                title: newStatus,
                                item: []
                            }]);

                            const $container = $('.kanban-container');
                            const $addListCol = $('.kanban-add-new-board-column');
                            if ($container.length && $addListCol.length) {
                                $container.append($addListCol);
                            }
                            window.kanban.moveElement(newStatus, taskId);
                        } else {
                            alert('Failed to add custom status: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON?.errors;
                            if (errors && errors.status_name) {
                                alert('Error: ' + errors.status_name[0]);
                            } else {
                                alert('Status already exists or is invalid');
                            }
                        } else {
                            alert('Failed to add custom status. Please try again.');
                        }
                    }
                });
            } else {
                window.kanban.moveElement(newStatus, taskId);
            }
        }
    });

    const kanbanContainer = document.querySelector('.kanban-container');
    if (kanbanContainer && typeof PerfectScrollbar !== 'undefined') {
        new PerfectScrollbar(kanbanContainer);
    }
})();