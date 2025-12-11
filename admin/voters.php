<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Voters List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Voters</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Add New Voter</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Voter ID</th>
                  <th>Name</th>
                  <th>Photo</th>
                  <th>Status</th>
                  <th>Actions</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM voters ORDER BY id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';

                      // Determine voting status
                      $hasVoted = isset($row['has_voted']) && $row['has_voted'] == 1;
                      $isLoggedIn = isset($row['is_logged_in']) && $row['is_logged_in'] == 1;
                      $hasDevice = !empty($row['device_fingerprint']);

                      $statusBadge = '';
                      if($hasVoted){
                          $statusBadge = '<span class="label label-success"><i class="fa fa-check"></i> Voted</span>';
                      } elseif($isLoggedIn){
                          $statusBadge = '<span class="label label-warning"><i class="fa fa-user"></i> Logged In</span>';
                      } elseif($hasDevice){
                          $statusBadge = '<span class="label label-info"><i class="fa fa-mobile"></i> Device Bound</span>';
                      } else {
                          $statusBadge = '<span class="label label-default"><i class="fa fa-clock-o"></i> Pending</span>';
                      }

                      echo "
                        <tr>
                          <td><strong>".$row['voters_id']."</strong></td>
                          <td>".$row['firstname']." ".$row['lastname']."</td>
                          <td>
                            <img src='".$image."' width='40px' height='40px' style='border-radius: 50%; object-fit: cover;'>
                            <a href='#edit_photo' data-toggle='modal' class='pull-right photo' data-id='".$row['id']."' title='Change Photo'><span class='fa fa-camera'></span></a>
                          </td>
                          <td>".$statusBadge."</td>
                          <td>
                            <div class='btn-group'>
                              <button class='btn btn-primary btn-sm edit btn-flat' data-id='".$row['id']."' title='Edit Voter'><i class='fa fa-edit'></i></button>
                              <button class='btn btn-warning btn-sm reset_password btn-flat' data-id='".$row['id']."' title='Reset Password'><i class='fa fa-key'></i></button>
                              <button class='btn btn-info btn-sm clear_device btn-flat' data-id='".$row['id']."' title='Clear Device'><i class='fa fa-mobile'></i></button>
                              <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."' title='Delete Voter'><i class='fa fa-trash'></i></button>
                            </div>
                          </td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/voters_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.reset_password', function(e){
    e.preventDefault();
    $('#reset_password').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.clear_device', function(e){
    e.preventDefault();
    $('#clear_device').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'voters_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.id);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_voters_id').val(response.voters_id);
      $('.fullname').html(response.firstname + ' ' + response.lastname);
    }
  });
}
</script>
</body>
</html>
