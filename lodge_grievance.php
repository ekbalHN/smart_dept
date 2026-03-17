<?php
require_once 'db/dbconnection.php';

// Success/Error Message Logic
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // In production, fetch these from session: $_SESSION['name'] and $_SESSION['roll']
    $s_name = "Ekbal Hussain";
    $s_roll = "US-231-182-0064";

    try {
        $sql = "INSERT INTO grievances (student_name, student_roll, category, priority, subject, description) 
                VALUES (:name, :roll, :cat, :pri, :sub, :desc)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $s_name,
            ':roll' => $s_roll,
            ':cat' => $category,
            ':pri' => $priority,
            ':sub' => $subject,
            ':desc' => $description
        ]);
        $message = "<div class='alert alert-success'>Grievance submitted successfully! Status: Pending.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Submission failed: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lodge Grievance | Smart Dept</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-outline card-warning shadow">
                    <div class="card-header">
                        <h3 class="card-title text-dark">
                            <i class="fas fa-edit mr-2"></i> Submit New Grievance
                        </h3>
                    </div>
                    <form action="" method="POST">
                        <div class="card-body">
                            <?php echo $message; ?>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control" required>
                                        <option value="Infrastructure">Infrastructure (Labs/Furniture)</option>
                                        <option value="Academic">Academic (Syllabus/Classes)</option>
                                        <option value="Examination">Examination</option>
                                        <option value="PC Issue">PC/Lab Equipment Issue</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Priority Level <span class="text-danger">*</span></label>
                                    <select name="priority" class="form-control" required>
                                        <option value="Low">Low (General Query)</option>
                                        <option value="Medium">Medium (Attention Needed)</option>
                                        <option value="High">High (Serious Issue)</option>
                                        <option value="Urgent">Urgent (Immediate Action)</option>
                                    </select>
                                    <small class="text-muted">Urgent issues notify admin immediately.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control"
                                    placeholder="Short summary of the issue" required>
                            </div>

                            <div class="form-group">
                                <label>Detailed Description</label>
                                <textarea name="description" class="form-control" rows="5"
                                    placeholder="Explain the issue in detail..." required></textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                            <button type="submit" class="btn btn-warning font-weight-bold">
                                <i class="fas fa-paper-plane mr-1"></i> Submit Grievance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>