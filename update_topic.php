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
//$user_id = $_GET[user];
$topic = $_GET[topic_name];
//$post_id = $_GET[post_id];
//$post_content = $_GET[post_content];
echo "<tr style='background-color: #dddddd;'><td>Change the Topic: </td>".$topic."</tr>";
?>
<hr />
<div>
    <form method="post" action='do_update_topic.php'>
        <input type="text" name="topic_name" required />
        <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>" />
        <input type="submit" name="topic_submit" value="Finish">
    </form>
    
    
</div>


</div>
    </body>
</html>