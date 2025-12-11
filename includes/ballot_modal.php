<!-- Preview Modal -->
<div class="modal fade" id="preview_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Vote Preview</h4>
            </div>
            <div class="modal-body">
                <p style="color: rgba(255,255,255,0.6); margin-bottom: 20px; font-size: 13px;">
                    <i class="fa fa-info-circle"></i> Please review your selections before submitting.
                </p>
                <div id="preview_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <i class="fa fa-arrow-left"></i> Go Back
                </button>
                <button type="submit" form="ballotForm" name="vote" class="btn btn-success">
                    <i class="fa fa-check"></i> Confirm & Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Nominee Profile Modal -->
<div class="modal fade" id="platform">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-user"></i> <span class="candidate"></span></h4>
            </div>
            <div class="modal-body">
                <div style="background: rgba(212,175,55,0.1); padding: 25px; border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
                    <h5 style="color: #D4AF37; margin-bottom: 15px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                        <i class="fa fa-quote-left"></i> About the Nominee
                    </h5>
                    <p id="plat_view" style="font-size: 15px; line-height: 1.8; color: rgba(255,255,255,0.9);"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <i class="fa fa-thumbs-up"></i> Got It
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Submitted Ballot Modal -->
<div class="modal fade" id="view">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-trophy"></i> Your Submitted Ballot</h4>
            </div>
            <div class="modal-body">
                <div class="text-center" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <i class="fa fa-trophy" style="font-size: 50px; color: #D4AF37; margin-bottom: 15px;"></i>
                    <h4 style="color: #D4AF37; font-size: 16px; letter-spacing: 2px; text-transform: uppercase;">2025 Media Challenge Awards</h4>
                </div>
                <?php
                    $id = $voter['id'];
                    $sql = "SELECT *, candidates.firstname AS canfirst, candidates.lastname AS canlast FROM votes LEFT JOIN candidates ON candidates.id=votes.candidate_id LEFT JOIN positions ON positions.id=votes.position_id WHERE voters_id = '$id' ORDER BY positions.priority ASC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                        echo "
                            <div class='votelist'>
                                <span style='color: #D4AF37; font-weight: 600;'>".htmlspecialchars($row['description'])."</span>
                                <span style='color: rgba(255,255,255,0.9);'><i class='fa fa-check' style='color: #4caf50; margin-right: 8px;'></i>".htmlspecialchars($row['canfirst']." ".$row['canlast'])."</span>
                            </div>
                        ";
                    }
                ?>
                <div class="text-center" style="margin-top: 25px; padding: 15px; background: rgba(76,175,80,0.1); border-radius: 8px; border: 1px solid rgba(76,175,80,0.2);">
                    <i class="fa fa-shield" style="color: #4caf50;"></i>
                    <small style="color: #81c784;"> Your vote is secure and has been recorded.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <i class="fa fa-close"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
