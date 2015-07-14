#!/bin/bash

dir_base=`dirname "$0"`

if [ ! -d "/usr/local/src/serverAPISYNC" ]; then
    mkdir -p "/usr/local/src/serverAPISYNC"
fi

cp "$dir_base/mongodb.py" /usr/local/src/serverAPISYNC/
cp "$dir_base/APISync.py" /usr/local/src/serverAPISYNC/
cp "$dir_base/settings.py" /usr/local/src/serverAPISYNC/
cp "$dir_base/serverAPISYNC" /etc/init.d/
chmod +x /etc/init.d/serverAPISYNC
update-rc.d serverAPISYNC defaults
/etc/init.d/serverAPISYNC start
echo -e -n "serverAPYSYNC installed\n"