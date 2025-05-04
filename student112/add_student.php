<?php
include 'db.php';

// Initialize variables and error messages
$errors = [];
$name = $email = $phone = $course_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course_id = $_POST['course_id'] ?? '';
    
    
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Name can only contain letters and spaces';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } else {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
    }
    
    if (empty($course_id)) {
        $errors['course_id'] = 'Course selection is required';
    } elseif (!is_numeric($course_id)) {
        $errors['course_id'] = 'Invalid course selection';
    }
    
    if (empty($errors)) {
        // Start transaction to ensure both inserts succeed or fail together
        $conn->begin_transaction();
        
        try {
            // Insert into students table
            $sql = "INSERT INTO students (name, email, phone, course_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $email, $phone, $course_id);
            
            if ($stmt->execute()) {
                // Get the newly inserted student ID
                $student_id = $conn->insert_id;
                
                // Insert default record into studentfees table
                $fees_sql = "INSERT INTO studentfees (student_id, fees_status, last_updated) 
                             VALUES (?, 'pending', NOW())";
                $fees_stmt = $conn->prepare($fees_sql);
                $fees_stmt->bind_param("i", $student_id);
                
                if ($fees_stmt->execute()) {
                    // Commit both inserts
                    $conn->commit();
                    header("Location: list_student.php");
                    exit();
                } else {
                    throw new Exception("Error creating fee record: " . $conn->error);
                }
            } else {
                throw new Exception("Error saving student: " . $conn->error);
            }
        } catch (Exception $e) {
            // Roll back on any error
            $conn->rollback();
            $errors['database'] = $e->getMessage();
        }
    }
}

// Fetch all courses for dropdown
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #BB86FC;
            --primary-variant: #3700B3;
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
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-variant);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
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

        .required-field::after {
            content: " *";
            color: var(--error);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
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
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(187, 134, 252, 0.3);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23BB86FC' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 1rem;
        }

        .btn {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary);
            color: var(--on-primary);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background-color: #9b6cfc;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.3);
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        
        .input-error {
            border-color: var(--error) !important;
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

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            animation: fadeIn 0.6s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-user-plus"></i> Add New Student</h2>
        
        <?php if (!empty($errors['database'])): ?>
            <div style="color: var(--error); margin-bottom: 1rem;">
                <?= htmlspecialchars($errors['database']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name" class="required-field">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter student's full name" 
                       value="<?= htmlspecialchars($name) ?>" 
                       <?= isset($errors['name']) ? 'class="input-error"' : '' ?> required>
                <?php if (isset($errors['name'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['name']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email" class="required-field">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter student's email" 
                       value="<?= htmlspecialchars($email) ?>" 
                       <?= isset($errors['email']) ? 'class="input-error"' : '' ?> required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter contact number" 
                       value="<?= htmlspecialchars($phone) ?>" 
                       <?= isset($errors['phone']) ? 'class="input-error"' : '' ?>>
                <?php if (isset($errors['phone'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['phone']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="course_id" class="required-field">Enroll in Course</label>
                <select id="course_id" name="course_id" <?= isset($errors['course_id']) ? 'class="input-error"' : '' ?> required>
                    <option value="">-- Select a Course --</option>
                    <?php while($c = $courses->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['course_id']) ?>" 
                            <?= ($course_id == $c['course_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if (isset($errors['course_id'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['course_id']) ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Register Student
            </button>
        </form>
    </div>
</body>
</html>