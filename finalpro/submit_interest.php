<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $programme_id = filter_input(INPUT_POST, 'programme_id', FILTER_VALIDATE_INT);
    $name = trim($_POST['name']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$programme_id || empty($name) || !$email) {
        header("Location: program_details.php?id=$programme_id&error=invalid_input");
        exit();
    }

    try {
        
        $stmt = $conn->prepare("SELECT * FROM InterestedStudents 
                              WHERE Email = ? AND ProgrammeID = ?");
        $stmt->execute([$email, $programme_id]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: program_details.php?id=$programme_id&error=already_registered");
            exit();
        }

        
        $stmt = $conn->prepare("INSERT INTO InterestedStudents 
                              (ProgrammeID, StudentName, Email) 
                              VALUES (?, ?, ?)");
        $stmt->execute([$programme_id, $name, $email]);
        
        
        header("Location: program_details.php?id=$programme_id&success=1");
        exit();
        
    } catch (PDOException $e) {
        
        error_log("Registration error: " . $e->getMessage());
        header("Location: program_details.php?id=$programme_id&error=database_error");
        exit();
    }
}


header("Location: index.php");
exit();
?>