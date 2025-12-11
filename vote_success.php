<?php
    session_start();
    include 'includes/conn.php';

    // Check if this is a valid success redirect
    if(!isset($_SESSION['vote_success']) || $_SESSION['vote_success'] !== true){
        header('Location: index.php');
        exit();
    }

    // Get voter info before clearing session
    $voterId = isset($_SESSION['voter']) ? $_SESSION['voter'] : null;
    $voterName = '';
    $votes = array();

    if($voterId){
        // Get voter name
        $stmt = $conn->prepare("SELECT firstname, lastname FROM voters WHERE id = ?");
        $stmt->bind_param("i", $voterId);
        $stmt->execute();
        $result = $stmt->get_result();
        $voterData = $result->fetch_assoc();
        $stmt->close();

        if($voterData){
            $voterName = $voterData['firstname'] . ' ' . $voterData['lastname'];
        }

        // Get submitted votes for display
        $votesQuery = $conn->prepare("
            SELECT p.description as position, c.firstname, c.lastname
            FROM votes v
            JOIN candidates c ON v.candidate_id = c.id
            JOIN positions p ON v.position_id = p.id
            WHERE v.voters_id = ?
            ORDER BY p.priority ASC
        ");
        $votesQuery->bind_param("i", $voterId);
        $votesQuery->execute();
        $votesResult = $votesQuery->get_result();
        while($row = $votesResult->fetch_assoc()){
            $votes[] = $row;
        }
        $votesQuery->close();
    }

    // Clear the success flag
    unset($_SESSION['vote_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Vote Submitted - 2025 Media Challenge Awards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="theme-color" content="#0a0a1a">

    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">

    <!-- Custom Theme -->
    <link rel="stylesheet" href="assets/css/media-challenge.css">

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        .success-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        .success-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .success-icon {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(76, 175, 80, 0.1) 100%);
            border: 3px solid rgba(76, 175, 80, 0.5);
            border-radius: 50%;
            margin: 0 auto 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: successPulse 2s ease-in-out infinite, scaleIn 0.6s ease-out;
        }

        .success-icon i {
            font-size: 70px;
            color: #4caf50;
            animation: checkmark 0.8s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes checkmark {
            0% { transform: scale(0) rotate(-45deg); opacity: 0; }
            50% { transform: scale(1.2) rotate(10deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        .success-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--gold-primary);
            margin-bottom: 15px;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .success-subtitle {
            font-size: 18px;
            color: var(--white-muted);
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        .voter-name {
            color: var(--gold-light);
            font-weight: 600;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .votes-summary {
            background: linear-gradient(145deg, rgba(26, 26, 62, 0.95) 0%, rgba(10, 10, 26, 0.98) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out 0.6s both;
        }

        .votes-summary h4 {
            color: var(--gold-primary);
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .vote-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .vote-item:last-child {
            border-bottom: none;
        }

        .vote-position {
            color: var(--white-muted);
            font-size: 13px;
            text-align: left;
        }

        .vote-candidate {
            color: var(--white);
            font-weight: 600;
            text-align: right;
        }

        .countdown-container {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 50px;
            padding: 15px 30px;
            display: inline-block;
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out 0.8s both;
        }

        .countdown-text {
            color: var(--white-muted);
            font-size: 14px;
        }

        .countdown-number {
            color: var(--gold-primary);
            font-weight: 700;
            font-size: 18px;
        }

        .security-notice {
            color: var(--white-subtle);
            font-size: 13px;
            margin-top: 20px;
            animation: fadeInUp 0.6s ease-out 1s both;
        }

        .security-notice i {
            color: var(--gold-primary);
            margin-right: 8px;
        }

        .btn-logout {
            background: var(--gradient-gold);
            border: none;
            color: var(--navy-dark);
            padding: 15px 40px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out 0.8s both;
        }

        .btn-logout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            color: var(--navy-dark);
        }

        /* Trophy animation */
        .floating-trophy {
            position: fixed;
            font-size: 40px;
            color: var(--gold-primary);
            opacity: 0.3;
            animation: floatTrophy 15s linear infinite;
            pointer-events: none;
        }

        @keyframes floatTrophy {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="hold-transition">
    <!-- Animated Background -->
    <div class="awards-bg"></div>

    <!-- Floating Trophies -->
    <i class="fa fa-trophy floating-trophy" style="left: 10%; animation-delay: 0s;"></i>
    <i class="fa fa-trophy floating-trophy" style="left: 30%; animation-delay: 3s;"></i>
    <i class="fa fa-trophy floating-trophy" style="left: 50%; animation-delay: 6s;"></i>
    <i class="fa fa-trophy floating-trophy" style="left: 70%; animation-delay: 9s;"></i>
    <i class="fa fa-trophy floating-trophy" style="left: 90%; animation-delay: 12s;"></i>

    <div class="success-page">
        <div class="success-container">
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="fa fa-check"></i>
            </div>

            <!-- Success Message -->
            <h1 class="success-title">Vote Submitted!</h1>
            <p class="success-subtitle">
                Thank you, <span class="voter-name"><?php echo htmlspecialchars($voterName); ?></span>!<br>
                Your vote has been successfully recorded.
            </p>

            <!-- Votes Summary -->
            <?php if(count($votes) > 0): ?>
            <div class="votes-summary">
                <h4><i class="fa fa-list-alt"></i> Your Votes</h4>
                <?php foreach($votes as $vote): ?>
                <div class="vote-item">
                    <span class="vote-position"><?php echo htmlspecialchars($vote['position']); ?></span>
                    <span class="vote-candidate"><?php echo htmlspecialchars($vote['firstname'] . ' ' . $vote['lastname']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Countdown -->
            <div class="countdown-container">
                <span class="countdown-text">Redirecting to login in </span>
                <span class="countdown-number" id="countdown">10</span>
                <span class="countdown-text"> seconds</span>
            </div>

            <br><br>

            <!-- Logout Button -->
            <a href="logout.php" class="btn btn-logout">
                <i class="fa fa-sign-out"></i> Exit Now
            </a>

            <!-- Security Notice -->
            <p class="security-notice">
                <i class="fa fa-shield"></i>
                Your vote is secure, confidential, and has been permanently recorded.
            </p>
        </div>
    </div>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Trigger confetti explosion
        function fireConfetti() {
            // First burst - center
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#D4AF37', '#F4D03F', '#FFD700', '#B8860B', '#ffffff']
            });

            // Left burst
            setTimeout(function() {
                confetti({
                    particleCount: 80,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0, y: 0.6 },
                    colors: ['#D4AF37', '#F4D03F', '#FFD700']
                });
            }, 200);

            // Right burst
            setTimeout(function() {
                confetti({
                    particleCount: 80,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1, y: 0.6 },
                    colors: ['#D4AF37', '#F4D03F', '#FFD700']
                });
            }, 400);

            // Continuous gold rain
            var duration = 3000;
            var end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 3,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#D4AF37', '#FFD700']
                });
                confetti({
                    particleCount: 3,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#D4AF37', '#FFD700']
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        }

        // Fire confetti on page load
        fireConfetti();

        // Countdown and redirect
        var countdown = 10;
        var countdownEl = document.getElementById('countdown');

        var timer = setInterval(function() {
            countdown--;
            countdownEl.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'logout.php';
            }
        }, 1000);

        // Fire more confetti at intervals
        setTimeout(fireConfetti, 3000);
        setTimeout(fireConfetti, 6000);
    });
    </script>
</body>
</html>
