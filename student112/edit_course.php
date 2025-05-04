<?php
session_start();
include 'db.php'; // Assuming this contains your database connection

// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
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
        die("Invalid course ID");
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $course_name = $conn->real_escape_string(trim($_POST['course_name']));
        $description = $conn->real_escape_string(trim($_POST['description']));
        
        if (empty($course_name)) {
            $message = "Course name cannot be empty";
            $message_type = "error";
        } else {
            // Use prepared statement for update
            $stmt = $conn->prepare("UPDATE courses SET course_name=?, description=? WHERE course_id=?");
            $stmt->bind_param("ssi", $course_name, $description, $course_id);
            
            if ($stmt->execute()) {
                $message = "Course updated successfully!";
                $message_type = "success";
            } else {
                $message = "Update failed: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
    }

    // Fetch course details using prepared statement
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        $message = "Course not found.";
        $message_type = "error";
    }
    $stmt->close();
} else {
    $message = "No course ID provided.";
    $message_type = "error";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--on-surface);
        }

        .required::after {
            content: " *";
            color: var(--error);
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            background-color: #2c2c2c;
            color: var(--on-surface);
            border: 1px solid #444;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(187, 134, 252, 0.3);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
        }

        .btn:hover {
            background-color: #9b6cfc;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.3);
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Edit Course</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($course): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="course_name" class="required">Course Name</label>
                    <input type="text" id="course_name" name="course_name" 
                           value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Course Description</label>
                    <textarea id="description" name="description"><?php 
                        echo htmlspecialchars($course['description']); 
                    ?></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update Course
                </button>
            </form>
            
            <a href="list_courses.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Courses List
            </a>
        <?php else: ?>
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