<?php
// Add selected images from a directory
// This file has the MySqlSlideshow class and the mysqlslideshow GET functions.

require_once("mysqlslideshow.class.php"); // instantiates $ss

// Construct the slideshow class:
// There is a 4th argument for the database name if not "mysqlslideshow" and a
// 5th argument for the table name if not "mysqlslideshow"

$self = $_SERVER["PHP_SELF"];

// Add Image

if($_POST["box"]) {
  extract($_POST);

  $adding = '';
  
  for($i=0; $i < $count; ++$i) {
    $image = $box[$i];
    if(empty($image)) continue;
    
    $tsubject = $subject[$i];
    $tdesc = $desc[$i];

    error_log("subject: $tsubject, desc: $tdesc");

    if(($ret = $ss->addImage($image, $tsubject, $tdesc)) === true) {
      $adding .= "<p>Image added: $image, subject=$tsubject, description=$tdesc</p>\n";
    } else {
      $adding .= "<p style='color: red'>$ret</p>\n";
    }
  }

  echo <<<EOF
<!DOCTYPE html>
<html>
<body>
<h1>Added Images</h1>
$adding
</body>
</html>
EOF;
  exit();
}

// Main Page

if($path = $_POST['path']) {
  $pattern = $_POST['pattern'];

  if(strpos($path, "/", -1)) {
    $path = "$path$pattern";
  } else {
    $path = "$path/$pattern";
  }

  $images = glob($path); // get all of the files from the directory

  $body = <<<EOF
<h1>No Files Matched</h1>
EOF;
      
  if(count($images)) {
    $body = <<<EOF
<h1>Select Images</h1>
<form method="post">
EOF;
    
    for($i=0; $i < count($images); ++$i) {
      $image = $images[$i];
      $body .= <<<EOF
<input type="checkbox" name="box[$i]" value="$image"/>$image<br>
<input type="text" name="subject[$i]" /> Subject<br>
<input type="text" name="desc[$i]" /> Description<br>
<br>
EOF;
    }

    $body .= <<<EOF
<input type="hidden" name="count" value="$i" />
<button>Submit</button>
</form>
</body>
</html>
EOF;
  }
}

if(!$_POST) {
  $body =<<<EOF
<h1>Enter Image Location</h1>
<form action="addimages.php" method="post">
<table>
<tr><th>Path to images</th><td><input type="text" name="path"></td></tr>
<tr><th>Select a pattern to match against</th><td><input type="text" name="pattern"></td></tr>
</table>
<button>Do It</button>
</form>
EOF;
}

// Render

echo <<<EOF
<!DOCTYPE html>
<html>
<body>
$body
</body>
</html>
EOF;


