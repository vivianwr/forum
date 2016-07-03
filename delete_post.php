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
//$topic_id = $_GET[topic_id];
$user_id = $_GET[user];
$topic = $_GET[topic];
$post_id = $_GET[post_id];
$post_content = $_GET[post_content];
$sql = 'SELECT post_content, post_date FROM posts WHERE post_by_who = :post_by_who';
$query = $db->prepare($sql);
$query->bindValue(':post_by_who', $user_id);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
echo "<tr style='background-color: #dddddd;'><td>Topic: </td>".$topic."</tr><br>";
foreach ($rows as $row)
            {
                $post_content = $row['post_content'];
                $post_date = $row['post_date'];
                echo '<tab1>'.$post_content.'</tab1>';
                echo '<tab1>'.$post_date.'</tab1>';
                echo '<tab1>'."<a href='do_delete_post.php?post_id=$post_id'>Delete</a><br>".'</tab1>';
            }
            
?>
