
<?php

session_start();

if (file_exists( '../../includes/config.php' )) { require( '../../includes/includes.php'); }  else { header( 'Location: ../../install' ); exit(); };

if(isset($config['VESTA_HOST_ADDRESS'])) { $_SESSION["sftpHost"] = base64_encode($config['VESTA_HOST_ADDRESS']); }
else { $_SESSION["sftpHost"] = base64_encode($config['HESTIA_HOST_ADDRESS']); }

$_SESSION['ftp_cred'] = base64_encode(hwicrypt($_POST['password'], 'e'));

$postvars = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-check-user-password','arg1' => $username,'arg2' => $_POST['password'], 'arg3' => $_SERVER['REMOTE_ADDR']);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $vst_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postvars));
$answer = curl_exec($curl);

if($answer == 0) {
    header("Location: app");
}
else {
    header("Location: index.php?error=1");
}
ftp_close($conn_id);
?>
