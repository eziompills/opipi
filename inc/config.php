<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors',1);
error_reporting(E_ALL);

// Database configuration
$host = 'sql311.infinityfree.com';
$dbname = 'if0_38791428_opapa';
$user = 'if0_38791428';
$pass = 'Azee2124277';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// PDO instantiation
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

function is_logged() { return isset($_SESSION['user']); }
function user() { return is_logged() ? $_SESSION['user'] : null; }
function is_admin() { return user() && user()['role']=='admin'; }
function is_owner() { return user() && user()['role']=='owner'; }
function is_staff() { return user() && user()['role']=='staff'; }
function is_customer() { return user() && user()['role']=='customer'; }

function require_role($roles = []) {
    if (!is_logged() || !in_array(user()['role'], $roles)) {
        header('Location: /login.php');
        exit;
    }
}
?>
