<?php
session_start();
// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #BB86FC;
            --primary-variant:rgb(105, 63, 202);
            --secondary: #03DAC6;
            --background: #121212;
            --surface: #1E1E1E;
            --error: #CF6679;
            --on-primary: #000000;
            --on-secondary: #000000;
            --on-background: #FFFFFF;
            --on-surface: #FFFFFF;
            --on-error: #000000;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', Arial, sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--on-background);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--surface);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h1, h2, h3 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        h2 {
            border-bottom: 2px solid var(--primary-variant);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        h2 i {
            font-size: 1.5rem;
        }

        a {
            color: var(--secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #2c2c2c;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: var(--primary-variant);
            color: var(--on-primary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        tr:nth-child(even) {
            background-color: #252525;
        }

        tr:hover {
            background-color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--on-primary);
        }

        .btn-primary:hover {
            background-color: #9b6cfc;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.3);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: var(--on-secondary);
        }

        .btn-secondary:hover {
            background-color: #00c9b6;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(3, 218, 198, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #aaa;
            font-style: italic;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        include 'db.php';
        

        $result = $conn->query("SELECT * FROM courses");
        ?>

        <h2><i class="fas fa-book-open"></i> Course Catalog</h2>
        
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_course.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete_course.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-secondary" onclick="return confirm('Are you sure you want to delete this course?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book fa-3x" style="color: #444; margin-bottom: 1rem;"></i>
                    <p>No courses found in the database.</p>
                    <a href="add_course.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Add New Course
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <a href="add_course.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Course
        </a>
        
        <a href="index1.php" class="btn btn-secondary" style="margin-left: 1rem; margin-top: 1rem; display: inline-block;">
            <i class="fas fa-home"></i> Home
        </a>
    </div>
</body>
</html>