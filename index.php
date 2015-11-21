<?php
session_start();
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
<input type="submit" value="Upload">
</form>
<form enctype="multipart/form-data" action="subscribe.php" method="post">
<input type="hidden" id="phone_hidden" name="phone_hidden"/>
Would you like to subscribe to S3-upload message<input type="submit" value="Subscribe">
</form>
<?php
if (isset($_GET["phone"])){
$_SESSION["phone"]=$_GET["phone"]; 
print_r($_SESSION);
}
?>
</body>
</html> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://raw.githubusercontent.com/sukanyaN/itmo-544-final/master/js/copytext.js"></script>



    
