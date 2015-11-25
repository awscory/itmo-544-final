<?php
session_start();
require 'vendor/autoload.php';
echo "Submit.php page";
if(!empty($_POST)){
echo $_POST['phone'];
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
                    'Date' => '2015-11-30',
                ],
                              
                'Prefix' => ' ',
                'Status' => 'Enabled',
                
            ],
            
        ],
    ],
]);


$filepath = new Imagick($uploadfile);
$filepath->flipImage();
mkdir("/tmp/Imagick");
$extension = end(explode('.', $fname));
echo $extension;
$path = '/tmp/Imagick/';
$imgid = uniqid("DesImage");
$imgloc = $imgid . '.' . $extension;
$DestPath = $path . $imgloc;
echo $DestPath;
$path->writeImage($DestPath);

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

$FinalS3Url=$result['ObjectURL'];

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);

//echo "No error as of now";

//print_r($result);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
  //  print "============\n". $endpoint . "================";
//echo "endpoint is available";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($link));

//print_r($link);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//echo "connection success";
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
//printf("%d Row inserted.\n", $stmt->affected_rows);

$stmt->close();
$sql1 = "SELECT topicarn,topicname FROM topic ";
$result = mysqli_query($link, $sql1);
//$imgLocations = array();
//print "Result set order...\n";

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
//	echo "topicarn:".$row["topicarn"]."<br>";
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
        //this will append the path of images to an array
//        $imgLocations[$row["JpgFileName"]] = $row["RawS3URL"];
  //      echo "id: " . $row["ID"]."- RawS3URL" . $row["RawS3URL"]. "<br>";
    }
} 
else {
    echo "----0 results";
}


$link->close();
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

