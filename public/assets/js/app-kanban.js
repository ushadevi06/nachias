/**
 * App Kanban - Premium Architecture
 */

'use strict';

(async function () {
    let kanbanWrapper = document.querySelector('.kanban-wrapper');
    if (!kanbanWrapper) return;

    let kanbanSidebar = document.querySelector('.kanban-update-item-sidebar'),
        assetsPath = document.querySelector('html').getAttribute('data-assets-path');

    // Kanban Board Data
    let kanbanData;
    try {
        const response = await fetch(assetsPath + 'json/kanban.json');
        if (!response.ok) throw new Error('Failed to fetch kanban.json');
        kanbanData = await response.json();
    } catch (error) {
        console.error('Error loading Kanban data:', error);
        kanbanData = [];
    }

    function renderDropdown() {
        return `
      <div class="dropdown kanban-tasks-item-dropdown">
        <i class="dropdown-toggle icon-base ri ri-more-2-fill cursor-pointer" id="kanban-tasks-item-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="kanban-tasks-item-dropdown">
          <a class="dropdown-item" href="${window.kanbanAddUrl || 'javascript:void(0)'}">Edit</a>
          <a class="dropdown-item" href="${window.kanbanViewUrl || 'javascript:void(0)'}">View</a>
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

    // Initialize jKanban
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
        click: function (el) {
            // Card body click disabled as per request
        }
    });

    window.kanban = kanban;

    // Post-Render Card Processing
    const kanbanItems = Array.from(document.querySelectorAll('.kanban-item'));
    if (kanbanItems.length) {
        kanbanItems.forEach(function (el) {
            const taskId = el.getAttribute('data-eid') || 'TASK-000';
            const title = el.textContent;
            const badge = el.getAttribute('data-badge');
            const badgeText = el.getAttribute('data-badge-text');
            const startDate = el.getAttribute('data-start-date') || 'N/A';
            const dueDate = el.getAttribute('data-due-date') || 'N/A';
            const progress = el.getAttribute('data-progress') || 0;

            el.innerHTML = ''; // Rebuild card

            // 1. Card Header (Task ID & Dropdown)
            const headerHtml = `
                <div class="kanban-item-header">
                    <span class="kanban-item-id">${taskId}</span>
                    ${renderDropdown()}
                </div>
            `;

            // 2. Status Badge (Neutralized)
            const badgeHtml = badge ? `<div class="badge rounded-pill bg-label-secondary mb-2">${badgeText}</div>` : '';

            // 3. Title Text
            const titleHtml = `<span class="kanban-text">${title}</span>`;

            // 4. Dates
            const datesHtml = `
                <div class="kanban-item-dates">
                    <div class="date-box start-date">
                        <small><i class="ri-calendar-line"></i> Start</small>
                        <div class="date-value">${startDate}</div>
                    </div>
                    <div class="date-box due-date">
                        <small><i class="ri-flag-2-line"></i> End</small>
                        <div class="date-value">${dueDate}</div>
                    </div>
                </div>
            `;

            // 5. Progress (Neutralized)
            const progressColor = 'bg-secondary';
            const progressHtml = `
                <div class="kanban-progress-wrapper">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>${progress}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar ${progressColor}" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            `;

            el.insertAdjacentHTML('beforeend', headerHtml + badgeHtml + titleHtml + datesHtml + progressHtml);
        });
    }

    // Interaction Handlers
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

    // Scrollbar
    const kanbanContainer = document.querySelector('.kanban-container');
    if (kanbanContainer && typeof PerfectScrollbar !== 'undefined') {
        new PerfectScrollbar(kanbanContainer);
    }
})();