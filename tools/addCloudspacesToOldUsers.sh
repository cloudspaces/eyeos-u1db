#!/bin/sh

directoryBase=/var/www/eyeos/eyeos/users

echo -e "\nScript started\n"
if [ -d "$directoryBase" ]; then
    for user in $(ls -l $directoryBase|awk '{print $9}');do
        if [ $user != "root" ] && [ $user != "admin" ]; then
            pathUser="$directoryBase/$user"
            pathCloudpaces="$pathUser/files/Cloudspaces"
            if [ -d "$pathCloudpaces" ]; then
                echo "User: $user has Cloudspaces folder"
            else
                mkdir -p $pathCloudpaces
                echo "Cloudspaces has been created for user: $user"
            fi
        else
            echo $user
        fi
    done
else
    echo "ERROR, $directoryBase does not exist"
fi