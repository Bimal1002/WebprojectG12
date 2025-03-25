<?php include 'db.php'; ?>
<?php
$program_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
$stmt->execute([$program_id]);
$program = $stmt->fetch(PDO::FETCH_ASSOC);


$module_stmt = $conn->prepare("
    SELECT 
        Modules.ModuleID,
        Modules.ModuleName, 
        Modules.Description, 
        ProgrammeModules.Year,
        Staff.StaffID,
        Staff.Name AS ModuleLeader
    FROM ProgrammeModules
    JOIN Modules ON ProgrammeModules.ModuleID = Modules.ModuleID
    LEFT JOIN Staff ON Modules.ModuleLeaderID = Staff.StaffID
    WHERE ProgrammeModules.ProgrammeID = ?
    ORDER BY ProgrammeModules.Year, Modules.ModuleName
");
$module_stmt->execute([$program_id]);
$modules = $module_stmt->fetchAll(PDO::FETCH_ASSOC);

$leader_stmt = $conn->prepare("
    SELECT Name FROM Staff WHERE StaffID = ?
");
$leader_stmt->execute([$program['ProgrammeLeaderID']]);
$programme_leader = $leader_stmt->fetch(PDO::FETCH_ASSOC);
if (isset($_GET['error'])) {
    $error_messages = [
        'invalid_input' => 'Please fill all fields correctly',
        'already_registered' => 'You are already registered for this program',
        'database_error' => 'Registration failed, please try again'
    ];
    $error = $error_messages[$_GET['error']] ?? 'An error occurred';
}

if (isset($_GET['success'])) {
    $success = "Thank you for registering! We'll contact you soon.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $program['ProgrammeName']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo $program['ProgrammeName']; ?></h1>
        <nav>
            <a href="index.php" class="home">Home</a>
        </nav>
    </header>

    <section class="program-details">
        <h2>Program Description</h2>
        <p><?php echo $program['Description']; ?></p>
        
        <div class="programme-leader">
            <h3>Programme Leader</h3>
            <p><?php echo $programme_leader['Name']; ?></p>
        </div>

        <h2>Modules</h2>
        <ul class="module-list">
            <?php foreach ($modules as $module): ?>
                <li class="module-item">
                    <div class="module-header">
                        <h3><?php echo $module['ModuleName']; ?></h3>
                        <span class="year">Year <?php echo $module['Year']; ?></span>
                    </div>
                    <div class="module-leader">
                        <strong>Module Leader:</strong> 
                        <?php echo $module['ModuleLeader'] ?? 'Not assigned'; ?>
                    </div>
                    <p class="module-description"><?php echo $module['Description']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="messages">
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
        </div>

        <h2>Register Your Interest</h2>
        <form action="submit_interest.php" method="POST">
            <input type="hidden" name="programme_id" value="<?= $program['ProgrammeID'] ?>">
            
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn-submit">Register Interest</button>
        </form>
    </section>
    
    <footer>
        <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html>