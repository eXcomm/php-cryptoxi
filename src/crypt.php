<?php
    require_once 'cryptoxi.class.php';
    // Start the session so we can use PHP sessions
    if(session_id() == '') {
        session_start();
    }
    // Include the jCryption library
    require_once("../lib/jcryption/jcryption.php");
    // Set the RSA key length
    $keyLength = 1024;
    // Create a jCrytion object
    $jCryption = new jCryption();
    
    // If the GET parameter "generateKeypair" is set
    if(isset($_GET["generateKeypair"])) {
        // Include some RSA keys
        require_once("../lib/jcryption/100_1024_keys.inc.php");
        // Pick a random RSA key from the array
        $keys = $arrKeys[mt_rand(0, 100)];
        // Save the RSA keypair into the session
        $_SESSION["e"] = array("int" => $keys["e"], "hex" => $jCryption->dec2string($keys["e"], 16));
        $_SESSION["d"] = array("int" => $keys["d"], "hex" => $jCryption->dec2string($keys["d"], 16));
        $_SESSION["n"] = array("int" => $keys["n"], "hex" => $jCryption->dec2string($keys["n"], 16));
        // Create an array containing the RSA keypair
        $arrOutput = array(
            "e" => $_SESSION["e"]["hex"],
            "n" => $_SESSION["n"]["hex"],
            "maxdigits" => intval($keyLength*2/16+3)
        );
        // JSON encode the RSA keypair
        echo json_encode($arrOutput);
    // If the GET parameter "handshake" is set
    } elseif (isset($_GET["handshake"])) {
        // Decrypt the AES key with the RSA key
        $key = $jCryption->decrypt($_POST['key'], $_SESSION["d"]["int"], $_SESSION["n"]["int"]);
        // Removed the RSA key from the session
        unset($_SESSION["e"]);
        unset($_SESSION["d"]);
        unset($_SESSION["n"]);
        // Save the AES key into the session
        $_SESSION["key"] = $key;
        // JSON encohe the challenge
        echo json_encode(array("challenge" => AesCtr::encrypt($key, $key, 256)));
    } else {
        process_recieved();
        // Encrypt it again for testing purposes
        $encryptedData = AesCtr::encrypt($decryptedData, $_SESSION["key"],256);
        // JSON encode the response
        // TODO don't send decrypted passphrase back when not testing
        
    }
    function process_send (){
        echo json_encode(array("data" => $encryptedData, "passphrase" => $decryptedPass));
    }
    function process_recieved (){
        $cryptoxi = new CryptoXI();
        if (isset($_GET['room']) && isset($_POST['passphrase']) && isset($_POST['text'])){
            //sending text
            $room = $_GET['room'];
            $passphrase = decrypt($_POST['passphrase']);
            $text = decrypt($_POST['text']);
            
            // store the text
            $cryptoxi->store($room,$passphrase,$text);


        }
        elseif (isset($_POST['new_room'])) {
            $new_room = decrypt($_POST['new_room']);
            if ($new_room) {
                $room_id = $cryptoxi->gen_room();
                if ($room_id) {
                    $rid = encrypt($room_id);
                    $sending = array('room_id' => $rid);
                    send($sending);
                } else {
                    //room id is not generated
                    $rid = encrypt('room id not generated');
                    $sending = array('error' => $rid);
                    send($sending);
                }
                
            }
        }

        

    }
    function decrypt ($str){
        return AesCtr::decrypt($str, $_SESSION["key"], 256);
    }
    function encrypt ($str){
        return AesCtr::encrypt($str, $_SESSION["key"], 256);
    }
    function send ($ar){
        echo json_encode($ar);
        return;
    }
    function safe_char ($str) {
        return preg_replace('/[^a-z]/', "", strtolower($str));
    }
?>