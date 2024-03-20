<?php
// Include necessary files
include_once('includes/header.php');
header('Content-Type: text/html; charset=utf-8');

function calculateTotalHoursWorked($timein, $timeout) {
    // Convert time strings to Unix timestamps
    $timein_unix = strtotime($timein);
    $timeout_unix = strtotime($timeout);

    // If timeout is earlier than timein, it means timeout crossed midnight
    if ($timeout_unix < $timein_unix) {
        // Add 24 hours to timeout to account for crossing midnight
        $timeout_unix = strtotime('+1 day', $timeout_unix);
    }

    // Calculate the time difference
    $time_difference = $timeout_unix - $timein_unix;

    // Convert time difference to total hours worked
    $total_hours_worked = $time_difference / (60 * 60);

    return $total_hours_worked;
}


function convertFractionalTimeToTimeFormat($fractionalTime) {
    $hours = floor($fractionalTime * 24);
    $minutes = round(($fractionalTime * 24 * 60) % 60);
    return sprintf("%02d:%02d %s", ($hours % 12 ?: 12), $minutes, ($hours < 12 ? 'AM' : 'PM'));
}

function isTimeValue($value) {
    // Check if the value is a time (fractional) value
    return is_numeric($value) && $value >= 0 && $value < 1;
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "payroll";
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

//
// Set character set for the connection
mysqli_set_charset($connection, "utf8mb4");

// Prepare the SQL statement
$query = "INSERT INTO employee (name, idnumber, department, pcutoff, pMonth, timein, timeout, dayoff1, dayoff2, dayoff3, dayoff4, dayoff5,totalhrswork) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

// Prepare the statement
$stmt = mysqli_prepare($connection, $query);

// Check if the statement preparation succeeded
if (!$stmt) {
    die("Error in preparing statement: " . mysqli_error($connection));
}

// Bind parameters
mysqli_stmt_bind_param($stmt, 'sssssssssssss', $names, $ids, $department, $per_cut_value, $perMonth, $timein, $timeout, $dayoff1, $dayoff2, $dayoff3, $dayoff4, $dayoff5,$totalhrswork);
// Get the uploaded file path
$file_tmp = $_FILES["excel_file"]["tmp_name"];


// Load the Excel file
$excel = new COM("Excel.Application") or die("Unable to open Excel");
$excel->Visible = 0;

// Open the Excel file
$workbook = $excel->Workbooks->Open($file_tmp);

// Get the first (active) worksheet
$worksheet = $workbook->Worksheets(1);

// Start generating the HTML table with Bootstrap classes
echo "<div class='table-responsive'>";
echo "<table class='table table-bordered'>";
echo "<thead class='thead-dark'><tr>";

// Read headers from B3 to K3 and display as table headers
for ($col = 1; $col <= 11; $col++) { // Columns B to K
    $cell_value = $worksheet->Cells(3, $col)->Value;
    echo "<th scope='col'>$cell_value</th>";
}
echo "<th scope='col'>Per Cut</th>";
echo "</tr></thead>";
echo "<tbody>";

// Read data and insert into the database
for ($row = 4; $row <= 168; $row++) { // Rows 4 to 168
    // Check if the value of column B (cell B$row) is null
    $cell_value = $worksheet->Cells($row, 2)->Value;
    if ($cell_value === null) {
        // Break the loop if the cell value is null
        break;
    }

    // Start a new row in the table body
    echo "<tr>";

    // Read and display data from columns B to K for the current row
    for ($col = 1; $col <= 11; $col++) { // Columns B to K
        $cell_value = $worksheet->Cells($row, $col)->Value;
        // Check if the value is a time (fractional) value
        if (isTimeValue($cell_value)) {
            // Convert fractional time to time format if the cell is not empty
            $cell_value = convertFractionalTimeToTimeFormat($cell_value);
        }
        echo "<td>$cell_value</td>";
    }

    // Calculate the "Per Cut" value from TIME-IN column (column 4)
    $per_cut_value = $worksheet->Cells($row, 4)->Value / 2;
    echo "<td>$per_cut_value</td>";

    // Read data from the Excel sheet
    $ids = $worksheet->Cells($row, 1)->Value;
    $department = $worksheet->Cells($row, 2)->Value;
    $departments = "test3";
 
    // // Sanitize and handle the 'name' value
    $names = $worksheet->Cells($row,3)->Value;
    $names = mb_convert_encoding($names, 'UTF-8', 'UTF-8');
    $perMonth = $worksheet->Cells($row, 4)->Value;


    
    $timein = $worksheet->Cells($row, 5)->Value;
    $timeout = $worksheet->Cells($row, 6)->Value;
         // Convert fractional time to time format if the cell is not empty
         if (isTimeValue($timein)) {
            $timein = convertFractionalTimeToTimeFormat($timein);
        }
        
        // Convert fractional time to time format if the cell is not empty
        if (isTimeValue($timeout)) {
            $timeout = convertFractionalTimeToTimeFormat($timeout);
        }
     $dayoff1 = $worksheet->Cells($row, 7)->Value;
    $dayoff2 = $worksheet->Cells($row, 8)->Value;
    $dayoff3 = $worksheet->Cells($row, 9)->Value;
    $dayoff4 = $worksheet->Cells($row, 10)->Value;
    $dayoff5 = $worksheet->Cells($row, 11)->Value;
    
    
    // $departments = $worksheet->Cells($row, 3)->Value;
    // $department = mb_convert_encoding($department, 'UTF-8', 'auto');


    $totalhrswork = calculateTotalHoursWorked($timein, $timeout);

    // Execute the statement
    $success = mysqli_stmt_execute($stmt);

    // Check if execution failed
    if (!$success) {
        echo "Error in inserting data: " . mysqli_error($connection);
    }

    // End the row
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";



// Close the Excel file without saving changes
$workbook->Close(false);
$excel->Quit();
$excel = null;
mysqli_stmt_close($stmt);

// Close connection
mysqli_close($connection);
?>
