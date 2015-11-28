<?php
session_start();
require_once('/var/www/html/snssetup.php');   
//going to test image magick
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Simple Sidebar - Start Bootstrap Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://raw.githubusercontent.com/sukanyaN/itmo-544-final/master/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="https://raw.githubusercontent.com/sukanyaN/itmo-544-final/master/css/simple-sidebar.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="gallery.php?raw=true">
                        Gallery
                    </a>
                </li>
                
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <form role="form" "enctype="multipart/form-data" action="submit.php" method="post">
								<div class="form-group">
									User Name: <input class="form-control" type="text" name="username" value="sukanya">
								</div>
								<div class="form-group">
								E-mail: <input class="form-control" type="text" name="email">
								</div>
								<div class="form-group">
								Phone : <input class="form-control" type="text" id="phone" name="phone">
								</div>
								<div class="form-group">
								<input class="form-control" type="hidden" name="MAX_FILE_SIZE" value="3000000">
								Your File :  <input type="file" name="userfile"> 
								</div>
								<input type="submit" value="Upload"/><span class="glyphicon glyphicon-upload"></span>Upload
							</form>
					</div>
					<div class="col-lg-12">
							<form role="form" enctype="multipart/form-data" action="subscribe.php" method="post">
								<div class="form-group">
									Phone to Subscribe : <input class="form-control" type="text" name="phoneNo"/>
								</div>
								<div class="form-group">
									Would you like to subscribe to receive message on Upload ?  
									<input type="submit" value="Subscribe"/><span class="glyphicon glyphicon-check"></span>Subscribe
								</div>
							</form>
					</div>
					<div class="well well-sm">
							<form role="form" enctype="multipart/form-data" action="introspection.php" method="post">
								<div class="form-group">
									Would you like to take a Database Backup   
									<input type="submit" value="BackUp"/>
								</div>
							</form>
					</div>
                
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="https://raw.githubusercontent.com/sukanyaN/itmo-544-final/master/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="https://raw.githubusercontent.com/sukanyaN/itmo-544-final/master/js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    
</body>
</html>
