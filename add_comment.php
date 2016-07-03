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

class add_comment
{
    private $db_type = "sqlite"; //
    private $db_sqlite_path = "./forumusers.db";
    private $db_connection = null;
    
    //private $comment = $_GET[comment];
    public $feedback = "";
    public function __construct()
    {
        if($this->createDatabaseConnection())
        {
            $this->doaddcomment();
            $this->addcommentPage();
        }
    }
    private function createDatabaseConnection()
    {
        try {
            $this->db_connection = new PDO($this->db_type . ':' . $this->db_sqlite_path);
            return true;
        } catch (PDOException $e) {
            $this->feedback = "PDO database connection problem: " . $e->getMessage();
        } catch (Exception $e) {
            $this->feedback = "General problem: " . $e->getMessage();
        }
        return false;
    }
    private function addcommentPage()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=docreatecomment" name="createcommentform">';
        echo '<fieldset>
    <legend>Reply:</legend>
    <textarea name="message" rows="10" cols="30" required></textarea><br>
    <input type="submit" value="Submit">
     </fieldset>';
        echo "<br>";
        echo '</form>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=docreatecomment" name="createcommentform">';
    }
    private function doaddcomment()
    {
        if(isset($_GET["action"]) && $_GET["action"] == "docreatecomment") {
    echo "good";
    $comment_content = $_POST['message'];
    //$post_id = $_SESSION['post_id'];
    //$user_id = $_SESSION['user_id'];
    echo $comment_content;
    echo $post_id;
    $comment_date = date('Y-m-d H:i:s');
    $sql_c = 'INSERT INTO comments (comment_content, comment_by_who, comment_by_post, comment_date) 
        VALUES(:comment_content, :comment_by_who, :comment_by_post, :comment_date)';
    $query_c = $this->db_connection->prepare($sql_c);
    $query_c->bindValue(':comment_by_post', $post_id);
    $query_c->bindValue(':comment_by_who', $user_id);
    $query_c->bindValue(':comment_content', $comment_content);
    $query_c->bindValue(':comment_date', $comment_date);
    //$query_c->execute();
    $state = $query_c->execute();
                if ($state) {
                    $this->feedback = "You create a comment successfully!";
                    return true;
                } else {
                    $this->feedback = "Sorry, your comment doesn't create successfully.";
                }
        return false;
    //echo $result_c->comment_content;
    }
    
    }
}
$post_id = $_GET[post_id];
$user_id = $_GET[user_id];
$application = new add_comment($post_id, $user_id);

?>

</div>
    </body>
</html>
