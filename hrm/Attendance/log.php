<?php
include '../connection.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $time_in = $_POST['time_in'];
    $time_out = $_POST['time_out'];
    $status = $_POST['status'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $hire_date = $_POST['hire_date'];
    $position = $_POST['position'];
    $department = $_POST['department'];

    // Validate input
    $errors = [];

    if (empty($employee_id) || !is_numeric($employee_id)) {
        $errors[] = "Valid Employee ID is required.";
    }

    if (empty($attendance_date) || !strtotime($attendance_date)) {
        $errors[] = "Valid Date is required.";
    }

    if (empty($time_in) || !strtotime($time_in)) {
        $errors[] = "Valid Time In is required.";
    }

    if (empty($time_out) || !strtotime($time_out)) {
        $errors[] = "Valid Time Out is required.";
    }

    if (empty($status)) {
        $errors[] = "Status is required.";
    }

    if (empty($first_name) || !preg_match("/^[a-zA-Z]+$/", $first_name)) {
        $errors[] = "Valid First Name is required (only letters).";
    }

    if (empty($last_name) || !preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $errors[] = "Valid Last Name is required (only letters).";
    }

    if (empty($birth_date) || !strtotime($birth_date) || (strtotime($birth_date) > strtotime('-18 years'))) {
        $errors[] = "Valid Birth Date is required (must be over 18 years old).";
    }

    if (empty($hire_date) || !strtotime($hire_date) || (strtotime($hire_date) > time())) {
        $errors[] = "Valid Hire Date is required (cannot be in the future).";
    }

    if (empty($position) || !preg_match("/^[a-zA-Z]+$/", $position)) {
        $errors[] = "Valid Position is required (only letters).";
    }

    if (empty($department) || !preg_match("/^[a-zA-Z]+$/", $department)) {
        $errors[] = "Valid Department is required (only letters).";
    }

    // Format date and time values for Oracle
    if (empty($errors)) {
        $attendance_date = date('Y-m-d', strtotime($attendance_date));
        $time_in = date('Y-m-d H:i:s', strtotime($attendance_date . ' ' . $time_in));
        $time_out = date('Y-m-d H:i:s', strtotime($attendance_date . ' ' . $time_out));

        // Prepare the SQL statement
        $sql = 'BEGIN LogAttendance(:employee_id, TO_DATE(:attendance_date, \'YYYY-MM-DD\'), TO_TIMESTAMP(:time_in, \'YYYY-MM-DD HH24:MI:SS\'), TO_TIMESTAMP(:time_out, \'YYYY-MM-DD HH24:MI:SS\'), :status); END;';

        try {
            $stmt = $connection->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':attendance_date', $attendance_date);
            $stmt->bindParam(':time_in', $time_in);
            $stmt->bindParam(':time_out', $time_out);
            $stmt->bindParam(':status', $status);

            // Execute the statement
            $result = $stmt->execute();

            if ($result) {
                header("Location: view.php");
                exit;
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Error logging attendance: " . htmlentities($errorInfo[2]);
            }
        } catch (PDOException $e) {
            echo "Error logging attendance: " . $e->getMessage();
        }
    } else {
        foreach ($errors as $error) {
            echo "<p class='text-danger'>" . htmlentities($error) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Attendance</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Log Attendance
        <a href="view.php" class="btn btn-primary btn-sm">Back</a>
        </h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="employee_id">Employee ID:</label>
                <input type="number" name="employee_id" id="employee_id" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="attendance_date">Date:</label>
                <input type="date" name="attendance_date" id="attendance_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="time_in">Time In:</label>
                <input type="time" name="time_in" id="time_in" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="time_out">Time Out:</label>
                <input type="time" name="time_out" id="time_out" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Vacation">Vacation</option>
                </select>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="birth_date">Birth Date:</label>
                <input type="date" name="birth_date" id="birth_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="hire_date">Hire Date:</label>
                <input type="date" name="hire_date" id="hire_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" name="position" id="position" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Log Attendance</button>
        </form>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
