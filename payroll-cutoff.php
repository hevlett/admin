<?php 
include_once('includes/header.php');

// Database connection
$conn = new mysqli("localhost", "root", "", "payroll");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$result = $conn->query("SELECT * FROM cutoff");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bootstrap Table with Eye Icon</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">Table with Eye Icon</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Month</th>
        <th>Year</th>
        <th>Cutoff</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>{$row["month"]}</td>";
              echo "<td>{$row["year"]}</td>";
              echo "<td>{$row["cutoffs"]}</td>";
              $url = "payroll-view2.php?month={$row["month"]}&year={$row["year"]}&cutoffs={$row["cutoffs"]}";
              echo "<td><a href='{$url}' class='btn btn-link'><i class='fas fa-eye'></i></a></td>";              
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No data available</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>

<?php
$conn->close();
include('includes/footer.php');
?>
