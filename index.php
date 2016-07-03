

<?php
  
/*  $dbhandle = new PDO("sqlite:project2.db") or die("Failed to open DB");
  if (!$dbhandle) die ($error);
  
  error_reporting(0);
  
  if ($_POST["login"]) {
      if ($_POST["username"] && $_POST["password"]) {
          $username = $_POST["username"];
          $password = hash('sha256',$_POST["password"]);
          $user = "SELECT * FROM users WHERE username = '$username'";
          $statement = $dbhandle->prepare($user);
          $statement->execute();
          $usercheck = $statement->fetchAll(PDO::FETCH_ASSOC);
          if ($usercheck == '0') {
              die("That username <i>$username</i> doesn't exist! Try making <i>$username</i> today! <a href = 'index.php'>&larr; Back</a>");
          }
          if ($usercheck['password'] != $password) {
              die("Incorrect password! <a href = 'index.php'>&larr; Back</a>");
          }
          else die("You are now logged in as $username!");
      }
  }
*/
  $username = $_POST["username"];
  $password = hash('sha256',$_POST["password"]);
  $newpassword = hash('sha256',$_POST["newpassword"]);
  
  $pdoHandler = new PDO("sqlite:project2.db") or die("Failed to open DB");
    // if(!$pdohandler) die ($error);.quit
 
    $userNameCheck_query = "SELECT username FROM user WHERE username = '$username'";
    $loginCheck_query = "SELECT * from user WHERE username = '$username' AND password = '$password'";
    $userCreate_query = "INSERT INTO user (username,password) VALUES ('$username','$password')";
    $passwordChange_query = "UPDATE user SET password = '$newpassword' WHERE username = '$username'";
    if($_POST["login"]){
        $pdoPrep_loginCheck = $pdoHandler->prepare($loginCheck_query);
        $pdoPrep_loginCheck->execute();
        
        $res_loginCheck = $pdoPrep_loginCheck->fetchAll(PDO::FETCH_ASSOC);
        $loginCheckRes_row = count($res_loginCheck);
        if($loginCheckRes_row !== 0){
            echo json_encode("lsucceed");
        }
        else{
            echo json_encode("lerror");
        }
    }

echo "
<body style = 'font-family: Arial, Helvetica, sans-serif;'>
    <div style = 'width: 80%; padding: 5px 15px 5px; border: 1px solid #e3e3e3; background-color: #fff; color: #000; margin-left: auto; margin-right: auto;'>
        <h1>Login</h1>
        <br />
        <form action='' method='post'>
            <table>
                <tr>
                    <td>
                        <b>Username:</b>
                    </td>
                    <td>
                        <input type = 'text' name='username' style type='padding: 4px;' />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Password:</b>
                    </td>
                    <td>
                        <input type='password' name='password' style='padding: 4px;' />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type = 'submit' value='login' />
                    </td>
                </tr>
            </table>
        </form>
        <br />
        <h6>
            No account? <a href='register.php'>Register!</a>
        </h6>
    </div>
    
</body>
</html>
"
    
?>