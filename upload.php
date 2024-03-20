<?php
include_once('includes/header.php');
function military_to_standard_time($time) {
    $datetime = DateTime::createFromFormat('H:i', $time);
    return $datetime ? $datetime->format('h:i A') : 'Absent';
}

function calculate_late($time_in) {
    if (empty($time_in) || $time_in === "No Time In") {
        return "Absent";
    }

    $standard_time_in = date("H:i", strtotime($time_in));
    $standard_time_in_hours = intval(substr($standard_time_in, 0, 2));
    $standard_time_in_minutes = intval(substr($standard_time_in, 3));

    if ($standard_time_in_hours < 7 || ($standard_time_in_hours == 7 && $standard_time_in_minutes <= 0)) {
        return "Not Late";
    } else {
        $late_minutes = ($standard_time_in_hours - 7) * 60 + $standard_time_in_minutes;
        $hours = floor($late_minutes / 60);
        $minutes = $late_minutes % 60;
        $late_time = sprintf("%02d:%02d:00", $hours, $minutes);
        return $late_time;
    }
}



function processExcelFile($day ,$id, $name ,$timeRow, $timeColoumn) {
    if(isset($_FILES["excel_file"]) && $_FILES["excel_file"]["error"] == 0){
        $file_name = $_FILES["excel_file"]["name"];
        $file_tmp = $_FILES["excel_file"]["tmp_name"];
        
        // Check file extension
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if($file_ext === "xlsx" || $file_ext === "xls"){
            // Open the Excel file
            $excel = new COM("Excel.Application") or die("Unable to open Excel");
            $excel->Visible = 0;
            $excel->Workbooks->Open($file_tmp);
            
            // Get the active worksheet
            $worksheet = $excel->ActiveSheet;

            // Get the value of A4 cell
            $column_index = $day; 
            $day_title = $worksheet->Cells(4, $column_index)->Value;

            //get date
            $getDate = 3; // C column is 3
            $date_title = $worksheet->Cells(3, $getDate)->Value;
            
            // Start from row 5
          

            // Initialize HTML table
            echo "<div style='text-align: center;'>";
            echo "<h2>$day_title</h2>";
           
            echo "<table border='1' style='margin: 0 auto;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Time In</th><th>Time Out</th><th>Late</th><th>Year</th><th>Month</th></tr>";

            // Connect to your database
            $servername = "localhost";
            $username = "root"; // Replace with your MySQL username
            $password = ""; // Replace with your MySQL password
            $dbname = "payroll";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
                $row = 5;
        
            // Loop until ID in column C is null
            while (!is_null($worksheet->Cells($row, $id)->Value)) {
                // Read values from cells
                $employee_id = $worksheet->Cells($row, $id)->Value;
                $employee_name = $worksheet->Cells($row, $name)->Value;
                $time_in_value = $worksheet->Cells($row + $timeRow, $timeColoumn)->Value;
                $day_value = $worksheet->Cells($row, $column_index)->Value;

                // Extract first 5 characters
                $first_5_chars = substr($time_in_value, 0, 5);
                $first_10_chars = substr($date_title,0,10);
                $getYear = substr($first_10_chars,0,4);

                $date = "2024-01-31";
                $month = date("m", strtotime($first_10_chars)); // This will return "01" for January
                

                // Extract last 5 characters
                $last_5_chars = substr($time_in_value, -5);

                // Check if first 5 characters and last 5 characters are the same
                // if ($first_5_chars === $last_5_chars) {
                //     $last_5_chars = "No Time Out";
                // }

                $convertedTimeIn = military_to_standard_time($first_5_chars);
                $convertedTimeOut = military_to_standard_time($last_5_chars);

                if($convertedTimeIn === $convertedTimeOut){
                    $convertedTimeOut = "No Time Out";
                }   

       
            
                $late_time = calculate_late($convertedTimeIn);

              
                if($convertedTimeIn == "Absent"){
                    $convertedTimeOut = "Absent";
                    $late_time = "Absent";
                }
                // echo "<tr><td>$employee_id</td><td>$employee_name</td><td>$convertedTimeIn</td><td>$convertedTimeOut</td><td>$late_time</td><td>$getYear</td><td>$month</td></tr>";   
         
                $working_days = "11";
                $daily_wage = "610";
                $total_salary = $working_days * $daily_wage;
          

                // $sql_check = "SELECT * FROM time_tracker WHERE employee_id = '$employee_id' AND day = '$day_title' AND month = '$month'";
                // $result = $conn->query($sql_check);
                
                // if ($result->num_rows > 0) {
                //     // Data already exists, so skip insertion
                //     echo "Data already exists for employee ID: $employee_id, day: $day_title, month: $month. Skipping insertion.<br>";
                // } else {
                //     // Data does not exist, proceed with insertion
                //     $sql = "INSERT INTO time_tracker (employee_id, name, day, timein, timeout, year, month, late)
                //             VALUES ('$employee_id', '$employee_name', '$day_title', '$convertedTimeIn', '$convertedTimeOut', '$getYear', '$month', '$late_time')";
                
                //     if ($conn->query($sql) === TRUE) {
                //         echo "<tr><td>$employee_id</td><td>$employee_name</td><td>$convertedTimeIn</td><td>$convertedTimeOut</td><td>$late_time</td><td>$getYear</td><td>$month</td></tr>";   
                //     } else {
                //         echo "Error: " . $sql . "<br>" . $conn->error;
                //     }
                // }
                
        
 
                // Move to the next set of data (increment row by 2)
                $row += 2;
            }
           


            // Close the HTML table
            echo "</table>";
            echo "</div>";

            // Close the Excel file
            $excel->Quit();
            $excel = null;

            // Close the database connection
            $conn->close();
        } else {
            echo "Only .xlsx and .xls files are allowed.";
        }
    } else {
        echo "Error uploading file.";
    }
}

$parameters = array(
    array(1, 3, 11, 1, 1), // Parameters for the first call
    array(2, 3, 11, 1, 2),  // Parameters for the second call
    array(3, 3, 11, 1, 3),
    array(4, 3, 11, 1, 4),
    array(5, 3, 11, 1, 5),
    array(6, 3, 11, 1, 6),
    array(7, 3, 11, 1, 7),
    array(8, 3, 11, 1, 8),
    array(9, 3, 11, 1, 9),
    array(10, 3, 11, 1, 10),
    array(11, 3, 11, 1, 11),
    array(12, 3, 11, 1, 12),
    array(13, 3, 11, 1, 13),
    array(14, 3, 11, 1, 14),
    array(15, 3, 11, 1, 15),
    array(16, 3, 11, 1, 16)
);

foreach ($parameters as $params) {
    processExcelFile($params[0], $params[1], $params[2], $params[3], $params[4]);
}
?>
