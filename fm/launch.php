
<?php

session_set_cookie_params(['samesite' => 'none']); session_start();

if (file_exists( '../../includes/config.php' )) { require( '../../includes/includes.php'); }  else { header( 'Location: ../../install' ); exit(); };

if(isset($config['VESTA_HOST_ADDRESS'])) { $_SESSION["sftpHost"] = base64_encode($config['VESTA_HOST_ADDRESS']); }
else { $_SESSION["sftpHost"] = base64_encode($config['HESTIA_HOST_ADDRESS']); }

$_SESSION['ftp_cred'] = base64_encode(hwicrypt($_POST['password'], 'e'));

$postvars1 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-add-user-sftp-key','arg1' => $username, 'arg2' => '30');

$curl1 = curl_init();
curl_setopt($curl1, CURLOPT_URL, $vst_url);
curl_setopt($curl1, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl1, CURLOPT_POST, true);
curl_setopt($curl1, CURLOPT_POSTFIELDS, http_build_query($postvars1));
curl_exec($curl1);

$local_file = 'private/sessions/'.$_POST['ftp_user'].'_fm_key';
$server_file = '/.ssh/hst-filemanager-key';
$ftp_server=base64_decode($_SESSION["sftpHost"]);
$ftp_user_name=$_POST['ftp_user'];
$ftp_user_pass=$_POST['password'];

$conn_id = ftp_connect($ftp_server);

$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
    header("Location: app");
}
else {
    header("Location: index.php?error=1");
}
ftp_close($conn_id);
?>
