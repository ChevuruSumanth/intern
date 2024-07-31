<?php
session_start();
include('config.php');

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all complaints from the database
$sql = "SELECT * FROM complaints";
$result = $conn->query($sql);

// Initialize complaints array
$complaints = [];

// Fetch complaints into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

// Function to get user's username based on user_id (for demo purpose)
function getUsername($conn, $user_id) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['username'];
    } else {
        return "Unknown User";
    }
}

// Handle actions (update status, delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
        $complaint_id = $_POST['complaint_id'];
        $new_status = $_POST['new_status'];

        $sql = "UPDATE complaints SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $complaint_id);
        $stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: admin_dashboard.php");
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $complaint_id = $_POST['complaint_id'];

        $sql = "DELETE FROM complaints WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f8f9fa;
    background-image: url(https://media.b2broker.com/app/uploads/2021/02/b2trader-updates-feb-3-1-800x451.png);
             background-repeat: no-repeat;
            background-size: cover;
    margin: 0;
    padding: 20px;
    color: #343a40;
}

.container {
    max-width: 1000px;
    margin: 20px auto;
    background-color: lightcyan;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #007bff;
    font-size: 2.5em;
    margin-bottom: 20px;
}

.logout-button {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    table-layout: fixed;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

th {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
}

td {
    background-color: #f2f2f2;
}

.status-form {
    display: inline-block;
    margin: 0;
}

.select, select {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 1em;
}

form {
    display: inline-block;
    margin: 0;
}

.table-container {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
}

button.button {
    margin-top: 10px;
    display: inline-block;
    padding: 8px 16px;
    font-size: 0.9em;
}

@media (max-width: 768px) {
    h1 {
        font-size: 2em;
    }

    .container {
        padding: 15px;
    }

    th, td {
        padding: 10px;
    }

    .button {
        font-size: 0.9em;
        padding: 8px 15px;
    }

    button.button {
        padding: 7px 14px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard - Manage Complaints</h1>
        <div class="logout-button">
            <form method="POST" action="logout.php">
                <button type="submit" class="button">Logout</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?php echo $complaint['id']; ?></td>
                        <td><?php echo getUsername($conn, $complaint['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['category']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                        <td>
                            <form class="status-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                <select name="new_status">
                                    <option value="Pending" <?php echo ($complaint['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?php echo ($complaint['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <input type="hidden" name="action" value="update_status">
                                <button type="submit" class="button">Update</button>
                            </form>
                        </td>
                        <td>
                            <form class="status-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="button" onclick="return confirm('Are you sure you want to delete this complaint?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
