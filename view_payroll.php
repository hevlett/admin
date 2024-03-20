<?php
ob_start(); // Start output buffering

include('includes/header.php');
include_once('config/db.php');
include_once('config/function.php');
// require_once('vendor/autoload.php');

use Dompdf\Dompdf;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedMonth = $_POST['month'];
    $selectedYear = $_POST['year'];
    $selectedDay = $_POST['day'];
    $selectedDepartment = $_POST['department'];

    // Use the selected filters to fetch payroll data
    $payrollData = getPayrollByMonthYearDay($conn, $selectedMonth, $selectedYear, $selectedDay, $selectedDepartment);
} else {
    $payrollData = []; // Default value or empty array
}

// Get the list of unique departments
$departments = getUniqueDepartments($conn);

// Export to PDF functionality
if (isset($_POST['export'])) {
    ob_clean(); // Clear the output buffer

    // Create a new PDF object
    $pdf = new Dompdf();

    // Generate the HTML content for the PDF
    $html = '<html><body>';

    // Add the table headers
    $html .= '<table style="border-collapse: collapse; width: 100%;">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th style="border: 1px solid black;">ID Number</th>';
    $html .= '<th style="border: 1px solid black;">Name</th>';
    $html .= '<th style="border: 1px solid black;">Department</th>';
    $html .= '<th style="border: 1px solid black;">Overall Total Hours</th>';
    $html .= '<th style="border: 1px solid black;">Month</th>';
    $html .= '<th style="border: 1px solid black;">Year</th>';
    $html .= '<th style="border: 1px solid black;">Day</th>';
    $html .= '<th style="border: 1px solid black;">Hourly Rate</th>';
    $html .= '<th style="border: 1px solid black;">Total Pay</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    // Add the payroll data to the HTML content
    foreach ($payrollData as $payrollItem) {
        $html .= '<tr>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['idnumber'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['name'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . (isset($payrollItem['department']) ? $payrollItem['department'] : 'N/A') . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['overallTotalHoursPerDay'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['month'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['year'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['day'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . $payrollItem['hourlyRate'] . '</td>';
        $html .= '<td style="border: 1px solid black;">' . number_format($payrollItem['totalPay'], 2, '.', ',') . '</td>';
        $html .= '</tr>';
    }

    // Close the HTML content
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body></html>';

    // Load the HTML into the PDF object
    $pdf->loadHtml($html);

    // Set the paper size and orientation
    $pdf->setPaper('A4', 'portrait');

    // Render the PDF
    $pdf->render();

    // Output the PDF as a download
    $pdf->stream('payroll_report.pdf', ['Attachment' => true]);
}


ob_end_flush(); 
?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">View Monthly Payroll</h4>
            <a href="dashboard.php" class="btn btn-danger float-end">Back</a>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="monthFilter" class="form-label">Select Month:</label>
                    <select class="form-select" id="monthFilter" name="month">
                        <!-- Add options for months (1 to 12) -->
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?= $i ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="yearFilter" class="form-label">Select Year:</label>
                    <select class="form-select" id="yearFilter" name="year">
                        <!-- Add options for years (current year - 5 to current year + 5) -->
                        <?php
                        $currentYear = date('Y');
                        $startYear = $currentYear - 5;
                        $endYear = $currentYear + 5;

                        for ($i = $startYear; $i <= $endYear; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dayFilter" class="form-label">Select Day:</label>
                    <select class="form-select" id="dayFilter" name="day">
                        <!-- Add options for days (1 to 31) -->
                        <?php for ($i = 1; $i <= 31; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="departmentFilter" class="form-label">Select Department:</label>
                    <select class="form-select" id="departmentFilter" name="department">
                        <!-- Add options for departments -->
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $department) : ?>
                            <option value="<?= $department ?>"><?= $department ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Filter</button>
            
            <!-- End Filter Form -->

            <!-- Payroll Data -->
            <?php if (!empty($payrollData)) : ?>
                <div class="mt-4">
                    <h5>Filtered Payroll Data:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Overall Total Hours</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Day</th>
                                <th>Hourly Rate</th>
                                <th>Total Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payrollData as $payrollItem) : ?>
                                <tr>
                                    <td><?= $payrollItem['idnumber'] ?></td>
                                    <td><?= $payrollItem['name'] ?></td>
                                    <td><?= isset($payrollItem['department']) ? $payrollItem['department'] : 'N/A' ?></td>
                                    <td><?= $payrollItem['overallTotalHoursPerDay'] ?></td>
                                    <td><?= $payrollItem['month'] ?></td>
                                    <td><?= $payrollItem['year'] ?></td>
                                    <td><?= $payrollItem['day'] ?></td>
                                    <td><?= $payrollItem['hourlyRate'] ?></td>
                                    <td><?= number_format($payrollItem['totalPay'], 2, '.', ',') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </form>
                    <!-- Export to PDF button -->
                    <form method="POST" action="">
                        <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                        <input type="hidden" name="year" value="<?= $selectedYear ?>">
                        <input type="hidden" name="day" value="<?= $selectedDay ?>">
                        <input type="hidden" name="department" value="<?= $selectedDepartment ?>">
                        <button type="submit" name="export" class="btn btn-primary">Export to PDF</button>
                    </form>
                </div>
            <?php endif; ?>
            <!-- End Payroll Data -->
        </div>
    </div>
</div>