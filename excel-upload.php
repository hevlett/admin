<?php
include_once('includes/header.php');

// Create connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "payroll";
$connection = mysqli_connect($servername, $username, $password, $dbname);

$monthNames = array(
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
);

// Function to convert month number to name
function convertToMonthName($monthNumber) {
    global $monthNames;
    
    // Check if the month number exists in the array
    if (array_key_exists($monthNumber, $monthNames)) {
        return $monthNames[$monthNumber];
    } else {
        return "Invalid month number";
    }
}

function processExcelData($timeInColumn, $timeOutColumn, $dayRow, $dayColumn, $daysrow) {
    global $connection;

    // Check if the form is submitted and the file is uploaded
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["excel_file"])) {
        // Create a new instance of the COM object for Excel
        $excel = new COM("Excel.Application") or die("Unable to open Excel");

        // Make Excel visible (optional)
        $excel->Visible = true;

        // Get the uploaded file details
        $file_name = $_FILES["excel_file"]["tmp_name"];

        // Open the Excel file
        $workbook = $excel->Workbooks->Open($file_name);

        // Select the active worksheet
        $worksheet = $workbook->Worksheets(1);

        // Set starting row and column for ID
        $currentRowID = 5; // starting row
        $currentColumnID = 3; // column C

        // Set starting row and column for Name
        $currentRowName = 5; // starting row
        $currentColumnName = 11; // column K

        // Set starting row and column for Department
        $currentRowDepartment = 5; // starting row
        $currentColumnDepartment = 21; // column U

        // Set starting row for Time In and Time Out
        $startRowTimeInOut = 6;

        //year
        $currentRowYear = 3; // starting row
        $currentColumnYear = 3; // column A

        // Get year and day values
        $cellValueYear = $worksheet->Cells($currentRowYear, $currentColumnYear)->Value;
        $cellValueDay = $worksheet->Cells($dayRow, $dayColumn)->Value;

        $year = substr($cellValueYear, 0, 4);
        $month = substr($cellValueYear, 5, 2);
        $day = substr($cellValueYear, 8, 2);
        $cutoff = "";
        if ($day == "01") {
            $cutoff = "1st";
        } else {
            $cutoff = "2nd";
        }
        $value = $worksheet->Cells(4, $daysrow)->Value;
        $months = convertToMonthName($month);

        // Start generating the HTML table
        echo '<div style="margin: 0 auto; width: 100%; margin-left: 30%;">'; // Centering the table
        echo '<table border="1">';
        echo ' <caption>' . $cellValueDay . '</caption>';
        echo '<br>';
        echo $year;
        echo '<br>';
        echo $months;
        echo '<br>';
        echo $day;
        echo '<br>';
        echo $cutoff;
        echo '<br>';
        echo $value;
        echo '<br>';

        echo '<thead><tr><th style="margin: 0 30px;">ID</th><th style="margin: 0 30px;">Name</th><th style="margin: 0 30px;">Department</th><th style="margin: 0 30px;">Time In</th><th style="margin: 0 30px;">Time Out</th><th style="margin: 0 30px;">Total Hours</th></tr></thead>';
        echo '<tbody>';

 // Prepare the SQL statement for insertion
$sql = "INSERT INTO time_tracker (employee_id, name, day, timein, timeout, year, month, totalhrs, cutoff) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($connection, $sql);

// Read data until the cell in column C is null
while ($worksheet->Cells($currentRowID, $currentColumnID)->Value != "") {
    // Read data from the cells for ID, Name, Department, Time In, and Time Out
    $cellValueID = $worksheet->Cells($currentRowID, $currentColumnID)->Value;
    $cellValueName = $worksheet->Cells($currentRowName, $currentColumnName)->Value;
    $cellValueDepartment = $worksheet->Cells($currentRowDepartment, $currentColumnDepartment)->Value;
    $cellValueTimeIn = $worksheet->Cells($startRowTimeInOut, $timeInColumn)->Value;
    $cellValueTimeOut = $worksheet->Cells($startRowTimeInOut, $timeOutColumn)->Value;

    // Format Time In to 12-hour format if it's not empty, otherwise set to "Absent"
    $cellValueTimeIn = !empty($cellValueTimeIn) ? date('h:i A', strtotime(substr($cellValueTimeIn, 0, 5))) : "Absent";

    // Format Time Out to 12-hour format if it's not empty, otherwise set to "No Time Out"
    $cellValueTimeOut = !empty($cellValueTimeOut) ? date('h:i A', strtotime(substr($cellValueTimeOut, -5))) : "No Time Out";

    // Calculate the difference in seconds if both Time In and Time Out are not "Absent" or "No Time Out"
    if ($cellValueTimeIn != "Absent" && $cellValueTimeOut != "No Time Out") {
        $timeIn = strtotime($cellValueTimeIn);
        $timeOut = strtotime($cellValueTimeOut);
        $difference = $timeOut - $timeIn;
        $totalHours = round($difference / 3600, 2); // Convert the difference to hours
        echo "<tr><td style='margin: 0 30px;'>$cellValueID</td><td style='margin: 0 30px;'>$cellValueName</td><td style='margin: 0 30px;'>$cellValueDepartment</td><td style='margin: 0 30px;'>$cellValueTimeIn</td><td style='margin: 0 30px;'>$cellValueTimeOut</td><td style='margin: 0 30px;'>$totalHours</td></tr>";
    } else {
        $totalHours = 0; // Set total hours to 0 if either Time In or Time Out is absent
        echo "<tr><td style='margin: 0 30px;'>$cellValueID</td><td style='margin: 0 30px;'>$cellValueName</td><td style='margin: 0 30px;'>$cellValueDepartment</td><td style='margin: 0 30px;'>$cellValueTimeIn</td><td style='margin: 0 30px;'>$cellValueTimeOut</td><td style='margin: 0 30px;'>N/A</td></tr>";
    }

    // Bind parameters and execute the SQL statement
    mysqli_stmt_bind_param($stmt, "sssssssss", $cellValueID, $cellValueName, $value, $cellValueTimeIn, $cellValueTimeOut, $year, $months, $totalHours, $cutoff);
    mysqli_stmt_execute($stmt);

    // Move to the next row
    $currentRowID += 2; // increment row by 2 for the next set of data for ID
    $currentRowName += 2; // increment row by 2 for the next set of data for Name
    $currentRowDepartment += 2; // increment row by 2 for the next set of data for Department
    $startRowTimeInOut += 2; // increment row by 2 for the next set of data for Time In and Time Out
}

// Close the statement


        // End generating the HTML table
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // End of centered div

        // Close the statement
        mysqli_stmt_close($stmt);

        // Close the workbook
        $workbook->Close();

        // Quit Excel application
        $excel->Quit();

        // Release COM objects
        unset($worksheet);
        unset($workbook);
        unset($excel);
    } else {
        // If the form is not submitted or file is not uploaded, redirect back to the form page
        header("Location: upload-excel.php");
        exit;
    }
}

// Example usage
for ($i = 1; $i <= 15; $i++) {
    processExcelData($i, $i, 4, $i, $i);
}

// Close the connection
mysqli_close($connection);
?>

