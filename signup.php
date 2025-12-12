<?php
/**
 * Voter Registration Page
 * Secure signup with secret code validation
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['voter'])) {
    header('Location: home.php');
    exit();
}

// Handle error messages from URL
$urlError = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_code':
            $urlError = 'Invalid secret code. Please contact Media Challenge Initiative for the correct code.';
            break;
        case 'registration_failed':
            $urlError = 'Registration failed. Please try again.';
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

<div class="login-box" style="width: 450px;">
    <div class="login-logo">
        <div class="trophy-icon">
            <i class="fa fa-trophy"></i>
        </div>
        <h1>Media Challenge</h1>
        <h2>Awards</h2>
        <div class="year">2025</div>
        <p class="tagline">Voter Registration</p>
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">Create your voter account to participate</p>

        <form action="signup_process.php" method="POST" id="signupForm">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="username" id="usernameInput" placeholder="Choose a Username" required autocomplete="off" minlength="3" maxlength="30">
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="firstname" id="firstnameInput" placeholder="First Name" required autocomplete="off">
                        <span class="fa fa-id-card form-control-feedback"></span>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="lastname" id="lastnameInput" placeholder="Last Name" required autocomplete="off">
                        <span class="fa fa-id-card form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Create Password (min 8 characters)" required minlength="8">
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="confirm_password" id="confirmPasswordInput" placeholder="Confirm Password" required>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-login" name="signup" id="signupBtn">
                        <span class="btn-text"><i class="fa fa-user-plus"></i> Create Account</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> Creating...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php" style="color: var(--gold-primary);">
                <i class="fa fa-arrow-left"></i> Already have an account? Sign In
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if (!empty($urlError)): ?>
        <div class="alert-custom alert-error" id="urlErrorAlert">
            <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
            <div class="alert-content">
                <p><?php echo htmlspecialchars($urlError); ?></p>
            </div>
            <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-custom alert-error" id="sessionErrorAlert">
            <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
            <div class="alert-content">
                <p><?php echo htmlspecialchars($_SESSION['error']); ?></p>
            </div>
            <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-custom alert-success" id="sessionSuccessAlert">
            <div class="alert-icon"><i class="fa fa-check-circle"></i></div>
            <div class="alert-content">
                <p><?php echo $_SESSION['success']; ?></p>
            </div>
            <button class="alert-close" onclick="this.parentElement.style.display='none'"><i class="fa fa-times"></i></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="login-footer">
        <p><i class="fa fa-calendar"></i> Sunday 14th December | 3:00PM</p>
        <p><i class="fa fa-map-marker"></i> Tirupati Mazima Mall, Kabalagala</p>
        <p style="margin-top: 15px;"><i class="fa fa-shield"></i> Your information is secure and confidential</p>
        <p style="margin-top: 10px;">&copy; <?php echo date('Y'); ?> Media Challenge Initiative</p>
    </div>
</div>

<style>
/* Additional Signup Page Styles */
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
    margin-top: 25px;
    animation: slideInUp 0.4s ease-out;
    position: relative;
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

/* Password match indicator */
.password-match {
    color: #4caf50;
}

.password-mismatch {
    color: #f44336;
}
</style>

<?php include 'includes/scripts.php' ?>
<script>
$(function(){
    // Password confirmation check
    $('#confirmPasswordInput').on('input', function() {
        var password = $('#passwordInput').val();
        var confirm = $(this).val();

        if (confirm.length > 0) {
            if (password === confirm) {
                $(this).css('border-color', '#4caf50');
            } else {
                $(this).css('border-color', '#f44336');
            }
        } else {
            $(this).css('border-color', '');
        }
    });

    // Form submission with validation
    $('#signupForm').on('submit', function(e) {
        var password = $('#passwordInput').val();
        var confirm = $('#confirmPasswordInput').val();
        var username = $('#usernameInput').val().trim();

        // Validate password match
        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }

        // Validate password length
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long!');
            return false;
        }

        // Validate username
        if (username.length < 3) {
            e.preventDefault();
            alert('Username must be at least 3 characters long!');
            return false;
        }

        // Show loading state
        $('#signupBtn').addClass('loading');
        $('#signupBtn .btn-text').hide();
        $('#signupBtn .btn-loading').show();
    });

    // Auto-hide alerts after 8 seconds
    setTimeout(function() {
        $('.alert-custom').fadeOut(400);
    }, 8000);
});
</script>
</body>
</html>
