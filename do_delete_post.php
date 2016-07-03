<?php
//require_once "mainpage.php";
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$post_id = $_GET[post_id];
$sql = 'DELETE FROM posts WHERE post_id = :post_id';
$query = $db->prepare($sql);
$query->bindValue(':post_id', $post_id);
$state = $query->execute();
                if ($state) {
                    echo "You delete a post successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, your post doesn't delete successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;
?>