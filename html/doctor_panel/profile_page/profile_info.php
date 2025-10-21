<?php
include '../doctors_db.php';
require_once '../../../php/check_session.php';
$required_role = 'donor';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='../html/Log In.php';</script>";
    exit;
}

$id = intval($_SESSION['user_id']);

//  Fetch logged-in user info
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_sql);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user = mysqli_fetch_assoc($user_result);
} else {
    // fallback values
    $user = [
        'id' => $user_id,
        'username' => 'testuser',
        'fullname' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'donor',
        'phone' => '',
        'location' => '',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

$per_sql = "SELECT * FROM doctors WHERE id = $user_id";
$per_result = mysqli_query($conn, $per_sql);

if ($per_result && mysqli_num_rows($per_result) > 0) {
    $personal_info = mysqli_fetch_assoc($per_result);
} else {
    $personal_info = [
        'age' => '',
        'gender' => '',
        'license' => '',
        'workplace' => '',
        'description' => ''
    ];
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $section = $_POST['section'];

    //ACCOUNT INFO
    if ($section === "account") {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);

        $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'phone'");
        $has_phone = mysqli_num_rows($columns_check) > 0;
        $columns_check2 = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'location'");
        $has_location = mysqli_num_rows($columns_check2) > 0;

        $update_parts = [
            "username='$username'",
            "fullname='$fullname'",
            "email='$email'"
        ];

        if ($has_phone) $update_parts[] = "phone='$phone'";
        if ($has_location) $update_parts[] = "location='$location'";

        $sql = "UPDATE users SET " . implode(", ", $update_parts) . " WHERE id=$user_id";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Account information updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating account: " . mysqli_error($conn) . "');</script>";
        }
    }

    //PERSONAL INFO
    if ($section === "personal_info") {
        $age = intval($_POST['age']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $license = mysqli_real_escape_string($conn, $_POST['license']);
        $location = mysqli_real_escape_string($conn, $_POST['location']); // ✅ your column name
        $description = mysqli_real_escape_string($conn, $_POST['description']); // ✅ text input

        $check = mysqli_query($conn, "SELECT id FROM doctors WHERE id = $id");

        if (mysqli_num_rows($check) > 0) {
            $sql = "UPDATE doctors 
                    SET age=$age, gender='$gender', license='$license', location='$location', description='$description'
                    WHERE id=$id";
        } else {
            $sql = "INSERT INTO doctor (id, age, gender, license, location, description, created_at)
                    VALUES ($id, $age, '$gender', '$license', '$location', '$description', NOW())";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Personal info updated!');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }

    echo "<meta http-equiv='refresh' content='0'>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="profile_style.css">
</head>
<body>

<div id="profile" class="page active">
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar">👤</div>
            <div class="profile-info">
                <!-- ✅ changed fullname → name -->
                <h2><?= htmlspecialchars($user['name'] ?? '') ?></h2>
                <p><?= htmlspecialchars($user['email'] ?? 'email@example.com') ?></p>
                <!-- ✅ removed status (no such column) -->
                <span class="status-badge">Active</span>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="switchTab(0)">Account Info</button>
            <button class="tab" onclick="switchTab(1)">Personal Info</button>
        </div>

        <!-- ACCOUNT INFO TAB -->
        <div class="tab-content active" id="tab0">
            <h3 class="section-title">View Account Information</h3>
            
            <!-- View Account Info -->
            <div class="info-display">
                <div class="info-row">
                    <span class="info-label-display">User ID:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['id'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label-display">Username:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['username'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label-display">Name:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label-display">Email:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label-display">Phone Number:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['phone_num'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label-display">Account Created:</span>
                    <span class="info-value-display"><?= htmlspecialchars($user['created_at'] ?? 'N/A') ?></span>
                </div>
            </div>

            <button class="toggle-edit-btn" onclick="toggleEdit()">✏️ Edit Account Information</button>

            <!-- Edit Account Info (Hidden by default) -->
            <div class="edit-section" id="editAccountSection" style="display: none;">
                <h4 class="section-title">Edit Account Information</h4>
                <form method="POST">
                    <input type="hidden" name="section" value="account">

                    <label>
                        Username: <span class="required">*</span>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="Enter username" required>
                    </label>

                    <label>
                        Name: <span class="required">*</span>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Enter your Name" required>
                    </label>

                    <label>
                        Email: <span class="required">*</span>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Enter your email" required>
                    </label>

                    <label>
                        Phone Number:
                        <input type="text" name="phone_num" value="<?= htmlspecialchars($user['phone_num'] ?? '') ?>" placeholder="e.g., 09123456789">
                    </label>

                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- PERSONAL INFO TAB -->
        <div class="tab-content" id="tab1">
            <h3 class="section-title">Personal Information</h3>

            <form method="POST">
                <input type="hidden" name="section" value="personal_info">

                <div class="form-row">
                    <label>
                        Age:
                        <input type="number" name="age" value="<?= htmlspecialchars($user['age'] ?? '') ?>" placeholder="e.g., 25" min="18" max="100">
                    </label>

                    <label>
                        Gender:
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= ($user['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($user['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($user['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        License:
                        <input type="text" name="license" value="<?= htmlspecialchars($user['license'] ?? '') ?>" placeholder="Enter your License" required>
                    </label>

                    <!-- ✅ Changed 'workplace' → 'location' to match DB -->
                    <label>
                        Location:
                        <input type="text" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>" placeholder="Enter your Location" required>
                    </label>

                    <label>
                        Description:
                        <input type="text" name="description" value="<?= htmlspecialchars($user['description'] ?? '') ?>" placeholder="Enter your Description" required>
                    </label>
                </div>

                <button class="btn btn-primary" type="submit">Save Personal Information</button>
            </form>
        </div>
    </div>
</div>

<script src="profile_config.js"></script>

</body>
</html>

<?php
mysqli_close($conn);
?>
