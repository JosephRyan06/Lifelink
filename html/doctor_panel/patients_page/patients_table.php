<?php
$required_role = 'doctor';
require_once '../../../php/check_session.php';

$user_id = $_SESSION['user_id'];
// READ
$sql = "SELECT * FROM patients WHERE user_id AND status = 'Pending' AND user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="patients_style.css">

        <title>Patients Table</title>

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
                                <a href="../donors_page/donors.php">Donor</a>
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

        <div class="content-2">
            <table>
                <tr>
                    <th><p>ID</p></th>
                    <th><p>Name</p></th>
                    <th><p>Age</p></th>
                    <th><p>Gender</p></th>
                    <th><p>Needed Organ</p></th>
                    <th><p>Blood Cell</p></th>
                    <th><p>Blood Type</p></th>
                    <th><p>Location</p></th>
                    <th><p>Action</p></th>
                </tr>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php
                            if (isset($_POST['update'])) {
                                $user_id = $_SESSION['user_id'];
                                $id = intval($_POST['id']);
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

                                $name = ($name === '') ? null : $name;
                                $age = ($age === '') ? null : $age;
                                $gender = ($gender === 'N/A') ? null : $gender;
                                $organ_type = ($organ_type === 'N/A') ? null : $organ_type;
                                $blood_cell = ($blood_cell === 'N/A') ? null : $blood_cell;
                                $blood_type = ($blood_type === 'N/A') ? null : $blood_type;
                                $location = ($location === '') ? null : $location;

                                // Prepare statement
                                $sql = "UPDATE patients 
                                        SET name = ?, age = ?, gender = ?, organ_type = ?, blood_cell = ?, blood_type = ?, location = ?
                                        WHERE id = ? AND user_id = ?";
                                $stmt = $conn->prepare($sql);

                                // Bind parameters (use null-safe binding)
                                $stmt->bind_param("sisssssii",
                                    $name, $age, $gender, $organ_type, $blood_cell, $blood_type, $location, $id, $user_id
                                );

                                // Execute query
                                if ($stmt->execute()) {
                                    echo "<meta http-equiv='refresh' content='0'>";
                                } else {
                                    echo "<meta http-equiv='refresh' content='0'>";
                                }

                                

                                $stmt->close();
                            }


                            if (isset($_GET['delete'])) {
                                $user_id = $_SESSION['user_id'];
                                $id = intval($_GET['delete']);
                                $sql = "DELETE FROM patients WHERE id = ? AND user_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $id);

                                if ($stmt->execute()) {
                                    echo "<script>alert('Patient has been successfully deleted!');</script>";
                                } else {
                                    echo "<script>alert('Error deleting patient: ');</script>" . $stmt->error;
                                }

                                
                                $stmt->close();
                            }
                            
                        ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td><p><?php echo htmlspecialchars($row['id']) ?></p></td>
                                <td><input type="text" name="name" class="name" value="<?= $row['name'] ?>"></td>
                                <td><input type="number" name="age" class="age" value="<?= $row['age'] ?>"></td>
                                <td>
                                    <select name="gender" class="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= ($row['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= ($row['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= ($row['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                        <option value="N/A" <?= ($row['organ_type'] == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="organ_type" name="organ_type" class="organ_type">
                                        <option value="" disabled selected>Choose an Organ</option>
                                        <option value="Kidney" <?= ($row['organ_type'] == 'Kidney') ? 'selected' : '' ?>>Kidney</option>
                                        <option value="Liver" <?= ($row['organ_type'] == 'Liver') ? 'selected' : '' ?>>Liver</option>
                                        <option value="Heart" <?= ($row['organ_type'] == 'Heart') ? 'selected' : '' ?>>Heart</option>
                                        <option value="Lungs" <?= ($row['organ_type'] == 'Lungs') ? 'selected' : '' ?>>Lungs</option>
                                        <option value="Pancreas" <?= ($row['organ_type'] == 'Pancreas') ? 'selected' : '' ?>>Pancreas</option>
                                        <option value="Intestine" <?= ($row['organ_type'] == 'Intestine') ? 'selected' : '' ?>>Intestine</option>
                                        <option value="Cornea" <?= ($row['organ_type'] == 'Cornea') ? 'selected' : '' ?>>Cornea</option>
                                        <option value="N/A" <?= ($row['organ_type'] == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="blood_cell" name="blood_cell" class="blood_cell">
                                        <option value="" disabled selected>Select Blood Cell</option>
                                        <option value="Whole Blood" <?= ($row['blood_cell'] ?? '') == 'Whole Blood' ? 'selected' : '' ?>>Whole Blood</option>
                                        <option value="Platelet" <?= ($row['blood_cell'] ?? '') == 'Platelet' ? 'selected' : '' ?>>Platelet (Apheresis)</option>
                                        <option value="Plasma" <?= ($row['blood_cell'] ?? '') == 'Plasma' ? 'selected' : '' ?>>Plasma (Plasmapheresis)</option>
                                        <option value="Double Red Cells" <?= ($row['blood_cell'] ?? '') == 'Double Red Cells' ? 'selected' : '' ?>>Double Red Cells</option>
                                        <option value="N/A" <?= ($row['organ_type'] == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="blood_type" name="blood_type" class="blood_type">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" <?= ($row['blood_type'] ?? '') == 'A+' ? 'selected' : '' ?>>A+</option>
                                        <option value="A-" <?= ($row['blood_type'] ?? '') == 'A-' ? 'selected' : '' ?>>A-</option>
                                        <option value="B+" <?= ($row['blood_type'] ?? '') == 'B+' ? 'selected' : '' ?>>B+</option>
                                        <option value="B-" <?= ($row['blood_type'] ?? '') == 'B-' ? 'selected' : '' ?>>B-</option>
                                        <option value="AB+" <?= ($row['blood_type'] ?? '') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                        <option value="AB-" <?= ($row['blood_type'] ?? '') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                        <option value="O+" <?= ($row['blood_type'] ?? '') == 'O+' ? 'selected' : '' ?>>O+</option>
                                        <option value="O-" <?= ($row['blood_type'] ?? '') == 'O-' ? 'selected' : '' ?>>O-</option>
                                        <option value="N/A" <?= ($row['organ_type'] == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </td>
                                <td><input type="text" name="location" class="location" value="<?= $row['location'] ?>"></td>
                                <td>
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-update" name="update">Update</button>
                                    <button type="button" class="btn-delete"><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Warning! This record will be lost once removed.')">Delete</a></button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9"><?php echo "<div class='message'><p>No records found.</p></div>"; ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>