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
</head>

<body>
    <header>
        <h1><a href="index.php">AProject</a></h1>
        <div id="nav">
            <?php
            session_start();
            require_once('connectdb.php');
            if (isset($_SESSION['username'])) {
                echo '<p><b><a href="index.php">Main Page</a></b></p>';
                echo '<p><a href="logout.php">Log out</a> </p>';
            } else {
                header("Location: index.php");
                exit();
            }
            ?>
        </div>
    </header>
    <?php
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        echo "<h2> Welcome, " . $_SESSION['username'] . "! </h2>";

    } ?>
    <div id="search-div">
        <div id="search-head">
            <h3>Your Projects</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                Add a project
            </button>
        </div>
        <br>
        <div id="message">
            <?php
            include 'addproject.php';
            include 'editproject.php';
            ?>
        </div>
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Project</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="personal.php" method="POST">
                            <input type="hidden" name="add" />
                            <div class="form-group row">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input name="title" type="text" class="form-control" placeholder="Title" required>
                                </div>
                            </div>
                            <div class="form-group row" id="dates">
                                <label for="start_date" class="col-sm-2 col-form-label">Start</label>
                                <div class="col-sm-4">
                                    <input name="start_date" type="date" class="form-control" required>
                                </div>
                                <label id="end" for="end_date" class="col-sm-2 col-form-label">End</label>
                                <div class="col-sm-4">
                                    <input name="end_date" type="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phase" class="col-sm-2 col-form-label">Phase</label>
                                <div class="col-sm-10">
                                    <div class="col-sm-4.1">
                                        <select name="phase" id="phase" class="custom-select"
                                            aria-label="Default select example" required>
                                            <option value="design">design</option>
                                            <option value="development">development</option>
                                            <option value="testing">testing</option>
                                            <option value="deployment">deployment</option>
                                            <option value="complete">complete</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Description</span>
                                <textarea class="form-control" aria-label="With textarea" required
                                    name="description"></textarea>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="submitted-add">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="projects-div">
        <?php
        try {
            $rows = $db->prepare("SELECT * FROM `users`, projects WHERE projects.uid = users.uid AND users.username = ?");
            $rows->execute([$username]);
            $found = false;
            $phase;

            if ($rows && $rows->rowCount() > 0) {
                while ($row = $rows->fetch()) {
                    $email = userQuery($row, $db);
                    printCards($row, $email);
                    $phase = $row['phase'];
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
                <div class=\"modal-header\" id=\"modalHeader" . $row['pid'] . "\">
                    <h5 class=\"modal-title\">" . $row['title'] . "</h5>
                    <div type=\"button\" id=\"edit-btn\">
                        <img id=\"edit-img\" src=\"./img/pencil.png\" alt=\"edit\" onclick=\"displayChange(" . $row['pid'] . " ,'" . $row['title'] . "' ,'" . $row['start_date'] . "' ,'" . $row['end_date'] . "' ,'" . $row['phase'] . "' ,'" . $row['description'] . "')\">
                        <a href=\"deleteproject.php?id=" . $row['pid'] . "\" onclick=\"return confirm('Are you sure you want to delete this project?');\">
                        <img id=\"edit-img\" src=\"./img/trash-can.png\" alt=\"delete\" >
                        </a>                        
                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                            <span aria-hidden=\"true\">&times;</span>
                        </button>
                    </div>
                </div>
                <div class=\"modal-body\" id=\"modalBody" . $row['pid'] . "\">
                    <h6 class=\"card-subtitle mb-2 text-muted\">Start Date: " . $row['start_date'] . "</h6>
                    <h6 class=\"card-subtitle mb-2 text-muted\">End Date: " . $row['end_date'] . "</h6>
                    <p>" . $row['description'] . "</p>
                    <p><b>Phase: </b>" . $row['phase'] . "</p>
                    <p><b>User email: </b>" . $email . "</p>

                <div class=\"modal-footer\" id=\"modalFooter" . $row['pid'] . "\">
                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
            </div>
                </div>
            </div>
            </div>
        </div>";
        }
        ?>
    </div><br />

    <script>
        if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

        function displayChange(id, title, start, end, phase, descr) {
            document.getElementById("modalHeader" + id).innerHTML = `
                    <h5 class=\"modal-title\">Editing project details</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                        <span aria-hidden=\"true\">&times;</span>
                    </button>`;
            document.getElementById("modalBody" + id).innerHTML = `
                        <form action="personal.php" method="POST" >
                        <input type="hidden" name="edit"/>
                        <input type="hidden" name="project-id" value="${id}"/>
                            <div class="form-group row">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input name="title" type="text" class="form-control" placeholder="Title" required value="${title}">
                                </div>
                            </div>
                            <div class="form-group row" id="dates">
                                <label for="start_date" class="col-sm-2 col-form-label">Start</label>
                                <div class="col-sm-4">
                                    <input name="start_date" type="date" class="form-control" required  value="${start}">
                                </div>
                                <label id="end" for="end_date" class="col-sm-2 col-form-label">End</label>
                                <div class="col-sm-4">
                                    <input name="end_date" type="date" class="form-control" required  value="${end}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phase" class="col-sm-2 col-form-label">Phase</label>
                                <div class="col-sm-10">
                                    <div class="col-sm-4.1">
                                        <select name="phase" id="phase" class="custom-select"
                                            aria-label="Default select example" required>
                                            <option value="design" <?php if ($phase == "design")
                                                echo 'selected="selected"'; ?>>design</option>
                                            <option value="development" <?php if ($phase == "development")
                                                echo 'selected="selected"'; ?>>development</option>
                                            <option value="testing" <?php if ($phase == "testing")
                                                echo 'selected="selected"'; ?>>testing</option>
                                            <option value="deployment" <?php if ($phase == "deployment")
                                                echo 'selected="selected"'; ?>>deployment</option>
                                            <option value="complete" <?php if ($phase == "complete")
                                                echo 'selected="selected"'; ?>>complete</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Description</span>
                                <textarea rows="5" class="form-control" aria-label="With textarea" required
                                    name="description">${descr}</textarea>
                            </div>
                            </div>
                            <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="submitted-changes">Save changes</button>
                    </div>
                    </form>
                    `;
        }

        function deleteProject() {
            if (confirm('Are you sure you want to delete this project?') == true) {
                x = 1;
            } else {
                x = 0;
            }
            $("#answer").load("personal.php?answer=" + x);
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