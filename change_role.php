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
echo "Hello ".$_SESSION['user_name'].". You could change these authors to users. Be careful.<br>";
$sql = 'SELECT user_id, user_name, user_date FROM users WHERE user_role = 2';
$query = $db->prepare($sql);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'There\'s no author.<br>';
        }
        else {
            foreach ($rows as $row)
            {
                $user_name = $row['user_name'];
                $user_date = $row['user_date'];
                echo '<tab1>'.$user_name.'</tab1>';
                echo '<tab1>'.$user_date.'</tab1>';
                echo "<br>";
                $change_to_user .= "<a href='do_change_role.php?user_name=$user_name'>Change to User</a><br>";
            }
            echo $change_to_user;
        }

echo "Hello ".$_SESSION['user_name'].". You could change these users to authors. Be careful.<br>";
$sql_c = 'SELECT user_id, user_name, user_date FROM users WHERE user_role = 3';
$query_c = $db->prepare($sql_c);
$query_c->execute();
$rows_c = $query_c->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows_c) {
            echo 'There\'s no user.<br>';
        }
        else {
            foreach ($rows_c as $row_c)
            {
                $user_name = $row_c['user_name'];
                $user_date = $row_c['user_date'];
                echo '<tab1>'.$user_name.'</tab1>';
                echo '<tab1>'.$user_date.'</tab1>';
                echo "<br>";
                $change_to_author .= "<a href='do_change_to_author.php?user_name=$user_name'>Change to Author</a><br>";
            }
            echo $change_to_author;
        }
        


?>
</div>
    </body>
</html>