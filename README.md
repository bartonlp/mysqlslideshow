# MySqlSlideShow

Store Slide Show information in a MySql database and show Slide Show in a browser.

## Disclamer

I don't use MS-Windows so this code has not been tested on MS-Windows. I have made an attempt to accommodate the dismal performance of IE by adding some code and examples that MAY work on IE. The code has not been tested BECAUSE I don't use MS-Windows.

## History

This class was designed in 2009 and has been updated April 2015 to new HTML5 and CSS3 features.

## Install

Either download the ZIP file or install with Composer.

For the ZIP file:

<ol style="list-style-type: decimal">
<li>unzip the zip file (you have probably already done this if you are reading this)</li>
<li>edit the 'dbclass.connectioninfo.i.php' file for your MySql site and then optionally move it to a location that is not in your Apache server path.</li>
<li>create your MySql database and table. If you have an existing database and you want to use it, then you don't need to create the database. Other wise in the 'mysql' client: 

<code>
CREATE DATABASE mysqlslideshow;
</code>

Create the database table. The file 'mktable.sql' if sourced within the 'msql' client will make the table 'mysqlslideshow'. If you want to call your table something else you can edit the 'mktable.sql' file and then either edit the 'mysqlslideshow.class.php' (NOT RECOMMENDED), or use the constructor with the additional optional arguments: The first three arguments come from the dbclass.conectioninfo.i.php file. Add the forth and fifth arguments for your database name and table name.  

<pre><code>
$ss = new MySqlSlideshow($Host, $User, $Password, 'YourDatabaseName', 'YourTableName');
</code></pre>

Or edit 'dbclass.connectioninfo.i.php' and add '$Database' and '$Table' and add them to the invocation above instead of the strings. You can create the table from inside the 'mysql' client by using the source statement:

<pre><code>
source mktable.sql;
</code></pre>

or just do it the hard way by typing in the create statement.</li>
<li>add some images to your database table. There are two easy ways to do this: 
<ol style="list-style-type: alpha">
    <li>use the 'addupdateimage.php' program. From you web browser (hopefully not IE) enter the following in the location area:

<pre><code>
http://yoursite/addupdateimage.php?image=imageFileName&subject=subject+text&description=more+text+here
</code></pre>

If you want the image data saved in the table instead of the path to the image add the '&type=image' to the line above. You can use a relative path or absolute path. Relative paths will be turned into absolute for the database. This will add one image.</li>
    
    <li>use the 'addimages.php' program. From your web browser (hopefully not IE) enter the following in the location area:

<pre><code>
http://yoursite/addimages.php?path=searchInfo&pattern=pattern
</code></pre>

Again if you want image data rather than a link in the database table add the '&type=image' to the end.
The 'path=searchInfo' is a path plus the optional conditional pattern like: '../images/\*.gif'. If just the path and a '\*' then all the files in that path will be looked at. NOTE: '../images' will not work but '../images/\*' will!

The 'pattern=pattern' is optional. If you want to further qualify the files you can use a PHP/perl style regular expression pattern. For example if 

<pre><code>
?path=../images/*&pattern=^big.*?(?:ball)|(?:flag)\.jpg
</code></pre>

then all of the file in the '../images' directory would be gathered and the pattern would then be applied to each. Say you have files "bigredflag.jpg", "bigblueball.jpg" along with many others, the pattern would put only those two into the selection list.  The program display your selected file with a check-box and input-boxes for a subject and description. Make your selections and click submit.</li>
</ol>

<li>try out the examples 'serverside.php' and 'browserside.html' on your own server. Then start writing your own code.  Have fun. If you don't have Apache running you can use the PHP server. Just enter 

<pre><code>
php -S localhost:8080
</code></pre>

from the project directory and then in your browser enter 

<pre><code>
localhost:8080/serverside.php
</code></pre>

or 'browserside.html' to see the sites.

Any questions can be sent to barton@bartonphillips.com I will try to answer reasonable questions.</li>
</ol>

## Examples

There are three example files:

* serverside.php
* browserside.html
* ie.html

'ie.html' has not been tested so if it miss behaves don't be surprised. You can let me know at barton@bartonphillips.com if you have a solution. 

## Conclusion

The class is hosted at https://github.com/bartonlp/mysqlslideshow as well as the 'PHP Classes' http://www.phpclasses.org.

Copyright &copy; 2009-2015 Barton Phillips
http://www.bartonphillips.com
barton@bartonphillips.com
OR
bartonphillips@gmail.com
