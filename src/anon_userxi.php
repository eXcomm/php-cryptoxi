<?PHP
require_once 'mysql.config.php';
$table_user = TABLE_PREFIX+TABLE_USER;
class UserXI {
    var $user, $pass;

    private function save_cookie (){

    }
    public function avatar_url ($size = 64) {
        $logo = md5($this->usr + $this->pass);
        $url = "http://static1.robohash.org/$logo?size=${size}x${size}";
    }
    public function login (){
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
        FROM  `${table_user}` 
        WHERE DATE(  `date` ) >= CURDATE( ) - INTERVAL 1 
        DAY AND  `user_name` =  '$u'
        AND  `password` =  '$p'
        LIMIT 0 , 30";
        $mysqli->close();

    }
    public function register (){
        
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        $user = md5(uniqid());
        $pass = md5(uniqid());
        $q = "INSERT INTO `${table_user}` (`id`, `user_name`, `password`, `date`) VALUES (NULL, '$user', '$pass', NOW());";
        $mysqli->query($q);
        $this->user = $user;
        $this->pass = $pass;
        $this->login();
        

    }

    function logout (){

    }
}
?>