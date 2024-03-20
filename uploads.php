<?php
function military_to_standard_time($time) {
    return date("h:i ", strtotime($time));
}


function calculate_late($time_in) {
    $late_seconds = 0;
    $standard_time_in = date("H:i", strtotime($time_in));
    $standard_time_in_hours = intval(substr($standard_time_in, 0, 2));
    $standard_time_in_minutes = intval(substr($standard_time_in, 3));

    if ($standard_time_in_hours < 7 || ($standard_time_in_hours == 7 && $standard_time_in_minutes > 0)) {
        $late_seconds = ($standard_time_in_hours - 7) * 3600 + $standard_time_in_minutes * 60;
    }

    return gmdate("H:i:s", $late_seconds);
}
// Check if file was uploaded without errors
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

        // Start from row 5
        $row = 5;
        
        
        // Initialize HTML table
        echo "<div style='text-align: center;'>";
        echo "<table border='1' style='margin: 0 auto;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Time In )</th><th>Time Out </th></tr>";

        // Loop until ID in column C is null
        while (!is_null($worksheet->Cells($row, 3)->Value)) {
            // Read values from cells
            $id = $worksheet->Cells($row, 3)->Value;
            $name = $worksheet->Cells($row, 11)->Value;
            $time_in = $worksheet->Cells($row + 1, 1)->Value;

            // Extract first 5 characters
            $first_5_chars = substr($time_in, 0, 5);

            // Extract last 5 characters
            $last_5_chars = substr($time_in, -5);
            $late_time = calculate_late($time_in);

            // Check if first 5 characters and last 5 characters are the same
            if ($first_5_chars === $last_5_chars) {
                $last_5_chars = "No Time Out";
            }

            // Display data if all values are not null
            if (!is_null($id) && !is_null($name) && !is_null($time_in)) {
                // Output data in HTML table row
                $time_in_standard = military_to_standard_time($time_in);
                // $late = military_to_standard_time($late_time);

                if($last_5_chars === "No Time Out"){
                    echo "<tr><td>$id</td><td>$name</td><td>$time_in_standard</td><td>$last_5_chars</td><td>$late_time</td></tr>";
                }else{
                    $time_out_standard = military_to_standard_time($last_5_chars);
                    
                    echo "<tr><td>$id</td><td>$name</td><td>$time_in_standard</td><td>$time_out_standard</td><td>$late_time</td></tr>";
                }

               
            }

            // Move to the next set of data (increment row by 4)
            $row += 4;
        }
        
        // Close the HTML table and div
        echo "</table>";
        echo "</div>";

        // Close the Excel file
        $excel->Quit();
        $excel = null;
    } else {
        echo "Only .xlsx and .xls files are allowed.";
    }
} else {
    echo "Error uploading file.";
}
?>
