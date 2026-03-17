<?php
require_once 'db/dbconnection.php';

// Setting timezone to ensure local time matches the schedule [cite: 31, 32]
date_default_timezone_set('Asia/Kolkata');

// Assume the student is in the 6th Semester [cite: 13, 62, 64]
$user_sem = '6th';

// Fetch Current Day and Time
$current_day = date('l');
$current_time = date('H:i:s');

try {
    // 1. Logic for ONGOING Class [cite: 38, 44]
    $ongoing_query = "SELECT s.*, p.paper_name, prof.name as prof_name, t.start_time, t.end_time 
                      FROM schedule s
                      JOIN papers p ON s.paper_id = p.id
                      JOIN professors prof ON s.professor_id = prof.id
                      JOIN time_slots t ON s.slot_id = t.id
                      WHERE s.day = :current_day 
                      AND s.semester = :user_sem
                      AND :current_time BETWEEN t.start_time AND t.end_time 
                      LIMIT 1";

    $stmt1 = $pdo->prepare($ongoing_query);
    $stmt1->execute([
        ':current_day' => $current_day,
        ':user_sem' => $user_sem,
        ':current_time' => $current_time
    ]);
    $ongoing_class = $stmt1->fetch();

    // 2. Logic for NEXT Class [cite: 38]
    $next_query = "SELECT s.*, p.paper_name, prof.name as prof_name, t.start_time, t.end_time 
                   FROM schedule s
                   JOIN papers p ON s.paper_id = p.id
                   JOIN professors prof ON s.professor_id = prof.id
                   JOIN time_slots t ON s.slot_id = t.id
                   WHERE s.day = :current_day 
                   AND s.semester = :user_sem
                   AND t.start_time > :current_time
                   ORDER BY t.start_time ASC 
                   LIMIT 1";

    $stmt2 = $pdo->prepare($next_query);
    $stmt2->execute([
        ':current_day' => $current_day,
        ':user_sem' => $user_sem,
        ':current_time' => $current_time
    ]);
    $next_class = $stmt2->fetch();

} catch (PDOException $e) {
    echo "Error fetching schedule: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Dept | Student Dashboard</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-md navbar-light soft-dark sticky-top shadow-sm">
        <div class="container px-3">
            <a href="#" class="navbar-brand d-flex align-items-center text-decoration-none gap-2">
                <img src="images/smart_dept.webp" alt="Smart Dept Logo" class="logo">
                <div class="d-flex align-items-center flex-wrap">
                    <span class="title-text">Smart</span>
                    <span class="title-text ms-1" style="color:
                    rgb(46, 17, 17); font-weight: 500;">Dept</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center text-center mt-3 mt-md-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#project_details">Project</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Team</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                    <li class="nav-item d-none d-md-block"><span class="nav-link">|</span></li>
                    <li class="nav-item ms-md-2 mt-2 mt-md-0 dropdown">
                        <a class="nav-link " href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="admin-badge">
                                <i class="fa-solid fa-user"></i>
                                <span class="ms-2 dropdown-toggle">Login</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://www.cpdal.in/CMS/index.html">Student</a></li>
                            <li><a class="dropdown-item" href="#">Professor</a></li>
                            <li><a class="dropdown-item" href="#">Admin</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="hero-section py-5 d-flex align-items-center position-relative overflow-hidden">
        <div class="glow-base glow-top-right"></div>
        <!-- <div class="glow-base glow-bottom-left"></div>
        <div class="glow-base glow-right"></div> -->
        <!-- <div class="glow-base glow-left"></div> -->

        <div class="container position-relative py-2" style="z-index: 2;">
            <div class="row align-items-center">

                <div class="col-12 col-md-6">
                    <div class="card card-outline card-danger shadow">
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-light border-bottom border-danger">
                            <h3 class="card-title text-grey mb-0">
                                <span class="live-indicator mr-2"></span> Class Live Now
                            </h3>

                            <div class="header-digital-clock text-right">
                                <div id="liveDay" class="text-red small font-weight-bold text-uppercase mb-0"
                                    style="letter-spacing: 1.5px; line-height: 1;">
                                    <?php echo date('l'); ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="liveDate" class="text-muted small mr-2">
                                        <?php echo date('M d, Y'); ?>
                                    </span>&nbsp;&nbsp;&nbsp;
                                    <span id="liveClock" class="text-grey font-weight-bold"
                                        style="font-family: 'Courier New', Courier, monospace; font-size: 1.5rem; text-shadow: 0 0 8px rgba(208, 233, 235, 0.6);">
                                        00:00:00
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($ongoing_class): ?>
                                <h2 class="text-primary font-weight-bold">
                                    <?php echo $ongoing_class['paper_name']; ?>
                                </h2>
                                <p class="lead mb-1"><strong>With</strong>
                                    <?php echo $ongoing_class['prof_name']; ?>
                                </p>
                                <p class="badge badge-info p-2" style="font-size: 1rem;">
                                    <i class="fas fa-door-open"></i> Venue:
                                    <?php echo $ongoing_class['room_number']; ?>
                                </p>
                                <hr>
                                <p class="text-muted small">Ends at:
                                    <?php echo date('h:i A', strtotime($ongoing_class['end_time'])); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted py-4">No active class at this moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card card-outline card-primary shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock mr-2"></i> Up Next</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($next_class): ?>
                                <h2 class="text-secondary">
                                    <?php echo $next_class['paper_name']; ?>
                                </h2>
                                <p class="lead mb-1"><strong>With</strong>
                                    <?php echo $next_class['prof_name']; ?>
                                </p>
                                <p class="badge badge-secondary p-2" style="font-size: 1rem;">
                                    <i class="fas fa-map-marker-alt"></i> Venue:
                                    <?php echo $next_class['room_number']; ?>
                                </p>
                                <hr>
                                <p class="text-primary font-weight-bold">Starts at:
                                    <?php echo date('h:i A', strtotime($next_class['start_time'])); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted py-4">No more classes scheduled for today.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center mb-2">
                    <div class="profile-container">
                        <div class="outer-circle"></div>
                        <div class="inner-circle"></div>
                        <img src="images/team.webp" alt="3D Character" class="character-image img-fluid">
                    </div>
                </div>

            </div>
            <div class="row py-2 align-items-center">
                <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
                    <img src="images/support.png" class="img-support" height="50%" width="100%" alt="support">
                </div>

                <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
                    <h2 class="hero-title text-center">Get Support</h2>
                    <p class="lead mt-3">
                        Your Ease, is Our Goal! Access your class schedule,
                        view upcoming classes, and stay informed about any changes in real-time.
                        With our user-friendly interface, you can easily navigate through your
                        daily routine and never miss a beat.
                    </p>
                
                    <!-- <h4 class="mb-3">Quick Actions</h4> -->
                    <div class="d-flex flex-wrap gap-5 justify-content-center pt-3">
                        <a href="full_routine.php" class="btn btn-app bg-success">
                            <i class="fas fa-calendar-alt"></i> View Full Routine
                        </a>
                        <a href="lodge_grievance.php" class="btn btn-app bg-warning">
                            <i class="fas fa-exclamation-triangle"></i> Lodge Grievance
                        </a>

                    </div>
                </div>
            </div>



        </div>
        </div>
    </header>

    <div id="project_details" style="display: block; position: relative; top: -100px; visibility: hidden;"></div>

    <section class="project-details py-5">
        <div class="glow-base glow-top-right"></div>
        <div class="container">
            <div class="text-center mb-5">


                <h5 class="text-uppercase text-primary font-weight-bold tracking-wider">Academic Project 2025-2026</h5>
                <h2 class="display-5 font-weight-bold text-dark">
                    <?php echo "Pandit Deendayal Upadhyaya Adarsha Mahavidyalaya (PDUAM), Dalgaon"; ?>
                </h2>
                <p class="lead font-italic" style="color: red; font-size: 36px;">Department of Computer Science </p>
                <hr class="w-50 mx-auto border-danger" style="border-width: 3px;">
            </div>


            <div
                class="row mb-5 shadow-lg p-4 bg-white rounded-lg border-top border-primary position-relative overflow-hidden">
                <div class="position-absolute" style="right: -20px; bottom: -20px; opacity: 0.05; z-index: 0;">
                    <i class="fas fa-code fa-10x"></i>
                </div>

                <div class="col-md-8 position-relative" style="z-index: 1;">
                    <h3 class="text-primary mb-3">
                        <i class="fas fa-project-diagram mr-2"></i> Project Title: "Smart Dept"
                    </h3>
                    <p class="text-justify leading-relaxed">
                        <strong>Description:</strong> An Integrated Grievance Redressal & Dynamic Class Scheduler System
                        designed to digitize academic routines and streamline problem reporting within the
                        department. This web-based application aims to bridge the gap between students
                        and administration through real-time communication tools.
                    </p>
                </div>

                <div class="col-md-4 border-left position-relative" style="z-index: 1;">
                    <h5 class="text-dark font-weight-bold mb-3">Quick Specifications</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-graduation-cap text-primary mr-2"></i>
                            <strong>Semester:</strong> 6th Semester (FYUGP Honors)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-laptop-code text-primary mr-2"></i>
                            <strong>Project Type:</strong> Web Application Development
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <strong>Tech Stack:</strong> PHP, MySQL, Bootstrap
                        </li>
                        <li>
                            <i class="fas fa-users text-primary mr-2"></i>
                            <strong>Group:</strong> Group B
                        </li>
                    </ul>
                </div>
            </div>

            <div id="team" style="display: block; position: relative; top: -100px; visibility: hidden;"></div>

            <h4 class="text-center mb-4" style="color: #380808; ">The Development Team</h4>
            <div class="row justify-content-center shadow-sm p-4 bg-white rounded border-top border-primary">

                <div class="col-lg-8 col-md-12 mb-5">
                    <div class="member-card shadow p-3 bg-light rounded border border-primary">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/supervisor.jpeg"
                                        class="img-fluid rounded-circle shadow border border-primary"
                                        alt="Dr. Zaved Iqubal Ahmed"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h4 class="mb-1 font-weight-bold" style="color: #380808;">Dr. Zaved Iqubal Ahmed</h4>
                                <div class="mb-2">
                                    <span class="badge badge-primary">Project Supervisor</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Assistant Professor, HoD
                                </p>
                                <p class="small text-dark mb-0">
                                    Department of Computer Science, PDUAM Dalgaon
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="member-card shadow-sm p-3 bg-white rounded">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/ekbal.jpeg"
                                        class="img-fluid rounded-circle shadow-sm border border-info"
                                        alt="Mr. Ekbal Hussain" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h5 class="mb-1 font-weight-bold text-left">Mr. Ekbal Hussain</h5>
                                <div class="mb-2">
                                    <span class="badge badge-info">Group Coordinator</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-university mr-1"></i> B.Sc 6th Sem (Computer Science)
                                </p>
                                <p class="small text-primary font-weight-bold mb-0">
                                    <i class="fas fa-laptop-code mr-1"></i> Full Stack Developer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="member-card shadow-sm p-3 bg-white rounded">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/manikul.jpeg" class="img-fluid rounded-circle shadow-sm border"
                                        alt="Mr. Manikul Islam" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h5 class="mb-1 font-weight-bold text-left">Mr. Manikul Islam</h5>
                                <div class="mb-2">
                                    <span class="badge badge-secondary">Developer</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-university mr-1"></i> B.Sc 6th Sem (Computer Science)
                                </p>
                                <p class="small text-dark font-weight-bold mb-0">
                                    <i class="fas fa-database mr-1"></i> Database Manager
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="member-card shadow-sm p-3 bg-white rounded">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/khusbar.jpeg" class="img-fluid rounded-circle shadow-sm border"
                                        alt="Mr. Khusbar Ali" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h5 class="mb-1 font-weight-bold text-left">Mr. Khusbar Ali</h5>
                                <div class="mb-2">
                                    <span class="badge badge-secondary">Developer</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-university mr-1"></i> B.Sc 6th Sem (Computer Science)
                                </p>
                                <p class="small text-dark font-weight-bold mb-0">
                                    <i class="fas fa-code mr-1"></i> UI Designer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="member-card shadow-sm p-3 bg-white rounded">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/ruksana.jpeg" class="img-fluid rounded-circle shadow-sm border"
                                        alt="Ms. Ruksana Parbin"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h5 class="mb-1 font-weight-bold text-left">Ms. Ruksana Parbin</h5>
                                <div class="mb-2">
                                    <span class="badge badge-secondary">Developer</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-university mr-1"></i> B.Sc 6th Sem (Computer Science)
                                </p>
                                <p class="small text-dark font-weight-bold mb-0">
                                    <i class="fas fa-swatchbook mr-1"></i> UI Designer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="member-card shadow-sm p-3 bg-white rounded">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 text-center">
                                <div class="img-frame">
                                    <img src="images/hamidul.jpeg" class="img-fluid rounded-circle shadow-sm border"
                                        alt="Mr. Hamidul Hoque" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-8 col-sm-9 text-left">
                                <h5 class="mb-1 font-weight-bold text-left">Mr. Hamidul Hoque</h5>
                                <div class="mb-2">
                                    <span class="badge badge-secondary">Developer</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-university mr-1"></i> B.Sc 6th Sem (Computer Science)
                                </p>
                                <p class="small text-dark font-weight-bold mb-0">
                                    <i class="fas fa-paint-brush mr-1"></i> UI Designer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>


    <footer class="card-footer text-center">
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Department of Computer Science</a>.</strong> All
        rights reserved.
    </footer>

    <script src="node_modules\sweetalert2\dist\sweetalert2.all.min.js"></script>
    <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome\js\all.min.js"></script>
    <script src="script.js"></script>

</body>

</html>