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

echo -e -n "\nIniciando la instalacion del servidor U1DB"
echo -e -n "\nConfiguración del servidor U1DB:\n"

while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce puerto[ENTER]= "
    read PORT
    let SIZE=${#PORT}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORT =~ $re ]] ; then
            if [[ $PORT -gt 1000 ]] ; then
                hasPort=true
                exit="Yes"
            else
                echo -e -n "Puerto no válido:$PORT\n"
            fi
        else
            echo -e -n "Puerto no válido:$PORT\n"
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
    echo -e -n "Introduce el path del servidor U1DB[ENTER]= "
    read workingDirectory
    let SIZE=${#workingDirectory}
    if [ $SIZE -gt 0 ]; then
        if [ ! -d "$workingDirectory" ]; then
            echo -e -n "No existe el path:$workingDirectory\n"
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

echo -e -n "Instalando servidor U1DB\n"
PATH_SERVER="$dir_base/serverU1DB"
PATH_SCRIPT="$dir_base/u1db-serve.py"
cp $PATH_SERVER /etc/init.d/

if [ ! -d "/usr/local/src/serverU1DB" ]; then
    mkdir -p "/usr/local/src/serverU1DB"
fi

cp $PATH_SCRIPT /usr/local/src/serverU1DB/
update-rc.d serverU1DB defaults
/etc/init.d/serverU1DB start
echo -e -n "Servidor U1DB instalado\n"

if [ ! -d "/usr/local/src/serverOauth" ]; then
    mkdir -p "/usr/local/src/serverOauth"
fi

echo -e -n "Iniciando instalacion server Oauth\n"
echo -e -n "Configuracion MongoDb\n"

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce Host[ENTER]= "
    read HOSTMONGO
    let SIZE=${#HOSTMONGO}
    if [ $SIZE -gt 0 ]; then
        if valid_ip $HOSTMONGO; then
            exit="Yes"
        else
            echo -n -e "IP no válida:$HOSTMONGO\n"
        fi
    else
        HOSTMONGO="localhost"
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce puerto[ENTER]= "
    read PORTMONGO
    let SIZE=${#PORTMONGO}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORTMONGO =~ $re ]] ; then
            if [[ $PORTMONGO -gt 1000 ]] ; then
                exit="Yes"
            else
                echo -e -n "Puerto no válido:$PORTMONGO\n"
            fi
        else
            echo -e -n "Puerto no válido:$PORTMONGO\n"
        fi
    else
        PORTMONGO=27017
        exit="Yes"
    fi
done

echo -e -n "Introduce nombre database[ENTER]= "
read DATABASE
let SIZE=${#DATABASE}
if [ $SIZE -eq 0 ]; then
    DATABASE="oauth"
fi

echo -e -n "Configuración servidor Oauth\n"

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce Host[ENTER]= "
    read HOSTOAUTH
    let SIZE=${#HOSTOAUTH}
    if [ $SIZE -gt 0 ]; then
        if valid_ip $HOSTOAUTH; then
            exit="Yes"
        else
            echo -n -e "IP no válida:$HOSTOAUTH\n"
        fi
    else
        HOSTOAUTH="localhost"
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce puerto[ENTER]= "
    read PORTOAUTH
    let SIZE=${#PORTOAUTH}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $PORTOAUTH =~ $re ]] ; then
            if [[ $PORTOAUTH -gt 1000 ]] ; then
                exit="Yes"
            else
                echo -e -n "Puerto no válido:$PORTOAUTH\n"
            fi
        else
            echo -e -n "Puerto no válido:$PORTOAUTH\n"
        fi
    else
        PORTOAUTH=8080
        exit="Yes"
    fi
done

exit="No"
while [ "$exit" != "Yes" ]; do
    echo -e -n "Introduce el intervalo de tiempo de la caducidad del token (en horas)[ENTER]="
    read EXPIRES
    let SIZE=${#EXPIRES}
    if [ $SIZE -gt 0 ]; then
        re='^[0-9]+$'
        if [[ $EXPIRES =~ $re ]] ; then
            exit="Yes"
            EXPIRES=$(($EXPIRES*3600))
        else
            echo -e -n "Caducidad no válida:$EXPIRES\n"
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
    if [ $size_port -gt 0 ]; then
        data="settings = {\n\t\"MongoDb\":{\n\t\t\"host\":\"$HOSTMONGO\",\n\t\t\"port\":$PORTMONGO,\n\t\t\"name\":\"$DATABASE\"\n\t},"
        data+="\n\t\"Server\":{\n\t\t\"host\":\"$HOSTOAUTH\",\n\t\t\"port\":$PORTOAUTH\n\t},"
        data+="\n\t\"Urls\":{\n\t\t\"REQUEST_TOKEN_URL\":\"/request_token\",\n\t\t\"AUTHORIZATION_URL\":\"/authorize\",\n\t\t\"ACCESS_TOKEN_URL\":\"/access_token\",\n\t\t\"CALLBACK_URL\":\"http://$HOSTOAUTH:$PORTOAUTH/request_token_ready\",\n\t\t\"RESOURCE_URL\":\"http://localhost:$port\"\n\t},"
        data+="\n\t\"VERIFIER\":\"verifier\","
        data+="\n\t\"token\": {\n\t\t\"expires\":$EXPIRES\n\t}\n}"
        echo -e -n $data > "/usr/local/src/serverOauth/settings.py"
        cp "$dir_base/mongodb.py" /usr/local/src/serverOauth/
        cp "$dir_base/oauth.py" /usr/local/src/serverOauth/
        cp "$dir_base/serverOauth.py" /usr/local/src/serverOauth/
        cp "$dir_base/serverOauth" /etc/init.d/
        update-rc.d serverOauth defaults
        /etc/init.d/serverOauth start
        echo -e -n "Servidor Oauth instalado\n"
    else
         echo -e -n "No existe PORT en fichero de configuracion en Path:$CONFIG_FILE\n"
    fi
else
    echo -e -n "No existe fichero de configuracion en Path:$CONFIG_FILE\n"
fi

