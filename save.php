<?PHP
class Save {
	
	function sqlite () {
		try 
	{
	  //create or open the database
	   $database = new SQLiteDatabase('db.sqlite', 0666, $error);
	}
	catch(Exception $e) 
	{
	  die($error);
	}
	echo 'connected. </br>';
	//add Movie table to database
	$query = 'CREATE TABLE IF NOT EXISTS Cryptoxi' .
			 '(Id TEXT, Time INTEGER, Message TEXT)';
			 
	if(!$database->queryExec($query, $error))
	{
	  die($error);
	}
		

	//insert data into database
	$a = 'Id';
	$b = 'Time';
	$c = 'Message';
	$query = 
	  "INSERT INTO Movies ('$a', '$b', '$c') " .
	  'VALUES ("Interstella 5555", "Daft Punk", 2003); ' /*.
			 
	  'INSERT INTO Movies (Title, Director, Year) ' .
	  'VALUES ("Cloverfield", "Matt Reeves", 2008); ' .
			 
	  'INSERT INTO Movies (Title, Director, YEAR) ' .
	  'VALUES ("Beverly Hills Chihuahua", "Raja Gosnell", 2008)'*/;
	
	if(!$database->queryExec($query, $error))
	{
	  die($error);
	}
	
	function select ($database){
		//read data from database
		$query = "SELECT * FROM Movies";
		$tit = 'Title';
		if($result = $database->query($query, SQLITE_BOTH, $error))
		{
		  while($row = $result->fetch())
		  {
			print("Title: {$row[$tit]} <br />" .
				  "Director: {$row['Director']} <br />".
				  "Year: {$row['Year']} <br /><br />");
		  }
		}
		else
		{
		  die($error);
		}
	}
	select($database);
	}
	
}
?>