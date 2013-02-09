<html>
<head>
<style>
body {
    background-color:#000;
    color:#0F0;
}
textarea, .keytext, .idtext, #log, #mysql, .mysqlform, .view {
    border:dashed #0F0 1px;
	background-color:#000;
	color:#0F0;
  
}

.keybox {
    float:left;
}
.idbox {
    margin-left:50%;
}
.top {
    margin-bottom:50px;
}
#crypt, #decrypt {
    width:100%;
}
.retrievebutton {
    margin-left:15px;
}
#crypttext {
    float:left;
    width:49%;
    padding-right:10px;
    
}
#decrypttext {
    padding-left:10px;
    float:right;
    width:49%;
    
}
.view {
    word-wrap: break-word;
    margin-top:20px;
    width:48%;
    min-height:100px;
    padding:5px;
}
.crypted {
    float:left;
    padding-right:10px;

}
#decrypted {
    float:right;
    padding-left:10px;
}
.button {
    width:100%;
    float:left;
margin-top:20px;
}
.buttons {
    width:49%;
    float:right;
    margin-bottom:10px;
}
.stats {
    width:49%;
    float:left;
}
#log {
    overflow:auto;	
    margin-top:40px;
    height:10.2em;
    font-size:11px;
    padding:5px;
	float:left;
}
#mysql {
	overflow:auto;
	float:right;
    margin-top:25px;
  height:10.2em;
    font-size:11px;
}
.mysqlform {
	font-size:10px;
	
}
table td{
	font-size:11px;
}
.custommysql {
	display:none;
}
</style>
<script src="jquery.js"></script>
<title> ♠ Crypto</title>
</head>
<body>
<div class="top"> 
    <div class="keybox">
        <span class="key">Key:</span>
        <input onFocus="clearText(this);" type="text" class="keytext" id="key" name="key" value="Enter Private Key" />
</div>
    <div class="idbox">
        <span class="id">Retrieve from ID:</span>
        <input onFocus="clearText(this);" style="width:225px;" maxlength="32" width="200px" value="Enter Key to Retrieve Message" class="idtext" type="text" id="id" name="id" />
   
      <input class="retrievebutton" id="button2" type="button" value="Retrieve Message"/>
    </div>
</div>
<div>    
<div id="crypttext">Enter Text to Crypt:<br /><textarea type="text" id="crypt" name="crypt" rows="10" ></textarea></div>
   <div id="decrypttext">Enter Text to Decrypt:<br><textarea type="text" id="decrypt" name="encrypt" rows="10" cols="30"></textarea></div>
   <div class="button">
     
     <div class="stats">| Characters: <span class="char"> 0 </span> | Words: <span class="words"> 0 </span> | </div>
     <div class="buttons">
       <input id="button" type="button" value="Encrypt"/>
       <input id="save" type="button" value="Save"/>
      
       <input style="width:225px; margin-left:20px;" maxlength="32" width="200px" class="idtext" type="text" value="Click 'Save' to get the ID" id="webid" name="id2" /> </div>
     <p><div class="view" id="log"></div>
     <div class="view" id="mysql">
     <pre id="readme"></pre>
       <div class="custommysql">
         <input checked type="checkbox" id="customdb"/><span class="mysqlcheck">Custom db will be used.</span><br/>
     <table style="display:none;" border="0">
  <tr>
    <td>Custom DB Host:</td>
    <td><span class="formfields">
      <input id="customdbhost" class="mysqlform" type="text"/>
    </span></td>
  </tr>
  <tr>
    <td><span class="formdesc">Custom DB User: </span></td>
    <td><span class="formfields">
      <input id="customdbuser" class="mysqlform" type="text"/>
    </span></td>
  </tr>
  <tr>
    <td><span class="formdesc">Custom DB Password:</span></td>
    <td><span class="formfields">
      <input id="customdbpass" class="mysqlform" type="text"/>
    </span></td>
  </tr>
  <tr>
    <td><span class="formdesc">Custom DB Table: </span></td>
    <td><span class="formfields">
      <input id="customdbtable" class="mysqlform" type="text"/>
    </span></td>
  </tr>
</table>
</div> 
     </div> 
     </p>
   </div>
</div>
<div class="view crypted" ><span id="crypted">Crypted text will appear here.</span><br/></div>
     <div class="view" id="decrypted">Decrypted text will appear here.</div>
     
<script language="javascript">
$('#readme').load('README.txt');
//$('#mysql').prepend('Click this box to enter custom MySql db information.<br/>');
function Log(msg) {
	$('#log').prepend('@' + time() + ': ' + msg+'<br/>');
}

function testhex1(test) {
	var alNumRegex = /^([a-fA-F0-9]+)$/; //only letters and numbers
	if (alNumRegex.test(test) || test == '') {
		return true; } 
	else {
		
		$('#decrypt').val(test.replace(/[^a-fA-F 0-9]+/g,'').replace(/ /g,''));
		return false;
	}
}
function testhex(test) {
	var alNumRegex = /^([a-fA-F0-9]+)$/; //only letters and numbers
	if (alNumRegex.test(test)) {
		return true; } 
	else {
		
		return false;
	}
}
function clearText(field) {
	if (field.defaultValue == field.value) field.value = '';
	//else if (field.value == '') field.value = field.defaultValue;
}

function time() {
	var foo = new Date; // Generic JS date object
	var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
	var unixtime = parseInt(unixtime_ms / 1000);
	return unixtime;
}
	
function transform () {
	var tocrypt = $('#crypt').val();
	var todecrypt = $('#decrypt').val();
	//check if decrypted is hex
	if(testhex1(todecrypt)==true){
		var key = $('#key').val().replace(/ /g,'');
		$.post('crypt.php', {
			tocrypt: tocrypt,
			todecrypt: todecrypt,
			key: key
		}, function(data) {
			console.log(data);
			var result = data.split("x-.-x");
			if(tocrypt !== ''){
				$('#crypted').html(result[0]);
			}
			if(todecrypt !== ''){
				$('#decrypted').html(result[1]);
			}
		});
		if (tocrypt == '' && todecrypt == ''){
			Log('Type something first... >.<\'');
			$('#crypted').html('Crypted text will appear here.');
			$('#decrypted').html('Decrypted text will appear here.');
		}
		else if (todecrypt == ''){
			Log('Text Crypted with key: "<i>'+key+'</i>"');
			$('#decrypted').html('Decrypted text will appear here.');
		}
		else if (tocrypt == ''){
			$('#crypted').html('Crypted text will appear here.');
			Log('Text Decrypted with key: "<i>'+key+'</i>"');
		}
		
		else {
			Log('Text Crypted / Decrypted with key: "<i>'+key+'</i>"');
		}
	}
	else { Log('Enter hex to decrypt field.'); }
}
$('#button').click( function(){ transform(); } );
$('#crypt').keyup(function() {
	$('.char').html($('#crypt').val().length);
	$('.words').html($('#crypt').val().split(' ').length);
});
$('#log').append('@' + time() + ': <i>Log has been started.</i>');

$('#save').click(function(){
	var crypted = $('#crypted').html();
	 if (crypted=='Crypted text will appear here.') { Log('Type something first -_-\''); }
	else if (testhex(crypted) == true){
		$.post('bottle.php', {
				crypted: crypted,
			}, function(data) {
				Log(data);
				var result = data.split("x-.-x");
		});
	}
	else {Log('Cannot save this non-hex nonsense :/ '+ testhex(crypted));}
});
     </script>
</body></html>