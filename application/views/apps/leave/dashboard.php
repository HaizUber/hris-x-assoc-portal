<div class="content-wrapper">
    <div class="container-fluid">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row pb-2 pt-2">
                    <div class="col-sm-12 text-center">
                        <h4 class="m-0 font-weight-bold">LEAVE MANAGEMENT</h4>
                        <h6 class="font-weight-bold">DASHBOARD</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards Section -->
        <div class="d-flex flex-wrap justify-content-center" style="padding: 0 10% 5% 10%;">

            <!-- File Leave Card -->
            <div class="card text-center" style="width: 16rem;">
                <a href="<?php echo base_url('leave/home') ?>">
                    <img class="card-img-top" src="<?php echo base_url('assets/open-file.png'); ?>" alt="File Leave" style="padding: 20px;">
                    <div class="card-body">
                        <h6 class="card-text font-weight-bold">FILE LEAVE</h6>
                        <h6>SUBMIT A LEAVE REQUEST</h6>
                    </div>
                </a>
            </div>

            <!-- View Filed Leave Card -->
            <div class="card text-center" style="width: 16rem;">
                <a href="<?php echo base_url('leave/filedleave') ?>">
                    <img class="card-img-top" src="<?php echo base_url('assets/folder.png'); ?>" alt="View Filed Leave" style="padding: 20px;">
                    <div class="card-body">
                        <h6 class="card-text font-weight-bold">VIEW FILED LEAVE</h6>
                        <h6>CHECK YOUR LEAVE REQUESTS</h6>
                    </div>
                </a>
            </div>

            <!-- View Leave Balance Card -->
            <div class="card text-center" style="width: 16rem;">
                <a href="<?php echo base_url('leave/balance') ?>">
                    <img class="card-img-top" src="<?php echo base_url('assets/magnifying-glass.png'); ?>" alt="View Leave Balance" style="padding: 20px;">
                    <div class="card-body">
                        <h6 class="card-text font-weight-bold">VIEW LEAVE BALANCE</h6>
                        <h6>CHECK YOUR AVAILABLE LEAVE</h6>
                    </div>
                </a>
            </div>

            <?php 
    log_message('debug', 'Is Approving Officer in View: ' . var_export($isApprovingOfficer, true));
?>

<!-- Approve Leave Card -->
<?php if ($isApprovingOfficer): ?>
    <div class="card text-center" style="width: 16rem;">
        <a href="<?php echo base_url('leave/approve') ?>">
            <img class="card-img-top" src="<?php echo base_url('assets/ready.png'); ?>" alt="Approve Leave" style="padding: 20px;">
            <div class="card-body">
                <h6 class="card-text font-weight-bold">APPROVE LEAVE</h6>
                <h6>APPROVE OR REJECT LEAVE REQUESTS</h6>
                <?php if ($pendingLeaveCount > 0): ?>
                    <span class="badge badge-danger"><?php echo $pendingLeaveCount; ?> Pending</span>
                <?php else: ?>
                    <span class="badge badge-success">No Pending Requests</span>
                <?php endif; ?>
            </div>
        </a>
    </div>
<?php endif; ?>


<!-- Employee Leave Balance Card -->
<?php if ($isHR): ?>
    <div class="card text-center" style="width: 16rem;">
        <a href="<?php echo base_url('leave/approve') ?>">
            <img class="card-img-top" src="<?php echo base_url('assets/employee.png'); ?>" alt="Employee Leave Balance" style="padding: 20px;">
            <div class="card-body">
                <h6 class="card-text font-weight-bold">Employee Leave Balance</h6>
                <h6>MODIFY CURRENT EMPLOYEE LEAVE BALANCES</h6>
            </div>
        </a>
    </div>
<?php endif; ?>


        </div>
    </div>
</div>
