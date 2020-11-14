# VWI / HWI File Manager Plugin
Web SFTP/FTP File Manager Plugin for Vesta / Hestia Web Interface

Installation Instructions:

1. Upload the 'fm' folder to the 'plugins' folder of your VWI / HWI installation.
2. CHMOD the private and repository folders by running `chmod -R 775 plugins/fm/{private,repository}`
3. Make sure the web user can write to the folder. If on HestiaCP / VestaCP, run `chown -R admin:admin /home/admin/web`, replacing "admin" with your web user.
2. Enable the plugin from the Plugins page or by adding "fm" to the plugins list in your VWI / HWI Settings.

[Project Home](https://github.com/cdgco/vestawebinterface)

[Plugin List](https://github.com/cdgco/VestaWebInterface/tree/master/plugins)
