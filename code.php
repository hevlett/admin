<?php

include_once( __DIR__ . '../config/function.php');

//saving admins
if (isset($_POST['saveAdmin'])) {
    $name = validate($_POST['name']);
    $password = validate($_POST['password']);
    $department = validate($_POST['department']);

    // Proceed with inserting the new record
    if (!empty($name) && !empty($password)) {
        $bcrypt_password = password_hash($password, PASSWORD_BCRYPT);

        $data = [
            'name' => $name,
            'password' => $bcrypt_password,
            'department' => $department,
        ];

        $result = insert('admins', $data);

        if ($result) {
            redirect('admins.php', 'Admin Created Successfully!');
        } else {
            redirect('admins-create.php', 'Something Went Wrong!');
        }
    } else {
        redirect('admins-create.php', 'Please fill required fields.');
    }
}
//updating admins
if (isset($_POST['updateAdmin'])) {
    $adminId = validate($_POST['adminId']);

    $adminData = getById('admins', $adminId);
    if ($adminData['status'] != 200) {
        redirect('admins-edit.php?id=' . $adminId, 'Please fill required fields.');
    }

    $name = validate($_POST['name']);
    $password = validate($_POST['password']);
    $department = validate($_POST['department']);

    // Check if the password field is not empty
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $adminData['data']['password'];

    // Update only if the required fields are not empty
    if (!empty($name)) {
        $data = [
            'name' => $name,
            'department' => $department,
        ];

        // Include password only if it's provided
        if (!empty($password)) {
            $data['password'] = $hashedPassword;
        }

        $result = update('admins', $adminId, $data);

        if ($result) {
            redirect('admins.php?id=' . $adminId, 'Admin Updated Successfully!');
        } else {
            redirect('admins-edit.php?id=' . $adminId, 'Something Went Wrong!');
        }
    } else {
        redirect('admins-edit.php?id=' . $adminId, 'Please fill required fields.');
    }
}
    //saving employees
    if (isset($_POST['saveEmployee'])) {
        $name = validate($_POST['name']);
        $idnumber = validate($_POST['idnumber']);
        $department = validate($_POST['department']);
        $prDay = validate($_POST['prd']);
        $prMonth = validate($_POST['prm']);
        $prHr = validate($_POST['phr']);
    
        // Check if the ID number already exists in the employee table
        $stmt = mysqli_prepare($conn, "SELECT * FROM employee WHERE idnumber = ?");
        mysqli_stmt_bind_param($stmt, "s", $idnumber);
    
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
    
            if ($result && mysqli_num_rows($result) > 0) {
                mysqli_stmt_close($stmt);
                redirect('employee-create.php', 'ID Number Already Used.');
                exit(); // Stop further processing since the ID is already in use in the employee table
            }
        } else {
            // Handle the error appropriately
            redirect('employee-create.php', 'Error checking ID existence.');
            exit();
        }
    
        mysqli_stmt_close($stmt);
    
        // Proceed with inserting the new record into the employee table
        if (!empty($name) && !empty($idnumber) && !empty($department)) {
            $employeeData = [
                'name' => $name,
                'idnumber' => $idnumber,
                'department' => $department,
                'pDay' => $prDay,
                'pMonth' => $prMonth,
                'pHr' => $prHr,
            ];
    
            // Use prepared statement for insertion into the employee table
            $employeeResult = insert('employee', $employeeData);
    
            if ($employeeResult) {
                //  a 'payslip' table with columns: employee_id, salary, bonuses, deductions, net_pay, pay_date
                 {
                    redirect('employee.php', 'Employee Created Successfully!');
                } 
            } else {
                redirect('employee-create.php', 'Something Went Wrong!');
            }
        } else {
            redirect('employee-create.php', 'Please fill required fields.');
        }
    }
    
    //updating employees
    if (isset($_POST['updateEmployee'])) {
        $employeeId = validate($_POST['employeeId']);
    
        $adminData = getById('employee', $employeeId);
        if ($adminData['status'] != 200) {
            redirect('employee-edit.php?id=' . $employeeId, 'Please fill required fields.');
        }
    
        $name = validate($_POST['name']);
        $idnumber = validate($_POST['idnumber']);
        $department = validate($_POST['department']);
        $prDay = validate($_POST['prd']);
        $prMonth = validate($_POST['prm']);
        $prHr = validate($_POST['phr']);
    
        // Proceed with updating the employee record
        if (!empty($name) && !empty($idnumber) && !empty($department)) {
            $data = [
                'name' => $name,
                'idnumber' => $idnumber,
                'department' => $department,
                'pDay' => $prDay,
                'pMonth' => $prMonth,
                'pHr' => $prHr,
            ];
    
            // Use the correct ID for the employee record
            $result = update('employee', $employeeId, $data);
    
            if ($result) {
                redirect('employee.php?id=' . $employeeId, 'Employee Updated Successfully!');
            } else {
                redirect('employee-edit.php?id=' . $employeeId, 'Something Went Wrong!');
            }
        } else {
            redirect('employee-edit.php', 'Please fill required fields.');
        }
    }
    
// Assign payroll
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assignPayroll'])) {
        // Get form data
        $employeeId = isset($_POST['employeeIdInput']) ? $_POST['employeeIdInput'] : null;
        $day = isset($_POST['day']) ? $_POST['day'] : null;
        $month = isset($_POST['month']) ? $_POST['month'] : null;
        $year = isset($_POST['year']) ? $_POST['year'] : null;
        $totalHoursPerDay = isset($_POST['totalHoursPerDay']) ? $_POST['totalHoursPerDay'] : null;
        $overallTotalHoursPerDay = isset($_POST['overallTotalHoursPerDay']) ? $_POST['overallTotalHoursPerDay'] : null;

        // Time In and Time Out values for each day
        $timeIn = [];
        $timeOut = [];

        for ($i = 1; $i <= 15; $i++) {
            $timeIn[] = isset($_POST["timeIn{$i}"]) ? $_POST["timeIn{$i}"] : null;
            $timeOut[] = isset($_POST["timeOut{$i}"]) ? $_POST["timeOut{$i}"] : null;
        }

        // Call the function to insert the payroll
        $result = insertPayslip($conn, $employeeId, $month, $year, $totalHoursPerDay, $overallTotalHoursPerDay, $day, $timeIn, $timeOut);

        // Check the result and display a message
        if ($result['status'] === 200) {
            redirect('payroll.php', 'Payroll assigned successfully.');
        } elseif ($result['status'] === 500) {
            redirect('create-payroll.php', 'Error assigning payroll: ' . $result['message']);
        }
    }
}




        
      
?>
    
   
