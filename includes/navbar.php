<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="home.php" class="navbar-brand">
                    <i class="fa fa-trophy" style="color: #ffc107; margin-right: 8px;"></i>
                    <b>Media Challenge</b> <span>Awards</span>
                </a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="home.php"><i class="fa fa-home"></i> HOME</a></li>
                </ul>
            </div>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="user user-menu">
                        <a href="#">
                            <img src="<?php echo (!empty($voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg' ?>" class="user-image" alt="Voter Photo">
                            <span class="hidden-xs"><?php echo htmlspecialchars($voter['firstname'].' '.$voter['lastname']); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" style="color: #ffcdd2 !important;">
                            <i class="fa fa-sign-out"></i> LOGOUT
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>