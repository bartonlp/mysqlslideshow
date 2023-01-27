# MySqlSlideShow

Store Slide Show information in a MySql database and show Slide Show in a browser.

## History

This class was designed in 2009 and was updated April 2015 to new HTML5 and CSS3 features. 
It was updated again Jan. 2023.

## Install

Either download the ZIP file or install with Composer.

<ol style="list-style-type: decimal">
<li>unzip the zip file (you have probably already done this if you are reading this)</li>
<li>the directory structure is as follows:
<ul>
<li>examples/ </li>
<li>README.html</li>
<li>README.md</li>
<li>composer.json</li>
<li>gulpfile.js</li>
</ul>
If you loaded this with 'composer' instead of from the ZIP then this stucture is under 'vendor/bartonlp/mysqlslideshow/'.
<li>In either case you may need get *mysql* running and then:

<pre><code>CREATE DATABASE mysqlslideshow;</code></pre>

Create the database table. The file 'mktable.sql', in the 'examples' directory, if sourced within the 'msql' client will make
the table 'mysqlslideshow'. If you want to call your table something else you can edit the 'mktable.sql' file and then either
edit the 'mysqlslideshow.class.php' (NOT RECOMMENDED), 
or use the constructor with the additional optional arguments: 
The first three arguments come from the 'dbclass.conectioninfo.i.php' file. 
Add the forth and fifth arguments for your database name and table name.  

<pre><code>$ss = new MySqlSlideshow($Host, $User, $Password, 'YourDatabaseName', 'YourTableName');</code></pre>

Or edit 'dbclass.connectioninfo.i.php' and add '$Database' and '$Table' and add them to the invocation above instead of the strings. 
You can create the table from inside the 'mysql' client by using the source statement:

<pre><code>source mktable.sql;</code></pre>

or just do it the hard way by typing in the create statement.</li>
<li>add some images to your database table. There are two easy ways to do this: 
<ol style="list-style-type: lower-alpha">
  <li>use the 'addupdateimage.php' program in the 'examples' directory. 
  <li>use the 'addimages.php' program in the 'examples' directory. 
</ol>

<li>try out the examples 'serverside.php' and 'browserside.html' on your own server. Then start writing your own code.  
Have fun. If you don't have Apache running you can use the PHP server. Just enter 

<pre><code>php -S localhost:8080</code></pre>

from the project directory and then in your browser enter 

<pre><code>localhost:8080/serverside.php</code></pre>

or 'browserside.html' to see the sites.

Any questions can be sent to <a href="mailto://barton@bartonphillips.com">barton@bartonphillips.com</a> I will try to answer reasonable questions.</li>
</ol>

## Examples

There are three files in the 'examples' directory:

* serverside.php
* browserside.html
* ie.html
* mysqlslideshow.class.php

'mysqlslideshow.classs.php' is the Ajax target and has the MySqlSlideShow class.

## Conclusion

The class is hosted at https://github.com/bartonlp/mysqlslideshow as well as the 'PHP Classes' http://www.phpclasses.org.

Copyright &copy; 2009-2023 Barton Phillips
http://www.bartonphillips.com
<a href="mailto://barton@bartonphillips.com">barton@bartonphillips.com</a>
OR
<a href="mailto://bartonphillips@gmail.com">bartonphillips@gmail.com</a>

Last Modified Jan. 27, 2023
