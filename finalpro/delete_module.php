<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No module specified for deletion";
    header("Location: admin_modules.php");
    exit();
}

$moduleID = (int)$_GET['id'];

try {
    $conn->beginTransaction();
    
    $stmt = $conn->prepare("DELETE FROM ProgrammeModules WHERE ModuleID = ?");
    $stmt->execute([$moduleID]);
    
    $stmt = $conn->prepare("DELETE FROM Modules WHERE ModuleID = ?");
    $stmt->execute([$moduleID]);
    
    $conn->commit();
    $_SESSION['success'] = "Module deleted successfully!";
    
} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Failed to delete module: " . $e->getMessage();
}

header("Location: admin_modules.php");
exit();
?>