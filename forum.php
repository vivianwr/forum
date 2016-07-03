<?php

class OneFileLoginApplication
{
    /**
     * @var string Type of used database (currently only SQLite, but feel free to expand this with mysql etc)
     */
    private $db_type = "sqlite"; //
    /**
     * @var string Path of the database file (create this with _install.php)
     */
    private $db_sqlite_path = "./forumusers.db";
    /**
     * @var object Database connection
     */
    private $db_connection = null;
    /**
     * @var bool Login status of user
     */
    private $user_is_logged_in = false;
    /**
     * @var string System messages, likes errors, notices, etc.
     */
    public $feedback = "";
    /**
     * Does necessary checks for PHP version and PHP password compatibility library and runs the application
     */
    public function __construct()
    {
        if ($this->performMinimumRequirementsCheck()) {
            $this->runApplication();
        }
    }
   
    private function performMinimumRequirementsCheck()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '>=')) return true;
        else return false; 
        
    }
    /**
     * This is basically the controller that handles the entire flow of the application.
     */
    public function runApplication()
    {
        // check is user wants to see register page (etc.)
        if (isset($_GET["action"]) && $_GET["action"] == "register") {
            $this->doRegistration();
            $this->showPageRegistration();
        } else if(isset($_GET["action"]) && $_GET["action"] == "changepassword") {
            $this->doChangePassword();
            $this->showPageChangePassword();
        } else {
            // start the session, always needed!
            $this->doStartSession();
            // check for possible user interactions (login with session/post data or logout)
            $this->performUserLoginAction();
            // show "page", according to user's login status
            if ($this->getUserLoginStatus()) {
                if($this->createDatabaseConnection())
                $this->showPageLoggedIn();
            } else {
                $this->showPageLoginForm();
            }
        }
    }
    /**
     * Creates a PDO database connection (in this case to a SQLite flat-file database)
     * @return bool Database creation success status, false by default
     */
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
    /**
     * Handles the flow of the login/logout process. According to the circumstances, a logout, a login with session
     * data or a login with post data will be performed
     */
    private function performUserLoginAction()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $this->doLogout();
        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST["login"])) {
            $this->doLoginWithPostData();
        }
    }
    /**
     * Simply starts the session.
     * It's cleaner to put this into a method than writing it directly into runApplication()
     */
    private function doStartSession()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
    }
    /**
     * Set a marker (NOTE: is this method necessary ?)
     */
    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true; // ?
    }
    /**
     * Process flow of login with POST data
     */
    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty()) {
            if ($this->createDatabaseConnection()) {
                $this->checkPasswordCorrectnessAndLogin();
            }
        }
    }
    /**
     * Logs the user out
     */
    private function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->feedback = "You were just logged out.";
    }
    /**
     * The registration flow
     * @return bool
     */
    private function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            if ($this->createDatabaseConnection()) {
                $this->createNewUser();
            }
        }
        // default return
        return false;
    }
    private function doChangePassword()
    {
            if ($this->createDatabaseConnection()) {
                $this->changepassword();
            }
        
    }
    /**
     * Validates the login form data, checks if username and password are provided
     * @return bool Login form data check success state
     */
    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->feedback = "Password field was empty.";
        }
        // default return
        return false;
    }
    /**
     * Checks if user exits, if so: check if provided password matches the one in the database
     * @return bool User login success status
     */
    private function checkPasswordCorrectnessAndLogin()
    {
        // remember: the user can log in with username or email address
        $sql = 'SELECT user_name, user_email, user_pass
                FROM users
                WHERE user_name = :user_name OR user_email = :user_name
                LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $_POST['user_name']);
        $query->execute();
        $result_row = $query->fetchObject();
        if ($result_row) {
            if (password_verify($_POST['user_password'], $result_row->user_pass)) {
                // write user data into PHP SESSION [a file on your server]
                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_is_logged_in'] = true;
                $this->user_is_logged_in = true;
                return true;
            } else {
                $this->feedback = "Wrong password.";
            }
        } else {
            $this->feedback = "This user does not exist.";
        }
        // default return
        return false;
    }
    /**
     * Validates the user's registration input
     * @return bool Success status of user's registration data validation
     */
    private function checkRegistrationData()
    {
        // if no registration form submitted: exit the method
        if (!isset($_POST["register"]) && !isset($_POST["changepassword"])) {
            return false;
        }
        // validating the input
        if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && strlen($_POST['user_password_new']) >= 6
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
            && !empty($_POST['user_role'])
            && strlen($_POST['user_role']) == 1
            && $_POST['user_role'] >= 1
            && $_POST['user_role'] <= 4
        ) {
            // only this case return true, only this case is valid
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Empty Username";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->feedback = "Empty Password";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->feedback = "Password and password repeat are not the same";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->feedback = "Password has a minimum length of 6 characters";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->feedback = "Username cannot be shorter than 2 or longer than 64 characters";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->feedback = "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
        } elseif (empty($_POST['user_email'])) {
            $this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } else if (empty($_POST['user_role'])) {
            $this->feedback = "Empty User's Role";
        } else if (strlen($_POST['user_role']) != 1 || $_POST['user_role'] > 4 || $_POST['user_role'] < 1) {
            $this->feedback = "We don't have other options for user's role, please select number between 1 to 4.";
        } else {
            $this->feedback = "An unknown error occurred.";
        } 
        // default return
        return false;
    }
    /**
     * Creates a new user.
     * @return bool Success status of user registration
     */
    private function createNewUser()
    {
        // remove html code etc. from username and email
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $user_email = htmlentities($_POST['user_email'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 char hash string.
        // the constant PASSWORD_DEFAULT comes from PHP 5.5 or the password_compatibility_library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
        $user_role = htmlentities($_POST['user_role'], ENT_QUOTES);
        $user_date = date('Y-m-d H:i:s');
        $sql = 'SELECT * FROM users WHERE user_name = :user_name OR user_email = :user_email';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':user_email', $user_email);
        $query->execute();
        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            $this->feedback = "Sorry, that username / email is already taken. Please choose another one.";
        } else {
            $sql = 'INSERT INTO users (user_name, user_email, user_pass, user_date, user_role)
                    VALUES(:user_name, :user_email, :user_password_hash, :user_date, :user_role)';
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':user_name', $user_name);
            $query->bindValue(':user_password_hash', $user_password_hash);
            $query->bindValue(':user_email', $user_email);
            $query->bindValue(':user_role', $user_role);
            $query->bindValue(':user_date', $user_date);
            // PDO's execute() gives back TRUE when successful, FALSE when not
            // @link http://stackoverflow.com/q/1661863/1114320
            $registration_success_state = $query->execute();
            if ($registration_success_state) {
                $this->feedback = "Your account has been created successfully. You can now log in.";
                return true;
            } else {
                $this->feedback = "Sorry, your registration failed. Please go back and try again.";
            }
        }
        // default return
        return false;
    }
    /**
     * Change the user's password
     * @return bool User login success status
     */
     private function changePassword()
    {
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $user_email = htmlentities($_POST['user_email'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];
        if ($user_password !== $_POST['user_password_repeat']) {
            $this->feedback = "The new password and the repeat one is not the same.";
            return false;
        }
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
        $sql = 'SELECT user_name, user_email, user_pass FROM users WHERE user_name = :user_name OR user_email = :user_email LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':user_email', $user_email);
        $query->execute();
        $result_row = $query->fetchObject();
        if ($result_row) {
            if (password_verify($_POST['user_password'], $result_row->user_pass)) {
                $sql = 'UPDATE users SET user_pass = :user_password_hash WHERE user_name = :user_name OR user_email = :user_email';
                $query = $this->db_connection->prepare($sql);
                $query->bindValue(':user_name', $user_name);
                $query->bindValue(':user_pass', $user_password_hash);
                $query->bindValue(':user_email', $user_email);
            // PDO's execute() gives back TRUE when successful, FALSE when not
            // @link http://stackoverflow.com/q/1661863/1114320
                $registration_success_state = $query->execute();
                if ($registration_success_state) {
                    $this->feedback = "Your password has been changed successfully. You can now log in.";
                    return true;
                } else {
                    $this->feedback = "Sorry, your password hasn't changed. Please go back and try again.";
                }
             }
        }
        return false;
    }
    /**
     * Simply returns the current status of the user's login
     * @return bool User's login status
     */
    public function getUserLoginStatus()
    {
        return $this->user_is_logged_in;
    }
    /**
     * Simple demo-"page" that will be shown when the user is logged in.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
     private function showPageLoggedIn()
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
          echo 'Hello ' . $_SESSION['user_name'] . ', you success.'; 
          echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a>';
     }
     /*
    private function showPageLoggedIn()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        $user_name = $_SESSION['user_name'];
        $sql = 'SELECT user_role FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute(); //true or false
        $result_row = $query->fetchObject(); //could use by $result_row->user_id
        $user_role = $result_row->user_role;
        if ($user_role == 1) $this->as_admin();
        else if ($user_role == 2) $this->as_author();
        else if ($user_role == 3) $this->as_user();
        else if ($user_role == 4) $this->as_moderator();
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a>';
    }
    private function as_user()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo 'Hello ' . $_SESSION['user_name'] . ', you are logged in as a User!<br/><br/>';
        $user_name = $_SESSION['user_name'];
        $sql = 'SELECT user_id FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute(); //true or false
        $result_row = $query->fetchObject(); //could use by $result_row->user_id
        $user_id = $result_row->user_id;
        $this->showTopicPage();
        //$this->showTopicPostPage();
        //$this->showPostCommentPage($user_id);
    }
    /** 
     * Forum as an author, which means he/she could CRUD posts
     */
     /*private function as_admin()
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo 'Hello ' . $_SESSION['user_name'] . ', you are logged in as an Author!<br/><br/>';
        $user_name = $_SESSION['user_name'];
        $sql = 'SELECT user_id FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute(); //true or false
        $result_row = $query->fetchObject(); //could use by $result_row->user_id
        $user_id = $result_row->user_id;
        $this->showPostPage($user_id);
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=createposts">Create Posts</a><br>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=deleteposts">Delete Posts</a><br>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=updateposts">Update Posts</a><br>';
        if(isset($_GET["action"]) && $_GET["action"] == "createposts") {
            $this->docreateposts($user_id);
            $this->showCreatePostPage();
        } else if(isset($_GET["action"]) && $_GET["action"] == "deleteposts") { //only can delete one by one
            $this->dodeleteposts($user_id);
            $this->showDeletePostPage($user_id);
        } else if(isset($_GET["action"]) && $_GET["action"] == "updateposts") {
            $this->showUpdatePostPage($user_id);
            $this->showUpdatePostPage1();
            $this->doupdateposts($user_id);
            
        }
     }
     private function showAllPostCommentPage($user_id)
     {
        $sql = 'SELECT post_id, post_topic, post_content, post_by_who, post_date FROM posts ORDER BY datetime() DESC Limit 1';
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any post now.<br>';
        }
        else {
            echo 'There are the current posts:<br>';
            foreach ($rows as $row)
            {
                echo "Topic: {$row['post_topic']}\n Content: {$row['post_content']}\n Poster: {$row['post_by_who']}\n Date: {$row['post_date']}\n<br>";
                $comment_by_post = $row->post_id;
                $sql_c = 'SELECT comment_content, comment_by_who, comment_date FROM comments WHERE comment_by_post = :comment_by_post ORDER BY datetime() DESC Limit 1';
                $query_c = $this->db_connection->prepare($sql_c);
                $query_c->bindValue(':comment_by_post', $comment_by_post);
                $query_c->execute();
                $rows_c = $query_c->fetchAll(PDO::FETCH_ASSOC);
                if (!$rows_c) {
                    echo 'There\'s no comment now.<br>';
                } else {
                    foreach($rows_c as $row_c)
                    {
                        echo "{$row_c['comment_content']}\n Poster: {$row_c['comment_by_who']}\n Date: {$row_c['comment_date']}\n<br>";
                    }
                }
                echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=createcomment" name="createcommentform">';
                echo '<label for="add_comment">Reply</label>';
                echo '<input id="add_comment" type="text" name="add_comment" required /><br>';
                echo '<input type="submit" name = "createcomment" value="Add" onClick="docreatecomment($user_id, $comment_by_post)" />';
                echo "<br>";
                echo '</form>';
            }

        }
     }
     private function showPostPage($user_id)
     {
        $sql = 'SELECT post_content, post_topic, post_date FROM posts WHERE post_by_who = :user_id';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_id', $user_id);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any post now.<br>';
        }
        else {
            echo 'There are your current posts:<br>';
            foreach ($rows as $row)
            {
                echo "Topic: {$row['post_topic']}\n Content: {$row['post_content']}\n Date: {$row['post_date']}\n<br>";
            }
        }
     }
     private function showPost_TopicPage($topic)
     {
         $sql = 'SELECT post_content, post_date, post_by_who FROM posts WHERE post_topic = :post_topic';
         $query = $this->db_connection->prepare($sql);
        $query->bindValue(':post_topic', $topic);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'This topic doesn\'t have any post now.<br>';
        }
        else {
            echo 'There are the posts about this topic:<br>';
            foreach ($rows as $row)
            {
                echo "Content: {$row['post_content']}\n Date: {$row['post_date']}\n Poster: {$row['post_by_who']}\n<br>";
            }
        }
     }
     private function showTopicPage()
     {
        $sql = 'SELECT topic_name FROM topics';
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $thetopic = "a";
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any topic now.<br>';
        }
        else {
            echo 'There are some topics:<br>';
            foreach ($rows as $row)
            {
                $topic = $row->topic_name;
                echo "Topic: {$row['topic_name']}\n";
                echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=showTopicPostPage">Posts</a><br>';
                if(isset($_GET["action"]) && $_GET["action"] == "showTopicPostPage") {
                    $thetopic = $row->topic_name;
                }
                //echo '<a href='mainpage.php'>Detail</a>';
            }
        }
        if(isset($_GET["action"]) && $_GET["action"] == "showTopicPostPage") {
            echo "show posts about this topic";
            $thetopic = $row->topic_name;
            echo $thetopic;
        }
     }
     private function showTopicPostPage()
     {
         if (isset($_GET["action"]) && $_GET["action"] == "showTopicPostPage") {
            echo $topic;
         }
         
     }
     private function showPostCommentPage($user_id)
     {
        $sql = 'SELECT * FROM posts';
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any post now.<br>';
        }
        else {
            echo 'There are posts:<br>';
            foreach ($rows as $row)
            {
                $post_id = $row->post_id;
                echo "Topic: {$row['post_topic']}\n Content: {$row['post_content']}\n Poster: {$row['post_by_who']}\n Date: {$row['post_date']}\n <br>";
                $sql_c = 'SELECT comment_content, comment_by_who, comment_date FROM comments WHERE comment_by_post = :comment_by_post ORDER BY datetime() DESC Limit 1';
                $query_c = $this->db_connection->prepare($sql_c);
                $query_c->bindValue(':comment_by_post', $comment_by_post);
                $query_c->execute();
                $rows_c = $query_c->fetchAll(PDO::FETCH_ASSOC);
                if (!$rows_c) {
                    echo 'There\'s no comment now.<br>';
                } else {
                    foreach($rows_c as $row_c)
                    {
                        echo "{$row_c['comment_content']}\n Poster: {$row_c['comment_by_who']}\n Date: {$row_c['comment_date']}\n<br>";
                    }
                }
                //echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=showCommentsPage">Comments</a><br>';
                echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=docreatecomment($user_id, $post_id)" name="createcommentform">';
                echo '<label for="add_comment">Reply</label>';
                echo '<input id="add_comment" type="text" name="add_comment" required /><br>';
                echo '<input type="submit" name = "createcomment" value="Add"  />';
                echo "<br>";
                echo '</form>';
            }
        }
     }
     private function docreatecomment($user_id, $post_id)
     {
        $comment_content = $_POST['add_comment'];
        $comment_date = date('Y-m-d H:i:s');
        $sql_add_com = "INSERT INTO comments (comment_content, comment_by_post, comment_by_who, comment_date) VALUES(:comment_content, :comment_by_post, :comment_by_who, :comment_date";
        $query->bindValue(':comment_content', $comment_content);
        $query->bindValue(':comment_by_who', $user_id);
        $query->bindValue(':comment_by_post', $post_id);
        $state = $query->execute();
                if ($state) {
                    $this->feedback = "You create a comment successfully!";
                    return true;
                } else {
                    $this->feedback = "Sorry, your commment doesn't create successfully.";
                }
        return false;
     }
     private function doupdateposts($user_id)
     {
         $post_id = $_POST['update_post_id'];
         $post_content = $_POST['update_post_content'];
         //$post_topic = $_POST['update_post_topic'];
         $sql = 'UPDATE posts SET post_content = :post_content WHERE post_id = :post_id';
         $query = $this->db_connection->prepare($sql);
         $query->bindValue(':post_content', $post_content);
         $query->bindValue(':post_id', $post_id);
         $state = $query->execute();
                if ($state) {
                    $this->feedback = "You update the post successfully!";
                    return true;
                } else {
                    $this->feedback = "Sorry, your post doesn't update successfully.";
                }
        return false;
     }
     private function dodeleteposts($user_id)
     {
         $post_id = $_POST['delete_post_id'];
             $sql = 'DELETE FROM posts WHERE post_id = :post_id';
             $query = $this->db_connection->prepare($sql);
             $query->bindValue(':post_id', $post_id);
             $state = $query->execute();
         if ($state) {
                    $this->feedback = "You delete posts successfully!";
                    return true;
                } else {
                    $this->feedback = "Sorry, your posts don't be deleted successfully.";
                }
             return false;
     }
     private function docreateposts($user_id)
     {
         $post_topic = $_POST['topic'];
         $post_content = $_POST['post_content'];
         $post_date = date('Y-m-d H:i:s');
         $sql = 'INSERT INTO posts (post_content, post_topic, post_date, post_by_who)
                    VALUES(:post_content, :post_topic, :post_date, :post_by_who)';
         $query = $this->db_connection->prepare($sql);
         $query->bindValue(':post_content', $post_content);
         $query->bindValue(':post_topic', $post_topic);
         $query->bindValue(':post_date', $post_date);
         $query->bindValue(':post_by_who', $user_id);
         $state = $query->execute();
                if ($state) {
                    $this->feedback = "You create a post successfully!";
                    return true;
                } else {
                    $this->feedback = "Sorry, your post doesn't create successfully.";
                }
        return false;
     }
     private function showUpdatePostPage1()
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
         echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=updatethepost" name="updatethepostform">';
         $post_id = $_POST['update_post_id'];
         $sql = 'SELECT post_content, post_topic FROM posts WHERE post_id = :post_id';
         $query = $this->db_connection->prepare($sql);
         $query->bindValue(':post_id', $post_id);
         $query->execute();
         $row = $query->fetchObject();
         echo 'Update your post:<br>';
         echo  "ID:".$post_id."<br>";
         //echo '<text name="update_post_topic" value="'.$row['post_topic'].'">'.$row['post_topic'].'/><br>';
         echo 'Message: <br><textarea name="post_content" rows="10" cols="30">'.$row['post_content'].'</textarea><br>';
         echo '<input type="submit" name = "createpost" value="'.$row['post_content'].'" />';
         //echo '<input type="submit" name = "updatethepost" value="Update" />';
         echo '</form>';
     }
     private function showUpdatePostPage($user_id)
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<h2>Choose the Posts You Want to Update:</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=updateposts" name="updatepostsform">';
        $sql = 'SELECT post_id, post_content, post_topic, post_date FROM posts WHERE post_by_who = :user_id';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_id', $user_id);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any post now.<br>';
        }
        else {
            echo '<label for="post_id">There are your current posts and you could choose the one you want to update.<br></label>';
            foreach($rows as $row)
               {
                   echo '<input id="update_post_id" type="radio" name="update_post_id" value="'.$row['post_id'].'">'.$row['post_id'].'/>';
                   echo "Topic: {$row['post_topic']}\n Content: {$row['post_content']}\n Date: {$row['post_date']}\n<br>";
               }
        }
       
        echo '<input type="submit" name = "updateposts" value="Select" />';
        echo '</form>';
     }
     private function showDeletePostPage($user_id)
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<h2>Choose the Posts You Want to Delete:</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=deleteposts" name="deletepostsform">';
        $sql = 'SELECT post_id, post_content, post_topic, post_date FROM posts WHERE post_by_who = :user_id';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_id', $user_id);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo 'You don\'t have any post now.<br>';
        }
        else {
            echo 'There are your current posts and you could choose the ones you want to delete.<br>';
            echo '<select name="delete_post_id" size="3" multiple="multiple" tabindex="1">';
            foreach ($rows as $row)
            {
                echo '<option value="'.$row['post_id'].'">'.$row['post_id'].' /option>';
                echo "Topic: {$row['post_topic']}\n Content: {$row['post_content']}\n Date: {$row['post_date']}\n<br>";
            }
        }
        echo '<input type="submit" name = "deleteposts" value="Delete Posts Selected" />';
        echo '</form>';
     }
     private function showCreatePostPage()
     {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<h2>Create a Post</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=createposts" name="createpostsform">';
        $sql = 'SELECT topic_name FROM topics';
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        echo '<label for="post_topics">There are some topics for you to choose:<br></label>';
        foreach($rows as $row)
            {
                echo '<input id="post_topics" type="radio" name="topic" value="'.$row['topic_name'].'">'.$row['topic_name'].'<br> />';
            }
        echo 'Message: <br><textarea name="post_content" rows="10" cols="30">Your Post:</textarea><br>';
        echo '<input type="submit" name = "createpost" value="Create Post" />';
        echo '</form>';
     }
     /**
     * Change the logged in user's password
     * @return bool User login success status
     */
    private function showPageChangePassword()
    {
         if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<h2>Change Password</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=changepassword" name="changepwdform">';
        echo '<label for="login_input_username">Username (or Email)</label>';
        echo '<input id="login_input_username" type="text" name="user_name" required /> ';
        echo '<label for="login_input_password">Old Password</label> ';
        echo '<input id="login_input_password" type="password" name="user_password" required /> ';
        echo '<label for="login_input_password_new">Password (min. 6 characters)</label>';
        echo '<input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />';
        echo '<label for="login_input_password_repeat">Repeat password</label>';
        echo '<input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />';
        echo '<input type="submit" name="changepassword" value="Change Password" />';
        echo '</form>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    }
    /**
     * Simple demo-"page" with the login form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageLoginForm()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        //echo '<h2>Forum</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" name="loginform">';
        echo '<label for="login_input_username">Username (or email)</label> ';
        echo '<input id="login_input_username" type="text" name="user_name" required /> ';
        echo '<label for="login_input_password">Password</label> ';
        echo '<input id="login_input_password" type="password" name="user_password" required /> ';
        echo '<input type="submit"  name="login" value="Log in" />';
        echo '</form>';
        //$change_role .= "<a href='quickstart2.php?'>Register with your Facebook' role</a>";
        //echo $change_role;
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=register">Register new account</a><br>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=changepassword">Change password</a>';
    }
    /**
     * Simple demo-"page" with the registration form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageRegistration()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<h2>Registration</h2>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=register" name="registerform">';
        echo '<label for="login_input_username">Username (only letters and numbers, 2 to 64 characters)<br></label>';
        echo '<input id="login_input_username" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required /><br>';
        echo '<label for="login_input_email">User\'s email<br></label>';
        echo '<input id="login_input_email" type="email" name="user_email" required /><br>';
        echo '<label for="login_input_password_new">Password (min. 6 characters)<br></label>';
        echo '<input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" /><br>';
        echo '<label for="login_input_password_repeat">Repeat password<br></label>';
        echo '<input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" /><br>';
        echo '<label for="login_input_user_role">User\'s role<br></label>';
        echo '
              <input type="radio" name="user_role" value=2> Author<br>
              <input type="radio" name="user_role" value=3 checked> User<br>
              ';
        echo '<input type="submit" name="register" value="Register" /> <script src="oauth.js"></script>';
        echo '</form>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    }
}
// run the application

$application = new OneFileLoginApplication();
