<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_modules.php");
    exit();
}

$moduleID = $_GET['id'];


$stmt = $conn->prepare("SELECT * FROM Modules WHERE ModuleID = ?");
$stmt->execute([$moduleID]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$module) {
    header("Location: admin_modules.php?message=Module not found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleName = $_POST['module_name'];
    $description = $_POST['description'];
    $moduleLeaderID = $_POST['module_leader_id'];
    
    $stmt = $conn->prepare("UPDATE Modules SET ModuleName = ?, Description = ?, ModuleLeaderID = ? WHERE ModuleID = ?");
    if ($stmt->execute([$moduleName, $description, $moduleLeaderID, $moduleID])) {
        header("Location: admin_modules.php?message=Module updated successfully");
        exit();
    } else {
        $error = "Failed to update module";
    }
}

$staff = $conn->query("SELECT StaffID, Name FROM Staff")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Module</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="?section=students" class="nav-link">Interested Students</a>
            <a href="?section=programs" class="nav-link">Manage Programs</a>
            <a href="admin_modules.php" class="active">Modules</a>
            <a href="admin_logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        </nav>
    </header>
    
    <section class="dashboard">
        <h2>Edit Module</h2>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form action="edit_module.php?id=<?= $moduleID ?>" method="post">
            <div class="form-group">
                <label for="module_name">Module Name:</label>
                <input type="text" id="module_name" name="module_name" value="<?= htmlspecialchars($module['ModuleName']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($module['Description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="module_leader_id">Module Leader:</label>
                <select id="module_leader_id" name="module_leader_id" required>
                    <option value="">Select a module leader</option>
                    <?php foreach ($staff as $member): ?>
                        <option value="<?= $member['StaffID'] ?>" <?= $member['StaffID'] == $module['ModuleLeaderID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($member['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">Update Module</button>
            <a href="admin_modules.php" class="btn-cancel">Cancel</a>
        </form>
    </section>
    <footer>
         <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html>