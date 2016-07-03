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
$topic_id = $_GET[topic_id];
$topic = $_GET[post_topic];
$user_id = $_GET[user_id];
$sql = 'SELECT post_id, post_content, post_date, post_by_who, user_name FROM users, posts WHERE user_id = post_by_who and post_topic = :topic_id';
$query = $db->prepare($sql);
//$query->bindValue(':post_by_who', $user_id);
$query->bindValue(':topic_id', $topic_id);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
echo "Topic: \n";
echo $topic;
echo "<br>";
        if (!$rows) {
            echo 'There\'s no post now.<br>';
        }
        else {
            foreach ($rows as $row)
            {
                $post_id = $row['post_id'];
                $post_by_who = $row['post_by_who'];
                $user_name = $row['user_name'];
                $post_content = $row['post_content'];
                $post_date = $row['post_date'];
                echo '<tab1>'.$post_content.'</tab1>';
                echo '<tab1>'.$user_name.'</tab1>';
                echo '<tab1>'.$post_date.'</tab1>';
                //echo "Content: {$row['post_content']}\n Date: {$row['post_date']}\n <br>";
                if ($post_by_who == $user_id) {
                echo '<tab1>'."<a href='update_post.php?post_id=$post_id&post_content=$post_content&topic=$topic'>Update</a>".'<tab1>';
                }
                if ($post_by_who == $user_id) {
                    echo "<a href='do_delete_post.php?post_id=$post_id'>Delete</a><br>";
                }
            }
            
        }
        echo "<a href='create_post.php?user=$user_id&topic_id=$topic_id&topic=$topic'>Create a post</a><br>";

        
?>
</div>
    </body>
</html>