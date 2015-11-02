<?php
require 'vendor/autoload.php';

print $_POST["email"];
$uploaddir = '/tmp/';
$uploadfile = $uploaddir. basename($_FILES['userfile']['name']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    print "File is valid, and was successfully uploaded.\n";
} else {
    print "Possible file upload attack!\n";
}
print 'Here is some more debugging info:';
print_r($_FILES);

$s3=new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$bucket = uniqid("S3-Sukanya-", true);
print "Creating bucket named {$bucket}\n";
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);


$client->waitUntil('BucketExists',array('Bucket' => $bucket));

$result = $client->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $uploadfile
]);  
$url = $result['ObjectURL'];
print $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'itmo-544-sukanya',
]);

$endpoint = $result['DBInstances']['Endpoint']['Address']
    print "============\n". $endpoint . "================";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","items") or die("Error " . mysqli_error($link));

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
$link->real_query("SELECT * FROM items");
$res = $link->use_result();
print "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    print $row['id'] . " " . $row['email']. " " . $row['phone'];
}
$link->close();
?>


