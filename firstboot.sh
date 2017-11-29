sudo apt-get install lamp-server^
sudo usermod -a -G dialout www-data
sudo systemctl enable ssh
sudo git clone https://github.com/ArnyminerZ/CNCPi.git /temp/install
sudo bash /temp/install/install.sh
sudo chown -R www-data:www-data /var/www
sudo chmod go-rwx /var/www
sudo chmod go+x /var/www
sudo chgrp -R www-data /var/www
sudo chmod -R go-rwx /var/www
sudo chmod -R g+rx /var/www
sudo chmod -R g+rwx /var/www

sudo rm -rf ./firstboot.sh