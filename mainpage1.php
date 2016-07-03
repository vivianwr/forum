<?php


class OneFileLoginApplication
{
    private $db_type = "sqlite"; //
 
    private $db_sqlite_path = "./forumusers.db";
    
    private $db_connection = null;
    
    private $user_is_logged_in = false;
    
    public $feedback = "";
    
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
    private function doStartSession()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
    }
    private function doonce()
    {
        require_once "forum.php";
    }
    public function login()
    {
        $this->doStartSession();
        $this->doonce();
        if ($this->createDatabaseConnection() && !empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
    echo 'Hello ' . $_SESSION['user_name'] . ', you success.'; 
    $user_name = $_SESSION['user_name'];
        $sql = 'SELECT user_role FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute(); //true or false
        $result_row = $query->fetchObject(); //could use by $result_row->user_id
        $user_role = $result_row->user_role;
        echo $user_role;
    }
    else echo "wrong";

}
}

$application = new OneFileLoginApplication();


?>