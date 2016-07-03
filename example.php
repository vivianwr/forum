<?php 
$user_name = $_GET[user_name];
echo 'Hello ' . $user_name. ', you are logged in.'; 
$post .= "<a href='project2.php?'>Log out</a><br>";
echo $post;
?>