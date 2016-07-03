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
//require_once "view_posts.php";
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$post_id = $_GET[post_id];
$user_now_id = $_GET[user];
$user_post_name = $_GET[user_name];
//post
$sql_p = 'SELECT * FROM posts WHERE post_id = :post_id';
$query_p = $db->prepare($sql_p);
$query_p->bindValue(':post_id', $post_id);
$query_p->execute();
$result_row = $query_p->fetchObject();
echo 'Content: '.$result_row->post_content.'<br> Date:'.$result_row->post_date.' Poster:'.$user_post_name.' <br>';
//comment
$sql = 'SELECT comment_content, user_name, comment_date FROM users, posts, comments WHERE post_id = :post_id and comment_by_post = post_id and comment_by_who = user_id';
$query = $db->prepare($sql);
$query->bindValue(':post_id', $post_id);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'This post doesn\'t have any comment now.<br>';
        }
        else {
            echo "Comment:<br>";
            foreach ($rows as $row)
            {
                echo "{$row['comment_content']}\n<br> Date: {$row['comment_date']}\n Poster: {$row['user_name']}\n<br>";
            }
            
        }
?>

<hr />
<div>
    <form method="post" action='post_reply_parse.php'>
        <p>Reply Content</p>
        <textarea name="reply_content" rows="10" cols="30" required></textarea><br>
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
        <input type="hidden" name="user_id" value="<?php echo $user_now_id; ?>" />
        <input type="submit" name="reply_submit" value="Post your Reply">
    </form>
    
    
</div>




     

</div>
    </body>
</html>