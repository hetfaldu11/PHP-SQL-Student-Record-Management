<?php
session_start();
include 'db.php'; // Contains database connection

// Check if user is logged in
if (!isset($_SESSION['loggedin']) ){
    header("Location: index.php");
    exit;
}

// Initialize variables
$message = '';
$message_type = '';
$course = null;

// Validate and sanitize course_id
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    
    if ($course_id <= 0) {
        $message = "Invalid course ID";
        $message_type = "error";
    } else {
        // Check if course exists
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();
        $stmt->close();
        
        if (!$course) {
            $message = "Course not found";
            $message_type = "error";
        }
    }
} else {
    $message = "No course ID provided";
    $message_type = "error";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_delete'])) {
    // Check if course exists again (prevent race condition)
    $stmt = $conn->prepare("SELECT course_id FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // First check if any students are enrolled in this course
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE course_id = ?");
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $check_stmt->bind_result($student_count);
        $check_stmt->fetch();
        $check_stmt->close();
        
        if ($student_count > 0) {
            $message = "Cannot delete course - $student_count students are enrolled";
            $message_type = "error";
        } else {
            // Proceed with deletion
            $delete_stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
            $delete_stmt->bind_param("i", $course_id);
            
            if ($delete_stmt->execute()) {
                $message = "Course deleted successfully";
                $message_type = "success";
                $course = null; // Clear course data after deletion
                $url="list_courses.php";
                echo '<a style="text-decoration:none; color:white;" class="action-btn" href="'.$url.'" class="button"> Done</a>';
            } else {
                $message = "Error deleting course: " . $conn->error;
                $message_type = "error";
            }
            $delete_stmt->close();
        }
    } else {
        $message = "Course not found (may have been already deleted)";
        $message_type = "error";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Course</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #BB86FC;
            --primary-variant: #3700B3;
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
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--surface);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border-bottom: 2px solid var(--primary-variant);
            padding-bottom: 0.5rem;
        }

        .course-details {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #2c2c2c;
            border-radius: var(--border-radius);
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
        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: 500;
            color: var(--primary);
            min-width: 120px;
        }

        .warning-box {
            background-color: rgba(207, 102, 121, 0.2);
            border-left: 4px solid var(--error);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-danger {
            background-color: var(--error);
            color: var(--on-error);
        }

        .btn-danger:hover {
            background-color: #e53935;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(207, 102, 121, 0.3);
        }

        .btn-secondary {
            background-color: #666;
            color: var(--on-background);
            margin-left: 1rem;
        }

        .btn-secondary:hover {
            background-color: #555;
            transform: translateY(-2px);
        }

        .message {
            margin: 1.5rem 0;
            padding: 0.8rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
        }

        .message.success {
            background-color: rgba(0, 200, 83, 0.2);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .message.error {
            background-color: rgba(207, 102, 121, 0.2);
            color: var(--error);
            border-left: 4px solid var(--error);
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            animation: fadeIn 0.6s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .container {
                padding: 1.5rem;
            }
            
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-secondary {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-trash-alt"></i> Delete Course</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($course): ?>
            <div class="course-details">
                <div class="detail-row">
                    <span class="detail-label">Course Name:</span>
                    <span><?php echo htmlspecialchars($course['course_name']); ?></span>
                </div>
                <?php if (!empty($course['description'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Description:</span>
                    <span><?php echo htmlspecialchars($course['description']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="warning-box">
                <h3><i class="fas fa-exclamation-triangle"></i> Warning</h3>
                <p>This action cannot be undone. Deleting this course will permanently remove it from the system.</p>
            </div>
            
            <form method="POST">
                <div class="btn-group">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Confirm Delete
                    </button>
                    <a href="list_courses.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        <?php elseif (!$message): ?>
            <div class="message error">
                Unable to load course details. Please try again.
            </div>
            <a href="list_courses.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Courses List
            </a>
        <?php endif; ?>
    </div>
</body>
</html>