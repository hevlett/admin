<?php
include_once('config/db.php');
include_once('config/function.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['month']) && isset($_GET['year']) && isset($_GET['day'])) {
    // Validate input values
    $employeeId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $month = filter_var($_GET['month'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
    $year = filter_var($_GET['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 2099]]);
    $day = filter_var($_GET['day'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 31]]);

    // Check if validation succeeded
    if ($employeeId !== false && $month !== false && $year !== false && $day !== false) {
        $payrollDeleteRes = deletePayroll($conn, $employeeId, $month, $year, $day);

        if ($payrollDeleteRes && $payrollDeleteRes['status'] == 200) {
            redirect('payroll.php', 'Payroll Deleted Successfully.');
        } else {
            redirect('payroll.php', 'Error Deleting Payroll: ' . $payrollDeleteRes['message']);
        }
    } else {
        redirect('payroll.php', 'Invalid Request Parameters.');
    }
} else {
    redirect('payroll.php', 'Invalid Request.');
}
?>
