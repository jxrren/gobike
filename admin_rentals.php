<?php
include 'database.php';
session_start();

class AdminRentals
{
    private $connection;
    private $user_id;
    private $user_email;
    private $user_type;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->initialize();
    }

    private function initialize()
    {
        if (isset($_SESSION['user_id']))
        {
            $this->user_id = $_SESSION['user_id'];
            $this->loadUserData();
        }
        else
        {
            $this->user_email = null;
            $this->user_type = null;
        }

        $this->handlePostRequests();
    }

    private function loadUserData()
    {
        $user_query = "SELECT email, type FROM users WHERE id = '{$this->user_id}'";
        $user_result = mysqli_query($this->connection, $user_query);
        $user_row = mysqli_fetch_assoc($user_result);
        $this->user_email = $user_row['email'];
        $this->user_type = $user_row['type'];
    }

    private function handlePostRequests()
    {
        if (isset($_POST['search_bike']) && $_POST['search_bike'] === 'Search Bike')
        {
            $_SESSION['current_tab'] = 'search_bike';
        }
        elseif (isset($_POST['search_user']) && $_POST['search_user'] === 'Search User')
        {
            $_SESSION['current_tab'] = 'search_user';
        }
        else
        {
            $_SESSION['current_tab'] = 'start_rental';
        }

        if (isset($_POST['start_rental']))
        {
            $this->startRental();
        }

        if (isset($_POST['end_rental']))
        {
            $this->endRental();
        }
    }

    private function startRental()
    {
        $user_email = $_POST['user_email'];
        $bike_name = $_POST['bike_name'];

        $user_query = "SELECT id FROM users WHERE email='{$user_email}'";
        $user_result = mysqli_query($this->connection, $user_query);
        $user_id = mysqli_fetch_assoc($user_result)['id'];

        $bike_query = "SELECT bike_id, cost_per_hour FROM bikes WHERE renting_location='{$bike_name}'";
        $bike_result = mysqli_query($this->connection, $bike_query);
        $bike_row = mysqli_fetch_assoc($bike_result);
        $bike_id = $bike_row['bike_id'];
        $cost_per_hour = $bike_row['cost_per_hour'];

        $start_time = date('Y-m-d H:i:s');
        $query = "INSERT INTO rentals (user_id, bike_id, start_time, end_time, total_cost) VALUES ('{$user_id}', '{$bike_id}', '{$start_time}', NULL, NULL)";
        $result = mysqli_query($this->connection, $query);

        if ($result)
        {
            echo "<script>alert('Rental started successfully!');</script>";
        }
        else
        {
            echo "<script>alert('Error: " . mysqli_error($this->connection) . "');</script>";
        }
    }


    private function endRental()
    {
        $user_email = $_POST['user_email'];
        $bike_name = $_POST['bike_name'];

        $user_query = "SELECT id FROM users WHERE email='{$user_email}'";
        $user_result = mysqli_query($this->connection, $user_query);
        $user_id = mysqli_fetch_assoc($user_result)['id'];

        $bike_query = "SELECT bike_id, cost_per_hour FROM bikes WHERE renting_location='{$bike_name}'";
        $bike_result = mysqli_query($this->connection, $bike_query);
        $bike_row = mysqli_fetch_assoc($bike_result);
        $bike_id = $bike_row['bike_id'];
        $cost_per_hour = $bike_row['cost_per_hour'];

        $rental_query = "SELECT start_time FROM rentals WHERE user_id='{$user_id}' AND bike_id='{$bike_id}' AND end_time IS NULL";
        $rental_result = mysqli_query($this->connection, $rental_query);
        $start_time_row = mysqli_fetch_assoc($rental_result);
        $start_time = $start_time_row['start_time'];

        $end_time = date('Y-m-d H:i:s');
        $start_time_timestamp = strtotime($start_time);
        $end_time_timestamp = strtotime($end_time);
        $rental_duration = $end_time_timestamp - $start_time_timestamp;
        $hours = ceil($rental_duration / 3600);
        $total_cost = ($hours < 1) ? 0 : $hours * $cost_per_hour;

        $update_query = "UPDATE rentals SET end_time='{$end_time}', total_cost='{$total_cost}' WHERE user_id='{$user_id}' AND bike_id='{$bike_id}' AND end_time IS NULL";
        $update_result = mysqli_query($this->connection, $update_query);

        if ($update_result)
        {
            echo "<script>alert('Rental ended successfully! Total cost: $" . number_format($total_cost, 2) . "');</script>";
        }
        else
        {
            echo "<script>alert('Error: " . mysqli_error($this->connection) . "');</script>";
        }
    }


    public function getAllBikes()
    {
        return mysqli_query($this->connection, "SELECT * FROM bikes");
    }

    public function getAvailableBikes()
    {
        return mysqli_query($this->connection, "SELECT * FROM bikes WHERE bike_id NOT IN (SELECT bike_id FROM rentals WHERE end_time IS NULL)");
    }

    public function getRentedBikes()
    {
        return mysqli_query($this->connection, "SELECT * FROM bikes WHERE bike_id IN (SELECT bike_id FROM rentals WHERE end_time IS NULL)");
    }

    public function getAllUsers()
    {
        return mysqli_query($this->connection, "SELECT * FROM users");
    }

    public function getRentingUsers()
    {
        return mysqli_query($this->connection, "SELECT DISTINCT u.* FROM users u JOIN rentals r ON u.id = r.user_id WHERE r.end_time IS NULL");
    }

    public function searchBikes($search_term)
    {
        $search_term = mysqli_real_escape_string($this->connection, $search_term);
        return mysqli_query($this->connection, "SELECT bike_id, renting_location, description, cost_per_hour FROM bikes WHERE renting_location LIKE '%$search_term%'");
    }

    public function searchUsers($search_term)
    {
        $search_term = mysqli_real_escape_string($this->connection, $search_term);
        return mysqli_query($this->connection, "SELECT id, name, surname, email FROM users WHERE email LIKE '%$search_term%'");
    }

    public function getCurrentTab()
    {
        return isset($_SESSION['current_tab']) ? $_SESSION['current_tab'] : 'start_rental';
    }

    public function getUserEmail()
    {
        return $this->user_email;
    }

    public function getUserType()
    {
        return $this->user_type;
    }
}

$adminRentals = new AdminRentals($connection);
$current_tab = $adminRentals->getCurrentTab();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Admin - Manage Rentals</title>
    <style>
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</head>

<body>
    <nav class="top-nav">
        <ul>
            <?php if ($adminRentals->getUserEmail()): ?>
                <li>Welcome, <?php echo htmlspecialchars($adminRentals->getUserEmail()); ?> (<?php echo htmlspecialchars($adminRentals->getUserType()); ?>)</li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <p>You are not logged in. <a href="login.php">Login</a></p>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="list-bikes">
        <div class="tab-container">
            <h1>Manage Rentals</h1>

            <button onclick="showTab('start_rental')">Start Rental</button>
            <button onclick="showTab('end_rental')">End Rental</button>
            <button onclick="showTab('list_all_bikes')">All Bikes</button>
            <button onclick="showTab('list_available_bikes')">Available Bikes</button>
            <button onclick="showTab('list_rented_bikes')">Unavailable Bikes</button>
            <button onclick="showTab('search_bike')">Search Bike</button>
            <button onclick="showTab('search_user')">Search User</button>
            <button onclick="showTab('list_all_users')">All Users</button>
            <button onclick="showTab('list_renting_users')">Current Users</button>

            <div id="start_rental" class="tab-content active">
                <h2>Start a Rental</h2>
                <form action="admin_rentals.php" method="POST">
                    <label for="user_email">User Email:</label>
                    <select name="user_email" id="user_email" required>
                        <?php
                        $users = $adminRentals->getAllUsers();
                        while ($user = mysqli_fetch_assoc($users))
                        {
                            echo "<option value=\"{$user['email']}\">{$user['email']}</option>";
                        }
                        ?>
                    </select>
                    <label for="bike_name">Bike Name:</label>
                    <select name="bike_name" id="bike_name" required>
                        <?php
                        $bikes = $adminRentals->getAvailableBikes();
                        while ($bike = mysqli_fetch_assoc($bikes))
                        {
                            echo "<option value=\"{$bike['renting_location']}\">{$bike['renting_location']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="start_rental">Start Rental</button>
                </form>
            </div>


            <div id="end_rental" class="tab-content">
                <h2>End a Rental</h2>
                <form action="admin_rentals.php" method="POST">
                    <label for="user_email">User Email:</label>
                    <select name="user_email" id="user_email" required>
                        <?php
                        $rentingUsers = $adminRentals->getRentingUsers();
                        while ($user = mysqli_fetch_assoc($rentingUsers))
                        {
                            echo "<option value=\"{$user['email']}\">{$user['email']}</option>";
                        }
                        ?>
                    </select>
                    <label for="bike_name">Bike Name:</label>
                    <select name="bike_name" id="bike_name" required>
                        <?php
                        $rentals = $adminRentals->getRentedBikes();
                        while ($rental = mysqli_fetch_assoc($rentals))
                        {
                            echo "<option value=\"{$rental['renting_location']}\">{$rental['renting_location']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="end_rental">End Rental</button>
                </form>
            </div>


            <div id="list_all_bikes" class="tab-content">
                <h2>All Bikes</h2>
                <?php
                $bikes = $adminRentals->getAllBikes();
                echo "<table><tr><th>Bike ID</th><th>Location</th><th>Description</th><th>Cost per Hour</th></tr>";
                while ($row = mysqli_fetch_assoc($bikes))
                {
                    echo "<tr><td>{$row['bike_id']}</td><td>{$row['renting_location']}</td><td>{$row['description']}</td><td>{$row['cost_per_hour']}</td></tr>";
                }
                echo "</table>";
                ?>
            </div>

            <div id="list_available_bikes" class="tab-content">
                <h2>Available Bikes</h2>
                <?php
                $bikes = $adminRentals->getAvailableBikes();
                echo "<table><tr><th>Bike ID</th><th>Location</th><th>Description</th><th>Cost per Hour</th></tr>";
                while ($row = mysqli_fetch_assoc($bikes))
                {
                    echo "<tr><td>{$row['bike_id']}</td><td>{$row['renting_location']}</td><td>{$row['description']}</td><td>{$row['cost_per_hour']}</td></tr>";
                }
                echo "</table>";
                ?>
            </div>

            <div id="list_rented_bikes" class="tab-content">
                <h2>Unavailable Bikes</h2>
                <?php
                $bikes = $adminRentals->getRentedBikes();
                echo "<table><tr><th>Bike ID</th><th>Location</th><th>Description</th><th>Cost per Hour</th></tr>";
                while ($row = mysqli_fetch_assoc($bikes))
                {
                    echo "<tr><td>{$row['bike_id']}</td><td>{$row['renting_location']}</td><td>{$row['description']}</td><td>{$row['cost_per_hour']}</td></tr>";
                }
                echo "</table>";
                ?>
            </div>

            <div id="search_bike" class="tab-content">
                <h2>Search Bike</h2>
                <form action="admin_rentals.php" method="POST">
                    <input type="text" name="search_term" placeholder="Search by Location" required>
                    <button type="submit" name="search_bike">Search Bike</button>
                </form>
                <?php
                if (isset($_POST['search_bike']))
                {
                    $search_term = $_POST['search_term'];
                    $bikes = $adminRentals->searchBikes($search_term);
                    echo "<table><tr><th>Bike ID</th><th>Location</th><th>Description</th><th>Cost per Hour</th></tr>";
                    while ($row = mysqli_fetch_assoc($bikes))
                    {
                        echo "<tr><td>{$row['bike_id']}</td><td>{$row['renting_location']}</td><td>{$row['description']}</td><td>{$row['cost_per_hour']}</td></tr>";
                    }
                    echo "</table>";
                }
                ?>
            </div>

            <div id="search_user" class="tab-content">
                <h2>Search User</h2>
                <form action="admin_rentals.php" method="POST">
                    <input type="text" name="search_term" placeholder="Search by Email" required>
                    <button type="submit" name="search_user">Search User</button>
                </form>
                <?php
                if (isset($_POST['search_user']))
                {
                    $search_term = $_POST['search_term'];
                    $users = $adminRentals->searchUsers($search_term);
                    echo "<table><tr><th>User ID</th><th>Name</th><th>Surname</th><th>Email</th></tr>";
                    while ($row = mysqli_fetch_assoc($users))
                    {
                        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['surname']}</td><td>{$row['email']}</td></tr>";
                    }
                    echo "</table>";
                }
                ?>
            </div>

            <div id="list_all_users" class="tab-content">
                <h2>All Users</h2>
                <?php
                $users = $adminRentals->getAllUsers();
                echo "<table><tr><th>User ID</th><th>Name</th><th>Surname</th><th>Email</th></tr>";
                while ($row = mysqli_fetch_assoc($users))
                {
                    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['surname']}</td><td>{$row['email']}</td></tr>";
                }
                echo "</table>";
                ?>
            </div>

            <div id="list_renting_users" class="tab-content">
                <h2>Users with Active Rentals</h2>
                <?php
                $users = $adminRentals->getRentingUsers();
                echo "<table><tr><th>User ID</th><th>Name</th><th>Surname</th><th>Email</th></tr>";
                while ($row = mysqli_fetch_assoc($users))
                {
                    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['surname']}</td><td>{$row['email']}</td></tr>";
                }
                echo "</table>";
                ?>
            </div>
</body>

</html>