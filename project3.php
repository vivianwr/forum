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

<?php

require_once "forum.php";

$db_type = "sqlite"; //
    
$db_sqlite_path = "./forumusers.db";
    
$db_connection = null;
    
//$user_is_logged_in = true;

$feedback = "";
    
$db = new PDO($db_type . ':' . $db_sqlite_path);

if (!empty($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
        $sql = 'SELECT user_id, user_role FROM users WHERE user_name = :user_name';
        $query = $db->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute(); //true or false
        $result_row = $query->fetchObject(); //could use by $result_row->user_id
        $user_role = $result_row->user_role;
        $user_id = $result_row->user_id;
        /*if ($user_role == 1) $this->as_admin();
        else if ($user_role == 2) $this->as_author();
        else if ($user_role == 3) $this->as_user();
        else if ($user_role == 4) $this->as_moderator();*/
    }
?>

<div id="content">
<?php
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$sql = 'SELECT topic_id, topic_name, topic_by_who FROM topics';
$query = $db->prepare($sql);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
//admin
if ($user_role == 1) {
        if (!$rows) {
            echo 'You don\'t have any topic now.<br>';
        }
        else {
            echo 'There are some topics:<br>';
            foreach ($rows as $row)
            {
                $topic = $row['topic_name'];
                $topic_id = $row['topic_id'];
                $topic_by_who = $row['topic_by_who'];
                echo $topic;
                echo "<br>";
            }
        }
        $post .= "<a href='admin.php?'>CRUD Topics</a><br>";
        echo $post;
        $change_role .= "<a href='change_role.php?'>Change other users' role</a>";
        echo $change_role;
        
}
//author
if ($user_role == 2) {
        if (!$rows) {
            echo 'You don\'t have any topic now.<br>';
        }
        else {
            echo 'There are some topics:<br>';
            foreach ($rows as $row)
            {
                $topic = $row['topic_name'];
                $topic_id = $row['topic_id'];
                echo $topic;
                echo '<tab1>'."<a href='crud_posts.php?post_topic=$topic&topic_id=$topic_id&user_id=$user_id'>Detail</a>".'</tab1>'."<br>";
            }
        }
}
//user
if ($user_role == 3) {
    if (!$rows) {
            echo 'You don\'t have any topic now.<br>';
        }
        else {
            echo 'There are some topics:<br>';
            foreach ($rows as $row)
            {
                $topic = $row['topic_name'];
                $topic_id = $row['topic_id'];
                echo $topic;
                echo '<tab1>'."<a href='view_posts.php?post_topic=$topic_id&user_id=$user_id'>Detail</a>".'</tab1>'."<br>";
            }
        }
}
//moderator
if ($user_role == 4) {
        if (!$rows) {
            echo 'You don\'t have any topic now.<br>';
        }
        else {
            echo 'There are some topics:<br>';
            foreach ($rows as $row)
            {
                $topic = $row['topic_name'];
                $topic_id = $row['topic_id'];
                $topic_by_who = $row['topic_by_who'];
                echo $topic;
                echo "<br>";
            }
            }
        $post .= "<a href='moderator.php?user_id=$user_id'>See Your Topics</a><br>";
        echo $post;
        $change_role .= "<a href='change_role.php?'>Change other users' role</a>";
        echo $change_role;
            
}

//echo $post;


?>
</div>
</body>
</html>