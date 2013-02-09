<?PHP
class cryptoxi{
	
	var $publickey = ""; //$_POST['key'];
	//var $encrypt = ""; // $_POST['tocrypt'];
	//var $decrypt = "";
	var $privatekey = "2c3fec85b5f6b0a1cd5a1010c67eb772";
	function cryptoxi($publickey, $privatekey){
		$this->publickey = $publickey;
		$this->privatekey = $privatekey;
	}
	//$encrypted =  mysql_aes_encrypt ($crypt,$key);

	private function hexit ($str) {
		return bin2hex($str);
	}
	private function binit ($str){
		return pack("H*",$str);
		
	}
	/*
	Hex to bin - bin to hex conversion example
	===========================================
	*/
	
	#echo hexit('brown fox'); echo '</br> binit: '; echo binit('62726f776e20666f78'); echo '</br>';
	
	#==========================================
	
	function encryptxi($val) 
	{ 
		$ky = $this->publickey;
		$key= $this->privatekey; 
		for($a=0;$a<strlen($ky);$a++) 
		  $key[$a%16]=chr(ord($key[$a%16]) ^ ord($ky[$a])); 
		$mode=MCRYPT_MODE_ECB; 
		$enc=MCRYPT_RIJNDAEL_128; 
		$val=str_pad($val, (16*(floor(strlen($val) / 16)+(strlen($val) % 16==0?2:1))), chr(16-(strlen($val) % 16))); 
		$result = mcrypt_encrypt($enc, $key, $val, $mode, mcrypt_create_iv( mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM)); 
		return $this->hexit($result);
	} 
	
	
	function decryptxi($val) 
	{ 	
		$ky = $this->publickey;
		$value = $this->binit($val);
		$key=$this->privatekey; 
		for($a=0;$a<strlen($ky);$a++) 
		  $key[$a%16]=chr(ord($key[$a%16]) ^ ord($ky[$a])); 
		$mode = MCRYPT_MODE_ECB; 
		$enc = MCRYPT_RIJNDAEL_128; 
		$dec = @mcrypt_decrypt($enc, $key, $value, $mode, @mcrypt_create_iv( @mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM ) ); 
		return rtrim($dec,(( ord(substr($dec,strlen($dec)-1,1))>=0 and ord(substr($dec, strlen($dec)-1,1))<=16)? chr(ord( substr($dec,strlen($dec)-1,1))):null)); 
	} 
}
?>