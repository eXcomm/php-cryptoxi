<?PHP
require_once 'libcryptoxi.class.php';
class CryptoXI {
    function gen_room (){
        $room_id = uniqid();
        $room_key = uniqid();

        $room = md5($room_id);
        //save $room, $roomkey, with date to database
        //if success return room id
        if (true) {
            return $room_id;
        } else {
            return false;
        }
        


        
    }
    function get_room_key ($room_id){
        $room = md5($room_id);
        if($this->is_room_valid($room_id)){
            //look up roomkey from database
            $roomkey = true;
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

    function is_room_valid($room_id){
        $room = md5($room_id);
        //look up $room number from database
        //if it exists within 2 hours
        if (true) {
            return true;
        } else {
            return false;
        }
        
    }

}

$c = new CryptoXI();
echo $c->gen_room();
?>
