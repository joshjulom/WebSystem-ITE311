<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Manage Users</h2>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Users List</h5>
                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= esc($user['name']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <?php if ($user['id'] == 1 || $user['email'] == 'admin@example.com'): ?>
                                                <span class="badge bg-danger">Admin (Protected)</span>
                                            <?php else: ?>
                                                <select class="form-select form-select-sm role-select" data-user-id="<?= $user['id'] ?>">
                                                    <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                                                    <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user['id'] != 1 && $user['email'] != 'admin@example.com'): ?>
                                                <button class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="<?= $user['id'] ?>" data-name="<?= esc($user['name']) ?>" data-email="<?= esc($user['email']) ?>" data-role="<?= $user['role'] ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-<?= $user['status'] == 'active' ? 'danger' : 'success' ?> btn-sm toggle-status-btn" data-user-id="<?= $user['id'] ?>" data-name="<?= esc($user['name']) ?>" data-current-status="<?= $user['status'] ?>">
                                                    <i class="fas fa-<?= $user['status'] == 'active' ? 'ban' : 'check' ?>"></i>
                                                    <?= $user['status'] == 'active' ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-info btn-sm change-password-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal" data-user-id="<?= $user['id'] ?>" data-name="<?= esc($user['name']) ?>">
                                                <i class="fas fa-key"></i> Change Password
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required pattern="[A-Za-z\s\-']+" title="Name can only contain letters, spaces, hyphens, and apostrophes">
                        <div class="form-text">Name can only contain letters, spaces, hyphens, and apostrophes.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email/Username</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="defaultPassword" class="form-label">Default Password</label>
                        <input type="text" class="form-control" id="defaultPassword" value="password123" readonly>
                        <div class="form-text">New users will be assigned this default password.</div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email/Username</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm">
                <input type="hidden" id="changePasswordUserId" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="password" required>
                        <div class="form-text">Must be at least 8 characters with uppercase, lowercase, and number.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    // Role change
    $('.role-select').change(function() {
        const userId = $(this).data('user-id');
        const newRole = $(this).val();

        $.post('<?= base_url("/admin/updateRole") ?>', {
            user_id: userId,
            role: newRole
        }, function(response) {
            if (response.success) {
                // Show success message
                showAlert('success', response.message);
            } else {
                // Show error and revert
                showAlert('danger', response.message);
                location.reload();
            }
        }, 'json').fail(function() {
            showAlert('danger', 'Failed to update role');
            location.reload();
        });
    });

    // Add user
    $('#addUserForm').submit(function(e) {
        const nameField = $('#name');
        const nameValue = nameField.val();
        const namePattern = /^[A-Za-z\s\-']+$/;

        // Check for special characters in name
        if (!namePattern.test(nameValue)) {
            e.preventDefault();
            showAlert('danger', 'Name can only contain letters, spaces, hyphens, and apostrophes. Special characters are not allowed.');
            nameField.focus();
            return false;
        }

        e.preventDefault();
        const formData = $(this).serialize();

        $.post('<?= base_url("/admin/addUser") ?>', formData, function(response) {
            if (response.success) {
                $('#addUserModal').modal('hide');
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', response.message);
            }
        }, 'json').fail(function() {
            showAlert('danger', 'Failed to add user');
        });
    });

    // Edit user button
    $('.edit-btn').click(function() {
        const userId = $(this).data('user-id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const role = $(this).data('role');

        $('#editUserId').val(userId);
        $('#editName').val(name);
        $('#editEmail').val(email);
        $('#editRole').val(role);
    });

    // Edit user form
    $('#editUserForm').submit(function(e) {
        e.preventDefault();
        const userId = $('#editUserId').val();
        const formData = $(this).serialize();

        $.post('<?= base_url("/admin/updateUser/") ?>' + userId, formData, function(response) {
            if (response.success) {
                $('#editUserModal').modal('hide');
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', response.message);
            }
        }, 'json').fail(function() {
            showAlert('danger', 'Failed to update user');
        });
    });

    // Change password button
    $('.change-password-btn').click(function() {
        const userId = $(this).data('user-id');
        const name = $(this).data('name');

        $('#changePasswordUserId').val(userId);
        $('#changePasswordModal .modal-title').text('Change Password for ' + name);
    });

    // Change password form
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        const userId = $('#changePasswordUserId').val();
        const formData = $(this).serialize();

        $.post('<?= base_url("/admin/changePassword/") ?>' + userId, formData, function(response) {
            if (response.success) {
                $('#changePasswordModal').modal('hide');
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        }, 'json').fail(function() {
            showAlert('danger', 'Failed to change password');
        });
    });

    // Toggle status button
    $('.toggle-status-btn').click(function() {
        const userId = $(this).data('user-id');
        const name = $(this).data('name');
        const currentStatus = $(this).data('current-status');
        const action = currentStatus === 'active' ? 'deactivate' : 'activate';

        if (confirm(`Are you sure you want to ${action} user ${name}?`)) {
            $.post('<?= base_url("/admin/toggleStatus/") ?>' + userId, {}, function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', response.message);
                }
            }, 'json').fail(function() {
                showAlert('danger', `Failed to ${action} user`);
            });
        }
    });

    function showAlert(type, message) {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        $('.container').prepend(alertHtml);
    }
});
</script>

<style>
.table-dark {
    background-color: #2c2f33;
}

.table-dark th,
.table-dark td {
    border-color: #40444b;
}

.badge {
    font-size: 0.75em;
}
</style>

<?= $this->endSection() ?>
