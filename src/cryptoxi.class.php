<?PHP
class CryptoXI {
    function gen_room (){
        $room = uniqid();
        return $room;
    }

}

$c = new CryptoXI();
echo $c->gen_room();
?>