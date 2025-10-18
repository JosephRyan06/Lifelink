<?php
session_start();

// Clear all session data
$_SESSION = array();

// If session uses cookies, also remove them properly
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: ../html/Log In.php");
exit;
?>
