<?php

session_start();

if (file_exists( '../../../includes/config.php' )) { require( '../../../includes/includes.php'); }  else { header( 'Location: ../../../install' ); exit(); };
if(base64_decode($_SESSION['loggedin']) == 'true') {} else { header('Location: ../'); exit(); }

$ftp_cred = hwicrypt(base64_decode($_SESSION['ftp_cred']), 'd');

$dist_config = [
    'public_path' => APP_PUBLIC_PATH,
    'public_dir' => APP_PUBLIC_DIR,
    'overwrite_on_upload' => false,
    'timezone' => 'UTC', // https://www.php.net/manual/en/timezones.php
    'download_inline' => ['pdf'], // download inline in the browser, array of extensions, use * for all

    'frontend_config' => [
        'app_name' => $sitetitle . ' - File Manager',
        'app_version' => APP_VERSION,
        'language' => 'english',
        'logo' => '../../images/'.$cpicon,
        'upload_max_size' => 100 * 1024 * 1024, // 100MB
        'upload_chunk_size' => 1 * 1024 * 1024, // 1MB
        'upload_simultaneous' => 3,
        'default_archive_name' => 'archive.zip',
        'editable' => ['.txt', '.css', '.js', '.ts', '.html', '.php', '.py',
        '.yml', '.xml', '.md', '.log', '.csv', '.conf', '.config', '.ini', '.scss', '.sh', '.env', '.example', '.htaccess'],
        'date_format' => 'YY/MM/DD hh:mm:ss',
        'guest_redirection' => '../',
    ],

    'services' => [
        'Filegator\Services\Logger\LoggerInterface' => [
            'handler' => '\Filegator\Services\Logger\Adapters\MonoLogger',
            'config' => [
                'monolog_handlers' => [
                    function () {
                        return new \Monolog\Handler\StreamHandler(
                            __DIR__.'/private/logs/app.log',
                            \Monolog\Logger::DEBUG
                        );
                    },
                ],
            ],
        ],
        'Filegator\Services\Session\SessionStorageInterface' => [
            'handler' => '\Filegator\Services\Session\Adapters\SessionStorage',
            'config' => [
                'handler' => function () {
                    //$save_path = null; // use default system path
                    $save_path = __DIR__.'/private/sessions';
                    $handler = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($save_path);

                    return new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage([], $handler);
                },
            ],
        ],
        'Filegator\Services\Cors\Cors' => [
            'handler' => '\Filegator\Services\Cors\Cors',
            'config' => [
                'enabled' => APP_ENV == 'production' ? false : true,
            ],
        ],
        'Filegator\Services\Tmpfs\TmpfsInterface' => [
            'handler' => '\Filegator\Services\Tmpfs\Adapters\Tmpfs',
            'config' => [
                'path' => __DIR__.'/private/tmp/',
                'gc_probability_perc' => 10,
                'gc_older_than' => 60 * 60 * 24 * 2, // 2 days
            ],
        ],
        'Filegator\Services\Security\Security' => [
            'handler' => '\Filegator\Services\Security\Security',
            'config' => [
                'csrf_protection' => true,
                'ip_allowlist' => [],
                'ip_denylist' => [],
            ],
        ],
        'Filegator\Services\View\ViewInterface' => [
            'handler' => '\Filegator\Services\View\Adapters\Vuejs',
            'config' => [
                'add_to_head' => '',
                'add_to_body' => '<script>
    var checkVueLoaded = setInterval(function() {
        if (document.getElementsByClassName("navbar-item").length) {
            clearInterval(checkVueLoaded);
            var navProfile = document.getElementsByClassName("navbar-item profile")[0]; navProfile.replaceWith(navProfile.cloneNode(true))
            document.getElementsByClassName("navbar-item logout")[0].text="Back to Panel";
        }
    }, 200);
</script>',
            ],
        ],
        'Filegator\Services\Storage\Filesystem' => [
            'handler' => '\Filegator\Services\Storage\Filesystem',
            'config' => [
                'separator' => '/',
                'config' => [],
                'adapter' => function () {
                    return new \League\Flysystem\Adapter\Local(
                        __DIR__.'/repository'
                    );
                },
            ],
        ],
        'Filegator\Services\Archiver\ArchiverInterface' => [
            'handler' => '\Filegator\Services\Archiver\Adapters\ZipArchiver',
            'config' => [],
        ],
        'Filegator\Services\Auth\AuthInterface' => [
            'handler' => '\Filegator\Services\Auth\Adapters\HestiaAuth',
            'config' => [
                'permissions' => ['read', 'write', 'upload', 'download', 'batchdownload', 'zip'],
            	'private_repos' => false,
		],
        ],
        'Filegator\Services\Router\Router' => [
            'handler' => '\Filegator\Services\Router\Router',
            'config' => [
                'query_param' => 'r',
                'routes_file' => __DIR__.'/backend/Controllers/routes.php',
            ],
        ],
    ],
];

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
            'root' => '/',
            'timeout' => 10,
            'directoryPerm' => 0755,
        ]);
	/*
	// FTP MODE
        return new \League\Flysystem\Adapter\Ftp([
              'host' => base64_decode($_SESSION['sftpHost']),
              'username' => basename($v_user),
              'password' => $ftp_cred,
              'port' => 21,
              'timeout' => 10,
        ]);
	*/
    };

return $dist_config;
