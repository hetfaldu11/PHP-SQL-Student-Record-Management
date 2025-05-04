<?php
session_start();
// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --danger-color: #f72585;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding: 2rem;
            color: var(--dark-color);
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        .welcome-message {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .card-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .card-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .dashboard-link {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: var(--transition);
            width: auto;
            margin-top: auto;
        }

        .dashboard-link:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .logout-container {
            text-align: center;
            margin-top: 3rem;
        }

        .logout-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(to right, var(--danger-color), #ff4da6);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(247, 37, 133, 0.2);
        }

        .logout-btn:hover {
            background: linear-gradient(to right, #ff4da6, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(247, 37, 133, 0.3);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-card {
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
        }

        .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
        .dashboard-card:nth-child(4) { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p class="welcome-message">Welcome back, Administrator</p>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-card" style="animation-delay: 0.1s;">
            <div class="card-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3 class="card-title">Add Student</h3>
            <p class="card-description">Register new students to the system</p>
            <a href="add_student.php" class="dashboard-link">Go to Page</a>
        </div>

        <div class="dashboard-card" style="animation-delay: 0.2s;">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="card-title">List Students</h3>
            <p class="card-description">View and manage all registered students</p>
            <a href="list_student.php" class="dashboard-link">Go to Page</a>
        </div>

        <div class="dashboard-card" style="animation-delay: 0.3s;">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <h3 class="card-title">List Courses</h3>
            <p class="card-description">View and manage all available courses</p>
            <a href="list_courses.php" class="dashboard-link">Go to Page</a>
        </div>

        <div class="dashboard-card" style="animation-delay: 0.4s;">
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h3 class="card-title">Fee Management</h3>
            <p class="card-description">Manage student fee payments and status</p>
            <a href="list_student2.php" class="dashboard-link">Go to Page</a>
        </div>
    </div>

    <div class="logout-container">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>