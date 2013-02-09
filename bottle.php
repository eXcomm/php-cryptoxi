<?PHP
/*
This is for storing the message
*/
/*


-- --------------------------------------------------------

--
-- Table structure for table `bottle`
--

CREATE TABLE IF NOT EXISTS `bottle` (
  `id` varchar(32) NOT NULL,
  `value` longtext NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

*/
$host=HOST;
$db_name =DB;
$user = USER; 
$pass= PASS;
$link = mysql_connect($host, $user, $pass);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
//$msg = $_POST['msg'];
//$id = md5(time().$msg);
$time = time();
echo '♠ Connected successfully @'.$time;
//mysql_query("INSERT INTO  `$TABLE`.`bottle` (`id`,`value`, `time`) VALUES ('$id','$msg','$time');") or die(mysql_error());
//echo 'Your message has been safely bottled up. Save this ID to retrieve the message.<br/> ID: '.$id.'<br/><br/><b>Your Message was:</b><br/>'.$msg;
//echo $id;
mysql_close($link);
?>