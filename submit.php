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
echo "s3 upload done";
/*
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);


$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'itmo-544-sukanya',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address']
    print "============\n". $endpoint . "================";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","items",3306) or die("Error " . mysqli_error($link));

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (!($stmt = $link->prepare("INSERT INTO items (id,Uname,Email,Phone,RawS3Url,FinalS3Url,JpgFileName,status,Issubscribed) VALUES (NULL,?,?,?,?,?,?,?,?)"))) {
    print "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$uname = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$s3rawurl = $url; //  $result['ObjectURL']; from above
$s3finishedurl = "none";
$filename = basename($_FILES['userfile']['name']);
$status =0;
$issubscribed=0;
$stmt->bind_param("sssssii",$uname,$email,$phone,$s3rawurl,$s3finishedurl,$filename,$status,$issubscribed);
if (!$stmt->execute()) {
    print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);

$stmt->close();
$sql1 = "SELECT * FROM items";
$result = mysqli_query($link, $sql1);
print "Result set order...\n";

$link->close();*/
?>

 


