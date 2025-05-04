<?php
include 'db.php';
session_start();
// Check if user is logged in
if (!isset($_SESSION['loggedin']) ){
    header("Location: index.php");
    exit;
}
// Check if student_id is provided
if(isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);

    // First delete from studentfees table (due to foreign key constraint)
    $sql1 = "DELETE FROM studentfees WHERE student_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $student_id);
    $stmt1->execute();

    // Then delete from students table
    $sql2 = "DELETE FROM students WHERE student_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $student_id);
    
    if($stmt2->execute()) {
        header("Location: list_student.php?delete_success=1");
    } else {
        header("Location: list_student.php?delete_error=1");
    }
    exit;
} else {
    header("Location: list_student.php");
    exit;
}
?>