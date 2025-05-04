<?php
include 'db.php';
session_start();
// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
$sql = "SELECT students.*, courses.course_name 
        FROM students 
        LEFT JOIN courses ON students.course_id = courses.course_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students</title>
    <style>
 :root {
            --primary: #BB86FC;
            --primary-variant:rgb(103, 60, 203);
            --secondary: #03DAC6;
            --background: #121212;
            --surface: #1E1E1E;
            --error: #CF6679;
            --success: #00C853;
            --on-primary: #000000;
            --on-secondary: #000000;
            --on-background: #FFFFFF;
            --on-surface: #FFFFFF;
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
            padding: 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        h2 {
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary);
            color: var(--on-primary);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn:hover {
            background-color: #9b6cfc;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.3);
        }

        .table-container {
            overflow-x: auto;
            background-color: var(--surface);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
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

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.5rem 1rem;
            background-color: var(--secondary);
            color: var(--on-secondary);
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .action-btn:hover {
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

        .empty-state i {
            font-size: 3rem;
            color: #444;
            margin-bottom: 1rem;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-container {
            animation: fadeIn 0.6s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }
            
            .btn, .action-btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student List</h2>
        
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Fees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= isset($row['course_name']) ? htmlspecialchars($row['course_name']) : 'N/A' ?></td>
                            <td>
                                <a href="fees.php?student_id=<?= $row['student_id'] ?>" class="action-btn">Status</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No students found in the database.</p>
            <?php endif; ?>
        </div>
        
        <a href="index1.php" class="button">Home</a>
    </div>
</body>
</html>