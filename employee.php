<?php
include_once('includes/header.php');
require_once ('config/function.php');


$recordsPerPage = 15;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

$departments = getUniqueDepartments($conn);
$selectedDepartment = '';

if (isset($_GET['department']) && !empty($_GET['department'])) {
    $selectedDepartment = $_GET['department'];
    $employeesResult = getEmployeesByDepartmentWithLimit($conn, $selectedDepartment, $offset, $recordsPerPage);
    $totalPages = ceil(getTotalEmployeeRecordsByDepartment($conn, $selectedDepartment) / $recordsPerPage);
} else {
    $employeesResult = getEmployeesWithLimit($conn, $offset, $recordsPerPage);
    $totalPages = ceil(getTotalEmployeeRecords($conn) / $recordsPerPage);
}


?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                Employees
                <a href="employee-create.php" class="btn btn-primary float-end ms-2">Add Employee</a>
                <a href="employee-list-cutoff.php" class="btn btn-primary float-end ms-2">Print Employees</a>
                <a href="dashboard.php" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <!-- Filter Form -->
            <form method="GET" action="">
                <div class="mb-3">
                    <label for="departmentFilter" class="form-label">Filter by Department:</label>
                    <select class="form-select" id="departmentFilter" name="department">
                        <option value="" <?php echo (!$selectedDepartment) ? 'selected' : ''; ?>>All Departments</option>
                        <?php foreach ($departments as $departmentItem) : ?>
                            <option value="<?= $departmentItem ?>" <?php echo ($selectedDepartment == $departmentItem) ? 'selected' : ''; ?>><?= $departmentItem ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </form>
            <!-- End Filter Form -->

            <div class="table-responsive mt-3">
                <table id="employeeTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_array($employeesResult['data'])) {
                            foreach ($employeesResult['data'] as $employeeItem) :
                        ?>
                                <tr>
                                    <td><?= isset($employeeItem['idnumber']) ? $employeeItem['idnumber'] : '' ?></td>
                                    <td><?= isset($employeeItem['name']) ? $employeeItem['name'] : '' ?></td>
                                    <td><?= isset($employeeItem['department']) ? $employeeItem['department'] : '' ?></td>
                                    <td>
                                        <a href="employee-edit.php?id=<?= $employeeItem['employee_id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="employee-delete.php?id=<?= $employeeItem['employee_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                        <a href="employee-dtr-date.php?id=<?= $employeeItem['employee_id'] ?>" class="btn btn-danger btn-sm">DTR</a>
                                        <a href="employee-payslip-cutoff.php?id=<?= $employeeItem['employee_id'] ?>" class="btn btn-danger btn-sm">Payslip</a>
                                    </td>
                                </tr>
                        <?php endforeach;
                        } else {
                        ?>
                            <tr>
                                <td colspan="4">No Record Found</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

                <div class="pagination-container d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php if ($currentPage > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&department=<?= urlencode($selectedDepartment) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&department=<?= urlencode($selectedDepartment) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&department=<?= urlencode($selectedDepartment) ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
