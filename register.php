<?php

$msg = "";

if(isset($_POST['submit'])) {
    try {
        $conn = new PDO("sqlite:database.db");
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // creates table if it does not exist
        $tables = $conn->prepare ("CREATE TABLE IF NOT EXISTS users (username TEXT NOT NULL, password TEXT NOT NULL)");
        $tables->execute();
        $username = $_POST['username'];
        $password = $_POST['password'];

        $password = password_hash($password, PASSWORD_DEFAULT, ["cost" => 11]);



        $sql = $conn->prepare ("INSERT INTO users (username, password) VALUES (:username, :password)");
        $sql->bindParam(':username', $username);
        $sql->bindParam('password', $password);

        if ($sql->execute());
        {
            $msg = "$username have been registered!";
            $conn = null;
            header("refresh:3;url= /");
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
</head>
<body>

    <?php if($msg != "") echo $msg . "<br>"; ?>

    <form method="post" action="register.php">
        <label>
            <input name="username" placeholder="username" autofocus>
        </label><br>
        <label>
            <input name="password" type="password" placeholder="password"
        </label><br>
        <input name="submit" type="submit" value="register"><br>
    </form>
</body>
</html>