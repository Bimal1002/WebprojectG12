<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="admin_dashboard.php?section=students" class="nav-link">Interested Students</a>
            <a href="admin_dashboard.php?section=programs" class="nav-link">Manage Programs</a>
            <a href="admin_modules.php" class="active">Modules</a>
            <a href="admin_logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        </nav>
    </header>

    <section class="dashboard">
        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <p><?= htmlspecialchars($_GET['message']) ?></p>
            </div>
        <?php endif; ?>

        <div class="management-header">
            <h2>Manage Modules</h2>

        </div>

        <table>
            <thead>
                <tr>
                    <th>Module ID</th>
                    <th>Module Name</th>
                    <th>Module Leader</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("
                    SELECT m.ModuleID, m.ModuleName, s.Name AS ModuleLeaderName
                    FROM Modules m
                    LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
                    ORDER BY m.ModuleName
                ");
                while ($module = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($module['ModuleID']) ?></td>
                        <td><?= htmlspecialchars($module['ModuleName']) ?></td>
                        <td><?= htmlspecialchars($module['ModuleLeaderName'] ?? 'Not assigned') ?></td>
                        <td>
                            <a href="edit_module.php?id=<?= $module['ModuleID'] ?>" class="btn-edit">Edit</a>
                            <a href="delete_module.php?id=<?= $module['ModuleID'] ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this module?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <footer>
        <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html>