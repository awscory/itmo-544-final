<?php
session_start();
require_once('/var/www/html/snssetup.php');   
//going to test image magick
?>
<!DOCTYPE html>
<meta charset="UTF-8"> 
<html>
<body>
<form enctype="multipart/form-data" action="submit.php" method="post">
User Name: <input type="text" name="username" value="sukanya"><br>
E-mail: <input type="text" name="email" value="****@***.iit.edu"><br>
Phone : <input type="text" id="phone" name="phone" value="312-000-0000"><br>
<input type="hidden" name="MAX_FILE_SIZE" value="3000000"><br>
Your File : <input type="file" name="userfile">
<input type="submit" name="submit" value="Upload">
</form><br><br>
<form enctype="multipart/form-data" action="subscribe.php" method="post">
Phone to Subscribe : <input type="text" name="phoneNo"/><br>
Would you like to subscribe to receive message on Upload ?  <input type="submit" value="Subscribe">
</form>
<form enctype="multipart/form-data" action="introspection.php" method="post">
Would you like to take a Database Backup  <input type="submit" value="BackUp">
</form>
<form>
<a href="gallery.php?raw=true"> Gallery</a>
<input type="hidden" name="gallery">
</form>
</body>
</html> 




    
