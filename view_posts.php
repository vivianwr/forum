<?php session_start(); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>PHP-RBAC Installation</title>

        <link rel="stylesheet" href="style.css">

    </head>

<body>

<div id="wrapper">
    <h2>Project3 forum</h2>
    <p>role-based access control forum</p>
</div>
<div id="content">
<?php
//require_once "forum.php";
//require_once "mainpage.php";
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$topic = $_GET[post_topic];
$user_now_id = $_GET[user_id];
$sql = 'SELECT post_id, post_content, post_date, user_name FROM posts, users WHERE post_topic = :post_topic and user_id = post_by_who';
$query = $db->prepare($sql);
$query->bindValue(':post_topic', $topic);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'This topic doesn\'t have any post now.<br>';
        }
        else {
            echo 'There are the posts about this topic:<br>';
            foreach ($rows as $row)
            {
                $post_id = $row['post_id'];
                $user_name = $row['user_name'];
                echo "Content: {$row['post_content']}\n Date: {$row['post_date']}\n Poster: {$row['user_name']}\n <br>";
                //echo "<a href='view_comment.php?post_id=$post_id'>Posts</a><br>";
                echo "<a href='view_comment.php?post_id=$post_id&user=$user_now_id&user_name=$user_name'>Detail</a><br>";
            }
        }
?>
</div>
    </body>
</html>