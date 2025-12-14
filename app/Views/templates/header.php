<?php
if (session('isLoggedIn')) {
    try {
        $notificationModel = new \App\Models\NotificationModel();
        $count = $notificationModel->getUnreadCount(session('user_id'));
    } catch (\Exception $e) {
        $count = 0;
        log_message('error', 'Error loading notifications: ' . $e->getMessage());
    }
    
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="<?= site_url('/') ?>">
      <i class="fas fa-graduation-cap"></i> WebSystem
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if (!session('isLoggedIn')): ?>
        <!-- For non-logged in users: Show Home, About, Contact, Login -->
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">
            <i class="fas fa-info-circle"></i>
            <span>About</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">
            <i class="fas fa-envelope"></i>
            <span>Contact</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'login' ? 'active' : '' ?>" href="<?= site_url('login') ?>">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
          </a>
        </li>
        <?php else: ?>
        <!-- For logged in users: Show Dashboard, My Courses, Announcements -->
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <?php if (session('role') === 'student'): ?>
        <!-- Student Navigation -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= strpos(uri_string(), 'course') !== false || strpos(uri_string(), 'assignment') !== false ? 'active' : '' ?>" href="#" id="coursesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-book"></i>
            <span>Courses</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="coursesDropdown">
            <li>
              <a class="dropdown-item" href="<?= site_url('courses') ?>">
                <i class="fas fa-search"></i> Available Courses
              </a>
            </li>
            <li><hr class="dropdown-divider" style="border-color: #404449;"></li>
            <li>
              <a class="dropdown-item" href="<?= site_url('my-courses') ?>">
                <i class="fas fa-graduation-cap"></i> My Enrolled Courses
              </a>
            </li>
          </ul>
        </li>
        <?php else: ?>
        <!-- Teacher/Admin Navigation -->
        <li class="nav-item">
          <?php if (session('role') === 'admin'): ?>
            <a class="nav-link <?= uri_string() == 'courses' || strpos(uri_string(), 'course') !== false ? 'active' : '' ?>" href="<?= site_url('courses') ?>">
              <i class="fas fa-cog"></i>
              <span>Manage Courses</span>
            </a>
          <?php else: ?>
            <a class="nav-link <?= uri_string() == 'courses' || strpos(uri_string(), 'course') !== false ? 'active' : '' ?>" href="<?= site_url('courses') ?>">
              <i class="fas fa-book"></i>
              <span>Courses</span>
            </a>
          <?php endif; ?>
        </li>
        <?php endif; ?>
        <!-- Announcements removed from navbar -->
        <?php if (session('role') === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'admin/users' ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">
            <i class="fas fa-users-cog"></i>
            <span>Users</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= strpos(uri_string(), 'notification') !== false ? 'active' : '' ?>" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
            <?php if (isset($count) && $count > 0): ?>
              <span class="badge bg-danger ms-1" id="notificationBadge"><?php echo $count; ?></span>
            <?php else: ?>
              <span class="badge bg-danger ms-1" id="notificationBadge" style="display: none;">0</span>
            <?php endif; ?>
          </a>
      <ul class="dropdown-menu dropdown-menu-end" id="notificationList" aria-labelledby="notificationDropdown">
        <li><a class="dropdown-item text-center"><small>Loading...</small></a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url('logout') ?>">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
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
      if (!data.notifications || data.notifications.length === 0) {
        $('#notificationList').append('<li><a class="dropdown-item" href="#">No notifications</a></li>');
        $('#notificationList').append('<li><hr class="dropdown-divider"/></li>');
        $('#notificationList').append('<li><a class="dropdown-item text-center" href="<?= site_url('notifications/all') ?>">View all notifications</a></li>');
        return;
      }

      data.notifications.forEach(function(notif) {
        var notifType = notif.type || 'general';
        var enrollmentId = notif.enrollment_id || null;
        var enrollmentStatus = notif.enrollment_status || null;

        // Format timestamp
        var ts = '';
        if (notif.created_at) {
          try {
            var d = new Date(notif.created_at);
            ts = '<small class="text-muted d-block">' + d.toLocaleString() + '</small>';
          } catch (e) {
            ts = '<small class="text-muted d-block">' + notif.created_at + '</small>';
          }
        }

        var item = '';
        if (notifType === 'enrollment' && enrollmentId && enrollmentStatus === 'pending' && '<?= session('role') ?>' === 'teacher') {
          // Compact enrollment item with actions
          item = '<li class="px-2"><div class="dropdown-item d-flex align-items-start" style="gap:10px;">'
               + '<div class="flex-grow-1">'
               + '<div class="fw-semibold">' + notif.message + '</div>'
               + ts
               + '</div>'
               + '<div class="d-flex flex-column align-items-end">'
               + '<button class="btn btn-sm btn-success accept-enrollment mb-1" data-id="' + notif.id + '" data-enrollment-id="' + enrollmentId + '"><i class="fas fa-check"></i></button>'
               + '<button class="btn btn-sm btn-danger reject-enrollment" data-id="' + notif.id + '" data-enrollment-id="' + enrollmentId + '"><i class="fas fa-times"></i></button>'
               + '</div></div></li>';
        } else {
          // Regular notification
          item = '<li class="px-2"><div class="dropdown-item d-flex align-items-start" style="gap:10px;">'
               + '<div class="flex-grow-1">'
               + '<div>' + notif.message + '</div>'
               + ts
               + '</div>'
               + '<div class="ms-2">'
               + '<button class="btn btn-sm btn-outline-primary mark-read" data-id="' + notif.id + '" title="Mark as read"><i class="fas fa-check"></i></button>'
               + '</div></div></li>';
        }

        $('#notificationList').append(item);
      });
      // Divider and view all link
      $('#notificationList').append('<li><hr class="dropdown-divider"/></li>');
      $('#notificationList').append('<li><a class="dropdown-item text-center" href="<?= site_url('notifications/all') ?>">View all notifications</a></li>');
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
  
  // Handle enrollment acceptance
  $(document).on('click', '.accept-enrollment', function() {
    var notificationId = $(this).data('id');
    var enrollmentId = $(this).data('enrollment-id');
    var button = $(this);
    var listItem = button.closest('li');
    
    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Accepting...');
    
    $.ajax({
      url: '<?= site_url('course/approve-enrollment') ?>/' + enrollmentId,
      method: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Mark notification as read
          $.post('<?= site_url('notifications/mark_read') ?>/' + notificationId, function() {
            listItem.remove();
            var currentCount = parseInt($('#notificationBadge').text()) || 0;
            if (currentCount > 0) {
              $('#notificationBadge').text(currentCount - 1).toggle(currentCount - 1 > 0);
            }
            // Show success message
            showNotificationToast('Enrollment approved successfully!', 'success');
          });
        } else {
          alert(response.message || 'Failed to approve enrollment');
          button.prop('disabled', false).html('<i class="fas fa-check"></i> Accept');
        }
      },
      error: function() {
        alert('Error approving enrollment');
        button.prop('disabled', false).html('<i class="fas fa-check"></i> Accept');
      }
    });
  });
  
  // Handle enrollment rejection
  $(document).on('click', '.reject-enrollment', function() {
    var notificationId = $(this).data('id');
    var enrollmentId = $(this).data('enrollment-id');
    var button = $(this);
    var listItem = button.closest('li');
    
    if (!confirm('Are you sure you want to reject this enrollment request?')) {
      return;
    }
    
    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
    
    $.ajax({
      url: '<?= site_url('course/reject-enrollment') ?>/' + enrollmentId,
      method: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Mark notification as read
          $.post('<?= site_url('notifications/mark_read') ?>/' + notificationId, function() {
            listItem.remove();
            var currentCount = parseInt($('#notificationBadge').text()) || 0;
            if (currentCount > 0) {
              $('#notificationBadge').text(currentCount - 1).toggle(currentCount - 1 > 0);
            }
            // Show info message
            showNotificationToast('Enrollment request rejected', 'info');
          });
        } else {
          alert(response.message || 'Failed to reject enrollment');
          button.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
        }
      },
      error: function() {
        alert('Error rejecting enrollment');
        button.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
      }
    });
  });
  
  // Helper function to show toast notifications
  function showNotificationToast(message, type) {
    var bgColor = type === 'success' ? '#28a745' : type === 'info' ? '#17a2b8' : '#dc3545';
    var toast = $('<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
    var toastContent = $('<div class="toast align-items-center text-white border-0" role="alert" style="background-color: ' + bgColor + ';">' +
      '<div class="d-flex">' +
        '<div class="toast-body">' + message + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
      '</div></div>');
    
    toast.append(toastContent);
    $('body').append(toast);
    
    var bsToast = new bootstrap.Toast(toastContent[0], { delay: 3000 });
    bsToast.show();
    
    toastContent.on('hidden.bs.toast', function() {
      toast.remove();
    });
  }
});
</script>
<?php endif; ?>
