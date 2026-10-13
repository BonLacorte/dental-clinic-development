<?php include '../app/views/partials/admin-header.php'; ?>
<?php
$limit = 10; // rows per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
//$page = isset($result['page']) && $result['page'] > 0 ? (int)$result['page'] : 1;
$offset = ($page - 1) * $limit;
?>
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg"></i> Add User
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="ps-4">User</th>
                        <th scope="col">Contact No.</th>
                        <th scope="col">Status</th>
                        <th scope="col">Last Active</th>
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
<!-- Add User -->
<div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form" action="" name="addUserForm" id="addUserForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" hidden name="csrf_token" id="csrf_token" value="<?= e($csrf_token); ?>">
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
                    <div class="mb-3">
                        <label for="role">Role</label>
                        <select class="form-select" name="role" id="role">
                            <option value="">-- Select Role --</option>
                            <?php foreach ($data['roles'] as $role): ?>
                                <option value="<?= e($role["id"]); ?>"><?= e($role["name"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="branch_all" value="" checked>
                            <label class="form-check-label" for="branch_all">
                                All
                            </label>
                        </div>
                        <?php foreach ($data['branches'] as $branch): ?>
                            <div class="form-check">
                                <input class="form-check-input branch-checkbox" type="checkbox" name="branch[]"
                                    id="branch<?= e($branch['id']); ?>" value="<?= e($branch['id']); ?>">
                                <label class="form-check-label" for="branch<?= e($branch['id']); ?>">
                                    <?= e($branch['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
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
<!-- Add User End -->
<!-- Edit User -->
<div class="modal fade" id="editUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form" action="" name="editUserForm" id="editUserForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" hidden name="csrf_token" id="csrf_token" value="<?= e($csrf_token); ?>">
                    <input type="number" hidden name="user_id" id="user_id">
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" name="editname" id="editname">
                    </div>
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="editemail" id="editemail">
                    </div>
                    <div class="mb-3">
                        <label for="contact">Contact No.</label>
                        <input class="form-control" type="text" name="editcontact" id="editcontact">
                    </div>
                    <div class="mb-3 d-flex justify-content-center align-items-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary editUserBranchRoleBtn"
                    data-csrf_token="<?= e($csrf_token); ?>" data-bs-toggle="modal"
                    data-bs-target="#editUserBranchRoleModal">Edit Roles</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit User End -->
<!-- Edit User Branches Role -->
<div class="modal fade" id="editUserBranchRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form" action="" name="editUserRoleForm" id="editUserRoleForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Branch Roles</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" hidden name="csrf_token" id="csrf_token" value="<?= e($csrf_token); ?>">
                    <input type="number" hidden name="user_id" id="user_id">
                    <!-- <div class="mb-3">
                        <?php foreach ($data['roles'] as $role): ?>
                            <div class="form-check">
                                <input class="form-check-input branch-checkbox" type="checkbox" name="roles[]"
                                    id="role<?= e($role['id']); ?>" value="<?= e($role['id']); ?>">
                                <label class="form-check-label" for="branch<?= e($role['id']); ?>">
                                    <?= e($role['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Assignment</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role_assignment" id="role_all"
                                value="all" checked>
                            <label class="form-check-label" for="role_all">
                                Same role for all branches <p id="globalRoleTag"></p>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role_assignment" id="role_unique"
                                value="unique">
                            <label class="form-check-label" for="role_unique">
                                Unique role for each branch
                            </label>
                        </div>
                        <div class="user-branch-table" id="user-branch-table">
                            <table class="table table-hover align-middle mb-0" id="userBranchTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Branch</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    <?php foreach ($data['branches'] as $branch): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <input type="text" name="editUserRoleFormBranchId<?php echo $i; ?>"
                                                    id="editUserRoleFormBranchId<?php echo $i; ?>" hidden
                                                    value="<?= e($branch['id']) ?>">
                                                <?= e($branch['name']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success"
                                                    id="branchSpanStatus<?php echo $i; ?>"
                                                    name="branchSpanStatus<?php echo $i; ?>">
                                                </span>
                                            </td>
                                            <!-- <td class="text-muted small">2 mins ago</td> -->
                                            <td class="text-end pe-4">
                                                <button class="btn btn-link btn-sm text-muted editBranchRole"
                                                    data-csrf_token="<?= e($csrf_token); ?>" 
                                                    data-branchId="<?= e($branch['id']) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editBranchRole">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-csrf_token="<?= e($csrf_token); ?>"
                    data-bs-toggle="modal" data-bs-target="#editUserModal">Edit Info</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit User Branches Role End -->
<!-- Edit Branch Role -->
<div class="modal fade" id="editUserRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form" action="" name="editRoleForm" id="editRoleForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-csrf_token="<?= e($csrf_token); ?>"
                    data-bs-toggle="modal" data-bs-target="#editUserModal">Close</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Branch Role End --> 
<!-- Modal End -->

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
    let currentPage = <?php echo (int) $page; ?>;
    let totalPages = 1;

    $(document).ready(function () {
        getAllTenantUsers(currentPage);

        var roleAssignmentRadioValue = $('input[name="role_assignment"]:checked').val();
        const userBranchTable = document.getElementById('user-branch-table');

        if (roleAssignmentRadioValue == 'all') {
            userBranchTable.style.display = 'none';
        } else {
            userBranchTable.style.display = 'block';
        }

        $('input[name="role_assignment"]').on('change', function () {
            var val = $(this).val();
            if (val === 'all') {
                userBranchTable.style.display = 'none';
            } else {
                userBranchTable.style.display = 'block';
            }
            //console.log('roleValue:'+val);
        });

        $(document).on('click', '.page-btn', function (e) {
            e.preventDefault();
            let page = $(this).data('page');
            getAllTenantUsers(page);
        });

        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            addUser(this);
        });

        $(document).on('click', '.editUserBtn', function () {
            let id = $(this).data('id');
            let csrf_token = $(this).data('csrf_token');
            editUser(id, csrf_token);
        });

        $(document).on('click', '.editUserBranchRoleBtn', function () {
            let userId = $('#user_id').val();
            let branchCount = <?php echo $i; ?>;

            for (var i = 0; i < branchCount; i++) {
                let editUserRoleFormBranchId = $(`#editUserRoleFormBranchId${i}`).val();
                getUserBranchById(userId, editUserRoleFormBranchId, i);
                console.log("spanId: " + editUserRoleFormBranchId);
            }

            // if ($('input[name="role_assignment"][value="unique"]').is(':checked')) {
            //     // "Same role for all branches" is selected
            //     for (var i = 0; i < branchCount; i++) {
            //         //console.log(i + " ");
            //         getUserBranchById(userId, '<?= e($branch['id']); ?>', i);
            //         console.log("spanId: "+i);
            //     }
            // }
        });

    });

    function getAllTenantUsers(page) {
        showLoader("Please Wait...");

        $.ajax({
            url: "/getAllTenantUsers",
            type: "GET",
            data: {
                page: page
            },
            dataType: "json",
            success: function (res) {

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
                                        <div class="small text-muted">${f.email}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">${f.contact_no}</span></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    ${f.status}
                                </span>
                            </td>
                            <td class="text-muted small">2 mins ago</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-link btn-sm text-muted editUserBtn" data-id="${f.id}" data-csrf_token="<?= e($csrf_token); ?>"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal">
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

                $('#usersTable tbody').html(rows);
                renderPagination(currentPage, totalPages);
                //hideLoader();
                //console.log('curPage:' + currentPage + ' totalPages:' + totalPages);
            }
        })
            .always(function () {
                hideLoader();
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

    function addUser(form) {
        let $form = $(form);
        let branches = [];
        // Only collect branch IDs if "All" is NOT checked
        if (!$('#branch_all').is(':checked')) {
            $form.find('input[name="branch[]"]:checked').each(function () {
                branches.push($(this).val());
            });
        }
        let formData = {
            name: $form.find('[name="name"]').val(),
            email: $form.find('[name="email"]').val(),
            contact: $form.find('[name="contact"]').val(),
            role: $form.find('[name="role"]').val(),
            branch: branches,
            csrf_token: $form.find('[name="csrf_token"]').val()
        };
        $.ajax({
            url: '/addUser', // change to your route
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                showLoader("Saving user...");
            },
            success: function (res) {
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
            },
            error: function (xhr) {
                showToast('Something went wrong', 'error');
            }
        })
            .always(function () {
                hideLoader();
            });
    }

    function editUser(id, csrf_token) {
        showLoader("Please Wait...");
        $.ajax({
            url: "/editUser",
            type: "GET",
            data: {
                id: id,
                csrf_token: csrf_token
            },
            dataType: "json",
            success: function (res) {

                if (res.error) {
                    showToast(res.error, 'error');
                    //console.log(res.error);
                    return;
                }

                if (!res.data || res.data.length === 0) {
                    showToast('No User Found.', 'message');
                    return;
                } else {
                    //showToast(res.message, 'success');
                    $('[name="user_id"]').val(res.data.id || 0);
                    $('#editname').val(res.data.name || '');
                    $('#editemail').val(res.data.email || '');
                    $('#editcontact').val(res.data.contact_no || '');
                    if (res.roleMode == 'Global') {
                        $('input[name="role_assignment"][value="all"]').prop('checked', true);
                        $('#globalRoleTag').text("(" + res.userRoles[0].name + ")"); // global = 1 role entry with no branch value
                    } else {
                        $('input[name="role_assignment"][value="unique"]').prop('checked', true);
                    }
                }
                console.log(res.data);
                console.log(res.userRoles);
                console.log(res.userBranches);
            }
        })
            .always(function () {
                hideLoader();
            });
    }

    // function editUserBranch() {
    //     showLoader("Please Wait...");
    //     $.ajax({
    //         url: "/editUserBranch",
    //         type: "GET",
    //         data: {
    //             id: id,
    //             csrf_token: csrf_token
    //         },
    //         dataType: "json",
    //         success: function (res) {

    //             if (res.error) {
    //                 showToast(res.error, 'error');
    //                 //console.log(res.error);
    //                 return;
    //             }

    //             if (!res.data || res.data.length === 0) {
    //                 showToast('No User Found.', 'message');
    //                 return;
    //             } else {
    //                 //showToast(res.message, 'success');
    //                 $('[name="user_id"]').val(res.data.id || 0);
    //                 $('#editname').val(res.data.name || '');
    //                 $('#editemail').val(res.data.email || '');
    //                 $('#editcontact').val(res.data.contact_no || '');
    //             }
    //         }
    //     })
    //     .always(function () {
    //         hideLoader();
    //     });
    // }

    function getUserBranchById(userId, branchId, spanId) {
        showLoader("Please Wait...");
        $.ajax({
            url: "/getUserBranchById",
            type: "GET",
            data: {
                userId: userId,
                branchId: branchId
            },
            dataType: "json",
            success: function (res) {

                if (res.error) {
                    showToast(res.error, 'error');
                    console.log(res.error);
                    return;
                }

                if (!res.data || res.data.length === 0) {
                    $('#branchSpanStatus' + spanId).text("No Access");
                    return;
                } else {
                    $('#branchSpanStatus' + spanId).text("With Access");
                }
            }
        })
            .always(function () {
                hideLoader();
            });
    }

    function countUserBranches() {

    }

</script>

</body>

</html>