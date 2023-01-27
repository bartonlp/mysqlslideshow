<?php

// This class facilitates a slide show where the links or image data is stored in a mysql table. In addition to the link/data the
// table comments, description,  date, etc.
//
// The class provides methods for getting, displaying, editing, and adding image items

// This class extends a Database class that handles the MySql connection and provides at a minimum:
// Database::__construct; Conects and opens database.
// Database::query; Dose a mysql_query.
// Database::getDb; return the resource from mysql_connect().

// The database should have the following fields:
/*
CREATE TABLE `mysqlslideshow` (
  `id` int(11) NOT NULL auto_increment,
  `imageinfo` varchar(255) NOT NULL,
  `subject` text default NULL,
  `description` text,
  `data` text default NULL,
  `created` datetime NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

// include a database class file.
// The 'dbMysqli.class.php' MUST be in the same place as this file! The other database
// files: 'dbMysqli.class.php', 'dbAbstract.class.php', 'SqlException.cass.php',
// 'Error.class.php' and 'helper-functions.php' MUST all be in the same place!

class MySqlSlideshow {
  private $table;
  private $numRows = -1; // uninited
  protected $db = null;
  
  static public $lastQuery = null;
  static public $lastNonSelectResult = null;
  
  // Constructor take three manditory and two optional arguments

  public function __construct($host, $user, $password, $database="mysqlslideshow",
                              $table="mysqlslideshow") {

    $this->host = $host;
    $this->user = $user;
    $this->password = $password;
    $this->database = $database;
    $this->table = $table;
    $this->db = $this->opendb();
  }

  //********************
  // These are the primary methods used for a slide show
  //********************
  
  // Get list if ids given an optional 'where' argument
  // returns an array of ids
  // false if error.
  
  public function getImageIds($where="") {
   if(!empty($where)) {
     $where = " where $where";
   }
   
   $this->query("select id from $this->table$where");
   while($id = $this->fetchrow('num')[0]) { $ids[] = $id; }
   return $ids;
  }

  public function getImageData($where='') {
   if(!empty($where)) {
      //echo "where=$where";
      $where = " where $where";
    }
    $this->query("select data from $this->table$where");

    while($data = $this->fetchrow('assoc')) { $datas[] = $date; }
    return $datas;
  }
    
  // Get the image given an id
  // Arguments:
  // $id: the id of the image in the database table
  // $type is 'data' or 'link'. 

  // returns an assoc array:
  // [data] has string that can be used in a "<img src=" tag, or if $type is not "base64" raw image data
  // [mime] has the mime type of the image
  // [subject] has a string with the subject text
  // [desc] has a string with the description text
  
  public function getImage($id, $type) {
    $this->query("select data, imageinfo from $this->table where id='$id'");
    [$data, $imageinfo] = $this->fetchrow('num');

    $mime = (json_decode($imageinfo, true))['mime'];

    switch($type) {
      case "data":
        return ["data"=>$data, "mime"=>$mime];
      case "link":
        return ["data"=>("data:$mime;base64," . (base64_encode(file_get_contents($data)))), "mime=>$mime"];
      default:
        throw(new Exception("Bad Type $type"));
    }
  }

  // Get the information (subject, and description)
  // $id is the entry to get (like getImage())
  // returns an assoc array with the two fields
  
  public function getInfo($id) {
    $this->query("select subject, description from $this->table where id='$id'");
    return $this->fetchrow('assoc');
  }

  //********************
  // The next two methods are used to add and modify the database table. See addimages.php and
  // addupdateimage.php
  //********************
  
  // Add an image to the databasse table. This does an insert.
  // Arguments:
  // $imagefile: either the path on the server of the image file, or if $type='data' the actual image data.
  // $subject: defaults to blank.
  // $description: defaults to blan.
  // returns true if OK else an error string.

  public function addImage($imagefile, $subject="", $desc="") {
    if(!file_exists($imagefile)) {
      return "File does not exist: $imagefile";
    }

    //$imagefile = realpath($imagefile); // make it into the absolute path
    
    $imageinfo = $this->escape(json_encode(getimagesize($imagefile)));
        
    $query = "insert into $this->table (imageinfo, data";
    $qdata = ") value('$imageinfo', '$imagefile'";
    
    if($subject) {
      $query .= ", subject";
      $qdata .= ", '$subject'";
    }
    if($desc) {
      $query .= ", description";
      $qdata .= ", '$desc'";
    }
    $query .= ", created$qdata, now())";

    if(!$this->query($query)) {
      return false;
    }
    
    return true;
  }

  // update an image whose id=$id. Set the subject and desc
  
  public function updateImageInfo($id, $subject, $desc) {
    $ids = $this->getImageIds();  // get an array of all the ids 

    // Is this id in the field?
    if(array_search($id, $ids) === false) {
      return "Id=$id is not in the table";
    }
    
    $s = "";
    if($subject) {
      $s = "subject='$subject'";
    }
    $d = $s ? ", " : "";
    
    if($desc) {
      $d .= "description='$desc'";
    }
    $query = "update $this->table set $s$d where id='$id'";

    $n = $this->query($query);

    if(!$n) {
      return "No changes made by query: $query";
    } else {
      return true;
    }
  }

  // Delete an image

  public function deleteImage($id) {
    if(!$this->query("delete from $this->table where id=$id")) {
      return "Id=$id is not in the table";
    }
    return true;
  }
  
  //********************
  // A helper method to display a <table> with all of the items in the database table
  //********************
  
  // Display all of the information in the table
  
  public function displayAllImageInfo() {
    $this->query("select id, subject, description, data, created from $this->table");

    // Get the headers first
    
    $row = $this->fetchrow('assoc');
    
    $body = "<tr>\n";
    foreach($row as $key=>$value) {
      $body .= "<th>$key</th>\n";
    }
    $body .= "</tr>\n</thead>\n<tbody>\n$line</tr>\n";
    
    do {
      $body .= "<tr>\n";
      foreach($row as $value) {
        $item = $value ?? "&nbsp;";
        $body .= "<td>$item</td>\n";
      }
      $body .= "</tr>\n";
    } while($row = $this->fetchrow('assoc'));
    
    return <<<EOF
<table id="displayAllImageInfo">
<thead>
<tr>
$body
</tbody>
</table>
EOF;
  }

  protected function opendb() {
    // Only do one open
    
    if($this->db) {
      return $this->db;
    }
    $db = new mysqli($this->host, $this->user, $this->password);
    
    if($db->connect_errno) {
      $this->errno = $db->connect_errno;
      $this->error = $db->connect_error;
      throw new Exception(__METHOD__ . ": Can't connect to database", $this);
    }
    
    $this->db = $db; // set this right away so if we get an error below $this->db is valid

    if(!@$db->select_db($this->database)) {
      throw new Exception(__METHOD__ . " Can't select database", $this);
    }
    return $db;
  }

  /**
   * query()
   * Query database table
   * @param string $query SQL statement.
   * @return mixed result-set for select etc, true/false for insert etc.
   * On error calls SqlError() and exits.
   */

  public function query($query) {
    $db = $this->db;

    self::$lastQuery = $query; // for debugging
    
    $result = $db->query($query);

    if($result === false) {
      throw(new Exception($query, $this));
    }
    
    if($result === true) { // did not return a result object 
      $numrows = $db->affected_rows;
      self::$lastNonSelectResult = $result;
    } else {
      // NOTE: we don't change result for inserts etc. only for selects etc.
      $this->result = $result;
      $numrows = $result->num_rows;
    }

    return $numrows;
  }

  /**
   * fetchrow()
   * @param resource identifier returned from query.
   * @param string, type of fetch: assoc==associative array, num==numerical array, or both
   * @return array, either assoc or numeric, or both
   * NOTE: if $result is a string then it is the type and we use $this->result for result.
   */
  
  public function fetchrow($result=null, $type="both") {
    if(is_string($result)) {
      $type = $result;
      $result = $this->result;
    } elseif(!$result) {
      $result = $this->result;
    } 

    if(!$result) {
      throw new Exception(__METHOD__ . ": result is null", $this);
    }

    switch($type) {
      case "assoc": // associative array
        $row = $result->fetch_assoc();
        break;
      case "num":  // numerical array
        $row = $result->fetch_row();
        break;
      case "both":
      default:
        $row = $result->fetch_array();
        break;
    }
    return $row;
  }

  // real_escape_string
  
  public function escape($string) {
    return $this->db->real_escape_string($string);
  }
}

// Here is the mysqlslideshow GET functions. NOTE the are all prefixed with 'class_' so they do not
// interfeer with other GET functions in the calling programs.
// These functions are called from ie.html and browserside.html via $.get("mysqlslideshow.class.php", { passed values }, ...

require("dbclass.connectinfo.i.php"); // has $Host, $User, $Password, $Database

$ss = new MySqlSlideshow($Host, $User, $Password, $Database);

//********************
// This is an Ajax call.
// Get the image given an id.
// The arguments are:
// image=id
// israw=true/false, if true then the raw image data is returned. If not present or false
//   then returns the 'link' data from getImage().
// addinfo=1 optional argument, if present and if not specified as raw then the "<::subject::>text<::description::>text" is
// appended onto the base64 image packet. If type=raw then addinfo is ignored!

if($_GET['class_image']) {
  extract($_GET);

  if($israw) {
    $ar = $ss->getImage($class_image, 'data', ""); // the second arg defaults to base64 so here we want to unset it.
    $data = $ar['data'];
    echo $data;
    exit();
  }
  
  $ar = $ss->getImage($class_image, 'link'); // This returns the base64 data
  $data = $ar['data'];  
  
  if($addinfo) {
    $ar = $ss->getInfo($class_image);
    $data .= "<::subject::>{$ar['subject']}<::description::>{$ar['description']}";
  }

  echo $data;
  exit();
}

// get all of the images. This is used by ie.html

if($_GET['class_images']) {
  extract($_GET);

  foreach($class_images as $image) {
    $ar = $ss->getImage($image, $type, "");
    $data = $ar['data'];
    
    if($addinfo) {
      $ar = $ss->getInfo($image);
      $data .= "<::subject::>{$ar['subject']}<::description::>{$ar['description']}";
    }
    $ret[] = $data;
  }
  $ret = json_encode($ret);

  echo $ret;
}

//********************
// This is an Ajax call.
// Gets the "subject" and "description"
// The arguments are:
// info=id
// returns an echoed string like this:
// "<::subject::>subject info<::description::>>description info"
// this string can be parsed to get the subject and description.

if($id = $_GET['class_info']) {
  $data = $ss->getInfo($id);
  echo "<::subject::>{$data['subject']}<::description::>{$data['description']}";
  exit();
}

//********************
// This is an Ajax call.
// Get the list of ids
// ids=1
// where=where arguments. optional
// returns the list of ids

if($_GET['class_ids']) {
  $ids = implode(",", $ss->getImageIds($_GET['where']));

  $data = implode(",", $ss->getImageData($_GET['where']));

  $ret = [$ids, $data];

  $ret = json_encode($ret);
  echo $ret;
  exit();
}

//********************
// This is an Ajax call.
// Display a <table> containing all of the rows of the database table
// table=1

if($_GET['class_table']) {
  $ret = $ss->displayAllImageInfo();
  echo $ret;
  exit();
}

// Helper function for var_dump()

if(!function_exists('vardump')) {  
  function vardump($msg=null) {
    $args = func_get_args();
    if(is_string($msg)) {
      array_shift($args); // remove msg from the args array
      $msg= "<b>$msg</b>\n";
    } else unset($msg);
  
    echo "<pre>$msg" . print_r($args, true) . "</pre>";
  }
}
