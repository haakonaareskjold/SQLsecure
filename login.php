<?php

$msg = "";
$username = $_POST['username'];
$password = $_POST['password'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new PDO("sqlite:database.db");
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



        $sql = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $sql->bindParam(':username', $username);
        $sql->execute();

        while ($row = $sql->fetch()) {
            if (password_verify($password, $row['password'])) {
                if (password_needs_rehash($row['password'], PASSWORD_DEFAULT, ["cost" => 10])) {

                    //if hash uses argon2id, it will rehash to PASSWORD_DEFAULT (bcrypt as of current date)
                    $newHash = password_hash($password, PASSWORD_DEFAULT, ["cost" => 10]);

                    //submit new hashed password to db
                    $sql = $conn->prepare ("UPDATE users SET password = :password WHERE username = :username");
                    $sql->bindParam('password', $newHash);
                    $sql->bindParam(':username', $username);
                    $sql->execute();
                    echo "password has been rehashed!" . PHP_EOL;
                }
                header("refresh:2;url= /");
                exit($msg = "$username have successfully logged in!");
            } else $msg = "Wrong password for $username, try again!";
        }

    }
    catch(PDOException $e)
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
    <title>Login</title>
</head>
<body>

    <?php if($msg != "") echo $msg . "<br>"; ?>
    <form method="post" action="login.php">
        <label>
            <input name="username" placeholder="username" autofocus>
        </label><br>
        <label>
            <input name="password" type="password" placeholder="password"
        </label><br>
        <input name="submit" type="submit" value="submit"><br>
    </form>
</body>
</html>