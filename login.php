<?php
session_start();
include 'database.php';

class UserLogin
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function loginUser($email, $password, $user_type_selected)
    {
        $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) == 1)
        {
            $user = mysqli_fetch_assoc($result);

            if ($user['type'] == 'Admin' || $user['type'] == $user_type_selected)
            {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['type'];

                if ($user['type'] == 'Admin')
                {
                    header("Location: admin_bikes.php");
                }
                else
                {
                    header("Location: list_bikes.php");
                }
                exit();
            }
            else
            {
                return "Invalid user type selection.";
            }
        }
        else
        {
            return "Invalid email or password.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $login = new UserLogin($connection);
    $error_message = $login->loginUser($_POST['email'], $_POST['password'], $_POST['type']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="login">
        <title>Login</title>
        <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <h2>Login</h2>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
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
                <input type="submit" value="Login">
            </div>
        </form>
        <button onclick="location.href='register.php'">Don't have an account? Register</button>
    </div>
</body>

</html>