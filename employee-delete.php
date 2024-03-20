<?php

require_once 'config/function.php';

$employeeId = checkParamId('id');

if ($employeeId !== null && is_numeric($employeeId)) {
    // Valid employeeId found, continue processing

    $employeeId = validate($employeeId);

    // Delete related records (e.g., payslips) before deleting the employee
    $payslipDeleteRes = delete('employee', $employeeId);

    if ($payslipDeleteRes) {
        // The related records (e.g., payslips) were deleted successfully
        $employeeDeleteRes = delete('employee', $employeeId);
        
        if ($employeeDeleteRes) {
            redirect('employee.php', 'Employee Deleted Successfully.');
        } else {
            redirect('employee.php', 'Something Went Wrong.');
        }
    } else {
        // Failed to delete related records (e.g., payslips)
        redirect('employee.php', 'Failed to delete related records.');
    }
} else {
    // Invalid or missing employeeId
    redirect('employee.php', 'No Valid Employee Id Given');
}
?>


