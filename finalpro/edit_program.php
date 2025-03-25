<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

$program = [];
$error = '';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$_GET['id']]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $level = $_POST['level'];

    if (empty($name) || empty($description) || empty($level)) {
        $error = "All fields are required";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE Programmes 
                                   SET ProgrammeName = ?, Description = ?, LevelID = ?
                                   WHERE ProgrammeID = ?");
            $stmt->execute([$name, $description, $level, $id]);
            header("Location: admin_dashboard.php?section=programs&message=Program updated successfully");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating program: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Program</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Edit Program</h1>
        <nav>
            <a href="admin_dashboard.php?section=programs" class="nav-link">Back to Programs</a>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <section class="form-container">
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($program): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $program['ProgrammeID'] ?>">
                
                <div class="form-group">
                    <label>Program Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($program['ProgrammeName']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" required><?= htmlspecialchars($program['Description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Level:</label>
                    <select name="level" required>
                        <option value="1" <?= $program['LevelID'] == 1 ? 'selected' : '' ?>>Undergraduate</option>
                        <option value="2" <?= $program['LevelID'] == 2 ? 'selected' : '' ?>>Postgraduate</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-save">Update Program</button>
            </form>
        <?php else: ?>
            <p>Program not found</p>
        <?php endif; ?>
    </section>
</body>
</html>