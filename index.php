<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>AProject</title>
    <link rel="stylesheet" href="./styles/styles.css?v=<?php echo time(); ?>">
    <style>
        
    </style>
</head>

<body>
    <header>
        <h1><a href="index.php">AProject</a></h1>
        <div id="nav">
            <?php
            session_start();
            require_once('connectdb.php');
            if (isset($_SESSION['username'])) {
                echo '<p><b><a href="personal.php">My Projects</a></b></p>';
                echo '<p><a href="logout.php">Log out</a> </p>';
            } else {
                echo '<p>Would like to log in? <a href="login.php">Log In</a> </p>';
                echo '<p><a href="register.php">Sign Up</a> </p>';
            }
            ?>
        </div>
    </header>
    <?php
    if (!isset($_SESSION['username'])) {
        $username = 'guest';
    } else {
        $username = $_SESSION['username'];
        echo "<h2> Welcome, " . $_SESSION['username'] . "! </h2>";

    } ?>
    <div id="search-div">
        <h3>Projects List</h3>
        <form class="form-inline my-2 my-lg-0" method="GET" action="index.php">
            <input name="search" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"
                required>
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        <p id="search-switch" onclick="switchToDate()">
            Serach by Date
        </p>
    </div>
    <div id="projects-div">
        <?php
        try {
            $query = "SELECT  * FROM  projects ";
            $rows = $db->query($query);
            $search = $_GET['search'];
            $found = false;

            if ($rows && $rows->rowCount() > 0) {
                while ($row = $rows->fetch()) {
                    if (!empty($search)) {
                        if (!validateDate($search)) {
                            // echo 'not date';
                            if (!strcmp(strtolower($row['title']), $search)) {
                                $found = true;
                                $email = userQuery($row, $db);
                                printCards($row, $email);
                            }
                        } else {
                            if ($row['start_date'] > $search) {
                                if (!$found) {
                                    echo '<h4>Projects started after ' . $search . '</h4>';
                                }
                                $found = true;
                                $email = userQuery($row, $db);
                                printCards($row, $email);
                            }
                        }
                    } else {
                        $email = userQuery($row, $db);
                        printCards($row, $email);
                    }
                }
            } else {
                echo "<p>No projects on the list.</p>\n";
            }
            if (!$found && !empty($search)) {
                echo "<p>No matching projects found.</p>\n";
            }
        } catch (PDOexception $ex) {
            echo "Sorry, a database error occurred! <br>";
            echo "Error details: <em>" . $ex->getMessage() . "</em>";
        }
        function validateDate($date, $format = 'Y-m-d')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        }
        function userQuery($row, $db)
        {
            $id = $row['uid'];
            $stmt = $db->prepare("SELECT * FROM users WHERE users.uid = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            $email = $user['email'];
            return $email;
        }
        function printCards($row, $email)
        {
            //card on the list
            echo "<div class=\"card\" style=\"width: 18rem;\">
                <div class=\"card-body\">
                <h5 class=\"card-title\">" . $row['title'] . "</h5>
                <h6 class=\"card-subtitle mb-2 text-muted\">Start Date: " . $row['start_date'] . "</h6>
                <p class=\"card-text\">" . $row['description'] . "</p>
                <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modal" . $row['pid'] . "\">
                Read more
                </button>
                </div>  
            </div>";
            //modal with full description
            echo "<div class=\"modal fade\" id=\"modal" . $row['pid'] . "\" tabindex=\"-1\" role=\"dialog\">
            <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                <h5 class=\"modal-title\">" . $row['title'] . "</h5>
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span>
                </button>
                </div>
                <div class=\"modal-body\">
                <h6 class=\"card-subtitle mb-2 text-muted\">Start Date: " . $row['start_date'] . "</h6>
                <h6 class=\"card-subtitle mb-2 text-muted\">End Date: " . $row['end_date'] . "</h6>
                <p>" . $row['description'] . "</p>
                <p><b>Phase: </b>" . $row['phase'] . "</p>
                <p><b>User email: </b>" . $email . "</p>
                </div>
                <div class=\"modal-footer\">
                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                </div>
            </div>
            </div>
        </div>";
        }
        ?>
    </div><br />

    <script>
        function switchToDate() {
            document.getElementById('search-div').innerHTML = `
            <h3>Projects List</h3>
            <form class="form-inline my-2 my-lg-0" method="GET" action="index.php">
                <input type="date" name="search" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"
                    required>
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
            <p id="search-switch" onclick="switchToTitle()">
                Serach by Title
            </p>
            `;
        }
        function switchToTitle() {
            document.getElementById('search-div').innerHTML = `
            <h3>Projects List</h3>
            <form class="form-inline my-2 my-lg-0" method="GET" action="index.php">
                <input name="search" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"
                    required>
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
            <p id="search-switch" onclick="switchToDate()">
                Serach by Date
            </p>
            `;
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
</body>

</html>