<?php
if (isset($_POST['edit'])) {
    $pid = isset($_POST['project-id']) ? $_POST['project-id'] : false;
    $title = isset($_POST['title']) ? $_POST['title'] : false;
    $start = isset($_POST['start_date']) ? $_POST['start_date'] : false;
    $end = isset($_POST['end_date']) ? $_POST['end_date'] : false;
    $phase = isset($_POST['phase']) ? $_POST['phase'] : false;
    $descr = isset($_POST['description']) ? $_POST['description'] : false;

    if (!empty($title) && !empty($start) && !empty($end) && !empty($descr)) {
        preg_replace('~^[\'"]?(.*?)[\'"]?$~', '$1', $descr);

        if ($start > $end) {
            echo '<script type=\'text/javascript\'>alert(\'Start date is later than end date.\');</script>';
        } else {
            try {
                $statement = $db->prepare('UPDATE projects 
                SET title = ?, start_date = ?, end_date = ?, phase = ?, description = ?
                WHERE pid =?');
                $statement->execute(array($title, $start, $end, $phase, $descr, $pid));
                echo "<p>Congratulations! You have edited your project with id #".$pid."</p>";

            } catch (PDOexception $ex) {
                echo "Sorry, a database error occurred! <br>";
                echo "Error details: <em>" . $ex->getMessage() . "</em>";
            }
        }
    }
}
?>