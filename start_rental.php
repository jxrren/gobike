<?php
include 'database.php';

class Rental
{
    public static function startRental($bikeId, $userId)
    {
        global $connection;
        // Start the rental
        $query = "INSERT INTO rentals (user_id, bike_id, start_time) VALUES (?, ?, NOW())";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $bikeId);
        $success = $stmt->execute();

        if ($success)
        {
            // Fetch the cost per hour
            $query = "SELECT cost_per_hour FROM bikes WHERE bike_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $bikeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $bike = $result->fetch_assoc();
            return ['success' => true, 'cost_per_hour' => $bike['cost_per_hour']];
        }
        else
        {
            return ['success' => false];
        }
    }
}

// Get the bike ID and user ID from the request
$bikeId = $_POST['bike_id'];
$userId = $_POST['user_id'];

// Start the rental and get the result
$result = Rental::startRental($bikeId, $userId);

if ($result['success'])
{
    echo 'Rental started successfully! Cost per hour: $' . htmlspecialchars($result['cost_per_hour']);
}
else
{
    echo 'Error starting rental!';
}

$connection->close();
