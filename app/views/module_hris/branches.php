<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page organization-page hris-consistency-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="org-page-intro">
                <p class="org-eyebrow d-print-none">HRIS · Organization</p>
                <h1 class="h5 mb-0" id="branchesHeading">Branches</h1>
                <p class="org-page-description d-print-none">Manage clinic locations and their branch details.</p>
            </div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                <i class="bi bi-plus-lg"></i> Add Branch
            </button>
        </div>

        <div class="card-body p-0">
            <p class="hris-scroll-hint" id="branchesTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="branchesHeading" aria-describedby="branchesTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="branchesTable">
                    <caption class="visually-hidden">Branch names, codes and addresses with existing record actions.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Name</th>
                            <th scope="col">Code</th>
                            <th scope="col">Address</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
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
<div class="modal fade hris-dialog" id="addBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBranchModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addBranchForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addBranchModalTitle">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addBranchForm_name" class="form-label">Branch Name</label>
                        <input id="addBranchForm_name" type="text" class="form-control" name="name">
                    </div>

                    <div class="mb-3">
                        <label for="addBranchForm_code" class="form-label">Branch Code</label>
                        <input id="addBranchForm_code" type="text" class="form-control" name="code">
                    </div>

                    <div class="mb-3">
                        <label for="addBranchForm_address" class="form-label">Address</label>
                        <textarea id="addBranchForm_address" class="form-control" name="address" rows="3"></textarea>
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
<div class="modal fade hris-dialog" id="editBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editBranchModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editBranchForm">
                <input type="hidden" name="id" id="editBranchId">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editBranchModalTitle">Edit Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editBranchForm_name" class="form-label">Branch Name</label>
                        <input id="editBranchForm_name" type="text" class="form-control" name="name">
                    </div>

                    <div class="mb-3">
                        <label for="editBranchForm_code" class="form-label">Branch Code</label>
                        <input id="editBranchForm_code" type="text" class="form-control" name="code">
                    </div>

                    <div class="mb-3">
                        <label for="editBranchForm_address" class="form-label">Address</label>
                        <textarea id="editBranchForm_address" class="form-control" name="address" rows="3"></textarea>
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
<div class="modal fade hris-dialog" id="deleteBranchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteBranchModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteBranchModalTitle">Delete Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete this branch? This action cannot be undone.
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
    showLoader("Loading branches...");

    $.ajax({
        url: '/hris/branches/getallbranches',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page
        },

        success: function (res) {

            hideLoader();

            if (res.error) {
                showToast(res.error, 'error');
                return;
            }

            currentPage = res.currentPage;
            let totalPages = res.totalPages;

            let rows = '';
            let branches = res.data || [];

            if (branches.length === 0) {

                rows = `
                    <tr>
                        <td colspan="4" class="text-center">
                            No branches found
                        </td>
                    </tr>
                `;

            } else {

                branches.forEach((b) => {
                    rows += `
                        <tr>
                            <td class="ps-4">${esc(b.name)}</td>
                            <td>${esc(b.code)}</td>
                            <td>${esc(b.address) || '-'}</td>
                            <td class="text-end pe-4">

                                <button
                                    class="btn btn-sm editBranchBtn hris-action"
                                    data-id="${b.id}" aria-label="Edit branch" title="Edit branch">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                </button>

                                <button
                                    class="btn btn-sm deleteBranchBtn hris-action hris-action--danger"
                                    data-id="${b.id}" aria-label="Delete branch" title="Delete branch">
                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#branchesTable tbody').html(rows);
            renderPagination(currentPage, totalPages);
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load branches.', 'error');
        }

    });
}

/* =========================
   PAGINATION UI
========================= */
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

        beforeSend: function () {
            showLoader("Saving branch...");
        },

        success: function (res) {
            hideLoader();

            if (res.error) {
                showToast(res.error, 'error');
                return;
            }

            showToast(res.message, 'success');
            $('#addBranchForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addBranchModal')).hide();
            getAllBranches(1);

        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');

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
            console.log(xhr.responseText);
            showToast('Unable to load branch.', 'error');

        }

    });
}

/* =========================
   UPDATE BRANCH
========================= */
function updateBranch(form) {

    let $form = $(form);
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

        beforeSend: function () {
            showLoader("Updating branch...");
        },

        success: function (res) {

            hideLoader();
            if (res.error) {
                showToast(res.error, 'error');
                return;
            }

            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editBranchModal')).hide();
            getAllBranches(currentPage);
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }

    });

}

/* =========================
   DELETE BRANCH
========================= */
function deleteBranch(id) {

    let csrfToken = $('#addBranchForm [name="csrf_token"]').val();

    $.ajax({
        url: '/hris/branches/deletebranch/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: csrfToken
        },

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteBranchModal')).hide();
            showLoader("Deleting branch...");
        },

        success: function (res) {
            hideLoader();
            pendingDeleteId = null;

            if (res.error) {
                showToast(res.error, 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllBranches(currentPage);

        },

        error: function (xhr) {
            hideLoader();
            pendingDeleteId = null;
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');

        }

    });

}
</script>
</body>
</html>