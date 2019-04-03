<?php

// fix for: PHP Fatal error:  Call-time pass-by-reference has been removed in ..../index.php on line 126
function myexec($command, &$output_lines) {
  exec($command, $output_lines);
}

$sstr = "";
if (isset($_GET['sstr'])) {
  $sstr = $_GET['sstr'];

  $pattern = "/[^;: \.\-_,<>0-9a-zA-Z@\{\[\]\}#]/";
  $sstr = preg_replace($pattern,".",$sstr); 

  if ($sstr == "")
    $sstr = ".";
}

$files = glob("*.log");
$file_count = count($files);
$last_file_name = $files[count($files)-1];

$file = "";
if (isset($_GET['file'])) {
  $file = $_GET['file'];
}
else {
  $file = $last_file_name;
}

$refresh = false;
if ($file == $last_file_name && $sstr == "") {
  $refresh = true;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Irc logs</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"> 

    <?php

    if ($refresh) {
      echo '
<meta http-equiv="refresh" content="30;?">
<script language="javascript" type="text/javascript">
window.onload=toBottom;

var count=30;
var counter=setInterval(timer, 1000);

function toBottom() {
  window.scrollTo(0, document.body.scrollHeight);
}

function timer() {
  count=count-1;
  if (count <= 0) {
     clearInterval(counter);
     return;
  }

 document.getElementById("timer").innerHTML=count;
}
</script>
';
    }
    ?>

  </head>
  <body>

    <table cellpadding=0 cellspacing=0 style="width: 100%">
      <tr>
        <td>
          <?php
          $i = $file_count-2;
          echo '<a href="?file='.$last_file_name.'">Today</a>';
          while($i >= 0 && ($file_count-$i) < 8) {
            echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
            echo '<a href="?file='.$files[$i].'">'.$files[$i].'</a>';
            $i -= 1;
          }
          ?>
        </td>
        <td style="text-align: right">
          <form action="?">
            <input type="text" name="sstr" value="<?php echo $sstr; ?>"> <input type="submit" value="Search">
          </form>
        </td>
      </tr></table>
    <?php
    echo '<hr />';

    if ($sstr != "") {
      $search_words = explode(" ", $sstr);
      $command = "grep -il ";
      $first_ready = false;
      foreach ($search_words as $word) {
        if (strlen($word) == 0)
          continue;

        if($first_ready) {
          $command .= " | xargs grep -il ";
        }    

        $command .= $word;
        if(!$first_ready)
          $command .= " *.log";

        $first_ready = true;
      }
      
      $command .= " | sort | uniq";

      $output_lines = array();
      myexec($command, $output_lines) ;

      echo "Search results for: ".$sstr;
      //echo "<br />".$command;
      echo '<br /><br />';
      foreach ($output_lines as $line) {  
        echo '<a href="?file='.$line.'">'.$line.'</a>';
        echo "<br />";
      }
      
      if (count($output_lines) == 0)
        echo 'no matches';

    }
    else {
      if (in_array($file, $files)) {
        echo $file.":";
        echo '<pre>';
        echo file_get_contents($file);
        echo '</pre>';
      }
      else {
        echo 'nice try :)';
      }
      
      if ($refresh) {
        echo 'Refreshing in <span id="timer">30</span> ...';
      }
      echo '<input type="button" value="Top" onclick="window.scrollTo(0, 0);">';
    } // if ($sstr != "") .. else .. end

    ?>

  </body>
</html>
