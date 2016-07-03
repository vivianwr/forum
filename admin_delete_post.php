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
//require_once "mainpage.php";
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$topic = $_GET[topic];
$post_id = $_GET[post_id];
$post_content = $_GET[post_content];
$sql = 'SELECT * FROM posts, users WHERE post_topic = :topic_id and post_by_who = user_id';
$query = $db->prepare($sql);
$query->bindValue(':post_id', $post_id);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
echo "<tr style='background-color: #dddddd;'><td>Topic: </td>".$topic."</tr><br>";
foreach ($rows as $row)
            {
                $post_content = $row['post_content'];
                $post_by_name = 
                $post_date = $row['post_date'];
                echo '<tab1>'.$post_content.'</tab1>';
                echo '<tab1>'.$post_date.'</tab1>';
                echo "<br>";
                $delete_post .= "<a href='do_delete_post.php?post_id=$post_id'>Delete</a><br>";
            }
            echo $delete_post;
?>
