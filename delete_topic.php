<?php session_start(); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>PHP-RBAC Installation</title>

        <link rel="stylesheet" href="style.css">
<style type="text/css">
            tab1 { padding-left: 4em; }
            tab2 { padding-left: 8em; }
            tab3 { padding-left: 12em; }
            tab4 { padding-left: 16em; }
            tab5 { padding-left: 20em; }
            tab6 { padding-left: 24em; }
            tab7 { padding-left: 28em; }
            tab8 { padding-left: 32em; }
            tab9 { padding-left: 36em; }
            tab10 { padding-left: 40em; }
            tab11 { padding-left: 44em; }
            tab12 { padding-left: 48em; }
            tab13 { padding-left: 52em; }
            tab14 { padding-left: 56em; }
            tab15 { padding-left: 60em; }
            tab16 { padding-left: 64em; }
        </style>
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
$user_id = $_GET[user_id];
$sql = 'SELECT * FROM topics WHERE topic_by_who = :topic_by_who';
$query = $db->prepare($sql);
$query->bindValue(':topic_by_who', $user_id);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
echo "<tr style='background-color: #dddddd;'><td>Your Topics: </td></tr><br>";
foreach ($rows as $row)
            {
                $topic_name = $row['topic_name'];
                $topic_date = $row['topic_date'];
                $topic_id = $row['topic_id'];
                echo '<tab1>'.$topic_name.'</tab1>';
                echo '<tab1>'.$topic_date.'</tab1>';
                echo '<tab1>'."<a href='do_delete_topic.php?topic_id=$topic_id'>Delete</a><br>".'</tab1>';
            }
?>
