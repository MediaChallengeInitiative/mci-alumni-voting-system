<!-- Add New Voter -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-user-plus"></i> Add New Voter</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_add.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="firstname" class="col-sm-3 control-label">Firstname</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter first name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="lastname" class="col-sm-3 control-label">Lastname</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter last name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>
                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo" accept="image/*">
                      <p class="help-block">Optional. Accepted formats: JPG, PNG, GIF</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <div class="callout callout-info" style="margin-bottom: 0; padding: 10px 15px;">
                            <p style="margin: 0;"><i class="fa fa-info-circle"></i> <strong>Auto-Generated Credentials:</strong></p>
                            <small>
                                <strong>Voter ID Format:</strong> MCIA{First letter}{First 2 of last}25 (ALL UPPERCASE)<br>
                                <em>Example: Emmanuel Bahindi → MCIAEBA25</em><br>
                                <strong>Default Password:</strong> AwardsNight2025
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Add Voter</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Voter -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-edit"></i> Edit Voter</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_edit.php">
                <input type="hidden" class="id" name="id">
                <div class="form-group">
                    <label for="edit_firstname" class="col-sm-3 control-label">Firstname</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_lastname" class="col-sm-3 control-label">Lastname</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_voters_id" class="col-sm-3 control-label">Voter ID</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_voters_id" name="voters_id" readonly style="background: #f5f5f5;">
                      <p class="help-block">Voter ID cannot be changed</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password -->
<div class="modal fade" id="reset_password">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-key"></i> Reset Password</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_reset_password.php">
                <input type="hidden" class="id" name="id">
                <div class="text-center">
                    <p>Reset password for voter:</p>
                    <h3 class="bold fullname" style="color: #3c8dbc;"></h3>
                    <p class="text-muted">Password will be reset to: <strong>AwardsNight2025</strong></p>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
              <button type="submit" class="btn btn-warning btn-flat" name="reset_password"><i class="fa fa-refresh"></i> Reset Password</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Voter -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-trash"></i> Delete Voter</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_delete.php">
                <input type="hidden" class="id" name="id">
                <div class="text-center">
                    <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete this voter?</p>
                    <h2 class="bold fullname" style="color: #dd4b39;"></h2>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
              <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Photo -->
<div class="modal fade" id="edit_photo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-camera"></i> Update Photo - <span class="fullname"></span></b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_photo.php" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id">
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>
                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo" accept="image/*" required>
                      <p class="help-block">Accepted formats: JPG, PNG, GIF</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="upload"><i class="fa fa-check-square-o"></i> Upload</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Clear Device Binding -->
<div class="modal fade" id="clear_device">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><i class="fa fa-mobile"></i> Clear Device Binding</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="voters_clear_device.php">
                <input type="hidden" class="id" name="id">
                <div class="text-center">
                    <p>Clear device binding for voter:</p>
                    <h3 class="bold fullname" style="color: #3c8dbc;"></h3>
                    <p class="text-muted">This will allow the voter to login from a new device.</p>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
              <button type="submit" class="btn btn-info btn-flat" name="clear_device"><i class="fa fa-refresh"></i> Clear Device</button>
              </form>
            </div>
        </div>
    </div>
</div>
