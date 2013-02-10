<?PHP
require_once 'mysql.config.php';
class UserXI {
    var $user, $pass;
    function UserXI ($user, $pass) {
       $this->$user = md5($user);
       $this->pass = md5($pass); 
    }
    private function save_cookie (){

    }
    private function login (){
        $u = $this->user;
        $p = $this->pass;
        // Look for entries in database 
        // Find ones that are max day old
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        // See if found any users with the same password
        $q = "SELECT  `id` 
        FROM  `chat_users` 
        WHERE DATE(  `date` ) >= CURDATE( ) - INTERVAL 1 
        DAY AND  `user_name` =  '$u'
        AND  `password` =  '$p'
        LIMIT 0 , 30";

    }
    private function register (){
        //http://static1.robohash.org/string?size=64x64
        $usr = md5(uniqid());
        $pass = md5(uniqid());
    }

    function logout (){

    }
}
?>