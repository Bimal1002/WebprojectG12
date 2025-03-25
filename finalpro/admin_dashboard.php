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
    <title>Admin Dashboard</title>
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
        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <p><?= htmlspecialchars($_GET['message']) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!isset($_GET['section']) || $_GET['section'] === 'students'): ?>
            
            <h2>Interested Students</h2>
            <table>
            <thead>
                    <tr>
                        <th>Programme</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Registered At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("
                        SELECT i.InterestID, p.ProgrammeName, i.StudentName, i.Email, i.RegisteredAt
                        FROM InterestedStudents i
                        JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
                        ORDER BY i.RegisteredAt DESC
                    ");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ProgrammeName']) ?></td>
                            <td><?= htmlspecialchars($row['StudentName']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['RegisteredAt']) ?></td>
                            <td>
                                <a href="delete_student.php?id=<?= $row['InterestID'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

                
            </table>

        <?php else: ?>
            <!-- Programs Management Section -->
            <div class="management-header">
                <h2>Manage Programs</h2>
                <a href="add_program.php" class="btn-add">Add New Program</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Program Name</th>
                        <th>Description</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("
                        SELECT p.ProgrammeID, p.ProgrammeName, p.Description, l.LevelName 
                        FROM Programmes p
                        JOIN Levels l ON p.LevelID = l.LevelID
                    ");
                    while ($program = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($program['ProgrammeName']) ?></td>
                            <td><?= htmlspecialchars($program['Description']) ?></td>
                            <td><?= htmlspecialchars($program['LevelName']) ?></td>
                            <td>
                                <a href="edit_program.php?id=<?= $program['ProgrammeID'] ?>" class="btn-edit">Edit</a>
                                <a href="delete_program.php?id=<?= $program['ProgrammeID'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this program?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
    <footer>
         <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html>