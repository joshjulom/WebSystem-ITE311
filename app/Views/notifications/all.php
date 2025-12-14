<?php /** @var array $notifications */ ?>
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">All Notifications</h3>
        <button id="notifBackBtn" class="btn btn-secondary btn-sm">&larr; Back</button>
      </div>
      <?php if (empty($notifications)): ?>
        <div class="alert alert-light">No notifications found.</div>
      <?php else: ?>
        <ul class="list-group">
          <?php foreach ($notifications as $notif): ?>
            <li id="notification-<?= $notif['id'] ?>" class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-semibold mb-1" style="font-size:0.95rem"><?= esc($notif['message']) ?></div>
                <small class="text-muted"><?= esc($notif['created_at'] ?? '') ?></small>
              </div>
              <div class="ms-3 text-end">
                <?php if (empty($notif['is_read']) || $notif['is_read'] == 0): ?>
                  <button class="btn btn-sm btn-outline-primary mark-read-all" data-id="<?= $notif['id'] ?>">Mark as read</button>
                <?php else: ?>
                  <span class="badge bg-success">Read</span>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  $(document).on('click', '.mark-read-all', function(e) {
    e.preventDefault();
    var btn = $(this);
    var id = btn.data('id');
    btn.prop('disabled', true).text('Marking...');
    $.post('<?= site_url('notifications/mark_read') ?>/' + id, function(res) {
      if (res.success) {
        var li = $('#notification-' + id);
        li.find('.mark-read-all').remove();
        li.find('.ms-3').append('<span class="badge bg-success">Read</span>');
        // update badge count in header
        var current = parseInt($('#notificationBadge').text()) || 0;
        if (current > 0) {
          $('#notificationBadge').text(current - 1).toggle(current - 1 > 0);
        }
      } else {
        alert(res.error || 'Failed to mark as read');
        btn.prop('disabled', false).text('Mark as read');
      }
    }).fail(function() {
      alert('Failed to mark as read');
      btn.prop('disabled', false).text('Mark as read');
    });
  });

  // Back button: go back if there is a referer, otherwise go to dashboard
  $('#notifBackBtn').on('click', function(e) {
    e.preventDefault();
    try {
      if (document.referrer && document.referrer.indexOf(location.hostname) !== -1) {
        history.back();
        // fallback to dashboard after short delay in case history.back() doesn't navigate
        setTimeout(function() { window.location.href = '<?= site_url('dashboard') ?>'; }, 300);
      } else {
        window.location.href = '<?= site_url('dashboard') ?>';
      }
    } catch (err) {
      window.location.href = '<?= site_url('dashboard') ?>';
    }
  });
});
</script>

<?= $this->endSection() ?>
