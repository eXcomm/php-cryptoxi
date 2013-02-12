<?PHP
require_once 'libcryptoxi.class.php';
require_once 'mysql.config.php';
error_reporting(E_ALL);

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
            $libcryptoxi->privatekey = $this->privatekey($room_id);
            $hex = $libcryptoxi->encryptxi($text);
            $chat_sessions_id = $this->get_roomID($room_id);
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
                `message` ,
                `chat_sessions_id`
                )
                VALUES (
                NULL ,  '$hex',  '$chat_sessions_id'
                );";
    
            if ($result = mysqli_query($mysqli, $q)) {
                printf ("New Record has id %d.\n", mysqli_insert_id($mysqli));
                //if success return room id
                // free result set 
                mysqli_free_result($result);
                $mysqli->close();

                return true;
            }
            else {
                printf("Error: %s\n", mysqli_error($mysqli));
                $mysqli->close();
                return false;
            }


        } else {
            // room isn't valid
            echo "<br>Room isn't valid<br>";
            return false;
        }
        

    }
    function retrieve($room_id, $passphrase){
        if ($this->is_room_valid($room_id)) {
            //continue
            $libcryptoxi = new libcryptoxi();
            $libcryptoxi->publickey = $passphrase;
            $libcryptoxi->privatekey = $this->privatekey($room_id);
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
        echo "<br>privatekey $privatekey<br>";

        return $privatekey;
    }
    function get_roomID ($room_id) {

        if($this->is_room_valid($room_id)){
            //look up roomkey from database
            return $this->is_room_valid($room_id, false, true);
        }
        else{
            return false;
        } 
    }

    function is_room_valid($room_id, $return_room_key = false, $return_room_id = false){
        $room = md5($room_id);
        // echo "<br>return_id: $return_room_id";
        // echo "<br>return_room_key: $return_room_key";
        // echo "<br>room_id: $room_id";
        // echo "<br>";
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
            // var_dump($row);
            // echo $room_key;
            // free result set 

            if ($result->num_rows > 0) {
                //we got a match
                // echo "<br>we got a match<br>";
                if ($result->num_rows > 1) {
                    # we got more than we bargained for
                    $row_cnt = $result->num_rows;
                    printf("Result set has %d rows.\n", $row_cnt);
                    $return = false;
                }
                if ($return_room_key) {

                    $return = $room_key;
                }
                elseif ($return_room_id) {
                    $return = $roomID;
                }
                 else {

                    $return = true;
                }
                
                
            } else {
                // echo "<br>Results Not > 0<br>";

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
echo "<h1>TESTS</h1>";
echo '<h2>gen_room</h2>';
$room = $c->gen_room();
echo $room;

echo '<br><h2>get_room_key</h2>';
echo $c->get_room_key($room );

echo '<br><h2>is_room_valid</h2>';
echo $c->is_room_valid ($room );

echo '<br><h2>get_roomID</h2>';
echo $c->get_roomID ($room );
echo '<br><h2>store</h2>';
echo $c->store ($room, 'alala', 'text' );
?>
