<?php
$required_role = 'doctor';
require_once '../../../php/check_session.php';

// Function to display all donors
function displayAllDonors($conn) {
    $picked_organ = "
        SELECT 
            u.id,
            d.user_id,
            d.status,
            u.username,
            u.fullname,
            u.email,
            u.phone,
            d.age,
            d.gender,
            d.organ_type,
            d.hospital,
            d.created_at
        FROM donations d
        INNER JOIN users u
        On d.user_id = u.id
        WHERE organ_type IS NOT NULL
    ";
    $picked_blood = "
        SELECT 
            u.id,
            d.user_id,
            d.status,
            u.username,
            u.email,
            u.phone,
            u.fullname,
            d.age,
            d.gender,
            d.blood_cell,
            d.blood_type,
            d.hospital,
            d.created_at
        FROM donations d
        INNER JOIN users u
        On d.user_id = u.id
        WHERE blood_cell IS NOT NULL
        AND blood_type IS NOT NULL
    ";

    $get_organ = $conn->query($picked_organ);
    $get_blood = $conn->query($picked_blood);

    echo "<div class='interactive-content'>";

    echo "<table id='organTable'>";
            
        if ($get_organ && $get_organ->num_rows > 0) {
            echo "
                <tr>
                    <th><p>ID</p></th>
                    <th><p>Status</p></th>
                    <th><p>Username</p></th>
                    <th><p>Email</p></th>
                    <th><p>Phone</p></th>
                    <th><p>Name</p></th>
                    <th><p>Age</p></th>
                    <th><p>Gender</p></th>
                    <th><p>Needed Organ</p></th>
                    <th><p>Hospital</p></th>
                    <th><p>Created At</p></th>
                </tr>";
        echo "<tbody>";
                while ($row = $get_organ->fetch_assoc()) {
                    echo "
                    <tr>
                        <td><p>" . htmlspecialchars($row['id'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['status'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['username'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['email'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['fullname'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['age'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['gender'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['organ_type'] ?? 'N/A') . "</p></td>
                        <td><p>" . htmlspecialchars($row['hospital'] ?? 'N/A') . "</p></td>
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
                        <th><p>Username</p></th>
                        <th><p>Email</p></th>
                        <th><p>Phone</p></th>
                        <th><p>Name</p></th>
                        <th><p>Age</p></th>
                        <th><p>Gender</p></th>
                        <th><p>Blood Cell</p></th>
                        <th><p>Blood Type</p></th>
                        <th><p>Hospital</p></th>
                        <th><p>Created At</p></th>
                    </tr>";
            echo "<tbody>";
                    while ($row = $get_blood->fetch_assoc()) {
                        echo "
                        <tr>
                            <td><p>" . htmlspecialchars($row['id'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['status'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['username'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['email'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['fullname'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['age'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['gender'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['blood_cell'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['blood_type'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['hospital'] ?? 'N/A') . "</p></td>
                            <td><p>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</p></td>
                        </tr>";
                    }
                    } else {
                        // 6. If no records found
                        echo "<div class='message'><p>No records found.</p></div>";
                    }  
                
            echo "</tbody>";
        echo "</table>";
    echo "</div>";
    echo "<br>";
}

// Function to display donors by status
function displayDonorsByPending($conn) {
    $pending = "
        SELECT 
            d.user_id AS user_id,
            u.username AS username,
            u.fullname AS fullname,
            d.donation_type,
            d.organ_type AS organ_type,
            d.blood_cell AS blood_cell,
            d.blood_type AS blood_type,
        CASE
            WHEN d.donation_type = 'organ' THEN 'Organ'
            WHEN d.donation_type = 'blood' THEN d.blood_cell
        END AS match_type,
        CASE
            WHEN d.organ_type = d.organ_type THEN d.organ_type
            WHEN d.blood_type = d.blood_type THEN d.blood_type
            WHEN d.blood_type IS NULL = d.blood_type IS NULL THEN 'N/A'
        END AS name_type
        FROM donations d INNER JOIN users u ON d.user_id = u.id
        WHERE status = 'Pending'
    ";
    $pending_result = $conn->query($pending);

    if ($pending_result->num_rows > 0) {
        while ($row = $pending_result->fetch_assoc()) {
            echo "
                <div class='content'>
                    <p><strong>{$row['user_id']}</strong> - {$row['fullname']}</p>
                    <p>{$row['username']}</p><br>
                    <p><strong>{$row['match_type']}</strong></p>
                    <p><strong>{$row['name_type']}</strong></p>
                </div>
            ";
        }
    } else {
        echo "<p>No Pending donors found.</p>";
    }
}

function displayDonorsByConfirmed($conn) {
    $confirmed = "
        SELECT 
            d.user_id AS user_id,
            u.username AS username,
            u.fullname AS fullname,
            d.donation_type,
            d.organ_type AS organ_type,
            d.blood_cell AS blood_cell,
            d.blood_type AS blood_type,
        CASE
            WHEN d.donation_type = 'organ' THEN 'Organ'
            WHEN d.donation_type = 'blood' THEN d.blood_cell
        END AS match_type,
        CASE
            WHEN d.organ_type = d.organ_type THEN d.organ_type
            WHEN d.blood_type = d.blood_type THEN d.blood_type
            WHEN d.blood_type IS NULL = d.blood_type IS NULL THEN 'N/A'
        END AS name_type
        FROM donations d INNER JOIN users u ON d.user_id = u.id
        WHERE status = 'Confirmed'
    ";
    $confirmed_result = $conn->query($confirmed);

    if ($confirmed_result->num_rows > 0) {
        while ($row = $confirmed_result->fetch_assoc()) {
            echo "
                <div class='content'>
                    <p><strong>{$row['user_id']}</strong> - {$row['fullname']}</p>
                    <p>{$row['username']}</p><br>
                    <p><strong>{$row['match_type']}</strong></p>
                    <p><strong>{$row['name_type']}</strong></p>
                </div>
            ";
        }
    } else {
        echo "<p class='empty-box'>No Confirmed donors found.</p>";
    }
}

function displayDonorsByDeclined($conn) {
    $declined = "
        SELECT 
            d.user_id AS user_id,
            u.username AS username,
            u.fullname AS fullname,
            d.donation_type,
            d.organ_type AS organ_type,
            d.blood_cell AS blood_cell,
            d.blood_type AS blood_type,
        CASE
            WHEN d.donation_type = 'organ' THEN 'Organ'
            WHEN d.donation_type = 'blood' THEN d.blood_cell
        END AS match_type,
        CASE
            WHEN d.organ_type = d.organ_type THEN d.organ_type
            WHEN d.blood_type = d.blood_type THEN d.blood_type
            WHEN d.blood_type IS NULL = d.blood_type IS NULL THEN 'N/A'
        END AS name_type
        FROM donations d INNER JOIN users u ON d.user_id = u.id
        WHERE status = 'Declined'
    ";
    $declined_result = $conn->query($declined);

    if ($declined_result->num_rows > 0) {
        while ($row = $declined_result->fetch_assoc()) {
            echo "
                <div class='content'>
                    <p><strong>{$row['user_id']}</strong> - {$row['fullname']}</p>
                    <p>{$row['username']}</p><br>
                    <p><strong>{$row['match_type']}</strong></p>
                    <p><strong>{$row['name_type']}</strong></p>
                </div>
            ";
        }
    } else {
        echo "<p class='empty-box'>No Declined donors found.</p>";
    }
}
?>
