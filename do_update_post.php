<?php 
session_start();
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
if ($_SESSION['user_name']) {
    if (isset($_POST['post_submit'])) {
        //$topic_id = $_POST['topic_id'];
        //$user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $post_content = $_POST['post_content'];
        $post_date = date('Y-m-d H:i:s');
        $sql_c = 'UPDATE posts SET post_content = :post_content, post_date = :post_date WHERE post_id = :post_id';
    $query_c = $db->prepare($sql_c);
    $query_c->bindValue(':post_id', $post_id);
    //$query_c->bindValue(':post_by_who', $user_id);
    $query_c->bindValue(':post_content', $post_content);
    $query_c->bindValue(':post_date', $post_date);
    //$query_c->execute();
    $state = $query_c->execute();
                if ($state) {
                    echo "You update a post successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, your post doesn't update successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;
    }
}



?>