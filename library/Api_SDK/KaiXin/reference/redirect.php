<?php

require_once('kxoauth/kxClient.php');

if(array_key_exists('code', $_GET))
{
	$connect = new KXClient();
	$response = $connect->getAccessTokenFromCode($_GET['code']);
	var_dump($response);
	if(isset($response->access_token))
	{
		session_start();
		$_SESSION['access_token'] =	$response->access_token;
		$_SESSION['refresh_token'] = $response->refresh_token;
		header("Location:index.php");
	} 
}
elseif (array_key_exists('access_token', $_GET)) {	
	session_start();
	$_SESSION['access_token'] = $_GET['access_token'];	
	$_SESSION['refresh_token'] = $_GET['refresh_token'];
	header("Location:index.php");
}
?>
<script type="text/javascript">
var access_token = window.location.hash;
if(access_token)
{
	access_token = access_token.substring(1);
	document.write(access_token);
	window.location.href = "redirect.php?"+access_token;
}
</script>