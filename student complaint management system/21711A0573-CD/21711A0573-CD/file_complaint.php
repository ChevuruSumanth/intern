<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for form submission
$title = $category = $description = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $title = htmlspecialchars(trim($_POST['title']));
    $category = htmlspecialchars(trim($_POST['category']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Validate input
    if (empty($title) || empty($category) || empty($description)) {
        $error = "All fields are required.";
    } else {
        // Insert complaint into database
        $student_id = $_SESSION['user_id'];
        $status = "Pending"; // Default status for new complaints

        $sql = "INSERT INTO complaints (student_id, title, category, description, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $student_id, $title, $category, $description, $status);

        if ($stmt->execute()) {
            // Redirect to dashboard or view complaints page
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error occurred while filing complaint. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File a Complaint</title>
    <style>
        /* Google Font Import */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: darkslategray;
    margin: 0;
    padding: 0;
    color: #333;
    line-height: 1.6;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    max-width: 600px;
    width: 100%;
    background-color: lightcyan;
    background-image: url(https://img.freepik.com/free-photo/desk-with-objects_23-2147982345.jpg);
             background-repeat: no-repeat;
            background-size: cover;
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

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    font-size: 1.1em;
    margin-bottom: 8px;
    display: block;
    color: #555;
}

input[type=text], select, textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 1em;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type=text]:focus, select:focus, textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
    outline: none;
}

textarea {
    height: 150px;
    resize: vertical;
}

.error {
    color: red;
    font-size: 0.9em;
    margin-top: 5px;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

.button {
    padding: 12px 24px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1em;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 2em;
    }

    .button {
        padding: 10px 20px;
        font-size: 1em;
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
}

    </style>
</head>
<body>
    <div class="container">
        <h1>File a Complaint</h1>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="title">Complaint Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="" selected disabled>Select Category</option>
                    <option value="Academic">Academic</option>
                    <option value="Facilities">Facilities</option>
                    <option value="Administrative">Administrative</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="button-container">
                <button type="submit" class="button">Submit Complaint</button>
            </div>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
