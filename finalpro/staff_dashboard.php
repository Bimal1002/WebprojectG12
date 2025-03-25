<?php
session_start();
include 'db.php';


if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT 
        m.ModuleID,
        m.ModuleName,
        m.Description,
        GROUP_CONCAT(DISTINCT p.ProgrammeName ORDER BY p.ProgrammeName SEPARATOR ', ') AS Programmes,
        GROUP_CONCAT(DISTINCT pm.Year ORDER BY pm.Year SEPARATOR ', ') AS Years
    FROM Modules m
    JOIN ProgrammeModules pm ON m.ModuleID = pm.ModuleID
    JOIN Programmes p ON pm.ProgrammeID = p.ProgrammeID
    WHERE m.ModuleLeaderID = ?
    GROUP BY m.ModuleID
");
$stmt->execute([$_SESSION['staff_id']]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['staff_name']) ?></h1>
        <nav>
            <a href="staff_logout.php" class="logout" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        </nav>
    </header>

    <section class="staff-dashboard">
        <h2>Your Modules</h2>
        
        <?php if (count($modules) > 0): ?>
            <div class="module-grid">
                <?php foreach ($modules as $module): ?>
                    <div class="module-card">
                        <h3><?= htmlspecialchars($module['ModuleName']) ?></h3>
                        <p><?= htmlspecialchars($module['Description']) ?></p>
                        <div class="module-meta">
                            <p><strong>Programmes:</strong> <?= htmlspecialchars($module['Programmes']) ?></p>
                            <p><strong>Years:</strong> <?= htmlspecialchars($module['Years']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You are not currently assigned to any modules.</p>
        <?php endif; ?>
    </section>
</body>
</html>