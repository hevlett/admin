<?php 
include_once('includes/header.php');

$servername = "localhost";
$username = "root";
$password = "";
$database = "payroll";

$conn = new mysqli($servername, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO cutoff (cutoffs, month, year) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST["cutOff"], $_POST["month"], $_POST["year"]);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rxO3XcMqmI7t3ho9HkP+Qu7cnU0E+W1jMM8c7a9XGE+CuPc2aiTpAen+z4odGWPr" crossorigin="anonymous">
  <style>
    body { background-color: #f8f9fa; }
    .container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); }
    h1 { margin-bottom: 30px; color: #007bff; }
  </style>
</head>
<body>
  <div class="container" style="margin-top: 10%;">
    <h1>Create Payroll</h1>
    <form method="post" action="create-payrolls.php">
      <div class="mb-3">
        <label for="cutOff" class="form-label">Cut Off</label>
        <select class="form-select" id="cutOff" name="cutOff">
            <option selected disabled>Select a Cut Off</option>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="month" class="form-label">Month</label>
        <select class="form-select" id="month" name="month">
            <option selected disabled>Select a Month</option>
            <option value="January">January</option>
            <option value="February">February</option>
            <option value="March">March</option>
            <option value="April">April</option>
            <option value="May">May</option>
            <option value="June">June</option>
            <option value="July">July</option>
            <option value="Agust">Agust</option>
            <option value="September">September</option>
            <option value="October">October</option>
            <option value="November">November</option>
            <option value="December">December</option>
            <!-- Add other months here -->
        </select>
      </div>
      <div class="mb-3">
          <label for="year" class="form-label">Year</label>
          <select class="form-select" id="year" name="year">
              <option selected disabled>Select a Year</option>
              <?php
              $currentYear = date("Y");
              $startYear = $currentYear;
              $endYear = $currentYear - 10; // Assuming you want to go back 10 years

              for ($year = $startYear; $year >= $endYear; $year--) {
                  echo "<option value=\"$year\">$year</option>";
              }
              ?>
          </select>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Create</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-0Wt3xfex+ozOotLp6O1zXc2q0BnABfMDr0H3NV/qSfR5z7vGpUHpACmOZss2hG5Y" crossorigin="anonymous"></script>
</body>
</html>

<?php include('includes/footer.php'); ?>
