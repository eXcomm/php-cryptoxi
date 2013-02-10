<?PHP
require_once 'mysql.config.php';
$table_user = TABLE_PREFIX+TABLE_USER;
class UserXI {
    var $user, $pass;
    private function start_session(){
        if(session_id() == '') {
            session_start();
        }
    }
    private function is_logged(){
        $this->start_session();
        if (isset($_SESSION['logged']) && $_SESSION['logged'] ==  md5($this->user+$this->pass)) {
            return true;
        } else {
            return false;
        }
        
    }
    private function session_save (){
        $_SESSION['logged'] = md5($this->user+$this->pass);
        $_SESSION['user_name'] = $this->user;

    }
    public function avatar_url ($size = 64) {
        $logo = md5($this->usr + $this->pass);
        $url = "http://static1.robohash.org/$logo?size=${size}x${size}";
    }
    public function login (){
        if ($this->is_logged())
            return;
        $u = $this->user;
        $p = md5($this->pass);
        // Look for entries in database 
        // Find ones that are max day old
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        // See if found any users with the same password
        $q = "SELECT  `id` 
        FROM  `${table_user}` 
        WHERE DATE(  `date` ) >= CURDATE( ) - INTERVAL 1 
        DAY AND  `user_name` =  '$u'
        AND  `password` =  '$p'
        LIMIT 0 , 30";

        $mysqli->query($q);
        if ($mysqli->mysqli_affected_rows() == 1) {
            $this->session_save();
            
        }
        else {
            die('found ' +$mysqli->mysqli_affected_rows()+' rows.');
        }
        $mysqli->close();

    }
    public function register (){
        if ($this->is_logged())
            return;
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        $user = md5(uniqid());
        $pass = md5(uniqid());
        $hashed_pass = md5($pass);
        $q = "INSERT INTO `${table_user}` (`id`, `user_name`, `password`, `date`) VALUES (NULL, '$user', '$hashed_pass', NOW());";
        $mysqli->query($q);

        $mysqli->close();
        $this->user = $user;
        $this->pass = $pass;
        
        $this->login();
        //send user the password pair
        return array('user'=>$user,'pass'=>$pass);
        

    }

    function logout (){
        unset( $_SESSION['logged']);
        return !$this->is_logged();

    }
}
?>