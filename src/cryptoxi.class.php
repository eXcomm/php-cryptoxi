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
    function is_line_secure(){
        //this actually just sees if there is a key to encrypt with.
        if (session_id() != '' && isset($_SESSION['key'])) {
            return true;
        } else {
            return false;
        }
        
    }
    function get_room_exp ($room_id){
        $room = md5($room_id);
        if($this->is_room_valid($room_id)){
            //look up roomkey from database
            $room_date =  $this->is_room_valid($room_id, false, false, true);
            return $room_date;
        }
        else{
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
            $chat_sessions_id = $this->get_roomID($room_id);
            //retrieve entries within the creation time of $room_id
            //for $room_id
            //order by ID Descending
            // lets say $result array is this
            $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

            /* check connection */
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }
            $table = TABLE_MSG;
            $db = MYSQL_DB;
            $qcount = "SELECT COUNT(`message`) FROM `$table` WHERE `chat_sessions_id` = '$chat_sessions_id' ORDER BY `id` DESC";
            if ($result = mysqli_query($mysqli, $qcount)) {
                $row = $result->fetch_array(MYSQLI_NUM);
                $count = $row[0];

                mysqli_free_result($result);
            }
            else {
                printf("Error: %s\n", mysqli_error($mysqli));
                mysqli_free_result($result);

            }



            $how_many = $this->retrieve_how_many($count);
            if (!$how_many || $count == 0) {
                // everything is read.
                // or no messages to retrieve.
                echo "<h3>how_many $how_many</h3>";
                echo "<h3>count $count</h3>";
                return true;
            }

            $q = "SELECT  `message` 
                FROM  `$table` 
                WHERE  `chat_sessions_id` =  '$chat_sessions_id'
                ORDER BY  `id` DESC 
                LIMIT 0 , $how_many
                ";
            
            
            if ($result = mysqli_query($mysqli, $q)) {
                //if ($count > 0 && $read <= $count) {
                    # code...
                //}
                $decrypted = array();
                while($row = $result->fetch_assoc()) {

                    $_SESSION['read'] += 1;
                    $decrypted[] = $libcryptoxi->decryptxi( $row['message']);
                }

                echo "<h3>FOUND $count rows.</h3>";
                // die('death');

                mysqli_free_result($result);
                $mysqli->close();

                //return decrypted array
                return array_reverse($decrypted);
            }
            else {
                printf("Error: %s\n", mysqli_error($mysqli));
                mysqli_free_result($result);
                $mysqli->close();
                return false;
            }


            

        } else {
            // room isn't valid
            return false;
        }
    }
    private function retrieve_how_many ($count, $limit = 100, $read = NULL ){
        if (is_null($read)){
            $read = $_SESSION['read'];
        }
        $Rx = ($count-$read) ;
        echo "<h3>Rx $Rx</h3>";
        echo "<h3>Limit $limit</h3>";
        if ($Rx == 0) {
            // there is nothing.
            echo "<h3>RX Nothing</h3>";
            return false;
        }
        if ($Rx !== $limit) {

            $Rx = $Rx % $limit;
            $Rx = ($Rx == 0) ? $limit : $Rx ;
        }

        if($read > $count){
            die('something went wrong. How did you read more than there was?');
        }

        
        return $Rx;

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
    function clean_up(){
        $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
         if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $table = TABLE_SES;
        $db = MYSQL_DB;
        $q = "DELETE 
            FROM  `$table` 
            WHERE `date` < DATE_SUB( NOW( ) , INTERVAL 2 HOUR )";
        if ($result = mysqli_query($mysqli, $q)) {
                
                echo "<h3>Rows Deleted ".mysqli_num_rows($result)."</h3>";
                mysqli_free_result($result);
                $mysqli->close();
                return true;
            }
            else {
                printf("Error: %s\n", mysqli_error($mysqli));
                mysqli_free_result($result);
                $mysqli->close();
                return false;
            }
    }

    function is_room_valid($room_id, $return_room_key = false, $return_room_id = false, $return_room_date = false){
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
            FROM  `$table` 
            WHERE  `room` =  '$room'
            AND  `date` >= DATE_SUB( NOW( ) , INTERVAL 2 HOUR ) 
            LIMIT 0 , 30";
        if ($result = mysqli_query($mysqli, $q)) {
            
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $room_key = $row['room_key'];
            $roomID = $row['id'];
            $room_date = $row['date'];
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
                elseif ($return_room_date) {
                    $return = $room_date;
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
include 'page.php';
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
echo $c->store ($room, 'alala', 'text1' );
echo $c->store ($room, 'alala', 'text2' );
echo $c->store ($room, 'alala', 'text3' );
echo $c->store ($room, 'alala', 'text4' );
echo $c->store ($room, 'alala', 'text5' );
echo $c->store ($room, 'alala', 'text6' );
echo $c->store ($room, 'alala', 'text7' );
echo $c->store ($room, 'alala', 'text8' );
echo $c->store ($room, 'alala', 'text9' );
echo '<br><h2>retrieve</h2>';
$r =  $c->retrieve ($room, 'alala');
echo "<pre>";
var_dump($r);
echo "</pre>";
echo '<br><h2>get_room_exp</h2>';
$date =  $c->get_room_exp ($room );
echo strtotime($date);
echo "<br>";
echo $date;
$time = DateTime::createFromFormat("Y-m-d H:i:s", $date);
echo "<br>";
echo $time->format('H-i-s');
$time->add(new DateInterval('P10h'));
echo "<br>";
echo $time->format('H-i-s');

?>
