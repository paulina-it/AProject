<?php
if (isset($_POST['submitted'])) {
    require_once 'connectdb.php';

    $username = isset($_POST['username']) ? $_POST['username'] : false;
    $email = isset($_POST['email']) ? $_POST['email'] : false;
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : false;
    $cpassword = isset($_POST['cpassword']) ? password_hash($_POST['cpassword'], PASSWORD_DEFAULT) : false;

    if (!empty($username) && !empty($email) && !empty($password) && !empty($cpassword)) {
        $stmt = $db->prepare('SELECT * FROM users');
        $stmt->execute();
        $error = false;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Invalid email format';
            $error = true;
        } else if ($_POST['password'] != $_POST['cpassword']) {
            echo 'Passwords do not match';
            $error = true;
        } else if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                if ($username == $row['username']) {
                    echo 'Username already exists. Please choose another. <br/>';
                    $error = true;
                }
                if ($email == $row['email']) {
                    echo 'This email is already in use. Please choose another.';
                    $error = true;
                }
            }
        }
        if (!$error) {
            try {
                $statement = $db->prepare('INSERT INTO users VALUES (default, ?, ?, ?)');
                $statement->execute(array($username, $password, $email));

                // $id = $db->lastInsertId();
                session_start();
                $_SESSION['username'] = $username;
                header("Location: index.php");
                // echo "Congratulations! You are now registered. Your ID is: $id  ";
                

            } catch (PDOexception $ex) {
                echo "Sorry, a database error occurred! <br>";
                echo "Error details: <em>" . $ex->getMessage() . "</em>";
            }
        }
    } else if (empty($_POST['username'])) {
        echo 'A username is required';
    } else if (empty($_POST['email'])) {
        echo 'An email is required';
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
        <h2>Sign up here</h2>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" name="cpassword">

                <small id="emailHelp" class="form-text text-muted">
                    Make sure to type the same password
                </small>
            </div>
            <button type="submit" class="btn btn-primary" value="submit" name="submitted">
                SignUp
            </button>

        </form>
        <br>
        <a href="index.php">
            <h5>Already have an account? Login here</h5>
        </a>
    </div>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
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
    <script>

    </script>
</body>

</html>