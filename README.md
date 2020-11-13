# HWI File Manager Plugin
Web SFTP/FTP File Manager Plugin for Hestia Web Interface

Installation Instructions:

1. Upload the 'fm' folder to the 'plugins' folder of your HWI installation.
2. CHMOD the private and repository folders by running `chmod -R 755 plugins/fm/{private,repository}`
3. Make sure the web user can write to the folder. If on HestiaCP / VestaCP, run `chown -R admin:admin /home/admin/web`, replacing "admin" with your web user.
2. Enable the plugin from the Plugins page or by adding "fm" to the plugins list in your HWI Settings.

[Project Home](https://github.com/cdgco/hestiawebinterface)

[Plugin List](https://github.com/cdgco/HestiaWebInterface/tree/master/plugins)
