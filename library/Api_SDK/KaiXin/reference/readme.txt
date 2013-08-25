


一、运行前请修改kxClient.php中的如下内容：

	public $client_id = ""; //api key

	public $client_secret = ""; //app secret

	public $redirect_uri = "";//回调地址，所在域名必须与开发者注册应用时所提供的网站根域名列表或应用的站点地                                      址（如果根域名列表没填写）的域名相匹配
  


二、修改后就可以运行authorize.php文件了。


authorize.php中的：

  c. Resource Owner Password Credentials：采用用户名、密码获取Access Token，适用于任何类型应用。
需签署保密协议之     后才能使用。使用前需在authorize.php文件中添加用户名和密码：
	$username = "";
	$password = "";

  d. Refresh Token：令牌刷新方式，适用于所有有Server端配合的应用。
	
	1、需用其它三种方式中的一种获取到refresh_token后才能使用。

	2、本代码中的refresh_token自动存入$_SESSION中，如$_SESSION中的变量失效，需要将refresh_token重新添加到$_SESSION中。
  
