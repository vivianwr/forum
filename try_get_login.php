<?php session_start();?>
<!doctype html>
<html>
  <head>
      </head>
      <body>
          <div id="wrapper">
              <h2>Vivian forum</h2>
              <p>Login</p>
              
<?php
              
                   if(!isset($_SESSION['user_is_logged_in'])) {
                       echo "<form action='try.php' method='post'>
                       Username: <input type='text' name='username' />&nbsp;
                       Password: <input type='text' name='password' />&nbsp;
                       <input type='submit' name='submit' value='Login' />";
                   }
                   else echo"<p>You successed ".$_SESSION['username']."</p>";
              
?>
              
<div id="content">
    
<?php
     
     echo "good to know";


?>
    
</div>              
              
              
              
          </div>
      </body>
</html>