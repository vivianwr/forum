<?php session_start(); ?>
<?php 
$user_name = $_GET[user_name];
echo 'Hello ' . $user_name. ', you are logged in.'; 
$db_type = "sqlite"; 
$db_sqlite_path = "./forumusers.db";
$db_connection = null;
$feedback = "";
$db = new PDO($db_type . ':' . $db_sqlite_path);
$sql = 'SELECT * FROM users WHERE user_name = :user_name';
$query = $db->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute();
        $state = $query->fetchObject();
        if ($state) {
            $_SESSION['user_name'] = $user_name;
            $post .= "<a href='project3.php?'>mainpage</a><br>";
            echo $post;
        } 
        else {
            $user_date = date('Y-m-d H:i:s');
            $user_role = 3;
            $sql_a = 'INSERT INTO users (user_name, user_date, user_role) VALUES (:user_name, :user_date, :user_role)';
            $query_a = $db->prepare($sql_a);
            $query_a->bindValue(':user_name', $user_name);
            $query_a->bindValue(':user_date', $user_date);
            $query_a->bindValue(':user_role', $user_role);
            $query_a->execute();
            $query_state = $query_a->fetchObject();
            if ($query_state) {
                $_SESSION['user_name'] = $user_name;
                $post .= "<a href='project3.php?'>mainpage</a><br>";
                echo $post;
            } 
            else {
                echo "You don\'t login in successfully!";
            }
        }
    






?>