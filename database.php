<?php
/**
 * Simple MySQLi connection script for Paombong Database System.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '192.168.8.10'; // Your specific IPv4 address
$user = 'root';
$pass = '';
$dbName = 'school_db';

$adminConn = new mysqli($host, $user, $pass);
if ($adminConn->connect_errno) {
    error_log('Admin connection failed: ' . $adminConn->connect_error);
    die('Database server unavailable.');
}

$adminConn->query(
    "CREATE DATABASE IF NOT EXISTS `$dbName` 
     CHARACTER SET utf8mb4 
     COLLATE utf8mb4_general_ci"
);
$adminConn->close();

/* ================= CONNECT TO DATABASE ================= */
$conn = new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_errno) {
    error_log('Database connection failed: ' . $conn->connect_error);
    die('Database connection error.');
}
$conn->set_charset('utf8mb4');

/* ================= CREATE TABLE ================= */
$createTableSql = "CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `gender` VARCHAR(10) DEFAULT NULL,
    `age` INT DEFAULT NULL,
    `purpose` VARCHAR(255) DEFAULT NULL,
    `numv` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($createTableSql);

/* ================= ENSURE REQUIRED COLUMNS EXIST ================= */
$neededCols = [
    'purpose'    => 'VARCHAR(255) DEFAULT NULL',
    'numv'       => 'VARCHAR(20) DEFAULT NULL',
    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
];

foreach ($neededCols as $col => $definition) {
    $res = $conn->query("SHOW COLUMNS FROM `students` LIKE '" . $conn->real_escape_string($col) . "'");
    if ($res->num_rows === 0) {
        $conn->query("ALTER TABLE `students` ADD COLUMN `$col` $definition");
    }
}
?>