<?php
//require_once "mainpage.php";
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$topic_id = $_GET[topic_id];
$sql = 'DELETE FROM topics WHERE topic_id = :topic_id';
$query = $db->prepare($sql);
$query->bindValue(':topic_id', $topic_id);
$state = $query->execute();
                if ($state) {
                    echo "You delete a topic successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, your topic doesn't delete successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;
?>