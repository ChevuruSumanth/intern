<?php
session_start();
include('config.php');

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Pagination variables
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch complaints from database with pagination
$sql = "SELECT * FROM complaints LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Initialize complaints array
$complaints = [];

// Fetch complaints into an array
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

// Function to get user's full name based on user_id
function getUserFullName($conn, $user_id) {
    $sql = "SELECT full_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['full_name'];
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
        header("Location: manage_complaints.php?page=$page");
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $complaint_id = $_POST['complaint_id'];

        $sql = "DELETE FROM complaints WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: manage_complaints.php?page=$page");
        exit();
    }
}

// Count total number of complaints for pagination
$sql_count = "SELECT COUNT(*) AS total FROM complaints";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_records = $row_count['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints</title>
    <style>
        /* Google Font Import */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: green;
    margin: 0;
    padding: 20px;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

h1 {
    text-align: center;
    color: #007bff;
    font-size: 2.5em;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 1em;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
}

th {
    background-color: blanchedalmond;
    font-weight: bold;
}

td:hover {
    background-color: #f9f9f9;
}

.status-form {
    display: inline-block;
}

.button-container {
    margin-top: 10px;
    text-align: center;
}

.button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
}

.button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.pagination {
    margin-top: 20px;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 5px;
}

.pagination a {
    padding: 10px 15px;
    text-decoration: none;
    border: 1px solid #ccc;
    margin: 0 4px;
    border-radius: 5px;
    font-size: 1em;
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
    border: 1px solid #007bff;
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
        font-size: 2em;
    }

    .button {
        padding: 8px 16px;
        font-size: 1em;
    }

    th, td {
        padding: 10px 12px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8em;
    }

    .button {
        padding: 8px 16px;
        font-size: 0.9em;
    }

    th, td {
        padding: 8px 10px;
    }

    .pagination a {
        padding: 8px 10px;
        font-size: 0.9em;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Complaints</h1>

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
                        <td><?php echo getUserFullName($conn, $complaint['student_id']); ?></td>
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

        <div class="button-container">
            <?php if ($page > 1): ?>
                <a href="manage_complaints.php?page=<?php echo $page - 1; ?>" class="button">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="manage_complaints.php?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="manage_complaints.php?page=<?php echo $page + 1; ?>" class="button">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
