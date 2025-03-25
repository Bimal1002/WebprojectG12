<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    try {
    
        $conn->beginTransaction();
        
    
        $stmt = $conn->prepare("DELETE FROM ProgrammeModules WHERE ProgrammeID = ?");
        $stmt->execute([$_GET['id']]);
        
        
        $stmt = $conn->prepare("DELETE FROM InterestedStudents WHERE ProgrammeID = ?");
        $stmt->execute([$_GET['id']]);
        
        $stmt = $conn->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
        $stmt->execute([$_GET['id']]);
        
        $conn->commit();
        
        header("Location: admin_dashboard.php?section=programs&message=Program deleted successfully");
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: admin_dashboard.php?section=programs&message=Error deleting program: " . urlencode($e->getMessage()));
    }
    exit();
}

header("Location: admin_dashboard.php?section=programs");