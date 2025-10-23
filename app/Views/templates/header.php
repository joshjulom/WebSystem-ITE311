<?php
if (session('isLoggedIn')) {
    $notificationModel = new \App\Models\NotificationModel();
    $count = $notificationModel->getUnreadCount(session('user_id'));
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= site_url('/') ?>">WebSystem</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
        </li>
        <?php if (!session('isLoggedIn')): ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'login' ? 'active' : '' ?>" href="<?= site_url('login') ?>">Login</a>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Notifications
            <span class="badge bg-danger" id="notificationBadge" style="display: <?php echo isset($count) && $count > 0 ? 'inline' : 'none'; ?>;"><?php echo $count ?? 0; ?></span>
          </a>
          <ul class="dropdown-menu" id="notificationList" aria-labelledby="notificationDropdown">
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url('logout') ?>">Logout</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<?php if (session('isLoggedIn')): ?>
<script>
$(document).ready(function() {
  function loadNotifications() {
    $.get('<?= site_url('notifications') ?>', function(data) {
      $('#notificationBadge').text(data.count).toggle(data.count > 0);
      $('#notificationList').empty();
      if (data.notifications.length === 0) {
        $('#notificationList').append('<li><a class="dropdown-item" href="#">No notifications</a></li>');
      } else {
        data.notifications.forEach(function(notif) {
          var item = '<li><div class="dropdown-item d-flex flex-column" style="padding: 8px 16px; font-size: 0.9em;">' +
                     '<span>' + notif.message + '</span>' +
                     '<button class="btn btn-sm btn-primary mark-read" data-id="' + notif.id + '">Mark as Read</button>' +
                     '</div></li>';
          $('#notificationList').append(item);
        });
      }
    }).fail(function() {
      console.error('Failed to load notifications');
    });
  }

  loadNotifications();

 
  setInterval(loadNotifications, 5000);

  $(document).on('click', '.mark-read', function() {
    var id = $(this).data('id');
    var button = $(this);
    $.post('<?= site_url('notifications/mark_read') ?>/' + id, function(data) {
      if (data.success) {
        button.closest('li').remove();
        var currentCount = parseInt($('#notificationBadge').text()) || 0;
        if (currentCount > 0) {
          $('#notificationBadge').text(currentCount - 1).toggle(currentCount - 1 > 0);
        }
      } else {
        alert('Failed to mark as read');
      }
    }).fail(function() {
      alert('Failed to mark as read');
    });
  });
});
</script>
<?php endif; ?>
