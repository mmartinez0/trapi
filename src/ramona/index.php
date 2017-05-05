<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log','ramona.log');

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

//
// https://codex.wordpress.org/Function_Reference/auth_redirect
//
auth_redirect();
//
// Checks user is logged in, if not it redirects them to login page.
//

$user_id = get_current_user_id();


if( user_can( $user_id, 'manage_options' ) == false )
	die( "I don't think so." );

function get_ramona_url() {
	$arr = array( 'http');
	if( $_SERVER["SERVER_PROTOCOL"] !== 'HTTP/1.1' )
		$arr[] = 's';
	$arr[] = '://';
	$arr[] = $_SERVER['SERVER_NAME'];
	if( $_SERVER['SERVER_PORT'] !== '80' )
		$arr[] = ':' . $_SERVER['SERVER_PORT'];
	$arr[] = (explode('ramona', $_SERVER['PHP_SELF'])[0]) . 'ramona/';
	return implode('', $arr);
}?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
  	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1"/>
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <meta name="description" content=""/>
	    <meta name="author" content="Miguel Martinez"/>

	    <link href="favicon.ico" rel="icon"/>

	    <title>Resources Videos</title>

	    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="vendor/angular-toastr/dist/angular-toastr.min.css" rel="stylesheet" type="text/css" />
		<link href="vendor/angular-loading-bar/build/loading-bar.min.css" rel="stylesheet" type="text/css" />
		<!--
		<link href="vendor/ng-dialog/css/ngDialog.css" rel="stylesheet" type="text/css" />
		<link href="vendor/ng-dialog/css/ngDialog-theme-default.css" rel="stylesheet" type="text/css" />
		-->
		<link href="https://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

	    <link href="app/dialog/dialog.css" rel="stylesheet" type="text/css" />
	    <link href="app/app.css" rel="stylesheet" type="text/css" />
	    <link href="app/video/video.css" rel="stylesheet" type="text/css" />

	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	    <script type="text/javascript">
	    	var trapi = {
	    		ramona_url: "<?php echo get_ramona_url(); ?>",
	    		ajax_url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
	    		baseurl: "<?php echo (wp_upload_dir()['baseurl']); ?>",
	    		trapi_url: "http://no-cache.miguelmartinez.com/trc/"
	    	};
	    </script>
	</head>

  	<body ng-controller="AppController">

		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" ui-sref="videos">Resources</a>
				</div>

				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li ng-class="{ active: $state.includes('videos') }"><a ui-sref="videos">Videos</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<div ui-view></div>

		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>

		<script src="vendor/angular/angular.min.js"></script>
		<script src="vendor/angular-ui-router/release/angular-ui-router.min.js"></script>
		<script src="vendor/angular-animate/angular-animate.min.js"></script>
		<script src="vendor/angular-toastr/dist/angular-toastr.min.js"></script>
		<script src="vendor/angular-loading-bar/build/loading-bar.min.js"></script>
		<!--script src="vendor/ng-dialog/js/ngDialog.js"></script-->

		<script src="app/widget/widget.module.js"></script>
		<script src="app/widget/fileselect/filereader.service.js"></script>
		<script src="app/widget/fileselect/fileselect.directive.js"></script>

		<script src="app/dialog/dialog.module.js"></script>
		<script src="app/dialog/dialog.service.js"></script>
		<script src="app/dialog/dialog.directive.js"></script>

		<script src="app/app.js"></script>
		<script src="app/app.constants.js"></script>
		<script src="app/app.config.js"></script>
		<script src="app/app.run.js"></script>
		<script src="app/app.controller.js"></script>

		<script src="app/home/home.js"></script>

		<script src="app/video/video.module.js"></script>
		<script src="app/video/video.service.js"></script>
		<script src="app/video/video.directive.js"></script>
		<script src="app/video/library.directive.js"></script>
		<script src="app/video/video.controller.js"></script>
		<script src="app/video/player.controller.js"></script>
		<script src="app/video/library.controller.js"></script>
	</body>
</html>
