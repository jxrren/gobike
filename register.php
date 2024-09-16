<?php
include 'database.php';

class UserRegistration
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function registerUser($name, $surname, $phone, $email, $type, $password)
    {
        if ($this->emailExists($email))
        {
            return "Email is already registered. Please use a different email.";
        }
        else
        {
            return $this->insertUser($name, $surname, $phone, $email, $type, $password);
        }
    }

    private function emailExists($email)
    {
        $check_query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($this->connection, $check_query);

        return mysqli_num_rows($result) > 0;
    }

    private function insertUser($name, $surname, $phone, $email, $type, $password)
    {
        $query = "INSERT INTO users (name, surname, phone, email, type, password) VALUES ('$name', '$surname', '$phone', '$email', '$type', '$password')";

        if (mysqli_query($this->connection, $query))
        {
            header("Location: login.php");
            exit();
        }
        else
        {
            return "An error occurred: " . mysqli_error($this->connection);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $registration = new UserRegistration($connection);
    $error_message = $registration->registerUser($_POST['name'], $_POST['surname'], $_POST['phone'], $_POST['email'], $_POST['type'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <div class="login">
        <title>Register</title>
</head>

<body>
    <div class="wrapper">
        <h2>Register</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="input-box">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-box">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div class="input-box">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-box">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-box">
                <label for="type">User Type:</label>
                <select id="type" name="type" required>
                    <option value="User">User</option>
                    <option value="Admin">Administrator</option>
                </select>
            </div>
            <div class="input-box button">
                <input type="submit" value="Register">
            </div>
            <div class="text">
                <h3>Already have an account? <a href="login.php">Login</a></h3>
            </div>
        </form>
    </div>
</body>

</html>