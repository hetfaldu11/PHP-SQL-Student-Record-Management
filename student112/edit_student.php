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
    <title>Student Management System</title>
    
<style>
    body {
        background-color: #121212;
        color: #ffffff;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    h1, h2, h3 {
        color: #BB86FC;
    }
    a {
        color: #03DAC6;
    }
    input, select, textarea, button {
        background-color: #1E1E1E;
        color: #ffffff;
        border: 1px solid #333;
        padding: 8px;
        margin: 5px 0;
        border-radius: 4px;
    }
    button {
        background-color: #3700B3;
        cursor: pointer;
    }
    button:hover {
        background-color: #6200EE;
    }
    .container {
        max-width: 800px;
        margin: auto;
        background-color: #1E1E1E;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px #000;
    }
</style>

</head>
<body>
    <div class="container">
    <?php
include 'db.php';

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) die("Student ID not provided.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, course_id=? WHERE student_id=?");
    $stmt->bind_param("sssii", $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['course_id'], $student_id);
    $stmt->execute();
    echo "Student updated successfully!";
    header("Location: list_student.php");
}

$student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();
$courses = $conn->query("SELECT * FROM courses");
?>

<h2>Edit Student</h2>
<form method="POST" action="">
    <input type="text" name="name" value="<?= $student['name'] ?>" required><br><br>
    <input type="email" name="email" value="<?= $student['email'] ?>" required><br><br>
    <input type="text" name="phone" value="<?= $student['phone'] ?>"><br><br>
    <select name="course_id" required>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['course_id'] ?>" <?= $course['course_id'] == $student['course_id'] ? 'selected' : '' ?>>
                <?= $course['course_name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit">Update</button>
</form>

    </div>
</body>
</html>
