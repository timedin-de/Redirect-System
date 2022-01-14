<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php
  //PHP-DB Manager 
  //Version: 1.0
  //1.12.2021
  //
  //Code and all it parts under copyright by TimedIn 
  //Website: timedin.de
  //All rights reseved
  //
  //I would appreciate it if you credit me anywhere ;) 

  //CHANGABLE VALUES
	
	const host = "localhost";
	const username = "username";
	const password = "password";
	const dbname = "dbname";

?>
<?php
// No touchy here pls :)
class DBManager
{
	public $link;
	function __construct()
	{
		//echo "<!--Starting DB-Manager-->";
		$this->connect();
	}

	public function connect()
	{
		$this->link = new mysqli(host, username, password, dbname);
		// Check connection
    if ($this->link->connect_error)
    {
      die("Connection failed: " . $this->link->connect_error);
    }
	}
	public function QuerySingleRow($sql)
	{
		$result = $this->link->query($sql);
  	$res = array();
  	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$this->link->close();
        return $row;
    }
    $this->link->close();
    return null;
	}
	public function QueryMultipleRows($sql)
	{
		$result = $this->link->query($sql);
		$res = array();
		if ($result->num_rows > 0) {
		  while($row = $result->fetch_assoc()) {
		    array_push($res, $row);
		  }
		} else {
		  $res = null;
		}
		$this->link->close();
		return $res;
	}
}
?>
<?php 
/*	DEBUG
$db = new DBManager();
var_dump($db->QueryMultipleRows("SELECT * FROM redirects"));*/
?>
