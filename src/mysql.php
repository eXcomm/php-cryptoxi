<?PHP
if(isset($_POST['db_host'],$_POST['db_name'], $_POST['db_user'],$_POST['db_pass'],$_POST['db_table'])){
$host= $_POST['db_host'];
$db_name = $_POST['db_name'];
$user = $_POST['db_user']; 
$pass= $_POST['db_pass'];
$table = $_POST['db_table'];
}
else{
 die("need credentials");
}
$link = mysql_connect($host, $user, $pass);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_query("CREATE TABLE IF NOT EXISTS '$table' (
  `id` varchar(32) NOT NULL,
  `value` longtext NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1");

$publickey = '511022420d29d00d3a62fadb0f1413c7';
?>