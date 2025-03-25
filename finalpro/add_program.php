<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $level = $_POST['level'];

    if (empty($name) || empty($description) || empty($level)) {
        $error = "All fields are required";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO Programmes (ProgrammeName, Description, LevelID) 
                                   VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $level]);
            header("Location: admin_dashboard.php?section=programs&message=Program added successfully");
            exit();
        } catch (PDOException $e) {
            $error = "Error adding program: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Program</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New Program</h1>
        <nav>
            <a href="admin_dashboard.php?section=programs" class="nav-link">Back to Programs</a>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <section class="form-container">
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Program Name:</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Level:</label>
                <select name="level" required>
                    <option value="1">Undergraduate</option>
                    <option value="2">Postgraduate</option>
                </select>
            </div>
            
            <button type="submit" class="btn-save">Add Program</button>
        </form>
    </section>
    <footer>
         <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html> 