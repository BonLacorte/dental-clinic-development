<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page organization-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="org-page-intro"><p class="org-eyebrow">Organization</p><h1 class="h5 mb-0" id="branchesHeading">Branches</h1><p class="org-page-description">Manage clinic locations and their branch details.</p></div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Branch
            </button>
        </div>

        <div class="card-body p-0">
            <div class="org-feedback d-none" id="orgPageFeedback" role="status" aria-live="polite" aria-atomic="true"></div>
            <p class="hris-scroll-hint" id="branchesTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="branchesHeading" aria-describedby="branchesTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="branchesTable">
                    <caption class="visually-hidden">Branch names, codes and addresses with existing record actions.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Branch Name / Code</th>
                            <th scope="col">Address</th>
                            <th scope="col" class="text-end pe-4" style="width: 13rem">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Branch pages">
                <ul class="pagination justify-content-end mb-0" id="branchesPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Branch -->
<div class="modal fade hris-dialog organization-dialog" id="addBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBranchModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="addBranchForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addBranchModalTitle">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                    <div class="mb-3">
                        <label for="addBranchForm_name" class="form-label">Branch Name</label>
                        <input id="addBranchForm_name" type="text" class="form-control" name="name" placeholder="e.g. Makati Clinic">
                    </div>

                    <div class="mb-3">
                        <label for="addBranchForm_code" class="form-label">Branch Code</label>
                        <input id="addBranchForm_code" type="text" class="form-control" name="code" placeholder="e.g. MKT">
                    </div>

                    <div class="mb-3">
                        <label for="addBranchForm_address" class="form-label">Address</label>
                        <textarea id="addBranchForm_address" class="form-control" name="address" rows="3" placeholder="Street, city and postal code"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Branch -->
<div class="modal fade hris-dialog organization-dialog" id="editBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editBranchModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="editBranchForm">
                <input type="hidden" name="id" id="editBranchId">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editBranchModalTitle">Edit Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                    <div class="mb-3">
                        <label for="editBranchForm_name" class="form-label">Branch Name</label>
                        <input id="editBranchForm_name" type="text" class="form-control" name="name" placeholder="e.g. Makati Clinic">
                    </div>

                    <div class="mb-3">
                        <label for="editBranchForm_code" class="form-label">Branch Code</label>
                        <input id="editBranchForm_code" type="text" class="form-control" name="code" placeholder="e.g. MKT">
                    </div>

                    <div class="mb-3">
                        <label for="editBranchForm_address" class="form-label">Address</label>
                        <textarea id="editBranchForm_address" class="form-control" name="address" rows="3" placeholder="Street, city and postal code"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete confirmation modal (replaces window.confirm) -->
<div class="modal fade hris-dialog organization-dialog" id="deleteBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteBranchModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteBranchModalTitle">Delete Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                Delete <strong id="deleteBranchName"></strong>? This action cannot be undone.
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteBranchBtn">Delete</button>
            </div>

        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
let currentPage = 1;
let pendingDeleteId = null;

/* =========================
   ESCAPE HELPER (XSS guard for html() interpolation)
========================= */
function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

$(document).ready(function () {
    initOrganizationUX();
    getAllBranches(currentPage);

    $('#addBranchForm').on('submit', function (e) {
        e.preventDefault();
        addBranch(this);
    });

    $('#editBranchForm').on('submit', function (e) {
        e.preventDefault();
        updateBranch(this);
    });

    $(document).on('click', '.editBranchBtn', function () {
        let id = $(this).data('id');
        getBranch(id);
    });

    $(document).on('click', '.deleteBranchBtn', function () {
        pendingDeleteId = $(this).data('id');
        $('#deleteBranchName').text($(this).attr('data-name'));
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('deleteBranchModal')
        ).show();
    });

    $('#confirmDeleteBranchBtn').on('click', function () {
        if (pendingDeleteId) {
            deleteBranch(pendingDeleteId);
        }
    });

    $(document).on('click', '.page-btn', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        getAllBranches(page);
    });
});

/* =========================
   GET ALL BRANCHES (server-side paginated)
========================= */
function getAllBranches(page) {
    $('#branchesTable').attr('aria-busy', 'true');
    $('#branchesTable tbody').html('<tr><td colspan="3" class="org-state"><div role="status">Loading branches…</div></td></tr>');
    $('#branchesPagination').empty();

    $.ajax({
        url: '/hris/branches/getallbranches',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page
        },

        success: function (res) {

            $('#branchesTable').removeAttr('aria-busy');

            if (res.error || res.success === false || !Array.isArray(res.data)) {
                branchReadError();
                return;
            }

            currentPage = res.currentPage;
            let totalPages = res.totalPages;

            $('#branchesTable tbody').html(renderBranchRows(res.data));
            renderPagination(currentPage, totalPages);
        },

        error: function (xhr) {
            $('#branchesTable').removeAttr('aria-busy');
            branchReadError();
        }

    });
}

/* =========================
   PAGINATION UI
========================= */
function renderBranchRows(branches) {
    let rows = '';

    if (branches.length === 0) {

        rows = `
            <tr>
                <td colspan="3" class="org-state">
                    No branches found. Add a branch to get started.
                </td>
            </tr>
        `;

    } else {

        branches.forEach((b) => {
            rows += `
                <tr>
                    <th scope="row" class="ps-4"><span class="org-record-name">${esc(b.name)}</span><span class="org-record-meta">${esc(b.code)}</span></th>
                    <td>${esc(b.address) || '-'}</td>
                    <td class="text-end pe-4"><div class="org-row-actions">

                        <button
                            class="btn btn-sm editBranchBtn hris-action"
                            type="button" data-id="${esc(b.id)}" aria-label="Edit branch ${esc(b.name)} (${esc(b.code)})" title="Edit branch ${esc(b.name)}">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit
                        </button>

                        <button
                            class="btn btn-sm deleteBranchBtn hris-action hris-action--danger"
                            type="button" data-id="${esc(b.id)}" data-name="${esc(b.name)} (${esc(b.code)})" aria-label="Delete branch ${esc(b.name)} (${esc(b.code)})" title="Delete branch ${esc(b.name)}">
                            <i class="bi bi-trash" aria-hidden="true"></i> Delete
                        </button>
                    </div></td>
                </tr>
            `;
        });
    }

    return rows;
}
function branchReadError() {
    $('#branchesTable').removeAttr('aria-busy');
    $('#branchesTable tbody').html('<tr><td colspan="3" class="org-state"><div role="status">Unable to load branches. <button class="btn btn-outline-secondary" type="button" id="retryBranches">Try Again</button></div></td></tr>');
}
$(document).on('click', '#retryBranches', function () { getAllBranches(currentPage); });

function renderPagination(page, totalPages) {

    let adjacents = 2;
    let start = Math.max(1, page - adjacents);
    let end = Math.min(totalPages, page + adjacents);

    let html = '';

    if (page > 1) {
        html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page - 1}">&laquo; Prev</a>
            </li>
        `;
    }

    if (start > 1) {
        html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="1">1</a>
            </li>
        `;

        if (start > 2) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }
    }

    for (let p = start; p <= end; p++) {
        html += `
            <li class="page-item ${p === page ? 'active' : ''}">
                <a href="#" class="page-link page-btn" data-page="${p}"${p === page ? ' aria-current="page"' : ''}>${p}</a>
            </li>
        `;
    }

    if (end < totalPages) {
        if (end < totalPages - 1) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }

        html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${totalPages}">
                    ${totalPages}
                </a>
            </li>
        `;
    }

    if (page < totalPages) {
        html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a>
            </li>
        `;
    }

    $('#branchesPagination').html(html);
}

/* =========================
   ADD BRANCH
========================= */
function addBranch(form) {

    let $form = $(form);
    if (!orgBegin($form)) return;

    let formData = {
        name: $form.find('[name="name"]').val(),
        code: $form.find('[name="code"]').val(),
        address: $form.find('[name="address"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({

        url: '/hris/branches/create',
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            orgFeedback("Saving branch...", 'info', $form);
        },

        success: function (res) {

            if (res.error || res.success === false || res.type === 'error') {
                orgFeedback(res.error || res.message || 'Unable to save this branch.', 'error', $form);
                return;
            }

            orgEnd($form);
            orgFeedback(res.message, 'success');
            $('#addBranchForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addBranchModal')).hide();
            getAllBranches(1);

        },

        error: function (xhr) {
            orgFeedback('Unable to save this branch. Your entries have been kept; try again.', 'error', $form);

        }

    });

}

/* =========================
   GET BRANCH BY ID
========================= */
function getBranch(id) {

    showLoader("Loading branch...");

    $.ajax({
        url: '/hris/branches/getbranchbyid/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let $form = $('#editBranchForm');

            $form.find('[name="id"]').val(res.data.id);
            $form.find('[name="name"]').val(res.data.name);
            $form.find('[name="code"]').val(res.data.code);
            $form.find('[name="address"]').val(res.data.address);

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editBranchModal')).show();

        },

        error: function (xhr) {
            hideLoader();
            showToast('Unable to load branch.', 'error');

        }

    });
}

/* =========================
   UPDATE BRANCH
========================= */
function updateBranch(form) {

    let $form = $(form);
    if (!orgBegin($form)) return;
    let id = $form.find('[name="id"]').val();

    let formData = {
        name: $form.find('[name="name"]').val(),
        code: $form.find('[name="code"]').val(),
        address: $form.find('[name="address"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({

        url: '/hris/branches/updatebranch/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            orgFeedback("Updating branch...", 'info', $form);
        },

        success: function (res) {

            if (res.error || res.success === false || res.type === 'error') {
                orgFeedback(res.error || res.message || 'Unable to save this branch.', 'error', $form);
                return;
            }

            orgEnd($form);
            orgFeedback(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editBranchModal')).hide();
            getAllBranches(currentPage);
        },

        error: function (xhr) {
            orgFeedback('Unable to save this branch. Your entries have been kept; try again.', 'error', $form);
        }

    });

}

/* =========================
   DELETE BRANCH
========================= */
function deleteBranch(id) {

    const $scope = $('#confirmDeleteBranchBtn');
    if (!orgBegin($scope)) return;
    let csrfToken = $('#addBranchForm [name="csrf_token"]').val();

    $.ajax({
        url: '/hris/branches/deletebranch/' + id,
        type: 'POST',
        dataType: 'json',
        complete: function () { orgEnd($scope); },
        data: {
            csrf_token: csrfToken
        },

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteBranchModal')).hide();
            orgFeedback("Deleting branch...", 'info', null);
        },

        success: function (res) {
            pendingDeleteId = null;

            if (res.error || res.success === false || res.type === 'error') {
                orgFeedback(res.error || res.message || 'Unable to delete this branch.', 'error');
                return;
            }
            orgFeedback(res.message, 'success');
            getAllBranches(currentPage);

        },

        error: function (xhr) {
            pendingDeleteId = null;
            orgFeedback('Unable to delete this branch. Try again.', 'error');

        }

    });

}
// Organization-only feedback and pending state. Messages are always rendered as text.
function orgFeedback(message, type = 'info', form = null) {
    const $target = form ? $(form).find('.org-form-feedback').first() : $('#orgPageFeedback');
    $target.removeClass('d-none org-feedback--error org-feedback--success')
        .toggleClass('org-feedback--error', type === 'error')
        .toggleClass('org-feedback--success', type === 'success')
        .text(message && typeof message === 'object' ? Object.values(message).join(' ') : (message || ''));
    if (form && type === 'error') $target.attr('tabindex', '-1').trigger('focus');
}
function orgBegin($scope) {
    if ($scope.data('orgPending')) return false;
    $scope.data('orgPending', true).attr('aria-busy', 'true');
    $scope.find(':submit').add($scope.filter('button')).prop('disabled', true);
    return true;
}
function orgEnd($scope) {
    $scope.data('orgPending', false).removeAttr('aria-busy');
    $scope.find(':submit').add($scope.filter('button')).prop('disabled', false);
}
function initOrganizationUX() {
    let returnFocus = null;
    $(document).on('click', '.organization-page button, .organization-page a', function () {
        returnFocus = this;
    });
    $('.organization-dialog').on('show.bs.modal', function () {
        $(this).find('.org-form-feedback').addClass('d-none').empty();
    }).on('shown.bs.modal', function () {
        const target = $(this).find('input:not([type="hidden"]):not([readonly]), select, textarea').filter(':visible:enabled').first()[0]
            || $(this).find('.btn-close')[0];
        // Destructive dialogs initially focus Cancel, never the destructive action.
        const cancel = $(this).find('.modal-footer [data-bs-dismiss="modal"]')[0];
        (this.id.startsWith('delete') ? cancel : target)?.focus();
    }).on('hide.bs.modal', function (event) {
        const $form = $(this).find('form');
        if ($form.data('orgPending')) {
            event.preventDefault();
            orgFeedback('Please wait while your changes are being saved.', 'info', $form);
        }
    }).on('hidden.bs.modal', function () {
        if (returnFocus?.isConnected) returnFocus.focus();
        else $('.organization-page h1').attr('tabindex', '-1').trigger('focus');
    });
}

</script>
</body>
</html>