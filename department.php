<?php

include 'config/function.php';

$departments = getUniqueDepartments($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Filter</title>
</head>

<body>
    <form>
        <label for="department">Select Department:</label>
        <select name="department" id="department" onchange="loadEmployees()" required>
            <?php
            if (count($departments) > 0) {
                foreach ($departments as $departmentItem) {
                    echo "<option value='{$departmentItem}'>{$departmentItem}</option>";
                }
            } else {
                echo '<option value="" disabled>No departments found</option>';
            }
            ?>
        </select>

        <label for="employeeIdName">Select Employee:</label>
        <select name="employeeIdName" id="employeeIdName" class="form-control" required>
        <?php
                            // Retrieve the list of employees with their departments
                            $employees = getAll('employee');
                            if (mysqli_num_rows($employees) > 0) {
                                foreach ($employees as $employeeItem) {
                                    // Display both the name and department
                                    echo "<option value='{$employeeItem['id']}'>{$employeeItem['name']}</option>";
                                }
                            } else {
                                echo '<option value="" disabled>No employees found</option>';
                            }
                            ?>
        </select>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function loadEmployees() {
            var department = $('#department').val();
            var employeeSelect = $('#employeeId');

            // Clear previous options
            employeeSelect.empty();

            // Fetch employees based on the selected department using AJAX
            if (department) {
                $.ajax({
                    url: 'code.php',
                    type: 'GET',
                    data: { department: department },
                    dataType: 'json',
                    success: function (employees) {
                        employees.forEach(function (employee) {
                            var option = $('<option>').val(employee.id).text(employee.name);
                            employeeSelect.append(option);
                        });
                    },
                    error: function () {
                        console.error('Error fetching employees.');
                    }
                });
            }
        }
    </script>
</body>

</html>



