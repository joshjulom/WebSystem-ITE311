<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-white mb-2">
                        <i class="fas fa-upload"></i> Upload Materials
                    </h1>
                    <p class="text-muted mb-0">Add learning materials for your course</p>
                </div>
                <a href="<?= site_url('/dashboard') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Upload Form -->
        <div class="col-lg-5 mb-4">
            <div class="card bg-dark border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-white mb-4">
                        <i class="fas fa-file-upload text-primary"></i> Upload New Material
                    </h5>

                    <form action="<?= site_url('admin/course/' . $course_id . '/upload') ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="uploadForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <label for="material" class="form-label text-white">
                                Select File <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" 
                                       class="form-control" 
                                       id="material" 
                                       name="material" 
                                       required
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.zip">
                                <div class="upload-placeholder text-center py-4">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-white mb-1">Click to browse or drag and drop</p>
                                    <small class="text-muted">
                                        Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP<br>
                                        Max size: 10MB
                                    </small>
                                </div>
                            </div>
                            <div id="fileName" class="mt-2 text-info small"></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload"></i> Upload Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Materials List -->
        <div class="col-lg-7">
            <div class="card bg-dark border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title text-white mb-0">
                            <i class="fas fa-folder-open text-warning"></i> Uploaded Materials
                        </h5>
                        <?php if (!empty($materials)): ?>
                            <span class="badge bg-primary"><?= count($materials) ?> file(s)</span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($materials)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-white">No Materials Yet</h5>
                            <p class="text-muted">Upload your first material to get started</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="py-3" style="width: 5%;">
                                            <i class="fas fa-file"></i>
                                        </th>
                                        <th class="py-3" style="width: 60%;">File Name</th>
                                        <th class="py-3" style="width: 20%;">Uploaded</th>
                                        <th class="py-3 text-center" style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materials as $material): ?>
                                        <tr>
                                            <td class="py-3">
                                                <?php
                                                $extension = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
                                                $iconClass = 'fa-file';
                                                $iconColor = 'text-muted';
                                                
                                                if ($extension === 'pdf') {
                                                    $iconClass = 'fa-file-pdf';
                                                    $iconColor = 'text-danger';
                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $iconClass = 'fa-file-word';
                                                    $iconColor = 'text-primary';
                                                } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                    $iconClass = 'fa-file-powerpoint';
                                                    $iconColor = 'text-warning';
                                                } elseif ($extension === 'zip') {
                                                    $iconClass = 'fa-file-archive';
                                                    $iconColor = 'text-info';
                                                }
                                                ?>
                                                <i class="fas <?= $iconClass ?> <?= $iconColor ?> fa-lg"></i>
                                            </td>
                                            <td class="py-3">
                                                <strong class="text-white"><?= esc($material['file_name']) ?></strong>
                                            </td>
                                            <td class="py-3">
                                                <small class="text-muted">
                                                    <?= isset($material['uploaded_at']) ? date('M d, Y', strtotime($material['uploaded_at'])) : 'N/A' ?>
                                                </small>
                                            </td>
                                            <td class="py-3 text-center">
                                                <button class="btn btn-sm btn-outline-danger delete-btn" 
                                                        data-material-id="<?= $material['id'] ?>"
                                                        data-file-name="<?= esc($material['file_name']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
.upload-area {
    position: relative;
    border: 2px dashed #404449;
    border-radius: 8px;
    background-color: #202225;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #5865F2;
    background-color: rgba(88, 101, 242, 0.05);
}

.upload-area.dragover {
    border-color: #5865F2;
    background-color: rgba(88, 101, 242, 0.1);
}

.upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.btn-outline-danger:hover {
    transform: scale(1.1);
}
</style>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // File input handling
    const fileInput = $('#material');
    const uploadArea = $('#uploadArea');
    const fileName = $('#fileName');
    const uploadForm = $('#uploadForm');

    // Show selected file name
    fileInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileName.html(`<i class="fas fa-check-circle"></i> Selected: <strong>${file.name}</strong> (${fileSize} MB)`);
            
            // Check file size
            if (file.size > 10 * 1024 * 1024) {
                fileName.html(`<i class="fas fa-exclamation-triangle text-danger"></i> File too large! Max size is 10MB`);
                fileInput.val('');
            }
        }
    });

    // Drag and drop
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            fileInput.trigger('change');
        }
    });

    // Form submission
    uploadForm.on('submit', function() {
        const uploadBtn = $('#uploadBtn');
        uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
    });

    // Delete material
    $('.delete-btn').on('click', function() {
        const materialId = $(this).data('material-id');
        const fileName = $(this).data('file-name');
        const row = $(this).closest('tr');

        if (confirm(`Are you sure you want to delete "${fileName}"?\n\nThis action cannot be undone.`)) {
            // Show loading state
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            // Perform deletion
            $.ajax({
                url: '<?= site_url('materials/delete/') ?>' + materialId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Fade out and remove row
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is now empty
                            if ($('tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                        
                        // Show success message
                        showAlert('Material deleted successfully!', 'success');
                    } else {
                        showAlert(response.message || 'Failed to delete material', 'danger');
                    }
                },
                error: function() {
                    showAlert('Error occurred while deleting material', 'danger');
                    row.find('.delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

    // Show alert function
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.container-fluid').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut(300, function() { $(this).remove(); });
        }, 5000);
    }
});
</script>

<?= $this->endSection() ?>
