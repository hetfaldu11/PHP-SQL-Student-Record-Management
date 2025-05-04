<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['loggedin']) ){
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "d2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO courses (course_name, description) VALUES ('$course_name', '$description')";

    if ($conn->query($sql))
     {
        $message = "Course added successfully!";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "error";
    }
    header("Location: list_courses.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
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
            margin-top: 1.5rem;
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
        <h2><i class="fas fa-book-medical"></i> Add New Course</h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="course_name" class="required">Course Name</label>
                <input type="text" id="course_name" name="course_name" placeholder="Enter course name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Course Description</label>
                <textarea id="description" name="description" placeholder="Provide a detailed description of the course"></textarea>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-plus-circle"></i> Add Course
            </button>
        </form>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

