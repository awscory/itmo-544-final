<?php
session_start();
?>

<html>
<body>

<form enctype="multipart/form-data" action="submit.php" method="post">
User Name: <input type="text" name="username" value="sukanya"><br>
E-mail: <input type="text" name="email" value="****@***.iit.edu"><br>
Phone : <input type="text" name="phone" value="312-000-0000"><br>
<input type="hidden" name="MAX_FILE_SIZE" value="3000000"><br>
Your File : <input type="file" name="userfile">
<input type="submit" name="Upload">
</form>
<form enctype="multipart/form-data" action="subscribe.php" method="post">
Would you like to subscribe to S3-upload message<input type="submit" name="Subscribe">
</form>
</body>
</html> 



    
