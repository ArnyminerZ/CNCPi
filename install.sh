#!/usr/bin/env bash
# this should be in /temp/install/CNCPi

file="/usr/local/bin/cncpiupdate"
if [ -f $file ] ; then
    rm -rf $file
fi

sudo cp /temp/install/cncpiupdate /usr/local/bin/cncpiupdate
sudo chmod +x /usr/local/bin/cncpiupdate

cncpiupdate