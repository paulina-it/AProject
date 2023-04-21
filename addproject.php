<?php

if (isset($_POST['add'])) {
    $title = isset($_POST['title']) ? $_POST['title'] : false;
    $start = isset($_POST['start_date']) ? $_POST['start_date'] : false;
    $end = isset($_POST['end_date']) ? $_POST['end_date'] : false;
    $phase = isset($_POST['phase']) ? $_POST['phase'] : false;
    $descr = isset($_POST['description']) ? $_POST['description'] : false;

    if (!empty($title) && !empty($start) && !empty($end) && !empty($descr)) {
        // echo $title, $start, $end, $phase, $descr, $uid;
        $stmt = $db->prepare('SELECT * FROM users');
        $stmt->execute();
        $id;
        preg_replace('~^[\'"]?(.*?)[\'"]?$~', '$1', $descr);

        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                if ($row['username'] == $_SESSION['username']) {
                    $id = $row['uid'];
                }
            }
        }
        if ($start > $end) {
            echo '<script type=\'text/javascript\'>alert(\'Start date is later than end date.\');</script>';

        } else {
            try {
                $statement = $db->prepare('INSERT INTO projects VALUES (default, ?, ?, ?, ?, ?, ?)');
                $statement->execute(array($title, $start, $end, $phase, $descr, $id));
                echo "<p>Congratulations! You have added a new project.</p>";

            } catch (PDOexception $ex) {
                echo "Sorry, a database error occurred! <br>";
                echo "Error details: <em>" . $ex->getMessage() . "</em>";
            }
        }
    }
}
?>