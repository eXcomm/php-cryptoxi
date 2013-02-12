<?PHP
require_once 'libcryptoxi.class.php';
require_once 'mysql.config.php';
class CryptoXI {
    function gen_room (){
        $room_id = uniqid();
        $room_key = uniqid();

        $room = md5($room_id);
        //save $room, $roomkey, with date to database
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $table = TABLE_SES;
        $db = MYSQL_DB;
        $q = "INSERT INTO  `$db`.`$table` (
            `id` ,
            `room` ,
            `room_key` ,
            `date`
            )
            VALUES (
            NULL ,  '$room',  '$room_key', NOW( )
            );";
        if ($result = mysqli_query($mysqli, $q)) {
            
            //if success return room id
            // free result set 
            mysqli_free_result($result);
            $mysqli->close();
            return $room_id;
        }
        else {
            printf("Error: %s\n", mysqli_error($mysqli));
            $mysqli->close();
            return false;
        }

    }
    function get_room_key ($room_id){
        $room = md5($room_id);
        if($this->is_room_valid($room_id)){
            //look up roomkey from database
            return $this->is_room_valid($room_id, true);
        }
        else{
            return false;
        } 

    }
    function store ($room_id, $passphrase, $text){
        if ($this->is_room_valid($room_id)) {
            //continue
            $libcryptoxi = new libcryptoxi();
            $libcryptoxi->publickey = $passphrase;
            $libcryptoxi->privatekey = $privatekey($room_id);
            $hex = $libcryptoxi->encryptxi($text);
            // store $hex to the database.
            $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

            /* check connection */
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }
            $table = TABLE_MSG;
            $db = MYSQL_DB;
            $q = "INSERT INTO  `$db`.`$table` (
                `id` ,
                `room` ,
                `room_key` ,
                `date`
                )
                VALUES (
                NULL ,  '$room',  '$room_key', NOW( )
                );";
            if ($result = mysqli_query($mysqli, $q)) {
                
                //if success return room id
                // free result set 
                mysqli_free_result($result);
                $mysqli->close();
                return $room_id;
            }
            else {
                printf("Error: %s\n", mysqli_error($mysqli));
                $mysqli->close();
                return false;
            }


        } else {
            // room isn't valid
            return false;
        }
        

    }
    function retrieve($room_id, $passphrase){
        if ($this->is_room_valid($room_id)) {
            //continue
            $libcryptoxi = new libcryptoxi();
            $libcryptoxi->publickey = $passphrase;
            $libcryptoxi->privatekey = $privatekey($room_id);
            //retrieve entries within the creation time of $room_id
            //for $room_id
            //order by ID Descending
            // lets say $result array is this
            $result = array();

            $decrypted = array();

            foreach ($result as $message) {
                $decrypted[] = $libcryptoxi->decryptxi($message);
            }
            //return decrypted array
            return $decrypted;
            

        } else {
            // room isn't valid
            return false;
        }
    }
    function privatekey($room_id){
        $static_key = "Anybody remember Ultima Online?";
        // today's date
        $t = time();
        $hour = date('H',$t);
        // expiration is 2 hours
        // is it going to be tomorrow in three hours?
        if ($hour+3 > 23) {
            $t+= 24*60*60; //tomorrow
            
        } 
        $year = date('Y',$t);
        $month = date('m',$t);
        $day = date('d',$t);

        $room = $this->get_room_key($room_id);
        $y = md5($year);
        $d = md5($day);
        $m = md5($month);
        $r = md5($room);
        $s = md5($static_key);

        $privatekey = md5($y.$d.$m.$r.$s);

        return $privatekey;
    }
    function get_roomID ($room_id){
        return is_room_valid($room_id, false, true);
    }

    function is_room_valid($room_id, $return_room_key = false, $return_id = false){
        $room = md5($room_id);
        //look up $room number from database
        //if it exists within 2 hours
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $table = TABLE_SES;
        $db = MYSQL_DB;
        $q = "SELECT  * 
            FROM  `chat_sessions` 
            WHERE  `room` =  '$room'
            AND  `date` >= DATE_SUB( NOW( ) , INTERVAL 2 HOUR ) 
            LIMIT 0 , 30";
        if ($result = mysqli_query($mysqli, $q)) {
            
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $room_key = $row['room_key'];
            $roomID = $row['id'];
            // echo $room_key;
            // free result set 

            if ($result->num_rows > 0) {
                //we got a match
                if ($result->num_rows > 1) {
                    # we got more than we bargained for
                    $return = false;
                }
                if ($return_room_key) {

                    $return = $room_key;
                }elseif ($return_id) {
                    $return = $roomID;
                }
                 else {

                    $return = true;
                }
                
                
            } else {

                $return = false;
            }
            mysqli_free_result($result);
            $mysqli->close();
        }
        else {
            printf("Error: %s\n", mysqli_error($mysqli));
            mysqli_free_result($result);
            $mysqli->close();
            $return = false;
        }
        return $return;

        
    }

}

$c = new CryptoXI();
echo '<br>gen_room';
$room = $c->gen_room();
echo $room;
echo '<br>get_room_key';
echo $c->get_room_key($room );
echo '<br>is_room_valid';
echo $c->is_room_valid ($room );
echo '<br>get_roomID';
echo $c->get_roomID ($room );
?>
