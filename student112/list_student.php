<?php
include 'db.php';

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
    <title>Student Records</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #BB86FC;
            --primary-variant:rgb(114, 75, 203);
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
        <div class="header">
            <h2><i class="fas fa-users"></i> Student Records</h2>
            <a href="index1.php" class="btn">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
        
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= isset($row['course_name']) ? htmlspecialchars($row['course_name']) : 'N/A' ?></td>
                            <!-- <td>
                                <a href="edit_student.php?student_id=<?= $row['student_id'] ?>" class="action-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td> -->
                            <td>
    <a href="edit_student.php?student_id=<?= $row['student_id'] ?>" class="action-btn">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="delete_student.php?student_id=<?= $row['student_id'] ?>" class="action-btn" onclick="return confirm('Are you sure you want to remove this student?');" style="background-color: var(--error); color: var(--on-primary); margin-left: 0.5rem;">
        <i class="fas fa-trash-alt"></i> Delete
    </a>
</td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <p>No student records found in the database</p>
                    <a href="add_student.php" class="btn" style="margin-top: 1rem;">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>