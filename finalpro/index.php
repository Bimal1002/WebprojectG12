<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1 id="main-title">Student Course Hub</h1>
        <nav aria-title= "main navigation" >
                <a href="admin_login.php" class="admin-login-button"> Admin Login</a>
                <a href="staff_login.php" class="staff-login"> Staff Login</a>    
        </nav>
        
    </header>

    
    <form method="GET" action="">
        <div class="filters">
            <select id="levelFilter" name="level">
                <option value="all" <?= (!isset($_GET['level']) || $_GET['level'] === 'all' ? 'selected' : '') ?>>All Levels</option>
                <option value="undergrad" <?= (isset($_GET['level']) && $_GET['level'] === 'undergrad' ? 'selected' : '') ?>>Undergraduate</option>
                <option value="postgrad" <?= (isset($_GET['level']) && $_GET['level'] === 'postgrad' ? 'selected' : '') ?>>Postgraduate</option>
            </select>
            <input type="text" name="search" placeholder="Search programs..." value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit">Filter</button>
        </div>
    </form>
    <h2>Our Programs: </h2>

    <section class="program-list">
        
        <?php
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $level = isset($_GET['level']) ? $_GET['level'] : 'all';

        $sql = "
            SELECT Programmes.ProgrammeID, Programmes.ProgrammeName, Programmes.Description, Levels.LevelName
            FROM Programmes
            JOIN Levels ON Programmes.LevelID = Levels.LevelID
            WHERE 1=1
        ";

        
        if (!empty($search)) {
            $sql .= " AND (Programmes.ProgrammeName LIKE :search OR Programmes.Description LIKE :search)";
        }

        if ($level !== 'all') {
            $levelName = ($level === 'undergrad') ? 'Undergraduate' : 'Postgraduate';
            $sql .= " AND Levels.LevelName = :level";
        }
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
        }

        if ($level !== 'all') {
            $stmt->bindValue(':level', $levelName, PDO::PARAM_STR);
        }

        $stmt->execute();
        while ($program = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='program'>";
            echo "<h2>{$program['ProgrammeName']}</h2>";
            echo "<p>{$program['Description']}</p>";
            echo "<p><strong>Level:</strong> {$program['LevelName']}</p>";
            echo "<a href='program_details.php?id={$program['ProgrammeID']}' class='btn'>View Details</a>";
            echo "</div>";
        }

        if ($stmt->rowCount() === 0) {
            echo "<p>Sorry, we don't have this program.</p>";
        }
        ?>
    </section>
    <footer>
         <p>&copy; 2025 Student Course Hub. All rights reserved.</p>
    </footer>
</body>
</html>