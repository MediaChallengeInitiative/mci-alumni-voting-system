<?php
    session_start();
    if(isset($_SESSION['admin'])){
        header('location: admin/home.php');
        exit();
    }

    if(isset($_SESSION['voter'])){
        header('location: home.php');
        exit();
    }

    // Handle URL error parameters
    $urlError = '';
    if(isset($_GET['error'])){
        switch($_GET['error']){
            case 'session_invalid':
                $urlError = 'Your session has expired or is invalid. Please log in again.';
                break;
            case 'device_mismatch':
                $urlError = 'You must use the same device you originally logged in from.';
                break;
        }
    }
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition login-page">
<!-- Animated Background -->
<div class="awards-bg"></div>

<!-- Floating Sparkles -->
<div class="sparkles-container">
    <div class="sparkle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="sparkle" style="left: 20%; animation-delay: 0.5s;"></div>
    <div class="sparkle" style="left: 30%; animation-delay: 1s;"></div>
    <div class="sparkle" style="left: 40%; animation-delay: 1.5s;"></div>
    <div class="sparkle" style="left: 50%; animation-delay: 2s;"></div>
    <div class="sparkle" style="left: 60%; animation-delay: 2.5s;"></div>
    <div class="sparkle" style="left: 70%; animation-delay: 3s;"></div>
    <div class="sparkle" style="left: 80%; animation-delay: 0.3s;"></div>
    <div class="sparkle" style="left: 90%; animation-delay: 0.8s;"></div>
</div>

<div class="login-box">
    <div class="login-logo">
        <div class="trophy-icon">
            <i class="fa fa-trophy"></i>
        </div>
        <h1>Media Challenge</h1>
        <h2>Awards</h2>
        <div class="year">2025</div>
        <p class="tagline">Alumni Nominations</p>
    </div>

    <div class="login-box-body">
        <!-- Error Messages - At Top, Stay Until Closed -->
        <?php if(!empty($urlError)): ?>
            <div class="alert-custom alert-error alert-persistent" id="urlErrorAlert">
                <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
                <div class="alert-content">
                    <p><?php echo htmlspecialchars($urlError); ?></p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-custom alert-error alert-persistent" id="sessionErrorAlert">
                <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
                <div class="alert-content">
                    <p><?php echo $_SESSION['error']; ?></p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-custom alert-success" id="sessionSuccessAlert">
                <div class="alert-icon"><i class="fa fa-check-circle"></i></div>
                <div class="alert-content">
                    <p><?php echo $_SESSION['success']; ?></p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <p class="login-box-msg">Sign in with your username to cast your vote</p>

        <form action="login.php" method="POST" id="loginForm">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="username" id="usernameInput" placeholder="Enter your Username" required autocomplete="off">
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Enter your Password" required>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-login" name="login" id="loginBtn">
                        <span class="btn-text"><i class="fa fa-sign-in"></i> Cast Your Vote</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> Signing In...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="signup.php" style="color: var(--gold-primary);">
                <i class="fa fa-user-plus"></i> Don't have an account? Register here
            </a>
        </div>
    </div>

    <div class="login-footer">
        <p><i class="fa fa-calendar"></i> Sunday 14th December | 3:00PM</p>
        <p><i class="fa fa-map-marker"></i> Tirupati Mazima Mall, Kabalagala</p>
        <p style="margin-top: 15px;"><i class="fa fa-shield"></i> Your vote is secure and confidential</p>
        <p style="margin-top: 10px;">&copy; <?php echo date('Y'); ?> Media Challenge Initiative</p>
    </div>
</div>

<style>
/* Additional Login Page Styles */
.sparkles-container {
    position: fixed;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.sparkle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--gold-shine);
    border-radius: 50%;
    box-shadow: 0 0 10px var(--gold-primary), 0 0 20px var(--gold-primary);
    animation: floatSparkle 8s ease-in-out infinite;
    opacity: 0;
}

@keyframes floatSparkle {
    0%, 100% {
        transform: translateY(100vh) scale(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
        transform: translateY(90vh) scale(1);
    }
    90% {
        opacity: 1;
        transform: translateY(10vh) scale(1);
    }
    100% {
        transform: translateY(0) scale(0);
        opacity: 0;
    }
}

/* Custom Alert Styles */
.alert-custom {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 18px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    animation: slideInUp 0.4s ease-out;
    position: relative;
}

/* Persistent alerts inside login form */
.login-box-body .alert-custom {
    margin-top: 0;
    margin-bottom: 20px;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-error {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.15) 0%, rgba(244, 67, 54, 0.08) 100%);
    border: 1px solid rgba(244, 67, 54, 0.4);
}

.alert-success {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(76, 175, 80, 0.08) 100%);
    border: 1px solid rgba(76, 175, 80, 0.4);
}

.alert-icon {
    font-size: 22px;
    flex-shrink: 0;
}

.alert-error .alert-icon {
    color: #f44336;
}

.alert-success .alert-icon {
    color: #4caf50;
}

.alert-content {
    flex: 1;
}

.alert-content p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.alert-error .alert-content p {
    color: #ff8a80;
}

.alert-success .alert-content p {
    color: #a5d6a7;
}

.alert-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    padding: 0;
    font-size: 16px;
    transition: color 0.3s ease;
}

.alert-close:hover {
    color: rgba(255, 255, 255, 0.9);
}

/* Button Loading State */
.btn-login {
    position: relative;
    overflow: hidden;
}

.btn-login.loading {
    pointer-events: none;
    opacity: 0.8;
}

.btn-login .btn-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Input Focus Animation */
.form-group {
    position: relative;
}

.form-group::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--gradient-gold);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.form-group:focus-within::after {
    width: 100%;
}

/* Shake animation for errors */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}
</style>

<?php include 'includes/scripts.php' ?>
<script>
$(function(){
    // Form submission with loading state
    $('#loginForm').on('submit', function(e) {
        var usernameInput = $('#usernameInput').val().trim();
        var passwordInput = $('#passwordInput').val();

        if(!usernameInput || !passwordInput) {
            e.preventDefault();
            $('.login-box-body').addClass('shake');
            setTimeout(function() {
                $('.login-box-body').removeClass('shake');
            }, 500);
            return false;
        }

        // Show loading state
        $('#loginBtn').addClass('loading');
        $('#loginBtn .btn-text').hide();
        $('#loginBtn .btn-loading').show();
    });

    // Auto-hide only success alerts after 8 seconds (error alerts stay until closed)
    setTimeout(function() {
        $('.alert-success').fadeOut(400);
    }, 8000);

    // Input animations
    $('.form-control').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group').removeClass('focused');
    });
});
</script>
</body>
</html>
