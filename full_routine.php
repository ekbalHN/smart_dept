<?php
// Securely connecting the external database configuration 
require_once 'db/dbconnection.php';

try {
    // 1. Fetch all unique time slots for the table headers
    $slots_query = "SELECT * FROM time_slots ORDER BY start_time ASC";
    $stmt_slots = $pdo->query($slots_query); // Using query() for static retrieval
    $slots = $stmt_slots->fetchAll();

    // 2. Fetch all schedule data with Joins [cite: 38, 51]
    // This allows the system to highlight Paper Name, Faculty, and Room Number dynamically
    $schedule_query = "SELECT s.*, p.paper_name, prof.name as prof_name, t.id as slot_id
                      FROM schedule s
                      JOIN papers p ON s.paper_id = p.id
                      JOIN professors prof ON s.professor_id = prof.id
                      JOIN time_slots t ON s.slot_id = t.id";
    $stmt_schedule = $pdo->query($schedule_query);

    // Organize data into a 3D array: [Day][Semester][SlotID] [cite: 31, 38]
    // This solves the problem of static PDF routines by allowing real-time digital views
    $full_routine = [];
    while($row = $stmt_schedule->fetch()) {
        $full_routine[$row['day']][$row['semester']][$row['slot_id']] = $row;
    }

    // Defining the structure for the tabular visualization [cite: 1, 3]
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $semesters = ['2nd', '4th', '6th'];

} catch (PDOException $e) {
    // Gracefully handling database errors to maintain professional interface [cite: 61]
    die("Error fetching routine: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Dept | Full Class Routine</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <style>
        .table-routine th { background-color: #007bff; color: white; text-align: center; vertical-align: middle !important; }
        .table-routine td { text-align: center; vertical-align: middle !important; min-width: 120px; }
        .day-cell { background-color: #f4f6f9; font-weight: bold; width: 100px; }
        .sem-cell { font-weight: 500; color: #555; width: 80px; }
        .class-box { font-size: 0.85rem; }
        .prof-name { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<div class="wrapper p-4">
    <div class="card shadow">
        <div class="mt-3 mb-2 mx-2 justify-content-end d-flex">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        </div>
        <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Department of Computer Science - Full Routine</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-routine m-0">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Sem</th>
                            <?php foreach($slots as $slot): ?>
                                <th><?php echo date('H:i', strtotime($slot['start_time'])) . "-" . date('H:i', strtotime($slot['end_time'])); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($days as $day): ?>
                            <?php foreach($semesters as $index => $sem): ?>
                                <tr>
                                    <?php if($index === 0): ?>
                                        <td rowspan="3" class="day-cell"><?php echo strtoupper($day); ?></td>
                                    <?php endif; ?>
                                    
                                    <td class="sem-cell">BSc(<?php echo $sem; ?>)</td>

                                    <?php foreach($slots as $slot): ?>
                                        <td>
                                            <?php 
                                            if(isset($full_routine[$day][$sem][$slot['id']])): 
                                                $class = $full_routine[$day][$sem][$slot['id']];
                                            ?>
                                                <div class="class-box">
                                                    <strong><?php echo $class['paper_name']; ?></strong><br>
                                                    <span class="prof-name">(<?php echo $class['prof_name']; ?>)</span><br>
                                                    <small class="text-muted">Room: <?php echo $class['room_number']; ?></small>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    
</div>
<footer class="card-footer">
          <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Smart Dept</a>.
    </strong> All rights reserved.
</footer>

</body>
</html>