<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "d2";
session_start();
// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

// Update fees_status if form is submitted
// In fees.php, modify the update section:
if (isset($_POST['update_status'])) {
    $student_id = intval($_POST['student_id']);
    $new_status = $conn->real_escape_string($_POST['fees_status']);

    // Check if record exists
    $check_sql = "SELECT COUNT(*) FROM studentfees WHERE student_id = $student_id";
    $result = $conn->query($check_sql);
    $row = $result->fetch_row();
    $exists = $row[0] > 0;

    if ($exists) {
        $update_sql = "UPDATE studentfees SET fees_status = '$new_status', last_updated = NOW() WHERE student_id = $student_id";
    } else {
        $update_sql = "INSERT INTO studentfees (student_id, fees_status, last_updated) VALUES ($student_id, '$new_status', NOW())";
    }

    if ($conn->query($update_sql)) {
        $message = "Fee status updated successfully.";
        $message_type = "success";
    } else {
        $message = "Error updating fee status: " . $conn->error;
        $message_type = "error";
    }
}

// Send reminder email
require 'vendor/autoload.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['send_email'])) {
    $student_id = intval($_POST['student_id']);
    $sql = "SELECT s.name, s.email, f.fees_status FROM students s 
            JOIN studentfees f ON s.student_id = f.student_id 
            WHERE s.student_id = $student_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $name = $student['name'];
        $email = $student['email'];
        $status = $student['fees_status'];

        $subject = "Fee Payment Reminder";
        $message_body = "Dear $name,\n\n";
        $message_body .= "Your fee status is currently: " . ucfirst($status) . "\n";
        $message_body .= "Please complete your payment at the earliest.\n\n";
        $message_body .= "Regards,\nStudent Records Department";

        // Create PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
            $mail->SMTPAuth = true;  // Enable SMTP authentication
            $mail->Username = 'hetfaldu10198@gmail.com';   // Your Gmail address
            $mail->Password = 'foqs brds hdjp ibgz';      // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS encryption
            $mail->Port = 587;  // SMTP port

            // Set sender and recipient
            $mail->setFrom('hetfaldu10198@gmail.com', 'DDU Records');
            $mail->addAddress($email, $name);

            // Set email subject and body
            $mail->Subject = 'Regarding Fee Payment';
            $mail->Body    = 'Your fees is pending.';

            // Send email
            $mail->send();

            // Success message
            $message = "Reminder email sent to: $email";
            $message_type = "success";

            // Update last_email_sent timestamp in the database
            $update_email_time = "UPDATE studentfees SET last_email_sent = NOW() WHERE student_id = $student_id";
            $conn->query($update_email_time);

        } catch (Exception $e) {
            // Error handling
            $message = "Failed to send email. Mailer Error: " . $mail->ErrorInfo;
            $message_type = "error";
        }
    }
}

// Display student info if student_id is passed
if (isset($_GET['student_id']) || isset($_POST['student_id'])) {
    $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : intval($_POST['student_id']);

// In fees.php, modify your SQL query to:
    $sql = "SELECT s.student_id, s.name, s.email, IFNULL(f.fees_status, 'pending') as fees_status, 
    IFNULL(f.last_email_sent, 'Never') as last_email_sent, 
    IFNULL(f.last_updated, NOW()) as last_updated
    FROM students s
    LEFT JOIN studentfees f ON s.student_id = f.student_id
    WHERE s.student_id = $student_id";

    $result = $conn->query($sql);
    if ($result === false) {
        echo "SQL Error: " . $conn->error;
        exit;
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .detail-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #666;
        }

        .status-pending {
            color: var(--warning-color);
            font-weight: bold;
        }

        .status-paid {
            color: var(--success-color);
            font-weight: bold;
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: var(--dark-color);
            margin-left: 10px;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-warning {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-money-bill-wave"></i> Student Fee Management</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="detail-card">
            <div class="detail-row">
                <div class="detail-label">Student ID:</div>
                <div><?php echo htmlspecialchars($row['student_id']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Name:</div>
                <div><?php echo htmlspecialchars($row['name']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div><?php echo htmlspecialchars($row['email']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Fee Status:</div>
                <div class="<?php echo $row['fees_status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                    <?php echo ucfirst(htmlspecialchars($row['fees_status'])); ?>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Last Updated:</div>
                <div><?php echo htmlspecialchars($row['last_updated']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Last Email Sent:</div>
                <div><?php echo $row['last_email_sent'] ? htmlspecialchars($row['last_email_sent']) : 'Never'; ?></div>
            </div>
        </div>
        
        <div class="form-card">
        <div class="form-card">
    <form method="POST" action="">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        
        <div class="action-buttons">
            <button type="submit" name="send_email" class="btn btn-warning">
                <i class="fas fa-envelope"></i> Send Reminder
            </button>
            
            <a href="index1.php" class="btn btn-primary">
                <i class="fas fa-home"></i> HOME
            </a>
        </div>
    </form>
</div>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo "<div class='container'>No student found with ID: $student_id</div>";
    }
} else {
    echo "<div class='container'>No student selected.</div>";
}

$conn->close();
?>