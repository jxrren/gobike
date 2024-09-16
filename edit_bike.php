<?php
require_once 'database.php';
session_start();

class EditBike
{
    private $connection;
    private $message = "";
    private $bike = null;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->checkUserLogin();
        $this->handlePostRequest();
        $this->fetchBikeDetails();
    }

    private function checkUserLogin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Admin')
        {
            header("Location: login.php");
            exit();
        }
    }

    private function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $bike_id = $this->connection->real_escape_string($_POST['bike_id']);
            $renting_location = $this->connection->real_escape_string($_POST['renting_location']);
            $description = $this->connection->real_escape_string($_POST['description']);
            $cost_per_hour = $this->connection->real_escape_string($_POST['cost_per_hour']);

            if (!empty($bike_id))
            {
                $query = "UPDATE bikes SET renting_location=?, description=?, cost_per_hour=? WHERE bike_id=?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("ssdi", $renting_location, $description, $cost_per_hour, $bike_id);
                if ($stmt->execute())
                {
                    $this->message = "<p>Bike updated successfully!</p>";
                    header("Refresh:2; url=admin_bikes.php");
                    exit();
                }
                else
                {
                    $this->message = "<p>Error: " . $this->connection->error . "</p>";
                }
            }
            else
            {
                $this->message = "<p>No bike ID provided.</p>";
            }
        }
    }

    private function fetchBikeDetails()
    {
        if (isset($_GET['bike_id']))
        {
            $bike_id = $this->connection->real_escape_string($_GET['bike_id']);
            $query = "SELECT * FROM bikes WHERE bike_id=?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $bike_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result)
            {
                $this->bike = $result->fetch_assoc();
            }
            else
            {
                die("Query failed: " . $this->connection->error);
            }
        }
        else
        {
            $this->message = "No bike ID provided.";
        }
    }

    public function render()
    {
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Bike</title>
</head>
<body>
    <div class="list-bikes">
        <div class="tab-container">
            <h1>Edit Bike</h1>';
        if ($this->message)
        {
            echo $this->message;
        }
        if ($this->bike)
        {
            echo '<form action="edit_bike.php" method="POST">
                <input type="hidden" name="bike_id" value="' . htmlspecialchars($this->bike['bike_id']) . '">
                <label for="renting_location">Renting Location:</label>
                <input type="text" id="renting_location" name="renting_location" value="' . htmlspecialchars($this->bike['renting_location']) . '" required><br><br>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" value="' . htmlspecialchars($this->bike['description']) . '" required><br><br>
                <label for="cost_per_hour">Cost per Hour:</label>
                <input type="number" id="cost_per_hour" name="cost_per_hour" step="0.01" value="' . htmlspecialchars($this->bike['cost_per_hour']) . '" required><br><br>
                <button type="submit">Update Bike</button>
            </form>';
        }
        echo '<br>
            <button onclick="location.href=\'admin_bikes.php\'">Back to Existing Bikes</button>
        </div>
    </div>
</body>
</html>';
    }
}

$editBike = new EditBike($connection);
$editBike->render();
