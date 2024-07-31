<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Function to get user's username based on user_id
function getUserUsername($conn, $user_id) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f8f9fa;
    background-image: url(https://i.pinimg.com/736x/c2/26/48/c22648ceb5a6f1a45742c2a9890f5c34.jpg);
             background-repeat: no-repeat;
            background-size: cover;
    margin: 0;
    padding: 20px;
    color: #343a40;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background-color: lightblue;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

h1 {
    text-align: center;
    color: #007bff;
    font-size: 2.5em;
    margin-bottom: 20px;
}

.welcome {
    margin-bottom: 20px;
    text-align: center;
    font-size: 1.5em;
    color: #555;
}

.logout-link {
    text-align: center;
    margin-top: 20px;
    padding: 15px;
}

.logout-link a {
    text-decoration: none;
    background-color: #007bff;
    color: #fff;
    padding: 12px 24px;
    border-radius: 5px;
    font-size: 1em;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.logout-link a:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

.button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    margin: 10px;
    font-size: 1em;
}

.button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.admin-actions {
    margin-top: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 10px;
    transition: border-color 0.3s ease;
}

.admin-actions:hover {
    border-color: #ccc;
}

.admin-actions h2 {
    margin-bottom: 20px;
    color: #007bff;
    font-size: 1.8em;
}

.admin-actions ul {
    list-style-type: none;
    padding: 0;
}

.admin-actions li {
    margin-bottom: 10px;
    font-size: 1.1em;
    color: #555;
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 2em;
    }

    .welcome {
        font-size: 1.1em;
    }

    .logout-link a {
        padding: 10px 20px;
        font-size: 0.9em;
    }

    .button {
        padding: 8px 16px;
        font-size: 0.9em;
    }

    .admin-actions h2 {
        font-size: 1.6em;
    }

    .admin-actions li {
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8em;
    }

    .welcome {
        font-size: 1em;
    }

    .logout-link a {
        padding: 8px 16px;
        font-size: 0.85em;
    }

    .button {
        padding: 8px 16px;
        font-size: 0.85em;
    }

    .admin-actions h2 {
        font-size: 1.4em;
    }

    .admin-actions li {
        font-size: 0.9em;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        
        <div class="welcome">
            <p>Welcome, <?php echo htmlspecialchars(getUserUsername($conn, $user_id)); ?>!</p>
        </div>

        <?php if ($role === 'admin'): ?>
            <div class="admin-actions">
                <h2>Admin Actions:</h2>
                <ul>
                    <li><a href="manage_complaints.php" class="button">Manage Complaints</a></li>
                    <!-- Add more admin-specific actions/buttons here -->
                </ul>
            </div>
        <?php else: ?>
            <div class="button-container">
                <a href="file_complaint.php" class="button">File a Complaint</a>
                <a href="view_complaints.php" class="button">View Your Complaints</a>
                <!-- Add more student-specific actions/buttons here -->
            </div>
        <?php endif; ?>

        <div class="logout-link">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
