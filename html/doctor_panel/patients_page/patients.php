<?php
$required_role = 'doctor';
require_once '../../../php/check_session.php';

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="patients_style.css">

        <title>LifeLink</title>
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
                                <a href="../donors_page/donors.php">Donors</a>
                                <hr class="nav-underline">
                            </li>
                            <li class="nav-link">
                                <a href="patients.php">Patients</a>
                                <hr class="default-nav">
                            </li>
                            <li class="nav-link">
                                <a href="../profile_page/profile_info.php">Profile</a>
                                <hr class="nav-underline">
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="content">
                <div class="content-container-1">
                    <div class="interactive-content">
                        <h2>Add Patients</h2>
                        <br>
                        <hr class="interactive-underline">
                        <br>
                        <?php

                        if (!isset($_SESSION['user_id'])) {
                            echo "<script>alert('Please log in to continue.'); window.location.href='../html/Log In.php';</script>";
                            exit();
                        }

                        // CREATE
                        if (isset($_POST['create'])) {
                            $user_id = $_SESSION['user_id'];
                            $status = 'Pending';
                            $name = $_POST['name'];
                            $age = $_POST['age'];
                            $gender = $_POST['gender'];
                            $organ_type = $_POST['organ_type'];
                            $blood_cell = $_POST['blood_cell'];
                            $blood_type = $_POST['blood_type'];
                            $location = $_POST['location'];

                            if (empty($gender)) {
                                    $gender = NULL;
                                }
                            if (empty($organ_type)) {
                                $organ_type = NULL;
                            }
                            if (empty($blood_cell)) {
                                $blood_cell = NULL;
                            }
                            if (empty($blood_type)) {
                                $blood_type = NULL;
                            }

                            $gender = ($gender === 'N/A') ? null : $gender;
                            $organ_type = ($organ_type === 'N/A') ? null : $organ_type;
                            $blood_cell = ($blood_cell === 'N/A') ? null : $blood_cell;
                            $blood_type = ($blood_type === 'N/A') ? null : $blood_type;

                            $sql = "INSERT INTO patients (user_id, status, name, age, gender, organ_type, blood_cell, blood_type, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ississsss", $user_id, $status, $name, $age, $gender, $organ_type, $blood_cell, $blood_type, $location);

                            if ($stmt->execute()) {
                                echo "<script>alert('Patient has been inserted!');</script>";
                                // Refresh page to show new record
                            } else {
                                echo "<script>alert('Error: ');</script>" . $stmt->error;
                            }

                            echo "<meta http-equiv='refresh' content='0'>";

                            $stmt->close();
                        }
                        ?>
                        </p>
                        <form method="POST">
                            <input type="text" name="name" placeholder="Name" class="add-patient" required>
                            <input type="number" name="age" placeholder="Age" min="1" max="120" class="add-patient" required>
                            <select id="gender" name="gender" class="add-patient">
                                <option value="" disabled selected>Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                                <option value="N/A">N/A</option>
                            </select>
                            <select id="organ_type" name="organ_type" class="add-patient">
                                <option value="" disabled selected>Needed Organ</option>
                                <option>Kidney</option>
                                <option>Liver</option>
                                <option>Heart</option>
                                <option>Lungs</option>
                                <option>Pancreas</option>
                                <option>Intestine</option>
                                <option>Cornea</option>
                                <option value="N/A">N/A</option>
                            </select>
                            <select id="blood_cell" name="blood_cell">
                                <option value="" disabled selected>Blood Cell</option>
                                <option value="Whole Blood">Whole Blood</option>
                                <option value="Platelet">Platelet (Apheresis)</option>
                                <option value="Plasma">Plasma (Plasmapheresis)</option>
                                <option value="Double Red Cells">Double Red Cells</option>
                                <option value="N/A">N/A</option>
                            </select>
                            <select id="blood_type" name="blood_type" class="add-patient">
                                <option value="" disabled selected>Blood Type</option>
                                <option>A+</option>
                                <option>A-</option>
                                <option>B+</option>
                                <option>B-</option>
                                <option>AB+</option>
                                <option>AB-</option>
                                <option>O+</option>
                                <option>O-</option>
                                <option value="N/A">N/A</option>
                            </select>
                            <input type="text" name="location" placeholder="Location" class="add-patient" required>
                            <button type="submit" name="create">Add</button>
                        </form>
                    </div>
                </div>

                <div class="content-container-2">
                    <?php
                        $user_id = $_SESSION['user_id'];
                        $picked_organ = "SELECT id, status, name, age, gender, organ_type, location, created_at FROM patients WHERE user_id = $user_id AND organ_type IS NOT NULL";
                        $picked_blood = "SELECT id, status, name, age, gender, blood_cell, blood_type, location, created_at FROM patients WHERE user_id = $user_id AND blood_cell IS NOT NULL AND blood_type IS NOT NULL";

                        $get_organ = $conn->query($picked_organ);
                        $get_blood = $conn->query($picked_blood);
                        
                        echo "<div class='interactive-content'>";
                            echo "<h2>Patients List</h2>";
                            echo "<br>";
                            echo "<hr class='interactive-underline'>";
                            echo "<br>";

                            echo "<table id='organTable'>";
                                    
                                if ($get_organ && $get_organ->num_rows > 0) {
                                    echo "
                                        <tr>
                                            <th><p>ID</p></th>
                                            <th><p>Status</p></th>
                                            <th><p>Name</p></th>
                                            <th><p>Age</p></th>
                                            <th><p>Gender</p></th>
                                            <th><p>Needed Organ</p></th>
                                            <th><p>Location</p></th>
                                            <th><p>Created At</p></th>
                                        </tr>";
                                echo "<tbody>";
                                        while ($row = $get_organ->fetch_assoc()) {
                                            echo "
                                            <tr>
                                                <td><p>" . htmlspecialchars($row['id'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['status'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['name'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['age'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['gender'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['organ_type'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['location'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</p></td>
                                            </tr>";
                                        }
                                        } else {
                                            // 6. If no records found
                                            echo "<div class='message'><p>No records found.</p></div>";
                                        }
                                        
                                echo "</tbody>";
                            echo "</table>";

                            echo "<br>";

                            echo "<table id='bloodTable' class='hidden'>";
                                if ($get_blood && $get_blood->num_rows > 0) {
                                    echo "
                                        <tr>
                                            <th><p>ID</p></th>
                                            <th><p>Status</p></th>
                                            <th><p>Name</p></th>
                                            <th><p>Age</p></th>
                                            <th><p>Gender</p></th>
                                            <th><p>Blood Cell</p></th>
                                            <th><p>Blood Type</p></th>
                                            <th><p>Location</p></th>
                                            <th><p>Created At</p></th>
                                        </tr>";
                                echo "<tbody>";
                                        while ($row = $get_blood->fetch_assoc()) {
                                            echo "
                                            <tr>
                                                <td><p>" . htmlspecialchars($row['id'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['status'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['name'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['age'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['gender'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['blood_cell'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['blood_type'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['location'] ?? 'N/A') . "</p></td>
                                                <td><p>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</p></td>
                                            </tr>";
                                        }
                                        } else {
                                            // 6. If no records found
                                            echo "<div class='message'><p>No records found.</p></div>";
                                        }
                                        
                                        // 6. Close connection
                                        $conn->close();
                                    
                                echo "</tbody>";
                            echo "</table>";
                            echo "<br>";
                            echo "<form method='POST' class='view'>
                                <a href='patients_table.php' class='link-edit'>Edit</a>
                            </form>";
                        echo "</div>";
                        echo "<br>";
                    ?>
                </div>
                
            </div>
        </body>
    </head>
</html>