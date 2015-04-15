<?php
// Add selected images from a directory

// This file should not be in the Apache path for security reasons   
require_once("dbclass.connectinfo.i.php"); // has $Host, $User, $Password

// This file has the MySqlSlideshow class

require_once("mysqlslideshow.class.php");

// Construct the slideshow class:
// There is a 4th argument for the database name if not "mysqlslideshow" and a
// 5th argument for the table name if not "mysqlslideshow"

$ss = new MySqlSlideshow($Host, $User, $Password, $Database); // use values from dbclass.connectinfo.i.php
$self = $_SERVER[PHP_SELF];

// Add Image

if($_POST) {
  extract($_POST);

  $adding = '';
  
  for($i=0; $i < $count; ++$i) {
    $image = eval("return \$box$i;");
    if($image) {
      $subject = eval("return \$subject$i;");
      $desc = eval("return \$desc$i;");
      if(($ret = $ss->addImage($image, $subject, $desc, $type)) === true) {
        $adding .= "<p>Image added: $image, subject=$subject, description=$desc</p>\n";
      } else {
        $adding .= "<p style='color: red'>$ret</p>\n";
      }
    }
  }

  echo <<<EOF
<!DOCTYPE html>
<html>
<body>
<h1>Adding Images</h1>
$adding
</body>
</html>
EOF;
  exit();
}

// Main Page

if($path = $_GET['path']) {
  $type = $_GET['type'] ? $_GET['type'] : 'link';
  
  $ar = glob($path); // get all of the files from the directory
  $images = Array();
    
  $pattern = $_GET['pattern'];

  if(!empty($pattern)) {
      foreach($ar as $file) {
      if(preg_match("/$pattern/", $file)) {
          $images[] = $file;
      }
    }
  } else {
    $images = $ar;
  }

  $body = <<<EOF
<h1>No Files Matched</h1>
EOF;
      
  if(count($images)) {
    $body = <<<EOF
<h1>Select Images</h1>
<form action="$self" method="post">
EOF;
    
    for($i=0; $i < count($images); ++$i) {
      $image = $images[$i];
      $body .= <<<EOF
<input type="checkbox" name="box$i" value="$image"/>$image<br>
<input type="text" name="subject$i" /> Subject<br>
<input type="text" name="desc$i" /> Description<br>
<br>
EOF;
    }

    $body .= <<<EOF
<input type="hidden" name="count" value="$i" />
<input type="hidden" name="type" value="$type" />
<input type="submit" value="Submit"/>
</form>
</body>
</html>
EOF;
  }
} else {
  $body = <<<EOF
<h1>NO Parameters Passed</h1>
<p>Required Parameter are:</p>
<ul>
<li>path: path to the directory with images</li>
<li>type: </li>
<li>pattern: Regular expression to filter the file in the 'path' by</li>
</ul>
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


