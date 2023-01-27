<?php
// Add, Delete or Update an Image in the Database table

require_once("dbclass.connectinfo.i.php"); // has $Host, $User, $Password

// This file has the MySqlSlideshow class and the GET functions

require_once("mysqlslideshow.class.php"); // $ss is instantiated here!

session_start();

//********************
// Add image
// image: the image path/filename
// subject: optional subject
// description: optional description
// Refresh with $addMessage

if($_POST['submit'] == 'add') {
  // Add a new image to the table.
  $image = $_POST['image'];
  $subject = $_POST['subject'];
  $desc = $_POST['description'];
  
  if(($ret = $ss->addImage($image, $subject, $desc)) === true) {
    $addMessage = "<h3>Image $image Added</h3>";
  } else {
    $addMessage = "<h3 style='color: red'>Error: $ret</h3>";
  }
  $_SESSION['add'] = $addMessage;
  $_SESSION['delete'] = '';
  $_SESSION['update'] = '';
  header("Refresh:0; url=addupdateimage.php#add");
}

// Delete image
// id: the id to delete
// Refresh with $delMessage set.

if($_POST['submit'] == 'delete') {
  $id = $_POST['id'];
  if($ss->deleteImage($id) === true) {
    $delMessage = "<div id='del'><h3>Image with ID=$id Has Been Deleted</h3></div>";
  } else {
    $delMessage = "<h3>Image with ID=$id Not Found</h3>";
  }
  $_SESSION['delete'] = $delMessage;
  $_SESSION['add'] = '';
  $_SESSION['update'] = '';
  header("Refresh:0; url=addupdateimage.php#delete");
}

// Update an existing images subject and description
// update: is the id of the image
// subject: optional subject
// description: optional description
// Refresh with $updateMessage set.

if($_POST['submit'] == "update") {
  $id = $_POST['id'];
  $subject = $_POST['subject'];
  $desc = $_POST['description'];
  
  if($ss->updateImageInfo($id, $subject, $desc) === true) {
    $updateMessage = "<h3>Image with id=$id has been updated with<br>subject=$subject,<br>and description=$desc<h3>";
  } else {
    $updateMessage = "<h3 style='color: red'>$ret</h3>";
  }
  $_SESSION['update'] = $updateMessage;
  $_SESSION['delete'] = '';
  $_SESSION['add'] = '';

  header("Refresh:0; url=addupdateimage.php#update");
}

$addMessage = $_SESSION['add'];
$delMessage = $_SESSION['delete'];
$updateMessage = $_SESSION['update'];

$current = "No Images<br>";

if($ss->query("select id, subject, description, data from mysqlslideshow")) {
  $current = <<<EOF
<table border="1">
<thead>
<tr><th>ID</th><th>IMAGE</th><th>Subject</th><th>Description</th></tr>
</thead>
<tbody>
EOF;
  while([$id, $subject, $desc, $data] = $ss->fetchrow('num')) {
    $current .= "<tr><td>$id</td><td>$data</td><td>$subject</td><td>$desc</td></tr>";
  }
  $current .= "</tbody>\n</table>";
}

// End of the add and update logic
//********************

echo <<<EOF
<!DOCTYPE html>
<html>
<body>
<h1>Add Image</h1>
<div><h3>Current Images</h3>$current</div>
<form method='post'>
  <p>Add Image:</p>
  <input type='text' name='image'> image<br>
  <input type='text' name='subject'> subject<br>
  <input type='text' name='description'> description<br>
  <button type='submit' name='submit' value="add">Add Image</button>
</form>
<div id="add">$addMessage</div>

<h1>Delete an Image Given Image ID</h1>
<form method='post'>
  <p>Delete Image:</p>
  <input type='text' name='id'> ID To Delete<br>
  <button type='submit' name='submit' value="delete">Delete Image</button>
</form>
<div id="delete">$delMessage</div>
<hr>

<h1>Update Image Given Image ID</h1>
<form method='post'>
  <p>Update Image:</p>
  <input type='text' name='id'> id<br>
  <input type='text' name='subject'> subject<br>
  <input type='text' name='description'> description<br>
  <button type='submit' name='submit' value='update'>Update Image</button>
</form>
<div id="update">$updateMessage</div>
</body>
</html>
EOF;
