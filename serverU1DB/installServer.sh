#!/bin/bash

dir_base=`dirname "$0"`

function valid_ip()
{
    local  ip=$1
    local  stat=1
    if [[ $ip =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
        OIFS=$IFS
        IFS='.'
        ip=($ip)
        IFS=$OIFS
        [[ ${ip[0]} -le 255 && ${ip[1]} -le 255 \
            && ${ip[2]} -le 255 && ${ip[3]} -le 255 ]]
        stat=$?
    fi
    return $stat
}

echo -e -n "\nStarting U1DB server installation"
echo -e -n "\nU1DB server configuration:\n"

while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter port [ENTER]= "
    read PORT
    let SIZE=${#PORT}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORT =~ $re ]] ; then
            if [[ $PORT -gt 1000 ]] ; then
                hasPort=true
                exit="Yes"
            else
                echo -e -n "Invalid port:$PORT\n"
            fi
        else
            echo -e -n "Invalid port:$PORT\n"
        fi
    else
        exit="Yes"
    fi
done

if [ ! -d "/var/lib/u1db" ]; then
    mkdir -p "/var/lib/u1db"
fi

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter U1DB server working directory [ENTER]= "
    read workingDirectory
    let SIZE=${#workingDirectory}
    if [ $SIZE -gt 0 ]; then
        if [ ! -d "$workingDirectory" ]; then
            echo -e -n "Path not exists:$workingDirectory\n"
        else
            PATH=$(realpath -s $workingDirectory)
            hasWorkingDirectory=true
            exit="Yes"
        fi
    else
        exit="Yes"
    fi
done

data="PORT="
if [ $hasPort ]; then
    data=$data$PORT
else
    data=$data"9000"
fi

data=$data"\nWORKING_DIR="
if [ $hasWorkingDirectory ]; then
    data=$data$PATH"\n"
else
    data=$data"/var/lib/u1db/\n"
fi
echo -e -n $data > /etc/default/U1DBServe

echo -e -n "Installing U1DB server\n"
PATH_SERVER="$dir_base/serverU1DB"
PATH_SCRIPT="$dir_base/u1db-serve.py"
PATH_CLIENT="$dir_base/u1db-client"
cp $PATH_SERVER /etc/init.d/

if [ ! -d "/usr/local/src/serverU1DB" ]; then
    mkdir -p "/usr/local/src/serverU1DB"
fi

cp $PATH_SCRIPT /usr/local/src/serverU1DB/
cp $PATH_CLIENT /usr/local/src/serverU1DB/
update-rc.d serverU1DB defaults
/etc/init.d/serverU1DB start
echo -e -n "Installed U1DB server\n"

if [ ! -d "/usr/local/src/serverOauth" ]; then
    mkdir -p "/usr/local/src/serverOauth"
fi

echo -e -n "Starting Oauth server installation\n"
echo -e -n "MongoDb configuration\n"

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter Host[ENTER]= "
    read HOSTMONGO
    let SIZE=${#HOSTMONGO}
    if [ $SIZE -gt 0 ]; then
        if valid_ip $HOSTMONGO; then
            exit="Yes"
        else
            echo -n -e "Invalid IP:$HOSTMONGO\n"
        fi
    else
        HOSTMONGO="localhost"
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter port[ENTER]= "
    read PORTMONGO
    let SIZE=${#PORTMONGO}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORTMONGO =~ $re ]] ; then
            if [[ $PORTMONGO -gt 1000 ]] ; then
                exit="Yes"
            else
                echo -e -n "Invalid port:$PORTMONGO\n"
            fi
        else
            echo -e -n "Invalid port:$PORTMONGO\n"
        fi
    else
        PORTMONGO=27017
        exit="Yes"
    fi
done

echo -e -n "Enter database name[ENTER]= "
read DATABASE
let SIZE=${#DATABASE}
if [ $SIZE -eq 0 ]; then
    DATABASE="oauth"
fi

echo -e -n "Oauth server configuration\n"

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter Host[ENTER]= "
    read HOSTOAUTH
    let SIZE=${#HOSTOAUTH}
    if [ $SIZE -gt 0 ]; then
        if valid_ip $HOSTOAUTH; then
            exit="Yes"
        else
            echo -n -e "Invalid IP:$HOSTOAUTH\n"
        fi
    else
        HOSTOAUTH="localhost"
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter port[ENTER]= "
    read PORTOAUTH
    let SIZE=${#PORTOAUTH}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORTOAUTH =~ $re ]] ; then
            if [[ $PORTOAUTH -gt 1000 ]] ; then
                exit="Yes"
            else
                echo -e -n "Invalid port:$PORTOAUTH\n"
            fi
        else
            echo -e -n "Invalid port:$PORTOAUTH\n"
        fi
    else
        PORTOAUTH=8080
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Enter the access token's expiration time (in hours) [ENTER]= "
    read EXPIRES
    let SIZE=${#EXPIRES}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $EXPIRES =~ $re ]] ; then
            exit="Yes"
            EXPIRES=$(($EXPIRES*3600))
        else
            echo -e -n "Invalid expiration time:$EXPIRES\n"
        fi
    else
        EXPIRES=86400
        exit="Yes"
    fi
done

CONFIG_FILE="/etc/default/U1DBServe"
if [ -r $CONFIG_FILE ]; then
    port=$(cat $CONFIG_FILE | grep PORT | awk -F"=" '{ print $2 }')
    let size_port=${#port}
    working_dir=$(cat $CONFIG_FILE | grep WORKING_DIR | awk -F"=" '{ print $2 }')
    let size_working_dir=${#working_dir}
    if [ $size_port -gt 0 -a $size_working_dir -gt 0 ]; then
        data="settings = {\n\t\"MongoDb\":{\n\t\t\"host\":\"$HOSTMONGO\",\n\t\t\"port\":$PORTMONGO,\n\t\t\"name\":\"$DATABASE\"\n\t},"
        data+="\n\t\"Server\":{\n\t\t\"host\":\"$HOSTOAUTH\",\n\t\t\"port\":$PORTOAUTH,\n\t\t\"header\":\"http://\"\n\t},"
        data+="\n\t\"Urls\":{\n\t\t\"REQUEST_TOKEN_URL\":\"/request_token\",\n\t\t\"AUTHORIZATION_URL\":\"/authorize\",\n\t\t\"ACCESS_TOKEN_URL\":\"/access_token\",\n\t\t\"CALLBACK_URL\":\"http://$HOSTOAUTH:$PORTOAUTH/request_token_ready\",\n\t\t\"RESOURCE_URL\":\"http://localhost:$port\"\n\t},"
        data+="\n\t\"VERIFIER\":\"verifier\","
        data+="\n\t\"token\": {\n\t\t\"expires\":$EXPIRES\n\t},"
        data+="\n\t\"U1DB\": {\n\t\t\"path\":\"$working_dir\"\n\t}\n}"
        echo -e -n $data > "/usr/local/src/serverOauth/settings.py"
        cp "$dir_base/mongodb.py" /usr/local/src/serverOauth/
        cp "$dir_base/oauth.py" /usr/local/src/serverOauth/
        cp "$dir_base/serverOauth.py" /usr/local/src/serverOauth/
        cp "$dir_base/serverOauth" /etc/init.d/
        update-rc.d serverOauth defaults
        /etc/init.d/serverOauth start
        echo -e -n "Oauth server installed\n"
    else
         echo -e -n "No port in config file.Path:$CONFIG_FILE\n"
    fi
else
    echo -e -n "Not exists config file.Path:$CONFIG_FILE\n"
fi

