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
$topic_id = $_GET[topic_id];
$user_id = $_GET[user];
$topic = $_GET[topic];
echo "<tr style='background-color: #dddddd;'><td>Topic: </td>".$topic."</tr>";
?>
<hr />
<div>
    <form method="post" action='add_post.php'>
        <p>Post:</p>
        <textarea name="post_content" rows="30" cols="30" required></textarea><br>
        <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>" />
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
        <input type="submit" name="post_submit" value="Finish">
    </form>
    
    
</div>


</div>
    </body>
</html>