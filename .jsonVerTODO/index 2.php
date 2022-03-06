<?php
  //PHP-Redirect Manager 
  //Version: 1.1
  //19.12.2020
  //
  //Code and all it parts under copyright by TimedIn 
  //Website: timedin.2ix.de
  //All rights reseved
  //
  //CONTACT OTHER BEFORE COMERCIAL OR OTHER USE
  //I would appreciate it if you credit me anywhere ;) 
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
  
  //Beginning of main Code

  require("DBManager.php");
  $DBMngr = new DBManager();

  $var = "";
  if (count($_GET) > 0 && isset($_GET["link"])) {
    $var =  $ignoreCase ? strtolower($_GET["link"]) : $_GET["link"];
  }
  //$var = substr($var, 2);
  $error = 0;
  if ($var != "" && $var != " ") {
    //$strJsonFileContents = file_get_contents("../r/json/url.json");
    //$array = json_decode($strJsonFileContents, true);
    $array = $DBMngr->QuerySingleRow("SELECT * FROM redirects WHERE id=\"".$var."\";");
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
    <title>TimedIn-Weiterleitungssystem</title>
    <meta charset="utf-8">
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
      <div class="center">
        <?php 
          if ($error != 1) {
            if($error == 2) {
              echo $incorrectJsonMsg;
            } elseif ($error == 3) {
              echo str_replace("%redID%", $var, $invalidIDMsg);
            }
            echo $additionalInvalidID;
          } else {
            echo $noIDMsg;
          }
          if($invalidIDinput) {
            echo "<form id=\"submitForm\" action=\"\" target=\"_blank\"  method=\"GET\">" .
            "<input placeholder =\"Redirect-ID\" type =\"text\" name=\"link\">" .
            "<label>Im Neuen Tab anzeigen: </label>".
            "<input type=\"checkbox\" checked onchange=\"toggleNewTab(this);\"name=\"newtab\">".
            "<input type =\"Submit\">". 
            "</form>";
          }
        ?>
        <p>Weiterleitungssystem Version: 1.1 by TimedIn <a href="https://www.timedin.de">TimedIn.de</a></p>
      </div>
  </body>
</html>
