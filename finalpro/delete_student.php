<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM InterestedStudents WHERE InterestID = ?");
        $stmt->execute([$_GET['id']]);
        
        
        header("Location: admin_dashboard.php?section=students&message=Student+record+deleted+successfully");
        exit();
    } catch (PDOException $e) {
    
        header("Location: admin_dashboard.php?section=students&message=Error+deleting+student+record");
        exit();
    }
}


header("Location: admin_dashboard.php?section=students");
exit();
?>