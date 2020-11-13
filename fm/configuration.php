

<?php

$dist_config = require __DIR__.'/configuration_sample.php';

session_set_cookie_params(['samesite' => 'none']); session_start();

if (file_exists( '../../../includes/config.php' )) { require( '../../../includes/includes.php'); }  else { header( 'Location: ../../../install' ); exit(); };

$ftp_cred = hwicrypt(base64_decode($_SESSION['ftp_cred']), 'd');

$dist_config['frontend_config']['app_name'] = $sitetitle . ' - File Manager';
$dist_config['frontend_config']['logo'] = '../../images/'.$cpicon;
$dist_config['frontend_config']['editable'] = ['.txt', '.css', '.js', '.ts', '.html', '.php', '.py',
        '.yml', '.xml', '.md', '.log', '.csv', '.conf', '.config', '.ini', '.scss', '.sh', '.env', '.example', '.htaccess'];
$dist_config['frontend_config']['guest_redirection'] = '../' ;
$dist_config['frontend_config']['upload_max_size'] = 1024 * 1024 * 1024;

$dist_config['services']['Filegator\Services\Storage\Filesystem']['config']['adapter'] = function () use ($ftp_cred) {

        if (isset($_SESSION['username'])) {
            $v_user = base64_decode($_SESSION['username']);
        }
        if (isset($_SESSION['proxied']) && base64_decode($_SESSION['proxied']) != 'admin' && $v_user === 'admin') {
            $v_user = base64_decode($_SESSION['proxied']);
        }
        return new \League\Flysystem\Sftp\SftpAdapter([
            'host' => base64_decode($_SESSION['sftpHost']),
	    'port' => intval(22),
	    'username' => basename($v_user),
	    'password' => $ftp_cred,
	    'privateKey' => 'private/sessions/'.basename($v_user).'_fm_key',
            'root' => '/',
            'timeout' => 10,
            'directoryPerm' => 0755,
        ]);

	/* FTP MODE
        return new \League\Flysystem\Adapter\Ftp([
              'host' => base64_decode($_SESSION['sftpHost']),
              'username' => basename($v_user),
              'password' => $ftp_cred,
              'port' => 21,
              'timeout' => 10,
        ]);
	*/
    };

$dist_config['services']['Filegator\Services\Auth\AuthInterface'] = [
        'handler' => '\Filegator\Services\Auth\Adapters\HestiaAuth',
        'config' => [
            'permissions' => ['read', 'write', 'upload', 'download', 'batchdownload', 'zip'],
            'private_repos' => false,
        ],
    ];

$dist_config['services']['Filegator\Services\View\ViewInterface']['config'] = [
    'add_to_head' => '',
    'add_to_body' => '
<script>
    var checkVueLoaded = setInterval(function() {
        if (document.getElementsByClassName("navbar-item").length) {
            clearInterval(checkVueLoaded);
            var navProfile = document.getElementsByClassName("navbar-item profile")[0]; navProfile.replaceWith(navProfile.cloneNode(true))
            document.getElementsByClassName("navbar-item logout")[0].text="Back to Panel";
        }
    }, 200);
</script>',
];


return $dist_config;
