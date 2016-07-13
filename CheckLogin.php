<?php
require_once 'Database.php';

session_start(); // Starting Session
$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "Username or Password is invalid";
	}
	else
	{
		// Define $username and $password
		$username=$_POST['username'];
		$password=$_POST['password'];
		$DB = Database::getInstance();
	    $DBH = $DB->getDBH();
	    $SQL = "
	        select name
	        from g_intra
	        where status = 1
	        	and email = :Email
	        	and password = :Password
	        	and dashboard = 1;
	    ";
	    $stmt = $DBH->prepare($SQL);
	    $username = stripslashes($username);
		$password = stripslashes($password);
	    $stmt->bindParam(':Email', $username, PDO::PARAM_STR);
	    $stmt->bindParam(':Password', PWDencode($password), PDO::PARAM_STR);
	    $stmt->execute();
	    $rows = $stmt->fetchAll();
		if (count($rows) == 1) {
			$_SESSION['login_user']=$username; // Initializing Session
			$_SESSION['login_name'] = $rows[0]['name'];
			header("location: /"); // Redirecting To Other Page
		} else {
			$error = "Username or Password is invalid";
		}
	}
}

function PWDencode($STR)		{

	$DATA = enCrypt($STR,"encode");
	return	$DATA;
}

function PWDdecode($CRYSTR)		{

	$DATA = enCrypt($CRYSTR,"decode");
	return	$DATA;
}

function enCrypt($data, $mode = 'encode')
{
   $key = substr(md5('HermanIsARichPerson'), 0, 24); //用MD5哈希生成一個密鑰，注意加密和解密的密鑰必須統一
   if ($mode == 'decode') {
      $data = base64_decode($data);
   }
   if (function_exists('mcrypt_create_iv')) {
      $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   }
   if (isset($iv) && $mode == 'encode') {
      $passcrypt = mcrypt_encrypt(MCRYPT_3DES, $key, $data, MCRYPT_MODE_ECB, $iv);
   } elseif (isset($iv) && $mode == 'decode') {
      $passcrypt = rtrim(mcrypt_decrypt(MCRYPT_3DES, $key, $data, MCRYPT_MODE_ECB, $iv));
   }
   if ($mode == 'encode') {
      $passcrypt = base64_encode($passcrypt);
   }
   return $passcrypt;
}