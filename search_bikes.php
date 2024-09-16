<?php
include 'database.php';

class Bike
{
    public static function searchBikes($query)
    {
        global $connection;
        $query = "SELECT * FROM bikes WHERE " . $query;
        $result = $connection->query($query);
        return $result;
    }
}

// Get the search query from POST request
$query = $_POST['query'];

// Search for bikes
$searchResults = Bike::searchBikes($query);

if ($searchResults->num_rows > 0)
{
    echo "<table border='1'>";
    echo "<tr><th>Bike ID</th><th>Renting Location</th><th>Description</th><th>Cost Per Hour</th><th>Rent</th></tr>";
    while ($row = $searchResults->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['bike_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['renting_location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>$" . htmlspecialchars($row['cost_per_hour']) . "</td>";
        echo "<td><button class='rent-button' data-bike-id='" . htmlspecialchars($row['bike_id']) . "'>Rent</button></td>";
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo "<p>No bikes found matching your criteria.</p>";
}

$connection->close();
