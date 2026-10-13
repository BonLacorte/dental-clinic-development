<?php include '../app/views/partials/admin-header.php'; ?>
<?php
$limit = 10; // rows per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
//$page = isset($result['page']) && $result['page'] > 0 ? (int)$result['page'] : 1;
$offset = ($page - 1) * $limit;
?>
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tenant Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTenantModal">
            <i class="bi bi-plus-lg"></i> Add Tenant
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tenantsTable">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="ps-4">Name</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Email</th>
                        <th scope="col">Contact No</th>
                        <th scope="col">Address</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white py-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mb-0" id="pagination"></ul>
        </nav>
    </div>

</div>

<!-- Modals -->
<div class="modal fade" id="addTenantModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form" action="" name="addTenantForm" id="addTenantForm">
                <input type="text" hidden name="csrf_token" id="csrf_token" value="<?= e($csrf_token); ?>">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Tenant</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" name="name" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="contact">Contact No.</label>
                        <input class="form-control" type="text" name="contact" id="contact">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
    let currentPage = <?php echo (int)$page; ?>;
    let totalPages = 1;

    $(document).ready(function() {
        getTenants(currentPage);

        $(document).on('click', '.page-btn', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            getTenants(page);
        });

        $('#addTenantForm').on('submit', function(e) {
            e.preventDefault();
            addTenant(this);
        });

        $(document).on('click', '.updateTenantBtn', function() {
            let id = $(this).data('id');
        });
    });

    function getTenants(page) {
        showLoader("Please Wait...");

        $.ajax({
            url: "/getTenants",
            type: "GET",
            data: {
                page: page
            },
            dataType: "json",
            success: function(res) {

                if (res.error) {
                    showToast(res.error, 'error');
                    hideLoader();
                    return;
                }

                currentPage = res.currentPage;
                totalPages = res.totalPages;

                let rows = '';

                if (!res.data || res.data.length === 0) {
                    rows = `<tr><td colspan="5" class="text-center">No data found</td></tr>`;
                } else {
                    res.data.forEach((f, i) => {
                        rows += `
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png"
                                         class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-bold">${f.name}</div>
                                        <div class="fw-bold">( ${f.code} )</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted"><div class="fw-bold">${f.contact_person}</div></td>
                            <td class="text-muted"><div class="fw-bold">${f.email}</div></td>
                            <td><span class="badge bg-light text-dark border">${f.contact_no}</span></td>
                            <td class="text-muted"><div class="fw-bold">${f.address}</div></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    ${f.status}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-link btn-sm text-muted updateUserBtn" data-id="${f.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-link btn-sm text-danger deleteUserBtn" data-id="${f.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                }

                $('#tenantsTable tbody').html(rows);
                renderPagination(currentPage, totalPages);
                hideLoader();
                //console.log('curPage:' + currentPage + ' totalPages:' + totalPages);
            }
        });
    }

    function renderPagination(page, totalPages) {
        let adjacents = 2;
        let start = Math.max(1, page - adjacents);
        let end = Math.min(totalPages, page + adjacents);

        let html = '';

        // PREV
        if (page > 1) {
            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page - 1}">&laquo; Prev</a>
            </li>
        `;
        }

        // FIRST PAGE + DOTS
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

        // PAGE NUMBERS
        for (let p = start; p <= end; p++) {
            html += `
            <li class="page-item ${p === page ? 'active' : ''}">
                <a href="#" class="page-link page-btn" data-page="${p}">${p}</a>
            </li>
        `;
        }

        // LAST PAGE + DOTS
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

        // NEXT
        if (page < totalPages) {
            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a>
            </li>
        `;
        }

        $('#pagination').html(html);
    }

    function addTenant(form) {
        let $form = $(form);
        let formData = {
            name: $form.find('[name="name"]').val(),
            email: $form.find('[name="email"]').val(),
            contact: $form.find('[name="contact"]').val(),
            //role: $form.find('[name="role"]').val(),
            csrf_token: $form.find('[name="csrf_token"]').val()
        };

        $.ajax({
            url: '/addUser', // change to your route
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                showLoader("Saving user...");
            },
            success: function(res) {
                hideLoader();

                if (res.error) {
                    showToast(res.error, 'error');
                    return;
                }
                showToast(res.message || 'User added successfully', 'success');

                // reset form
                $('#addUserForm')[0].reset();

                // close modal
                let modalEl = document.querySelector('#addUserModal');
                let modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                // refresh table
                getAllTenantUsers(currentPage);
                console.log(res.data);
            },
            error: function(xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Something went wrong', 'error');
            }
        });
    }
</script>

</body>

</html>