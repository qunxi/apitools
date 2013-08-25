<a href="authorize.php">回到授权页</a>
<?php

/**
 * @file
 * User has successfully authenticated with Kaixin. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('kxoauth/kxClient.php');

/* Get user access tokens out of the session. */

$access_token = '';
if(array_key_exists('access_token', $_SESSION))
{
	$access_token = $_SESSION['access_token'];
}
else 
{

}
print_r($_SESSION);
/* Create a KaixinOauth object with consumer/user tokens. */
$connection = new KXClient($access_token);

/* If method is set change API call made. Test is called by default. */

  $example2 = $connection->users_me();
var_dump($example2);/*
$example1 = $connection->users_show(106352845);
var_dump($example1);
$example3 = $connection->friends_me();
var_dump($example3);
$example4 = $connection->friends_relationship(106352845,92976574);
var_dump($example4);
$example5 = $connection->app_friends();
var_dump($example5);
$example6 = $connection->app_status(106352845);
var_dump($example6);
$example7 = $connection->app_invited(106352845);
  
var_dump($example7);
$example8 = $connection->album_create("啦啦啦2.0");

var_dump($example8);
$example9 = $connection->album_show();//$uid为空时返回当前用户专辑列表

var_dump($example9);
$example10 = $connection->photo_show(38886852);//$uid,$pid
  	var_dump($example10);

  if(isset($_FILES['pic']) && $_FILES['pic']['error'] == 0)
  {
  	$pic = "@".$_FILES['pic']['tmp_name'];
  	$pic = "@D:\app_16x16_1.gif";//直接加路径也可以
  	$example = $connection->photo_upload(38787341, $pic);
  	var_dump($example);
  	$example11 = $connection->records_add("我爱北京天安门",$pic);
  	var_dump($example11);
  }
  	$example12 = $connection->records_add("我爱北d上搜索");
  	var_dump($example12);*/
  ?>
  </p>
<form action="index.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <p>
    <label>
      <input type="file" name="pic" id="pic" />
    </label>
  </p>
  <p>
    <label>
      <input type="submit" name="submit" id="submit" value="�ϴ���Ƭ" />
    </label>
  </p>
</form>
<p>&nbsp;</p>
