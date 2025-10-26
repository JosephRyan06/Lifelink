<?php
$required_role = 'doctor';
require_once '../../../php/check_session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Records</title>
    <link rel="stylesheet" href="donors_style.css">
</head>
<body>
    <div class="nav-bar">
        <div class="nav-container">
            <div class="logo">
                <img src="../../../image/logo.png" alt="logo">
                <h2 class="web-title">LifeLink</h2>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-link">
                        <a href="../dashboard_page/dashboard.php">Dashboard</a>
                        <hr class="nav-underline">
                    </li>
                    <li class="nav-link">
                        <a href="donors.php">Donors</a>
                        <hr class="default-nav">
                    </li>
                    <li class="nav-link">
                        <a href="../patients_page/patients.php">Patients</a>
                        <hr class="nav-underline">
                    </li>
                    <li class="nav-link">
                        <a href="../profile_page/profile_info.php">Profile</a>
                        <hr class="nav-underline">
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="content-container">
        <div class="all-donors">
            <h2>Donors List</h2>
            <hr class="interactive-underline">
            <br>
            <div class="table-container">
                <?php 
                    include_once 'donors_table.php'; 
                    displayAllDonors($conn);

                ?>
            </div>
        </div>

        <div class="status-donors">
            <h2>Donor Status</h2>
            <hr class="interactive-status">
            <br>
            <div class="status-section">
                <?php
                    echo "<div class='status-pending'>";
                    echo "<h2 class='other-text'>Pending</h2>";
                    displayDonorsByPending($conn);
                    echo "</div>";
                ?>

                <?php
                    echo "<div class='status-confirmed'>";
                    echo "<h2 class='other-text'>Confirmed</h2>";
                    displayDonorsByConfirmed($conn);
                    echo "</div>";
                ?>

                <?php
                    echo "<div class='status-declined'>";
                    echo "<h2 class='other-text'>Declined</h2>";
                    displayDonorsByDeclined($conn);
                    echo "</div>";
                ?>
            </div>
        </div>
    </div>
</body>
</html>

