<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        $msg = "";
        $usernameErr = "";
        $passwordErr = "";
        $username = $_POST['username'];
        $password = $_POST['password'];

        function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if (empty($_POST["username"])) {
            $nameErr = "Username is required";
        } else {
            $username = test_input($_POST["username"]);
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $username)) {
                $nameErr = "Only letters and white space allowed";
            }
        }

        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
        }

            $conn = new PDO("sqlite:database.db");
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // creates table if it does not exist
            $tables = $conn->prepare ("CREATE TABLE IF NOT EXISTS users (username TEXT NOT NULL, password TEXT NOT NULL)");
            $tables->execute();

            $password = password_hash($password, PASSWORD_DEFAULT, ["cost" => 10]);

            $sql = $conn->prepare ("INSERT INTO users (username, password) VALUES (:username, :password)");
            $sql->bindParam(':username', $username);
            $sql->bindParam('password', $password);

            if ($sql->execute()) {
                $msg = "$username have been registered!";
                $conn = null;
                header("refresh:3;url= /");
            } else {
                $msg = "Something went wrong, try again later.";
            }
    } catch(PDOException $e)
    {
        die( "Connection failed: " . $e->getMessage());
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        .error {
            color: red;
            font-size: larger;
        }
    </style>
</head>
<body>

    <?php if($msg != "") echo $msg . "<br>"; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label>
            <input name="username" type="text"  placeholder="username" minlength="3" maxlength="12" autofocus required>
        </label>
        <label>
            <input name="password" type="password" minlength="10" maxlength="40" placeholder="password" required>
        </label>
        <input name="submit" type="submit" value="register">
    </form>
</body>
</html>