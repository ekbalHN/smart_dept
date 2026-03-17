<?php
// db_connect.php logic included here
$conn = mysqli_connect("localhost", "root", "", "smart_dept");

if (isset($_POST['add_schedule'])) {
    $day = $_POST['day'];
    $sem = $_POST['semester'];
    $paper = $_POST['paper_id'];
    $prof = $_POST['professor_id'];
    $slot = $_POST['slot_id'];
    $room = $_POST['room'];

    // Algorithm: Check for Professor Conflict (Is the teacher already busy?)
    $check = mysqli_query($conn, "SELECT * FROM schedule WHERE day='$day' AND slot_id='$slot' AND professor_id='$prof'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Conflict: Professor is already assigned to another class at this time!";
    } else {
        $sql = "INSERT INTO schedule (day, semester, paper_id, professor_id, slot_id, room_number) 
                VALUES ('$day', '$sem', '$paper', '$prof', '$slot', '$room')";
        mysqli_query($conn, $sql);
        $success = "Schedule entry added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Smart Dept - Admin Dashboard</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="mt-3 mb-2 mx-2 justify-content-end d-flex">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Generate Class Schedule</h3>
                    </div>
                    <form method="POST" action="">
                        <div class="card-body">
                            <?php if (isset($error))
                                echo "<div class='alert alert-danger'>$error</div>"; ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Day</label>
                                    <select name="day" class="form-control" required>
                                        <option>Monday</option>
                                        <option>Tuesday</option>
                                        <option>Wednesday</option>
                                        <option>Thursday</option>
                                        <option>Friday</option>
                                        <option>Saturday</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control">
                                        <option id="2nd-sem">2nd</option>
                                        <option id="4th-sem">4th</option>
                                        <option id="6th-sem">6th</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Room Number</label>
                                    <input type="text" name="room" class="form-control" placeholder="e.g. F-04"
                                        required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Paper Name</label>
                                    <select name="paper_id" class="form-control">
                                        <?php
                                        $res = mysqli_query($conn, "SELECT * FROM papers");
                                        while ($row = mysqli_fetch_assoc($res))
                                            echo "<option value='" . $row['id'] . "'>" . $row['paper_name'] . "</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Professor Name</label>
                                    <select name="professor_id" class="form-control">
                                        <option value="">-- Select Professor --</option>
                                        <?php
                                        $res = mysqli_query($conn, "SELECT * FROM professors");
                                        while ($row = mysqli_fetch_assoc($res))
                                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Time Slot</label>
                                    <select name="slot_id" class="form-control">
                                        <?php
                                        $res = mysqli_query($conn, "SELECT * FROM time_slots");
                                        while ($row = mysqli_fetch_assoc($res))
                                            echo "<option value='" . $row['id'] . "'>" . $row['start_time'] . " - " . $row['end_time'] . "</option>";
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 justify-content-center">
                                <button type="submit" class="btn btn-outline-primary mb-3 my-3" value="add"
                                    name="add_schedule">Add to Schedule</button>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Smart Dept</a>.</strong> All
                            rights reserved.
                        </footer>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>

</html>