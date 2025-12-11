<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<!-- Animated Background -->
<div class="awards-bg"></div>

<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">

            <!-- Main content -->
            <section class="content">
                <?php
                    $parse = parse_ini_file('admin/config.ini', FALSE, INI_SCANNER_RAW);
                    $title = $parse['election_title'];
                ?>
                <div class="page-header" style="text-align: center;">
                    <b style="display: block; text-align: center;">Media Challenge</b>
                    <div class="title-main" style="text-align: center;">Awards</div>
                    <div class="title-year" style="text-align: center;">2025</div>
                </div>
                <p class="subtitle">
                    <i class="fa fa-star"></i> Select your preferred nominee in each category <i class="fa fa-star"></i>
                </p>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">

                    <!-- Custom Alert Container -->
                    <div id="alertContainer"></div>

                    <?php
                        if(isset($_SESSION['error'])){
                            ?>
                            <div class="alert-custom alert-error alert-dismissible" id="errorAlert">
                                <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
                                <div class="alert-content">
                                    <ul style="margin: 0; padding-left: 20px;">
                                        <?php
                                            foreach($_SESSION['error'] as $error){
                                                echo "<li>".$error."</li>";
                                            }
                                        ?>
                                    </ul>
                                </div>
                                <button class="alert-close" onclick="$(this).parent().fadeOut()"><i class="fa fa-times"></i></button>
                            </div>
                            <?php
                             unset($_SESSION['error']);
                        }
                        if(isset($_SESSION['success'])){
                            echo "
                                <div class='alert-custom alert-success alert-dismissible' id='successAlert'>
                                    <div class='alert-icon'><i class='fa fa-check-circle'></i></div>
                                    <div class='alert-content'>
                                        <p>".$_SESSION['success']."</p>
                                    </div>
                                    <button class='alert-close' onclick='$(this).parent().fadeOut()'><i class='fa fa-times'></i></button>
                                </div>
                            ";
                            unset($_SESSION['success']);
                        }
                    ?>

                    <!-- Hidden alert for JS errors -->
                    <div class="alert-custom alert-error" id="jsAlert" style="display:none;">
                        <div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
                        <div class="alert-content">
                            <p class="message"></p>
                        </div>
                        <button class="alert-close" onclick="$(this).parent().fadeOut()"><i class="fa fa-times"></i></button>
                    </div>

                    <?php
                        $sql = "SELECT * FROM votes WHERE voters_id = '".$voter['id']."'";
                        $vquery = $conn->query($sql);
                        if($vquery->num_rows > 0){
                            // Get voted timestamp
                            $votedAt = '';
                            if(!empty($voter['voted_at'])){
                                $votedAt = date('F j, Y \a\t g:i A', strtotime($voter['voted_at']));
                            }
                    ?>
                            <div class="voted-message">
                                <div class="voted-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                                <h3><i class="fa fa-trophy"></i> Thank You for Voting!</h3>
                                <p>Your vote has been successfully recorded for the 2025 Media Challenge Awards.</p>
                                <?php if($votedAt): ?>
                                <p class="voted-timestamp"><i class="fa fa-clock-o"></i> Voted on <?php echo $votedAt; ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Submitted Ballot Display -->
                            <div class="submitted-ballot-card">
                                <div class="ballot-header">
                                    <i class="fa fa-file-text-o"></i>
                                    <h4>Your Submitted Ballot</h4>
                                </div>
                                <div class="ballot-body">
                                    <?php
                                        $id = $voter['id'];
                                        $sql = "SELECT *, candidates.firstname AS canfirst, candidates.lastname AS canlast, candidates.photo AS canphoto
                                                FROM votes
                                                LEFT JOIN candidates ON candidates.id=votes.candidate_id
                                                LEFT JOIN positions ON positions.id=votes.position_id
                                                WHERE voters_id = '$id'
                                                ORDER BY positions.priority ASC";
                                        $query = $conn->query($sql);
                                        while($row = $query->fetch_assoc()){
                                            $canImage = (!empty($row['canphoto'])) ? 'images/'.$row['canphoto'] : 'images/profile.jpg';
                                            echo "
                                                <div class='ballot-vote-item'>
                                                    <div class='vote-category'>
                                                        <i class='fa fa-star'></i>
                                                        ".htmlspecialchars($row['description'])."
                                                    </div>
                                                    <div class='vote-selection'>
                                                        <img src='".$canImage."' alt='".htmlspecialchars($row['canfirst'])."' class='vote-photo'>
                                                        <span class='vote-name'>
                                                            <i class='fa fa-check-circle'></i>
                                                            ".htmlspecialchars($row['canfirst']." ".$row['canlast'])."
                                                        </span>
                                                    </div>
                                                </div>
                                            ";
                                        }
                                    ?>
                                </div>
                                <div class="ballot-footer">
                                    <div class="security-badge">
                                        <i class="fa fa-shield"></i>
                                        <span>Your vote is secure, confidential, and permanently recorded.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center" style="margin-top: 30px;">
                                <a href="logout.php" class="btn btn-outline-gold">
                                    <i class="fa fa-sign-out"></i> Logout
                                </a>
                            </div>
                    <?php
                        }
                        else{
                            ?>
                            <!-- Voting Ballot -->
                            <form method="POST" id="ballotForm" action="submit_ballot.php">
                                <?php
                                    include 'includes/slugify.php';

                                    $candidate = '';
                                    $sql = "SELECT * FROM positions ORDER BY priority ASC";
                                    $query = $conn->query($sql);
                                    $categoryCount = 0;
                                    while($row = $query->fetch_assoc()){
                                        $categoryCount++;
                                        $sql = "SELECT * FROM candidates WHERE position_id='".$row['id']."'";
                                        $cquery = $conn->query($sql);
                                        while($crow = $cquery->fetch_assoc()){
                                            $slug = slugify($row['description']);
                                            $checked = '';
                                            if(isset($_SESSION['post'][$slug])){
                                                $value = $_SESSION['post'][$slug];

                                                if(is_array($value)){
                                                    foreach($value as $val){
                                                        if($val == $crow['id']){
                                                            $checked = 'checked';
                                                        }
                                                    }
                                                }
                                                else{
                                                    if($value == $crow['id']){
                                                        $checked = 'checked';
                                                    }
                                                }
                                            }
                                            $input = ($row['max_vote'] > 1) ? '<input type="checkbox" class="flat-red '.$slug.'" name="'.$slug."[]".'" value="'.$crow['id'].'" '.$checked.'>' : '<input type="radio" class="flat-red '.$slug.'" name="'.slugify($row['description']).'" value="'.$crow['id'].'" '.$checked.'>';
                                            $image = (!empty($crow['photo'])) ? 'images/'.$crow['photo'] : 'images/profile.jpg';
                                            $candidate .= '
                                                <li data-candidate-id="'.$crow['id'].'">
                                                    '.$input.'
                                                    <div class="candidate-info">
                                                        <img src="'.$image.'" height="100px" width="100px" class="clist" alt="'.htmlspecialchars($crow['firstname'].' '.$crow['lastname']).'">
                                                        <div class="candidate-details">
                                                            <span class="cname clist">'.htmlspecialchars($crow['firstname'].' '.$crow['lastname']).'</span>
                                                            <button type="button" class="btn btn-platform platform" data-platform="'.htmlspecialchars($crow['platform']).'" data-fullname="'.htmlspecialchars($crow['firstname'].' '.$crow['lastname']).'">
                                                                <i class="fa fa-info-circle"></i> View Platform
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            ';
                                        }

                                        $instruct = ($row['max_vote'] > 1) ? 'You may select up to '.$row['max_vote'].' candidates' : 'Select only one candidate';

                                        echo '
                                            <div class="row category-row" data-aos="fade-up" data-aos-delay="'.($categoryCount * 100).'">
                                                <div class="col-xs-12">
                                                    <div class="box box-solid category-box" id="'.$row['id'].'">
                                                        <div class="box-header with-border">
                                                            <div class="category-number">'.$categoryCount.'</div>
                                                            <h3 class="box-title"><b>'.htmlspecialchars($row['description']).'</b></h3>
                                                        </div>
                                                        <div class="box-body">
                                                            <div class="category-instructions">
                                                                <span><i class="fa fa-info-circle"></i> '.$instruct.'</span>
                                                                <button type="button" class="btn btn-reset reset" data-desc="'.slugify($row['description']).'">
                                                                    <i class="fa fa-refresh"></i> Reset Selection
                                                                </button>
                                                            </div>
                                                            <div id="candidate_list">
                                                                <ul>
                                                                    '.$candidate.'
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ';

                                        $candidate = '';
                                    }

                                ?>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-preview" id="preview">
                                        <span class="btn-text"><i class="fa fa-file-text"></i> Preview Ballot</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fa fa-spinner fa-spin"></i> Loading...
                                        </span>
                                    </button>
                                    <button type="submit" class="btn btn-submit" name="vote" id="submitBtn">
                                        <span class="btn-text"><i class="fa fa-check-square-o"></i> Submit Vote</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fa fa-spinner fa-spin"></i> Submitting...
                                        </span>
                                    </button>
                                </div>

                                <!-- Progress Indicator -->
                                <div class="voting-progress">
                                    <div class="progress-text">
                                        <span id="selectedCount">0</span> of <span id="totalCategories"><?php echo $categoryCount; ?></span> categories selected
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </form>
                            <!-- End Voting Ballot -->
                            <?php
                        }
                    ?>

                </div>
            </div>
          </section>

        </div>
      </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/ballot_modal.php'; ?>
</div>

<style>
/* Additional Voting Page Styles */
.alert-custom {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 18px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    animation: slideInDown 0.4s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
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

.alert-error .alert-icon { color: #f44336; }
.alert-success .alert-icon { color: #4caf50; }

.alert-content {
    flex: 1;
    color: var(--white-muted);
}

.alert-content ul {
    margin: 0;
    padding-left: 20px;
}

.alert-content li {
    margin-bottom: 5px;
}

.alert-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    font-size: 16px;
    transition: color 0.3s ease;
}

.alert-close:hover { color: rgba(255, 255, 255, 0.9); }

/* Category Box Enhancements */
.category-box {
    position: relative;
    overflow: visible !important;
}

.category-number {
    position: absolute;
    top: -15px;
    left: 20px;
    width: 40px;
    height: 40px;
    background: var(--gradient-gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: var(--navy-dark);
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    z-index: 10;
}

.category-instructions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-wrap: wrap;
    gap: 10px;
}

.category-instructions span {
    color: var(--white-muted);
    font-size: 13px;
}

.category-instructions i {
    color: var(--gold-primary);
    margin-right: 8px;
}

.btn-reset {
    background: transparent;
    border: 1px solid rgba(212, 175, 55, 0.3);
    color: var(--gold-primary);
    border-radius: 25px;
    padding: 8px 18px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: rgba(212, 175, 55, 0.1);
    border-color: var(--gold-primary);
    color: var(--gold-light);
}

/* Candidate Card Improvements */
#candidate_list ul li {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px !important;
}

.candidate-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.candidate-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-platform {
    background: transparent;
    border: 1px solid var(--gold-primary);
    color: var(--gold-primary);
    border-radius: 25px;
    padding: 6px 15px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-platform:hover {
    background: var(--gold-primary);
    color: var(--navy-dark);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding: 40px 0;
    flex-wrap: wrap;
}

.btn-preview, .btn-submit {
    padding: 18px 45px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 200px;
}

.btn-preview {
    background: transparent;
    border: 2px solid var(--gold-primary);
    color: var(--gold-primary);
}

.btn-preview:hover {
    background: rgba(212, 175, 55, 0.1);
    box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
    transform: translateY(-3px);
    color: var(--gold-light);
}

.btn-submit {
    background: var(--gradient-gold);
    border: none;
    color: var(--navy-dark);
}

.btn-submit:hover {
    box-shadow: 0 10px 40px rgba(212, 175, 55, 0.5);
    transform: translateY(-3px);
}

.btn-preview.loading, .btn-submit.loading {
    pointer-events: none;
    opacity: 0.8;
}

/* Progress Indicator */
.voting-progress {
    max-width: 400px;
    margin: 0 auto 40px;
    text-align: center;
}

.progress-text {
    color: var(--white-muted);
    font-size: 14px;
    margin-bottom: 12px;
}

.progress-text span {
    color: var(--gold-primary);
    font-weight: 600;
}

.progress-bar-container {
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: var(--gradient-gold);
    border-radius: 10px;
    transition: width 0.4s ease;
}

/* View Ballot Button */
.btn-view-ballot {
    background: var(--gradient-gold);
    border: none;
    color: var(--navy-dark);
    padding: 18px 40px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: all 0.3s ease;
}

.btn-view-ballot:hover {
    box-shadow: 0 10px 40px rgba(212, 175, 55, 0.5);
    transform: translateY(-3px);
    color: var(--navy-dark);
}

.btn-outline-gold {
    background: transparent;
    border: 2px solid var(--gold-primary);
    color: var(--gold-primary);
    padding: 12px 30px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.btn-outline-gold:hover {
    background: rgba(212, 175, 55, 0.1);
    color: var(--gold-light);
}

/* Voted Timestamp */
.voted-timestamp {
    color: var(--white-muted);
    font-size: 13px;
    margin-top: 10px;
}

.voted-timestamp i {
    color: var(--gold-primary);
    margin-right: 5px;
}

/* Submitted Ballot Card */
.submitted-ballot-card {
    background: linear-gradient(145deg, rgba(26, 26, 62, 0.95) 0%, rgba(10, 10, 26, 0.98) 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 16px;
    overflow: hidden;
    margin-top: 30px;
    animation: fadeInUp 0.6s ease-out;
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

.ballot-header {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
    padding: 20px 25px;
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    display: flex;
    align-items: center;
    gap: 12px;
}

.ballot-header i {
    font-size: 24px;
    color: var(--gold-primary);
}

.ballot-header h4 {
    margin: 0;
    color: var(--gold-primary);
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 1px;
}

.ballot-body {
    padding: 25px;
}

.ballot-vote-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    margin-bottom: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.ballot-vote-item:hover {
    background: rgba(212, 175, 55, 0.05);
    border-color: rgba(212, 175, 55, 0.2);
}

.ballot-vote-item:last-child {
    margin-bottom: 0;
}

.vote-category {
    color: var(--white-muted);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.vote-category i {
    color: var(--gold-primary);
    font-size: 12px;
}

.vote-selection {
    display: flex;
    align-items: center;
    gap: 12px;
}

.vote-photo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--gold-primary);
}

.vote-name {
    color: var(--white);
    font-weight: 600;
    font-size: 14px;
}

.vote-name i {
    color: #4caf50;
    margin-right: 6px;
}

.ballot-footer {
    padding: 20px 25px;
    background: rgba(76, 175, 80, 0.08);
    border-top: 1px solid rgba(76, 175, 80, 0.15);
}

.security-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #81c784;
    font-size: 13px;
}

.security-badge i {
    color: #4caf50;
    font-size: 16px;
}

/* Responsive Ballot */
@media (max-width: 768px) {
    .ballot-vote-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .vote-selection {
        width: 100%;
        justify-content: flex-start;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn-preview, .btn-submit {
        width: 100%;
        max-width: 300px;
    }

    .category-instructions {
        flex-direction: column;
        align-items: flex-start;
    }

    .candidate-info {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }

    .candidate-details {
        align-items: center;
        width: 100%;
    }
}
</style>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
    // Initialize iCheck
    $('.content').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });

    // Track selected categories
    var totalCategories = <?php echo isset($categoryCount) ? $categoryCount : 0; ?>;

    function updateProgress() {
        var selectedCategories = new Set();
        $('input[type="radio"]:checked, input[type="checkbox"]:checked').each(function() {
            var categoryClass = $(this).attr('class').split(' ')[1];
            selectedCategories.add(categoryClass);
        });

        var count = selectedCategories.size;
        $('#selectedCount').text(count);
        var percentage = totalCategories > 0 ? (count / totalCategories) * 100 : 0;
        $('#progressBar').css('width', percentage + '%');
    }

    // Update progress on selection change
    $('input[type="radio"], input[type="checkbox"]').on('ifChanged', function() {
        updateProgress();

        // Add visual feedback to selected candidate
        var li = $(this).closest('li');
        if($(this).is(':checked')) {
            li.addClass('selected');
        } else {
            li.removeClass('selected');
        }
    });

    // Initial progress update
    updateProgress();

    // Reset button handler
    $(document).on('click', '.reset', function(e){
        e.preventDefault();
        var desc = $(this).data('desc');
        $('.'+desc).iCheck('uncheck');
        $('.'+desc).closest('li').removeClass('selected');
        updateProgress();
    });

    // Platform modal handler
    $(document).on('click', '.platform', function(e){
        e.preventDefault();
        $('#platform').modal('show');
        var platform = $(this).data('platform');
        var fullname = $(this).data('fullname');
        $('.candidate').html(fullname);
        $('#plat_view').html(platform || 'No platform information available.');
    });

    // Preview button with loading state
    $('#preview').click(function(e){
        e.preventDefault();
        var form = $('#ballotForm').serialize();

        if(form == ''){
            showAlert('You must vote for at least one candidate', 'error');
            return;
        }

        // Show loading state
        var btn = $(this);
        btn.addClass('loading');
        btn.find('.btn-text').hide();
        btn.find('.btn-loading').show();

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: form,
            dataType: 'json',
            success: function(response){
                btn.removeClass('loading');
                btn.find('.btn-text').show();
                btn.find('.btn-loading').hide();

                if(response.error){
                    var errmsg = '';
                    var messages = response.message;
                    for (i in messages) {
                        errmsg += messages[i] + ' ';
                    }
                    showAlert(errmsg, 'error');
                }
                else{
                    $('#preview_modal').modal('show');
                    $('#preview_body').html(response.list);
                }
            },
            error: function() {
                btn.removeClass('loading');
                btn.find('.btn-text').show();
                btn.find('.btn-loading').hide();
                showAlert('An error occurred. Please try again.', 'error');
            }
        });
    });

    // Submit button with loading state
    $('#ballotForm').on('submit', function(e) {
        var form = $('#ballotForm').serialize();

        if(form == '' || form == 'vote=') {
            e.preventDefault();
            showAlert('Please select at least one candidate before submitting.', 'error');
            return false;
        }

        // Show loading state
        var btn = $('#submitBtn');
        btn.addClass('loading');
        btn.find('.btn-text').hide();
        btn.find('.btn-loading').show();

        // Disable preview button too
        $('#preview').prop('disabled', true);
    });

    // Show alert function
    function showAlert(message, type) {
        var alertClass = type === 'error' ? 'alert-error' : 'alert-success';
        var iconClass = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';

        var alertHtml = `
            <div class="alert-custom ${alertClass}" style="display: none;">
                <div class="alert-icon"><i class="fa ${iconClass}"></i></div>
                <div class="alert-content"><p>${message}</p></div>
                <button class="alert-close" onclick="$(this).parent().fadeOut()"><i class="fa fa-times"></i></button>
            </div>
        `;

        $('#alertContainer').html(alertHtml);
        $('#alertContainer .alert-custom').slideDown(300);

        // Auto hide after 5 seconds
        setTimeout(function() {
            $('#alertContainer .alert-custom').fadeOut(300);
        }, 5000);

        // Scroll to alert
        $('html, body').animate({
            scrollTop: $('#alertContainer').offset().top - 100
        }, 300);
    }

    // Auto-hide existing alerts
    setTimeout(function() {
        $('.alert-custom').fadeOut(400);
    }, 8000);
});
</script>
</body>
</html>
