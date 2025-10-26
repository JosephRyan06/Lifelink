<?php 
$required_role = 'doctor';
require_once '../../../php/check_session.php';

function displayCurrentlyOnGoing1($conn) {
    //TOTAL OF DONORS
    $sql = "SELECT COUNT(*) AS total_pending FROM donations WHERE status = 'Pending'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo "<div class='pending-box'>";
        echo "<p>Total Pending: <strong>{$row['total_pending']}</strong></p>";
        echo "</div>";
    } else {
        echo "<p>No pending donors found.</p>";
    }
}

function displayCurrentlyOnGoing2($conn) {
    //TOTAL OF PATIENTS
    $user_id = $_SESSION['user_id'];
    $sql = "
        SELECT
        COUNT(*) AS total_pending
        FROM patients
        WHERE
            status = 'Pending'
            AND user_id = $user_id
    ";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo "<div class='pending-box'>";
        echo "<p>Total Pending: <strong>{$row['total_pending']}</strong></p>";
        echo "</div>";
    } else {
        echo "<p>No pending donors found.</p>";
    }
}

function displayFoundMatches($conn) {
    //TOTAL OF ORGANS
    echo "<h3 class='other-text'>Organ Match</h3>";

    $user_id = $_SESSION['user_id'];

    $sql_organ = "
        SELECT 
            d.organ_type, 
            COUNT(*) AS total_matches
        FROM donations d
        INNER JOIN patients p
            ON d.organ_type = p.organ_type
        INNER JOIN users u
            ON p.user_id = u.id
        WHERE
            d.status = 'Pending'
            AND p.status = 'Pending'
            AND p.user_id = $user_id
        GROUP BY d.organ_type
        ORDER BY total_matches DESC
    ";

    $result_organ = $conn->query($sql_organ);

    if ($result_organ->num_rows > 0) {
        echo "<table border='1px'>
                <tr>
                    <th>Organ</th>
                    <th>Total</th>
                </tr>";
        while ($row = $result_organ->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['organ_type']}</td>
                    <td>{$row['total_matches']}</td>
                </tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p>No organ matches found.</p>";
    }

    //TOTAL OF BLOOD CELLS
    echo "<h3 class='other-text'>Blood Cell Match</h3>";

    $sql_blood_cell = "
        SELECT 
            d.blood_cell, 
            COUNT(*) AS total_matches
        FROM donations d
        INNER JOIN patients p
            ON d.blood_cell = p.blood_cell
        INNER JOIN users u
            ON p.user_id = u.id
        WHERE
            d.status = 'Pending'
            AND p.status = 'Pending'
            AND p.user_id = $user_id
        GROUP BY d.blood_cell
        ORDER BY total_matches DESC
    ";

    $result_blood_cell = $conn->query($sql_blood_cell);

    if ($result_blood_cell->num_rows > 0) {
        echo "<table border='1px'>
                <tr>
                    <th>Blood Cell</th>
                    <th>Total</th>
                </tr>";
        while ($row = $result_blood_cell->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['blood_cell']}</td>
                    <td>{$row['total_matches']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No blood cell matches found.</p>";
    }

    //TOTAL OF BLOOD TYPES
    echo "<h3 class='other-text'>Blood Type Match</h3>";

    $sql_blood_type = "
        SELECT 
            d.blood_type, 
            COUNT(*) AS total_matches
        FROM donations d
        INNER JOIN patients p
            ON d.blood_type = p.blood_type
        INNER JOIN users u
            ON p.user_id = u.id
        WHERE
            d.status = 'Pending'
            AND p.status = 'Pending'
            AND p.user_id = $user_id
        GROUP BY d.blood_type
        ORDER BY total_matches DESC
    ";

    $result_blood_type = $conn->query($sql_blood_type);

    if ($result_blood_type->num_rows > 0) {
        echo "<table border='1px'>
                <tr>
                    <th>Blood Type</th>
                    <th>Total</th>
                </tr>";
        while ($row = $result_blood_type->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['blood_type']}</td>
                    <td>{$row['total_matches']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No blood type matches found.</p>";
    }
}

function displayConfirmedMatches($conn) {
    //TOTAL OF CONFIRMATION
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT COUNT(*) AS total_confirmation FROM donations WHERE status = 'Confirmed'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo "<div class='confirmed-box'>";
        echo "<p>Total Matches: <strong>{$row['total_confirmation']}</strong></p>";
        echo "</div>";
    } else {
        echo "<p>No pending donors found.</p>";
    }

    echo "</td>";
    echo "<td>";

    $sql = "
        SELECT
            COUNT(*) AS total_confirmation
        FROM patients
        WHERE
            status = 'Confirmed'
            AND user_id = $user_id
    ";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo "<div class='confirmed-box'>";
        echo "<p>Total Matches: <strong>{$row['total_confirmation']}</strong></p>";
        echo "</div>";
    } else {
        echo "<p>No pending donors found.</p>";
    }
}
?>