<?php
session_start();
require 'vendor/autoload.php';
echo "Submit.php page";
if(!empty($_POST)){
echo $_POST['email'];
echo $_POST['phone'];
}
else {
echo "Post data is empty";
}
print_r ($_POST);
echo $_FILES['userfile'];
print_r ($_FILES);

if (isset ($_FILES['userfile'])){
$uploaddir = '/tmp/';
$uploadfile = $uploaddir. basename($_FILES['userfile']['name']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    print "File is valid, and was successfully uploaded.\n";
} else {
    print "Possible file upload attack!\n";
}
}
else
{

print "file not valid ";
}
print 'Here is some more debugging info:';
print_r($_FILES);

$s3=new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$bucket = uniqid("S3-Sukanya-", false);
print "Creating bucket named {$bucket}\n";
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);


$result = $s3->waitUntil('BucketExists',array('Bucket' => $bucket));

echo "bucket creation done";

$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => "uploads".$uploadfile,
'ContentType' => $_FILES['userfile']['type'],
    'Body'   => fopen($uploadfile, 'r+')
]);  
$url = $result['ObjectURL'];
echo $url;
echo "s3 file uploaded";

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);

echo "No error as of now";

print_r($result);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    print "============\n". $endpoint . "================";
echo "endpoint is available";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($link));

print_r($link);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
echo "connection success";
$sql_insert = "INSERT INTO items (UName,Email,Phone,RawS3Url,FinalS3Url,JpgFileName,status,Issubscribed) VALUES (?,?,?,?,?,?,?,?)";
if (!($stmt = $link->prepare($sql_insert))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
else
{
echo "statement was success";
}

$uname = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$s3rawurl = $url; //  $result['ObjectURL']; from above
$s3finishedurl = "none";
$filename = basename($_FILES['userfile']['name']);
$status =0;
$issubscribed=0;
$stmt->bind_param("ssssssii",$uname,$email,$phone,$s3rawurl,$s3finishedurl,$filename,$status,$issubscribed);
if (!$stmt->execute()) {
    print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);

$stmt->close();
$sql1 = "SELECT ID, JpgFileName, RawS3URL FROM items ";
$result = mysqli_query($link, $sql1);
$imgLocations = array();
print "Result set order...\n";

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        //this will append the path of images to an array
        $imgLocations[$row["JpgFileName"]] = $row["RawS3URL"];
        echo "id: " . $row["ID"]."- RawS3URL" . $row["RawS3URL"]. "<br>";
    }
} 
else {
    echo "----0 results";
}

$link->close();
/*********************************************************************/

?>
<!DOCTYPE html>
<html><head>
<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="https://raw.githubusercontent.com/dimsemenov/Magnific-Popup/master/dist/magnific-popup.css">

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

<div class="gallery">
  <?php foreach ($imgLocations as $key => $value) {
  ?>
  <a href="<?php echo $value ?>"> <img src="<?php echo $value ?>"></img><?php echo $key ?></a>
  <?php }?>
</div>

</body>

<!-- jQuery 1.7.2+ or Zepto.js 1.0+ -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<!-- Magnific Popup core JS file -->
<script src="https://raw.githubusercontent.com/dimsemenov/Magnific-Popup/master/dist/jquery.magnific-popup.js"></script>

<!-- put this in a separate js file on github and link -->
<script type="javascript">
$(document).ready(function() {
	$('.gallery').magnificPopup({
		type: 'image',
		delegate: 'a',
		gallery: {
			enabled: true,
			
		},
		
	});
});
 
</script>
</html>
