<?PHP
/*
This is for retrieving the message.
*/
$link = mysql_connect(HOST,USR,PASS );
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
$id = $_GET['id'];

$query = "SELECT `value` AND `time`  FROM `$TABLE`.`bottle` WHERE `id` = '$id'"; 	 
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result) or die(mysql_error());

echo 'This message has been bottled up @'.$row['time']. "<br/><br/>A dusty note has been retrieved from the bottle: ". $row['value'];

$row = mysql_fetch_array();
echo $row[0];
mysql_close($link);
?>