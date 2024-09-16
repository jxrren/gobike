<?php
require_once 'database.php';
session_start();

class AdminBikes
{
    private $connection;
    private $user_id;
    private $user_email;
    private $user_type;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->checkUserLogin();
    }

    private function checkUserLogin()
    {
        if (isset($_SESSION['user_id']))
        {
            $this->user_id = $_SESSION['user_id'];
            $this->loadUserData();
        }
        else
        {
            $this->showLoginPrompt();
            exit;
        }
    }

    private function loadUserData()
    {
        $query = "SELECT email, type FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_row = $result->fetch_assoc();
        $this->user_email = $user_row['email'];
        $this->user_type = $user_row['type'];
    }

    private function showLoginPrompt()
    {
        echo '<p>You are not logged in. <a href="login.php">Login</a></p>';
    }

    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $renting_location = $this->connection->real_escape_string($_POST['renting_location']);
            $description = $this->connection->real_escape_string($_POST['description']);
            $cost_per_hour = $this->connection->real_escape_string($_POST['cost_per_hour']);

            $query = "INSERT INTO bikes (renting_location, description, cost_per_hour) VALUES (?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssd", $renting_location, $description, $cost_per_hour);
            if ($stmt->execute())
            {
                echo "<p>Bike added successfully!</p>";
            }
            else
            {
                echo "<p>Error: " . $this->connection->error . "</p>";
            }
        }
    }

    public function displayBikeList()
    {
        $query = "SELECT * FROM bikes";
        $result = $this->connection->query($query);

        if (!$result)
        {
            die("Query failed: " . $this->connection->error);
        }

        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Bikes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="top-nav-container">
        <nav class="top-nav">
            <ul>
                <li class="welcome">Welcome, ' . htmlspecialchars($this->user_email) . ' (' . htmlspecialchars($this->user_type) . ')</li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="list-bikes">
        <div class="tab-container">
            <button onclick="location.href=\'admin_rentals.php\'" style="display: block; margin: 0 auto;">Rental Management</button>
            <h1>Insert New Bike</h1>
            <form action="admin_bikes.php" method="POST">
                <label for="renting_location">Renting Location:</label>
                <input type="text" id="renting_location" name="renting_location" required><br><br>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required><br><br>
                <label for="cost_per_hour">Cost per Hour:</label>
                <input type="number" id="cost_per_hour" name="cost_per_hour" step="0.01" required><br><br>
                <button type="submit" class="btn">Add Bike</button>
            </form>
            <br>';

        if ($result->num_rows > 0)
        {
            echo '<h2>Existing Bikes</h2>
            <table border="1">
                <tr>
                    <th>Bike ID</th>
                    <th>Renting Location</th>
                    <th>Description</th>
                    <th>Cost per Hour</th>
                    <th>Actions</th>
                </tr>';
            while ($row = $result->fetch_assoc())
            {
                echo '<tr>
                    <td>' . htmlspecialchars($row['bike_id']) . '</td>
                    <td>' . htmlspecialchars($row['renting_location']) . '</td>
                    <td>' . htmlspecialchars($row['description']) . '</td>
                    <td>' . htmlspecialchars($row['cost_per_hour']) . '</td>
                    <td>
                        <a href="edit_bike.php?bike_id=' . $row['bike_id'] . '"><button class="btn">Edit</button></a>
                    </td>
                </tr>';
            }
            echo '</table>';
        }
        else
        {
            echo '<p>No bikes available.</p>';
        }

        echo '</div></div></body></html>';
    }
}

$adminBikes = new AdminBikes($connection);
$adminBikes->handlePostRequest();
$adminBikes->displayBikeList();
