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
        $mysqli = new mysqli('localhost', 'my_user', 'my_password', 'my_db');
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        // See if found any users with the same password

    }

    function logout (){

    }
}
?>