<?php
session_start();
require 'vendor/autoload.php';
#create RDSclient using the us-west-2 
echo $_GET['raw'];
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

#fetch the DB instance read replcia to read content
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-SN-dbreplica']);


#get the end point to the instance
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB");

?>

<!DOCTYPE html>
<html><head>
<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="https://rawgit.com/sukanyaN/itmo-544-final/master/css/magnific-popup.css">

<!-- Bootstrap Core CSS -->
    <link href="https://rawgit.com/sukanyaN/itmo-544-final/master/css/bootstrap.min.css" rel="stylesheet">


<style>
.magnific-gallery
{
	list-style: none;
}

.magnific-gallery li
{
	float: left;
	height: 100px;
}

.magnific-gallery img
{
	height: 100%;
}

    </style>
</head>
<body>
<div class="container-fluid">
<div class="well">
<?php 
if (!$link)
{
die("connection failed". mysqli_connect_error());
}
else
{
if(isset($_SESSION['email']) && $_GET['raw'] != 'true'){
$useremail = $_SESSION['email'];
echo "your email id is ";
echo $_SESSION['email'];
$sqlstat= "SELECT ID, JpgFileName, RawS3URL,FinalS3Url FROM items WHERE Email='$useremail'";
unset($_SESSION['email']);
}
else
{
echo "enter email id to view the Finished flip image";
$sqlstat= "SELECT ID, JpgFileName, RawS3URL FROM items";
}
$result = mysqli_query($link, $sqlstat);

$imgLocations = array();
$imgLocations1 = array();
print "Result set order...\n";

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        //this will append the path of images to an array
        $imgLocations[] = $row["RawS3URL"];
	        $imgLocations1[] = $row["FinalS3Url"];
//        echo "id: " . $row["ID"]."- RawS3URL" . $row["RawS3URL"]. "<br>";
    }
} 
else {
    echo "----0 results";
}

$link->close();
}
?>
<ul class="magnific-gallery">
  <?php 
$i = 0;
while($i < sizeof($imgLocations)) {
  ?>
  <li>	
  <a href="<?php echo $imgLocations[$i] ?>"> <img class="img-responsive thumbnail" src="<?php echo $imgLocations[$i] ?>"></a>
  <?php
  	 if($_GET['raw'] != 'true') {
  	 ?>
  	<a href="<?php echo $imgLocations1[$i] ?>"> <img class="img-responsive thumbnail" src="<?php echo $imgLocations1[$i] ?>"></a>
  <?php } ?>
  </li>
  <?php $i++;
	}?>
</ul>
</div>
<div class="well">
<a href="index.php">Main Page </a>
</div>
</div>
</body>

<!-- jQuery 1.7.2+ or Zepto.js 1.0+ -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<!-- Magnific Popup core JS file -->
<script src="https://rawgit.com/sukanyaN/itmo-544-final/master/js/jquery.magnific-popup.js"></script>

<!-- js file on github and link -->
<script src="https://rawgit.com/sukanyaN/itmo-544-final/master/js/jqgallery.js"></script>


<!-- Bootstrap Core JavaScript -->
<script src="https://rawgit.com/sukanyaN/itmo-544-final/master/js/bootstrap.min.js"></script>
</html>
