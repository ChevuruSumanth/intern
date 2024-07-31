<?php
session_start();
include('config.php');

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch complaint details from database
if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    $sql = "SELECT * FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $complaint = $result->fetch_assoc();
    } else {
        echo "Complaint not found.";
        exit();
    }
} else {
    echo "Complaint ID not specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details</title>
    <style>
        body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 20px;
    color: #343a40;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background-color: fafefa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
}

h1 {
    text-align: center;
    color: #007bff;
    font-size: 2.5em;
    margin-bottom: 20px;
}

.complaint-info {
    margin-top: 30px;
    line-height: 1.6;
}

.complaint-info p {
    margin: 12px 0;
    font-size: 1.1em;
    color: #555;
}

.complaint-info p span {
    font-weight: 500;
    color: #000;
}

.back-link {
    text-align: center;
    margin-top: 30px;
}

.back-link a {
    text-decoration: none;
    background-color: #007bff;
    color: #fff;
    padding: 12px 24px;
    border-radius: 5px;
    font-size: 1em;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.back-link a:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 2em;
    }

    .complaint-info p {
        font-size: 1em;
    }

    .back-link a {
        padding: 10px 20px;
        font-size: 0.9em;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8em;
    }

    .complaint-info p {
        font-size: 0.9em;
    }

    .back-link a {
        padding: 8px 16px;
        font-size: 0.85em;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Complaint Details</h1>

        <div class="complaint-info">
            <p><strong>ID:</strong> <?php echo $complaint['id']; ?></p>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint['category']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
            <p><strong>Student ID:</strong> <?php echo $complaint['student_id']; ?></p>
            <!-- You can add more details here as needed -->
        </div>

        <div class="back-link">
            <a href="manage_complaints.php">Back to Manage Complaints</a>
        </div>
    </div>
</body>
</html>
