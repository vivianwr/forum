<?php 
session_start();
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$user_name = $_GET[user_name];
//$post_date = date('Y-m-d H:i:s');
$sql_c = 'UPDATE users SET user_role = 2 WHERE user_name = :user_name';
$query_c = $db->prepare($sql_c);
$query_c->bindValue(':user_name', $user_name);
$state = $query_c->execute();
                if ($state) {
                    echo "The people's role is changed successfully!";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                    return true;
                } else {
                    echo "Sorry, you don't change successfully.";
                    $post .= "<a href='project3.php'>Return</a>";
                    echo $post;
                }
        return false;


?>