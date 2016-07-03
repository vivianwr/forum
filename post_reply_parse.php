<?php 
session_start();
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
if ($_SESSION['user_name']) {
    if (isset($_POST['reply_submit'])) {
        $post_id = $_POST['post_id'];
        $user_id = $_POST['user_id'];
        $comment_content = $_POST['reply_content'];
        $comment_date = date('Y-m-d H:i:s');
        $sql_c = 'INSERT INTO comments (comment_content, comment_by_who, comment_by_post, comment_date) 
        VALUES(:comment_content, :comment_by_who, :comment_by_post, :comment_date)';
    $query_c = $db->prepare($sql_c);
    $query_c->bindValue(':comment_by_post', $post_id);
    $query_c->bindValue(':comment_by_who', $user_id);
    $query_c->bindValue(':comment_content', $comment_content);
    $query_c->bindValue(':comment_date', $comment_date);
    //$query_c->execute();
    $state = $query_c->execute();
                if ($state) {
                    echo "You create a comment successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, your comment doesn't create successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;
    }
}



?>