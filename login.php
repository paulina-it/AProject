<?php

if (isset($_POST['submitted'])) {
    require_once 'connectdb.php';

    $username = isset($_POST['username']) ? $_POST['username'] : false;
    $password = isset($_POST['password']) ? $_POST['password'] : false;
    //check if empty
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        //insert into database
        try {
            $statement = $db->prepare('SELECT password FROM users WHERE username = ?');
            $statement->execute([$username]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);

            while ($r = $statement->fetch()) {
                if (password_verify($password, $r['password'])) {
                    session_start();
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                    exit();
                } else {
                    echo 'Login failed. Check the username and the password.';
                }
            }

        } catch (PDOexception $ex) {
            echo "Sorry, a database error occurred! <br>";
            echo "Error details: <em>" . $ex->getMessage() . "</em>";
        }
    } else if (empty($_POST['username'])) {
        echo 'A username is required';
    } else if (empty($_POST['password'])) {
        echo 'A password is required';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styles.css?v=<?php echo time(); ?>">
    <title>AProject</title>
    <style>
        #register-div {
            width: 60%;
            margin: auto;
            margin-top: 5em;
        }
    </style>
</head>

<body>
    <header>
        <h1><a href="index.php">AProject</a></h1>

    </header>
    <div id="register-div">
        <h2>Log in here</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary" value="submit" name="submit">
                Login
            </button>
            <input type="hidden" name="submitted" value="true" />
        </form>
        <br>
        <a href="register.php">
            <h5>Do not have an account yet? Register here</h5>
        </a>
    </div>
    <script src="
    https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="
    sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>

    <script src="
    https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>

    <script src="
    https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
        </script>
</body>

</html>