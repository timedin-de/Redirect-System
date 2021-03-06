<?php
  //PHP-Redirect Manager 
  //Version: 1.1
  //19.12.2020
  //
  //Code and all it parts under copyright by TimedIn 
  //Website: timedin.2ix.de
  //All rights reseved
  //
  
  //CHANGABLE VALUES

  //request to lower case makes request id to lowercase so the case doesnt matter (the id in the json has to be lowercase as well)
  $ignoreCase = false;
  

  //if Redirect ID is invalid (%redID% gets replaced with the incoming id )
  $invalidIDMsg = "Die Weiterleitung mit der ID: %redID% konnte nicht gefunden werden.";
  //if no Redirect ID is supplied
  $noIDMsg = "Es wurde keine Weiterleitungs-ID angegeben.";
  //if entry isn't found or array cant be created
  $incorrectJsonMsg = "Die Datenbank verbindung ist beschädigt oder unvolständig.";
  //if no valid id supplied show input field to manually type it in
  $invalidIDinput = true;
  //Additional things when id is supplied but no successfull
  $additionalInvalidID = 
  "<br>".
  "<a href='/'>>Zurück zur Startseite. &lt;</a><br>".
  "<a href='#' onclick='event.preventDefault(),window.history.go(-1)'>>Zurück zur vorigen Seite &lt;</a><br>".
  "<br>Bitte kontrolliere die Weiterleitungs-ID";
  
  //Specify absolute path to dbmanager and credentials file
  require(".../DBManager.php");
  $config = parse_ini_file('...credentials/mysql.ini');


//Main code - no changable values here
  $DBMngr = new DBManager($config["hostname"], $config["db_user_basic"],$config["db_pass_basic"], $config["db_name"]);



    $sql = "DELETE FROM Redirects WHERE validUntil < \" " . date('Y-m-d H:i:s') . " \" ";
    $DBMngr->delete($sql);

  $var = "";
  if (count($_GET) > 0 && isset($_GET["link"])) {
    $var =  $ignoreCase ? strtolower($_GET["link"]) : $_GET["link"];
  }
  //$var = substr($var, 2);
  $error = 0;
  if ($var != "" && $var != " ") {
    //$strJsonFileContents = file_get_contents("../r/json/url.json");
    //$array = json_decode($strJsonFileContents, true);
    $array = $DBMngr->QuerySingleRow("SELECT * FROM Redirects WHERE id=\"%a0\"",$var);
    if($array != null) {
        if (array_key_exists("url", $array)) {
          $url = $array["url"];
          if(array_key_exists("visible", $array)) {
            $visible = $array["visible"];
          } else {
            $visible = false;
          }
        } else {
          $error = 3;
        }
    } else {
      $error = 3;
    }
  } else {
    $error = 1;
  }
  if($error == 0) {
    if($visible) {
      header("Location: ".$url); // Redirect with header
      exit();
    } else {
      echo "<html>\n<body>\n";
      echo "<script>\n";
      echo "location.href =\"".$url."\";\n</script>\n";
      echo "</body>\n</html>";
      exit();
    }
  }
?>
<!--Change here the appereance of the page when its not an valid Redirect ID-->
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <title>Weiterleitungssystem | TimedIn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Weiterleitungssystem für gekürzte URLs und SocialMedia-Links von TimedIn.">
    <link rel="stylesheet" type="text/css" href="/css/master.css">

    <style>
      .center {
        text-align: center;
        margin: auto;
        width: 50%;
        padding: 10px;
      }
    </style>
    <script type="text/javascript">
      function toggleNewTab(self) {
      document.getElementById("submitForm").target = self.checked ? "_blank" : ""
      }
    </script>
  </head>
  <body>
      <h1>TimedIn-Weiterleitungen</h1>
      <div class="content-box">
        <?php 
          if ($error != 1) {
            if($error == 2) {
              echo $incorrectJsonMsg;
            } elseif ($error == 3) {
              //TODO correct escaping of HTMLEntities
              echo htmlspecialchars(str_replace("%redID%", $var, $invalidIDMsg));
            }
            echo $additionalInvalidID;
          } else {
            //echo $noIDMsg;
          }
          if($invalidIDinput) {
            echo "<form id=\"submitForm\" action=\"\"  method=\"GET\">" .
            "<input placeholder =\"Link-Code\" type =\"text\" name=\"link\"><br>" .
            "<label for=\"newtab\">Im neuen Tab anzeigen: </label>".
            "<input type=\"checkbox\" onchange=\"toggleNewTab(this);\"name=\"newtab\"><br>".
            "<input type =\"Submit\">". 
            "</form>";
          }
          
        ?>
        <p>Das Weiterleitungssystem für Kurzlink-Codes in sozialen Netzwerken, Empfehlungen und Informationen.<br><br>
          Aktiviere "im neuen Tab anzeigen" damit dieses Fenster offen bleibt und mehrere Codes eingegeben werden können. <br><br>Einige Codes können nach einer Zeit oder Anzahl von Verwendungen ablaufen.</p>
      </div>
      <?php include '/var/www/timedin.de/files/footer.php'; ?>
  </body>
</html>
