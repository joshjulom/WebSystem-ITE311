<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Material for Course</h2>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <form action="<?= site_url('admin/course/' . $course_id . '/upload') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="material" class="form-label">Select File</label>
                <input type="file" class="form-control" id="material" name="material" required>
                <div class="form-text">Allowed types: pdf, doc, docx, ppt, pptx, zip. Max size: 10MB.</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="<?= site_url('/dashboard') ?>" class="btn btn-secondary">Back</a>
        </form>

        <h3 class="mt-5">Uploaded Materials</h3>
        <?php if (empty($materials)): ?>
            <p>No materials uploaded yet.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($materials as $material): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $material['file_name'] ?>
                        <div>
                            <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-success btn-sm me-2">Download</a>
                            <a href="<?= site_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
