<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user's complaints from database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM complaints WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize complaints array
$complaints = [];

// Fetch complaints into an array
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f0f0f0;
    background-image: url(https://e0.pxfuel.com/wallpapers/231/938/desktop-wallpaper-graduation-background-graduate-student.jpg);
             background-repeat: no-repeat;
            background-size: cover;
    margin: 0;
    padding: 20px;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 800px;
    margin: 40px auto;
    background-color: lightcyan;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

h1 {
    text-align: center;
    color: #007bff;
    font-size: 2.5rem;
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 1rem;
}

th, td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
}

th {
    background-color: lightgoldenrodyellow;
    font-weight: 500;
    font-size: 1.1rem;
}

td:hover {
    background-color: #feeffe;
}

tr:nth-child(even) td {
    background-color: #f9f9f9;
}

tr:nth-child(odd) td {
    background-color: #fff;
}

tr:hover td {
    background-color: #f1f1f1;
}

button, .button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
}

button:hover, .button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.pagination {
    margin-top: 30px;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 5px;
}

.pagination a {
    padding: 10px 15px;
    text-decoration: none;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}

.pagination a:hover:not(.active) {
    background-color: #e9ecef;
    color: #007bff;
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 2rem;
    }

    button, .button {
        padding: 8px 16px;
        font-size: 0.9rem;
    }

    th, td {
        padding: 12px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8rem;
    }

    button, .button {
        padding: 6px 14px;
        font-size: 0.8rem;
    }

    th, td {
        padding: 10px;
    }

    .pagination a {
        padding: 8px 10px;
        font-size: 0.8rem;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>View Your Filed Complaints</h1>

        <?php if (count($complaints) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No complaints filed yet.</p>
        <?php endif; ?>
        
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
