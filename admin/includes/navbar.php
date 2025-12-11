<header class="main-header">
  <!-- Logo -->
  <a href="home.php" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>V</b>TS</span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>Voting</b>System</span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php
              $userPhoto = (isset($user['photo']) && !empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg';
              $userName = (isset($user['firstname']) ? htmlspecialchars($user['firstname']) : 'Admin') . ' ' . (isset($user['lastname']) ? htmlspecialchars($user['lastname']) : '');
            ?>
            <img src="<?php echo $userPhoto; ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?php echo $userName; ?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="<?php echo $userPhoto; ?>" class="img-circle" alt="User Image">
              <p>
                <?php echo $userName; ?>
                <small>Member since <?php echo isset($user['created_on']) ? date('M. Y', strtotime($user['created_on'])) : 'N/A'; ?></small>
              </p>
            </li>
            <li class="user-footer">
              <div class="pull-left">
                <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" id="admin_profile">Update</a>
              </div>
              <div class="pull-right">
                <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
<?php include 'includes/profile_modal.php'; ?>
