<?php
include 'database.php';
session_start();

class User
{
    private $id;
    private $email;
    private $type;

    public function __construct($id)
    {
        global $connection;
        $this->id = $id;
        $this->loadUserData();
    }

    private function loadUserData()
    {
        global $connection;
        $query = "SELECT email, type FROM users WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $this->email = $user['email'];
        $this->type = $user['type'];
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getType()
    {
        return $this->type;
    }
}

class Bike
{
    public static function getAvailableBikes()
    {
        global $connection;
        $query = "SELECT * FROM bikes WHERE bike_id NOT IN (SELECT bike_id FROM rentals WHERE end_time IS NULL)";
        $result = $connection->query($query);
        return $result;
    }

    public static function getAllBikes()
    {
        global $connection;
        $query = "SELECT * FROM bikes";
        $result = $connection->query($query);
        return $result;
    }

    public static function getRentedBikes()
    {
        global $connection;
        $query = "SELECT b.*, r.user_id, u.email, r.start_time FROM bikes b JOIN rentals r ON b.bike_id = r.bike_id JOIN users u ON r.user_id = u.id WHERE r.end_time IS NULL";
        $result = $connection->query($query);
        return $result;
    }

    public static function getRentedByUser($userId)
    {
        global $connection;
        $query = "SELECT b.*, r.rental_id, r.start_time FROM bikes b JOIN rentals r ON b.bike_id = r.bike_id WHERE r.user_id = ? AND r.end_time IS NULL";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public static function getAllPastRentals()
    {
        global $connection;
        $query = "SELECT b.bike_id, b.renting_location, b.description, u.email, r.start_time, r.end_time, r.total_cost
                  FROM bikes b
                  JOIN rentals r ON b.bike_id = r.bike_id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.end_time IS NOT NULL
                  ORDER BY r.end_time DESC"; // Order by end_time descending
        $result = $connection->query($query);
        return $result;
    }
}


class Rental
{
    public static function startRental($bikeId, $userId)
    {
        global $connection;
        $query = "INSERT INTO rentals (user_id, bike_id, start_time) VALUES (?, ?, NOW())";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $bikeId);
        $stmt->execute();
    }

    public static function endRental($rentalId, $endTime, $totalCost)
    {
        global $connection;
        $query = "UPDATE rentals SET end_time = ?, total_cost = ? WHERE rental_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sdi", $endTime, $totalCost, $rentalId);
        $stmt->execute();
    }
}

class Search
{
    public static function searchBikes($query)
    {
        global $connection;
        $query = "SELECT * FROM bikes WHERE " . $query;
        $result = $connection->query($query);
        return $result;
    }
}

// Check if the user is logged in
if (isset($_SESSION['user_id']))
{
    $userId = $_SESSION['user_id'];
    $user = new User($userId);
    $userEmail = $user->getEmail();
    $userType = $user->getType();
?>
    <link rel="stylesheet" href="style.css">
    <div class="top-nav-container">
        <ul>
            <li class="welcome">Welcome, <span><?php echo htmlspecialchars($userEmail); ?></span> (<span><?php echo htmlspecialchars($userType); ?></span>)</li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
<?php
}
else
{
?>
    <p>You are not logged in. <a href="login.php">Login</a></p>
<?php
}
?>

<div class="list-bikes">
    <div class="tab-container">
        <ul class="tabs">
            <li><button class="tab-link" data-tab="available_bikes">Available Bikes</button></li>
            <li><button class="tab-link" data-tab="search_bike">Search Bike</button></li>
            <li><button class="tab-link" data-tab="rented_by_me">Rented By You</button></li>
            <li><button class="tab-link" data-tab="all_bikes">All Bikes</button></li>
            <li><button class="tab-link" data-tab="rented_bikes">All Rented Bikes</button></li>
            <li><button class="tab-link" data-tab="past_rentals">Past Rentals</button></li>
        </ul>

        <div id="available_bikes" class="tab-content active">
            <h2>Currently Available Bikes</h2>
            <?php
            $availableBikes = Bike::getAvailableBikes();
            if ($availableBikes->num_rows > 0)
            {
                echo "<table border='1'>";
                echo "<tr><th>Bike ID</th><th>Renting Location</th><th>Description</th><th>Cost Per Hour</th><th>Rent</th></tr>";
                while ($row = $availableBikes->fetch_assoc())
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
                echo "<p>No available bikes.</p>";
            }
            ?>
        </div>

        <div id="rented_by_me" class="tab-content">
            <h2>Currently Rented By Me</h2>
            <?php
            $rentedByMe = Bike::getRentedByUser($userId);
            if ($rentedByMe->num_rows > 0)
            {
                echo "<table border='1'>";
                echo "<tr><th>BikeID</th><th>Renting Location</th><th>Description</th><th>Cost per Hour</th><th>Return</th></tr>";
                while ($row = $rentedByMe->fetch_assoc())
                {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bike_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['renting_location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>$" . htmlspecialchars($row['cost_per_hour']) . "</td>";
                    echo "<td><button class='return-button' data-bike-id='" . htmlspecialchars($row['bike_id']) . "' data-rental-id='" . htmlspecialchars($row['rental_id']) . "' data-start-time='" . htmlspecialchars($row['start_time']) . "' data-cost-per-hour='" . htmlspecialchars($row['cost_per_hour']) . "'>Return</button></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "<p>No bikes currently rented by you.</p>";
            }
            ?>
        </div>

        <div id="search_bike" class="tab-content">
            <h2>Search for a Bike</h2>
            <form id="search_form">
                <label for="bike_id">Bike ID:</label><br>
                <input type="text" id="bike_id" name="bike_id"><br>
                <label for="renting_location">Renting Location:</label><br>
                <input type="text" id="renting_location" name="renting_location"><br>
                <label for="description">Description:</label><br>
                <input type="text" id="description" name="description"><br>
                <input type="submit" value="Search">
            </form>
            <div id="search_results"></div>
            <script>
                document.getElementById('search_form').addEventListener('submit', function(event) {
                    event.preventDefault();
                    var bikeId = document.getElementById('bike_id').value;
                    var rentingLocation = document.getElementById('renting_location').value;
                    var description = document.getElementById('description').value;
                    var query = '';
                    if (bikeId) query += 'bike_id LIKE "%' + bikeId + '%" AND ';
                    if (rentingLocation) query += 'renting_location LIKE "%' + rentingLocation + '%" AND ';
                    if (description) query += 'description LIKE "%' + description + '%" AND ';
                    if (query) query = query.slice(0, -5);
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'search.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        document.getElementById('search_results').innerHTML = this.responseText;
                    };
                    xhr.send('query=' + encodeURIComponent(query));
                });
            </script>
        </div>

        <div id="all_bikes" class="tab-content">
            <h2>All Bikes</h2>
            <?php
            $allBikes = Bike::getAllBikes();
            if ($allBikes->num_rows > 0)
            {
                echo "<table border='1'>";
                echo "<tr><th>Bike ID</th><th>Renting Location</th><th>Description</th><th>Cost Per Hour</th></tr>";
                while ($row = $allBikes->fetch_assoc())
                {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bike_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['renting_location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>$" . htmlspecialchars($row['cost_per_hour']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "<p>No bikes available.</p>";
            }
            ?>
        </div>

        <div id="rented_bikes" class="tab-content">
            <h2>All Rented Bikes</h2>
            <?php
            $rentedBikes = Bike::getRentedBikes();
            if ($rentedBikes->num_rows > 0)
            {
                echo "<table border='1'>";
                echo "<tr><th>Bike ID</th><th>Renting Location</th><th>Description</th><th>Rented By</th><th>Start Time</th></tr>";
                while ($row = $rentedBikes->fetch_assoc())
                {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bike_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['renting_location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "<p>No bikes currently rented.</p>";
            }
            ?>
        </div>

        <div id="past_rentals" class="tab-content">
            <h2>Past Rentals</h2>
            <?php
            // Fetch all past rentals
            $pastRentals = Bike::getAllPastRentals();

            if ($pastRentals->num_rows > 0)
            {
                echo "<table border='1'>";
                echo "<tr><th>Bike ID</th><th>Renting Location</th><th>Description</th><th>User Email</th><th>Start Time</th><th>End Time</th><th>Total Cost</th></tr>";
                while ($row = $pastRentals->fetch_assoc())
                {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bike_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['renting_location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                    echo "<td>$" . htmlspecialchars($row['total_cost']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "<p>No past rentals found.</p>";
            }
            ?>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tab-link').forEach(function(tabLink) {
                tabLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    var tabId = tabLink.getAttribute('data-tab');
                    var tabContent = document.getElementById(tabId);
                    var currentTab = document.querySelector('.tab-content.active');
                    if (currentTab) {
                        currentTab.classList.remove('active');
                        currentTab.style.display = 'none';
                    }
                    tabContent.classList.add('active');
                    tabContent.style.display = 'block';
                });
            });

            document.querySelectorAll('.tab-content').forEach(function(tabContent) {
                if (!tabContent.classList.contains('active')) {
                    tabContent.style.display = 'none';
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.rent-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    var bikeId = this.getAttribute('data-bike-id');
                    var userId = <?php echo json_encode($userId); ?>;

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'start_rental.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        alert(this.responseText); // Show the result
                    };
                    xhr.send('bike_id=' + encodeURIComponent(bikeId) + '&user_id=' + encodeURIComponent(userId));
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.return-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    var bikeId = this.getAttribute('data-bike-id');
                    var rentalId = this.getAttribute('data-rental-id');
                    var startTime = this.getAttribute('data-start-time');
                    var costPerHour = parseFloat(this.getAttribute('data-cost-per-hour'));

                    var endTime = new Date().toISOString().slice(0, 19).replace('T', ' ');

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'end_rental.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        alert(this.responseText); // Show the result
                    };
                    xhr.send('rental_id=' + encodeURIComponent(rentalId) +
                        '&end_time=' + encodeURIComponent(endTime));
                });
            });
        });

        function calculateTotalCost(startTime, endTime, costPerHour) {
            var start = new Date(startTime);
            var end = new Date(endTime);
            var hours = Math.ceil((end - start) / (1000 * 60 * 60)); // Round up to the next whole hour
            return (hours * costPerHour).toFixed(2);
        }
    </script>



    <div id="search_bike" class="tab-content">
        <h2>Search for a Bike</h2>
        <form id="search_form">
            <label for="bike_id">Bike ID:</label><br>
            <input type="text" id="bike_id" name="bike_id"><br>
            <label for="renting_location">Renting Location:</label><br>
            <input type="text" id="renting_location" name="renting_location"><br>
            <label for="description">Description:</label><br>
            <input type="text" id="description" name="description"><br>
            <input type="submit" value="Search">
        </form>
        <div id="search_results"></div>
        <script>
            document.getElementById('search_form').addEventListener('submit', function(event) {
                event.preventDefault();
                var bikeId = document.getElementById('bike_id').value;
                var rentingLocation = document.getElementById('renting_location').value;
                var description = document.getElementById('description').value;
                var query = '';
                if (bikeId) query += 'bike_id LIKE "%' + bikeId + '%" AND ';
                if (rentingLocation) query += 'renting_location LIKE "%' + rentingLocation + '%" AND ';
                if (description) query += 'description LIKE "%' + description + '%" AND ';
                if (query) query = query.slice(0, -5);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'search_bikes.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    document.getElementById('search_results').innerHTML = this.responseText;
                    document.querySelectorAll('.rent-button').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var bikeId = this.getAttribute('data-bike-id');
                            var userId = <?php echo json_encode($userId); ?>;

                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'start_rental.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                alert(this.responseText); // Show the result
                            };
                            xhr.send('bike_id=' + encodeURIComponent(bikeId) + '&user_id=' + encodeURIComponent(userId));
                        });
                    });
                };
                xhr.send('query=' + encodeURIComponent(query));
            });
        </script>
    </div>