<?php
session_start();
include('includes/header.php'); 



// // Check if the user is logged in
// if(!isset($_SESSION["loggedin"]) || !isset($_SESSION['loggedinUser']['user_id'])) {
//     echo "<script>window.location.href = 'index.php';</script>";
//     exit();
// }

$user_id = $_SESSION['loggedinUser']['user_id'];
$employeeCount = getCount('employee');
$adminCount = getCount('admins');
$payrollCount = getCount('payslip');
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
   

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    Employees: <?php echo $employeeCount; ?>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="employee.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    Admins: <?php echo $adminCount; ?>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="admins.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    Payrolls: <?php echo $payrollCount; ?>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="payroll.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
