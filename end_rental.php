<?php
include 'database.php';

class Rental
{
    public static function endRental($rentalId)
    {
        global $connection;

        // Fetch the start time and bike ID from the rental record
        $query = "SELECT start_time, bike_id FROM rentals WHERE rental_id = ? AND end_time IS NULL";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $rentalId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rental = $result->fetch_assoc();

        if (!$rental)
        {
            return ['success' => false, 'message' => 'Rental not found or already ended'];
        }

        $startTime = new DateTime($rental['start_time']);
        $endTime = new DateTime();
        $endTime->modify('+6 hours');

        $bikeId = $rental['bike_id'];

        // Calculate duration in hours
        $interval = $endTime->diff($startTime);
        $hours = $interval->h + ($interval->days * 24);
        if ($interval->i > 0 || $interval->s > 0)
        {
            $hours += 1; // Round up if any minutes or seconds
        }

        // Fetch the cost per hour of the bike
        $query = "SELECT cost_per_hour FROM bikes WHERE bike_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $bikeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bike = $result->fetch_assoc();

        if (!$bike)
        {
            return ['success' => false, 'message' => 'Bike not found'];
        }

        $costPerHour = $bike['cost_per_hour'];

        // Calculate total cost
        $totalCost = $hours * $costPerHour;
        $totalCostFormatted = number_format($totalCost, 2);

        // Update the rental with end time and total cost
        $query = "UPDATE rentals SET end_time = ?, total_cost = ? WHERE rental_id = ?";
        $stmt = $connection->prepare($query);

        $endTimeFormatted = $endTime->format('Y-m-d H:i:s');
        $stmt->bind_param("sdi", $endTimeFormatted, $totalCostFormatted, $rentalId);
        $success = $stmt->execute();

        return [
            'success' => $success,
            'total_cost' => $totalCostFormatted
        ];
    }
}

// Get the rental ID from POST request
$rentalId = $_POST['rental_id'];

// End the rental and get the result
$result = Rental::endRental($rentalId);

if ($result['success'])
{
    echo 'Rental ended successfully! Total cost: $' . htmlspecialchars($result['total_cost']);
}
else
{
    echo 'Error ending rental: ' . htmlspecialchars($result['message']);
}

$connection->close();
