<?php

session_start();

require 'db.php';

// Define the validate function if it's not already defined
if (!function_exists('validate')) {
    function validate($inputData) {
        global $conn;
        $validatedData = mysqli_real_escape_string($conn, $inputData);
        return trim($validatedData);
    }
}

// Define the redirect function if it's not already defined
if (!function_exists('redirect')) {
    function redirect($url, $message = null, $status = null) {
        if ($message !== null) {
            $_SESSION['status'] = $message;
        }

        if ($status !== null) {
            // Set additional status, if needed
        }

        header('Location: ' . $url);
        exit;
    }
}

// Display messages or status after any process.
if (!function_exists('alertMessage')) {

    // Declare the alertMessage function
    function alertMessage() {
        if (isset($_SESSION['status'])) {
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h6>' . $_SESSION['status'] . '</h6>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            unset($_SESSION['status']);
        }
    }

}


// Check if the function is not defined before declaring it
if (!function_exists('insert')) {

    // Declare the insert function
    function insert($tableName, $data)
    {
        global $conn;

        $table = validate($tableName);

        $columns = array_keys($data);
        $values = array_values($data);

        $finalColumn = implode(',', $columns);
        $finalValues = "'" . implode("', '", $values) . "'";

        $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
        // echo "Generated Query: $query"; // Add this line to print the query
        $result = mysqli_query($conn, $query);
        return $result;
    }

}

// update data using this function
// Check if the function is not defined before declaring it
if (!function_exists('update')) {

    // Declare the update function
    function update($tableName, $id, $data)
    {
        global $conn;

        $table = validate($tableName);
        $id = validate($id);

        $updateData = array();
        $params = array();

        foreach ($data as $column => $value) {
            $column = validate($column);
            $updateData[] = "$column = ?";
            $params[] = $value;
        }

        $params[] = $id;

        $updateDataString = implode(", ", $updateData);

        $query = "UPDATE $table SET $updateDataString WHERE id = ?";

        $stmt = mysqli_prepare($conn, $query);

        // Dynamically bind parameters
        $bindTypes = str_repeat("s", count($params));
        mysqli_stmt_bind_param($stmt, $bindTypes, ...$params);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);

        return $result;
    }

}


// Check if the function is not defined before declaring it
if (!function_exists('getAll')) {

    // Declare the getAll function
    function getAll($tableName, $status = NULL)
    {
        global $conn;

        $table = validate($tableName);
        $status = validate($status);

        if ($status == 'status') {
            $query = "SELECT * FROM $table WHERE $status'0'";
        } else {
            $query = "SELECT * FROM $table";
        }

        return mysqli_query($conn, $query);
    }

}

// Check if the function is not defined before declaring it
if (!function_exists('getById')) {

    // Declare the getById function
    function getById($tableName, $id)
    {
        global $conn;

        $table = validate($tableName);
        $id = validate($id);

        $query = "SELECT * FROM $table WHERE name='$id' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            if (mysqli_num_rows($result) == 1) {

                $row = mysqli_fetch_assoc($result);
                $response = [
                    'status' => 200,
                    'data' => $row,
                    'message' => 'Record Found'
                ];
                return $response;

            } else {
                $response = [
                    'status' => 404,
                    'message' => 'No Data Found'
                ];
                return $response;
            }

        } else {
            $response = [
                'status' => 500,
                'message' => 'Something went Wrong'
            ];
            return $response;
        }
    }

}

// Check if the function is not defined before declaring it
if (!function_exists('delete')) {

    // Declare the delete function
    function delete($tableName, $id)
    {
        global $conn;

        // Validate input
        $table = validate($tableName);
        $id = validate($id);

        // Check if the table has a foreign key constraint
        if ($table === 'employee') {
            // Before deleting an employee, it needs to handle related records in other tables,
            // such as 'payslip'. In this case, needs to delete or update related records first.

            // Example: Delete related payslips
            $payslipQuery = "DELETE FROM payslip WHERE employee_id = ?";
            $payslipStmt = mysqli_prepare($conn, $payslipQuery);

            if ($payslipStmt) {
                mysqli_stmt_bind_param($payslipStmt, "i", $id);
                $payslipResult = mysqli_stmt_execute($payslipStmt);

                // Check if the execution was successful
                if (!$payslipResult) {
                    // Handle the error if needed
                    mysqli_stmt_close($payslipStmt);
                    return false;
                }

                mysqli_stmt_close($payslipStmt);
            } else {
                // Handle the error if needed
                return false;
            }
        }

        // Continue with the deletion of the main record
        $query = "DELETE FROM $table WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, "i", $id);

            // Execute the statement
            $result = mysqli_stmt_execute($stmt);

            // Check if the execution was successful
            if ($result) {
                return true; // Success
            } else {
                // Error handling
                return false; // Error
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Error handling
            return false; // Error
        }
    }

}


// Check if the function is not defined before declaring it
if (!function_exists('checkParamId')) {

    // Declare the checkParamId function
    function checkParamId($type)
    {
        if (isset($_GET[$type]) && $_GET[$type] !== '') {
            return $_GET[$type];
        } else {
            return null;
        }
    }

}
    

// if (!function_exists('fetchEmployeeList')) {

//     // Declare the fetchEmployeeList function
//     function fetchEmployeeList() {
//         $connection = connectToDatabase();

//         $query = "SELECT * FROM employees";
//         $result = mysqli_query($connection, $query);
//         $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);

//         mysqli_close($connection);

//         return $employees;
//     }

// }


// Check if the function is not defined before declaring it
if (!function_exists('getEmployeeById')) {

    // Declare the getEmployeeById function
    function getEmployeeById($conn, $employeeId)
    {
        $sql = "SELECT * FROM employee WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $employeeId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $employeeInfo = mysqli_fetch_assoc($result);

                if ($employeeInfo) {
                    return $employeeInfo;
                } else {
                    echo "No data found for employee ID: $employeeId"; // Debug statement
                }
            } else {
                echo "Error fetching result: " . mysqli_error($conn); // Debug statement
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn); // Debug statement
        }

        return null;
    }

}


// Check if the function is not defined before declaring it
if (!function_exists('getDepartmentByEmployeeId')) {

    // Declare the getDepartmentByEmployeeId function
    function getDepartmentByEmployeeId($conn, $employeeId) {
        $employeeId = mysqli_real_escape_string($conn, $employeeId);

        $sql = "SELECT department FROM employee WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $employeeId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            } else {
                return null;
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn); // Debug statement
        }

        return null;
    }

}


// Check if the function is not defined before declaring it
if (!function_exists('getPayslipById')) {

    // Declare the getPayslipById function
    function getPayslipById($payslipId) {
        global $conn;

        $query = "SELECT * FROM payslip WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $payslipId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $payslipData = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                return array('status' => 200, 'data' => $payslipData);
            } else {
                mysqli_stmt_close($stmt);
                return array('status' => 404, 'message' => 'Payroll not found');
            }
        } else {
            return array('status' => 500, 'message' => 'Error preparing statement.');
        }
    }

}

 // Check if the function is not defined before declaring it
if (!function_exists('getPayrollByEmployeeId')) {

    // Declare the getPayrollByEmployeeId function
    function getPayrollByEmployeeId($employeeId) {
        global $conn; // Declare $conn as a global variable to access it within the function

    
        $query = "SELECT * FROM payslip WHERE employee_id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            // Handle the error
            die("Error in preparing the SQL query: " . $conn->error);
        }

        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the data and return it
            $payrollData = $result->fetch_assoc();
            return ['status' => 200, 'data' => $payrollData];
        } else {
            // No payroll data found
            return ['status' => 404, 'message' => 'No payroll data found for the given employee ID'];
        }
    }

}


if (!function_exists('getCount')) {
    function getCount($tableName) {
        global $conn; 
    
        $query = "SELECT COUNT(*) AS count FROM $tableName";
        $result = mysqli_query($conn, $query);
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['count'];
        } else {
            return false;
        }
    }
}

if (!function_exists('getTotalRecords')) {
    function getTotalRecords($tableName) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) as total_records FROM $tableName");
            $stmt->execute();
    
            // Use get_result to fetch the result set
            $result = $stmt->get_result();
    
            // Fetch the result as an associative array
            $row = $result->fetch_assoc();
    
            // Return the total_records value
            return $row['total_records'];
        } catch (PDOException $e) {
            // Handle the exception as needed (e.g., log the error)
            die("Error: " . $e->getMessage());
        }
    }
}


// Function to get employees by department with the option to include duplicates
if (!function_exists('getEmployeesByDepartmentWithLimit')) {
    function getEmployeesByDepartmentWithLimit($conn, $department, $offset, $limit, $includeDuplicates = false) {
        $sql = "SELECT employee.id AS employee_id, employee.name, employee.idnumber, employee.department, payslip.totalPay, payslip.hourlyRate, payslip.month, payslip.year, payslip.day, payslip.overallTotalHoursPerDay
                FROM employee
                LEFT JOIN payslip ON employee.id = payslip.employee_id
                WHERE employee.department = ?
                ORDER BY employee.id DESC
                LIMIT ?, ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $department, $offset, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        $seenNames = []; // Array to keep track of seen names
    
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if the name is already seen
            if ($includeDuplicates || !in_array($row['name'], $seenNames)) {
                $data[] = $row;
                $seenNames[] = $row['name']; // Add the name to the seenNames array
            }
        }
        
        mysqli_stmt_close($stmt);
    
        return ['data' => $data, 'numRows' => count($data)];
    }
}


// Function to get employees by department without duplicates
if (!function_exists('getEmployeesByDepartmentWithoutDuplicates')) {
    function getEmployeesByDepartmentWithoutDuplicates($conn, $department, $offset, $limit) {
        return getEmployeesByDepartmentWithLimit($conn, $department, $offset, $limit, false);
    }
}


// Function to get employees with the option to include duplicates
if (!function_exists('getEmployeesWithLimit')) {
    function getEmployeesWithLimit($conn, $offset, $recordsPerPage, $includeDuplicates = false) {
        global $offset, $recordsPerPage;
        $sql = "SELECT employee.id AS employee_id, employee.name, employee.idnumber, employee.department, payslip.totalPay, payslip.hourlyRate, payslip.month, payslip.year, payslip.day, payslip.overallTotalHoursPerDay
                FROM employee
                LEFT JOIN payslip ON employee.id = payslip.employee_id
                ORDER BY employee.id DESC
                LIMIT ?, ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        mysqli_stmt_bind_param($stmt, "ii", $offset, $recordsPerPage);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        $seenNames = []; // Array to keep track of seen names
    
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if the name is already seen
            if ($includeDuplicates || !in_array($row['name'], $seenNames)) {
                $data[] = $row;
                $seenNames[] = $row['name']; // Add the name to the seenNames array
            }
        }
        
        mysqli_stmt_close($stmt);
    
        return ['data' => $data, 'numRows' => count($data)];
        // Example usage on the page where you want to display multiple names
    $employeesDataWithoutDuplicates = getEmployeesWithLimit($conn, $offset, $recordsPerPage, false);
    $employeesResultWithoutDuplicates = $employeesDataWithoutDuplicates['data'];
    }
}


if (!function_exists('getTotalEmployeeRecordsByDepartment')) {
    function getTotalEmployeeRecordsByDepartment($conn, $department) {
        $query = "SELECT COUNT(*) as total FROM employee WHERE department = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            die("Error: " . $conn->error);
        }
    
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}

if (!function_exists('getUniqueDepartments')) {
    function getUniqueDepartments($conn) {
        $departments = array();
        $query = "SELECT DISTINCT department FROM employee";
        $result = mysqli_query($conn, $query);
    
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $departments[] = $row['department'];
            }
        }
    
        return $departments;
    }
}


// total hours
if (!function_exists('getTotalHoursForEmployee')) {
    function getTotalHoursForEmployee($conn, $employeeId) {
        // Assume you have a database table named 'employee_hours' with columns 'employee_id' and 'hours'
        $sql = "SELECT SUM(totalHours) AS totalHours FROM payslip WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['totalHours'];
        } else {
            return 0; // Return 0 if no hours found for the employee
        }
    }
}


//total hours overall from day 1 to day 15
if (!function_exists('getOverallTotalHoursForEmployee')) {
    function getOverallTotalHoursForEmployee($conn, $employeeId) {
        $sql = "SELECT SUM(overallTotalHoursPerDay) AS overallTotalHours FROM payslip WHERE employee_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $employeeId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $overallTotalHours);
    
            if (mysqli_stmt_fetch($stmt)) {
                mysqli_stmt_close($stmt);
                return $overallTotalHours;
            }
        }
    
        return 0;
    }
    
}


if (!function_exists('getTotalEmployeeRecords')) {
    function getTotalEmployeeRecords($conn) {
        $query = "SELECT COUNT(*) as total FROM employee";
        $result = $conn->query($query);

        if (!$result) {
            die("Error: " . $conn->error);
        }

        $row = $result->fetch_assoc();
        return $row['total'];
    }
}


// Function to fetch records with limit
if (!function_exists('getAllWithLimit')) {
    function getAllWithLimit($table, $offset, $limit) {
        global $conn; 

        $query = "SELECT * FROM $table LIMIT $offset, $limit";
        $result = $conn->query($query); 

        if (!$result) {
            die("Error: " . $conn->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}


if (!function_exists('getDistinctDepartments')) {
    function getDistinctDepartments() {
        global $conn;
        $query = "SELECT DISTINCT department FROM employee";
        $result = mysqli_query($conn, $query);

        $departments = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = $row['department'];
        }

        return $departments;
    }
}

// to insert payslip
if (!function_exists('insertPayslip')) {
    function insertPayslip($conn, $employeeId, $month, $year, $totalHoursPerDay, $overallTotalHoursPerDay, $day, $timeIn, $timeOut) {
        // Fixed hourly rate
        $hourlyRate = 50;

        // Convert $overallTotalHoursPerDay to a float
        $overallTotalHoursPerDay = floatval($overallTotalHoursPerDay);

        // Convert timeIn and timeOut arrays to strings
        $timeInString = implode(',', $timeIn);
        $timeOutString = implode(',', $timeOut);

        $totalPay = $hourlyRate * $overallTotalHoursPerDay;

        // Check if the record already exists
        $sql = "SELECT * FROM payslip WHERE employee_id = ? AND month = ? AND year = ? AND day = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iisi", $employeeId, $month, $year, $day);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                return array('status' => 500, 'message' => 'Record already exists for the given employee, month, year, and day combination.');
            } else {
                // Insert a new record
                $sql = "INSERT INTO payslip (employee_id, month, year, hourlyRate, totalHours, totalPay, overallTotalHoursPerDay, day, timeIn, timeOut)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iissddiiss", $employeeId, $month, $year, $hourlyRate, $totalHoursPerDay, $totalPay, $overallTotalHoursPerDay, $day, $timeInString, $timeOutString);

                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    mysqli_stmt_close($stmt);
                    return array('status' => 200, 'message' => 'Payroll assigned successfully.');
                } else {
                    mysqli_stmt_close($stmt);
                    return array('status' => 500, 'message' => 'Error assigning payroll.');
                }
            }
        } else {
            return array('status' => 500, 'message' => 'Error preparing statement.');
        }
    }
}

//delete payroll
if (!function_exists('deletePayroll')) {
    function deletePayroll($conn, $employeeId, $month, $year, $day) {
        // Add appropriate validation for the parameters if needed

        $sql = "DELETE FROM payslip WHERE employee_id = ? AND month = ? AND year = ? AND day = ?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "iiii", $employeeId, $month, $year, $day);
        mysqli_stmt_execute($stmt);

        $result = ['status' => 200, 'message' => 'Payroll Deleted Successfully.'];

        if (mysqli_stmt_affected_rows($stmt) <= 0) {
            $result = ['status' => 400, 'message' => 'No matching payroll data found for deletion.'];
        }

        mysqli_stmt_close($stmt);
        return $result;
    }
}
// view payroll by month year and day
if (!function_exists('getPayrollByMonthYearDay')) {
    function getPayrollByMonthYearDay($conn, $selectedMonth, $selectedYear, $selectedDay, $selectedDepartment) {
        // You can modify this SQL query to include the department filter
        $sql = "SELECT employee.id AS employee_id, employee.name, employee.idnumber, employee.department, payslip.totalPay, payslip.hourlyRate, payslip.month, payslip.year, payslip.day, payslip.overallTotalHoursPerDay
                FROM employee
                LEFT JOIN payslip ON employee.id = payslip.employee_id
                WHERE payslip.month = ? AND payslip.year = ? AND payslip.day = ?";
        
        // Include the department filter in the SQL query if a department is selected
        if (!empty($selectedDepartment)) {
            $sql .= " AND employee.department = ?";
        }

        $sql .= " ORDER BY employee.id DESC";

        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters based on the presence of the department filter
        if (!empty($selectedDepartment)) {
            mysqli_stmt_bind_param($stmt, "iiis", $selectedMonth, $selectedYear, $selectedDay, $selectedDepartment);
        } else {
            mysqli_stmt_bind_param($stmt, "iii", $selectedMonth, $selectedYear, $selectedDay);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $data;
    }
}





    

?>