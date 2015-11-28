<?php
session_start();
require 'vendor/autoload.php';
echo "Submit.php page";
if(!empty($_POST['email'])){
echo $_POST['email'];
$_SESSION['email'] = $_POST['email'];
echo "file name here";
echo $_POST['userfile'];
}
else {
echo "Post data is empty";
}
if (isset ($_FILES['userfile'])){

$uploaddir = '/tmp/';
$uploadfile = $uploaddir. basename($_FILES['userfile']['name']);
$filename = $_FILES['userfile']['name'];
echo $filename;
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    print "File is valid, and was successfully uploaded.\n";
} else {
    print "Possible file upload attack!\n";
}

$s3=new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$bucket = uniqid("S3-Sukanya-", false);
//print "Creating bucket named {$bucket}\n";
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);


$result = $s3->waitUntil('BucketExists',array('Bucket' => $bucket));

//echo "bucket creation done";

$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => "uploads".$uploadfile,
'ContentType' => $_FILES['userfile']['type'],
    'Body'   => fopen($uploadfile, 'r+')
]);  
$url = $result['ObjectURL'];
//adding expiration to bucket
$objectrule = $s3->putBucketLifecycleConfiguration([
    'Bucket' => $bucket,
    'LifecycleConfiguration' => [
        'Rules' => [ 
            [
                'Expiration' => [
                    'Days' => 1,
                ],
                              
                'Prefix' => ' ',
                'Status' => 'Enabled',
                
            ],
            
        ],
    ],
]);

// reference http://php.net/manual/en/imagick.writeimage.php
$filepath = new Imagick($uploadfile);
$filepath->flipImage();
mkdir("/tmp/Imagick");
$extension = end(explode('.', $filename));
echo "extension here";
echo $extension;
$path = '/tmp/Imagick/';
$imgid = uniqid("DesImage");
$imgloc = $imgid . '.' . $extension;
$DestPath = $path . $imgloc;
echo $DestPath;
///tmp/Imagick/DesImage56553cb459719.png
//$filepath->setImageFormat ("png");
//file_put_contents ($DestPath, $filepath);
$filepath->writeImage($DestPath); 

//bucket creation of flip image
$flipbucket = uniqid("flippedimage",false);
echo $flipbucket;

$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $flipbucket,
]);

$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $flipbucket,
   'Key' => "flipped".$imgloc,
'SourceFile' => $DestPath,
]);

$s3finishedurl=$result['ObjectURL'];
//adding expiration to flipped image bucket
$objectrule = $s3->putBucketLifecycleConfiguration([
    'Bucket' => $flipbucket,
    'LifecycleConfiguration' => [
        'Rules' => [ 
            [
                'Expiration' => [
                    'Days' => 1,
                ],
                              
                'Prefix' => ' ',
                'Status' => 'Enabled',
                
            ],
            
        ],
    ],
]);

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
//itmo-544-SN-dbreplica
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);


$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($link));


if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
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
$filename = basename($_FILES['userfile']['name']);
$status =0;
$issubscribed=0;
$stmt->bind_param("ssssssii",$uname,$email,$phone,$s3rawurl,$s3finishedurl,$filename,$status,$issubscribed);
if (!$stmt->execute()) {
    print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->close();
$link->close();
//itmo-544-SN-dbreplica using read replica DB to get values
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-SN-dbreplica']);


$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

$linkr = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($linkr));


if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$sql1 = "SELECT topicarn,topicname FROM topic ";
$result = mysqli_query($linkr, $sql1);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
	if ($row["topicname"] == 'Mp2-S3Upload1')
	{
	//create sns and configure autoscaling to send notification to sns on alarm
	$sns= new Aws\Sns\SnsClient([
	    'version' => 'latest',
	    'region'  => 'us-east-1'
	]);
	$result = $sns->publish([
	    'Message' => 'Image uploaded successfully', // REQUIRED
	    'Subject' => 'Image has been uploaded successfully to S3',
	    'TopicArn' => $row["topicarn"],
	]);
	}
   }
} 
else {
    echo "----0 results";
}
$linkr->close();
}

function redirect()
{
  // echo "inside redirect";
   ?>
   <script>location.href='/gallery.php'</script>
   <?php
   die();
}
redirect(); 

?>

