<?php 
session_start();
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
if ($_SESSION['user_name']) {
    if (isset($_POST['topic_submit'])) {
        $user_id = $_POST['user_id'];
        $topic_name = $_POST['topic_name'];
        $post_date = date('Y-m-d H:i:s');
        $sql_c = 'INSERT INTO topics (topic_name, topic_by_who, topic_date) 
        VALUES(:topic_name, :topic_by_who, :post_date)';
    $query_c = $db->prepare($sql_c);
    $query_c->bindValue(':topic_name', $topic_name);
    $query_c->bindValue(':topic_by_who', $user_id);
    $query_c->bindValue(':post_date', $post_date);
    //$query_c->execute();
    $state = $query_c->execute();
                if ($state) {
                    echo "You create a topic successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, your topic doesn't create successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;
    }
}



?>