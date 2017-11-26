# CNCPi [![RPi Image](https://img.shields.io/badge/RPi%20version-v0.1.1-green.svg)](https://mega.nz/#!stdl1KhJ!xKgWGLVipQiJaQ315iOKmivwXRzTCyXC6xjMysisvio) [![Web](https://img.shields.io/badge/Web%20version-v0.1.103-green.svg)](https://github.com/ArnyminerZ/CNCPi/archive/master.zip)
## How to Install CNCPi in a RaspberryPi
### Method 1 (Most Recommended): BakeryPi
1. Download [Pi Bakery](http://www.pibakery.org/download.html)
2. Import the file [image_settings.xml](https://github.com/ArnyminerZ/CNCPi/blob/master/image_settings.xml)
3. Write to SD
4. Ready :)
### Method 2: Image File
1. Download the [IMG file](https://mega.nz/#!stdl1KhJ!xKgWGLVipQiJaQ315iOKmivwXRzTCyXC6xjMysisvio+)
2. Follow the instructions [provided by Raspberry](https://www.raspberrypi.org/documentation/installation/installing-images/)
3. Write to the SD
4. Ready :)
## How to install CNCPi in a Linux system
1. Install Apache2 and Apache2 PHP: `sudo apt-get install apache2 php libapache2-mod-php -y`
2. Run the command `git clone https://github.com/ArnyminerZ/CNCPi.git /temp/install` as superuser.
3. Run the command `bash /temp/install/install.sh` as superuser.<br />
You're done :)
## How to Update CNCPi in any system
Simply run the command `cncpiupdate`, it will download the latest repository and will install it

# Arduino
## What software to install?
You need to install [GRLB](https://github.com/grbl/grbl) or other [GCode](http://reprap.org/wiki/G-code) based software, with a Serial Communication.
