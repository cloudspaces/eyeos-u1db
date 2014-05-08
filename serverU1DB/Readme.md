<h1>Installation U1DB server & Oauth server</h1>

<b>Table contents</b>

<ul>
    <li><a href='#Instalacion' style='font-family:verdana;font-size:14px;text-decoration:none'>Installation</a>
        <ul>
            <li><a href='#Requisitos' style='font-family:verdana;font-size:14px;text-decoration:none'>Requirements</a></li>
            <li><a href='#Pasos' style='font-family:verdana;font-size:14px;text-decoration:none'>Step by step</a></li>
        </ul>
    </li>
</ul>

<h2><a name=Instalacion>Installation</a></h2>
<hr>
<p style="margin-bottom:30px;font-family:verdana;font-size:12px;">
    The installation will be done on a Ubuntu 12.04 operating system, you can choose a higher version, but then the requirements might be different.
</p>

<h3><a name=Requisitos>Requirements</a></h3>
<hr>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;MongoDb
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
    </div>
    <div style='margin:0 auto'>
        <img src="../img/Step1_ImportKey.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | sudo tee /etc/apt/sources.list.d/mongodb.list
    </div>
    <div style='margin:0 auto'>
        <img src="../img/Step2_CrearListFile.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get update
    </div>
    <div style='margin:0 auto'>
        <img src="../img/Step3_ReloadPkgDatabase.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install mongodb-org=2.6.0 mongodb-org-server=2.6.0 mongodb-org-shell=2.6.0 mongodb-org-mongos=2.6.0 mongodb-org-tools=2.6.0
    </div>
    <div style='margin:0 auto'>
        <img src="../img/Step4_InstalarPkgMongo.jpg"/>
    </div>
    <div style="margin-top:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# echo "mongodb-org hold" | sudo dpkg –set-selections<br>
        &nbsp;&nbsp;&nbsp;&nbsp;# echo "mongodb-org-server hold" | sudo dpkg --set-selections<br>
        &nbsp;&nbsp;&nbsp;&nbsp;# echo "mongodb-org-shell hold" | sudo dpkg --set-selections<br>
        &nbsp;&nbsp;&nbsp;&nbsp;# echo "mongodb-org-mongos hold" | sudo dpkg --set-selections<br>
        &nbsp;&nbsp;&nbsp;&nbsp;# echo "mongodb-org-tools hold" | sudo dpkg –set-selections<br>
    </div>
    <div style='margin:0 auto'>
        <img src="../img/Step5_Fijarversion.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# mongo –version
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step6_Version.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# service mongod status
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step7_VerifyStatusService.jpg" />
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;PyMongo
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install build-essential python-dev
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step1_InstallPymongo.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install python-pip
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step2_InstallPymongo_2.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# pip install pymongo
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step3_InstallPymongo.jpg" />
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Module Oauth
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# pip install oauth
    </div> 
    <div style="margin:0 auto">
        <img src="../img/Step1_OauthPython.jpg" />
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;U1DB
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;Install the package python-u1db_0.1.4-0ubuntu1_all.deb. This package is present in the folder '/var/www/eyeos/eyeos/packages'.
    </div>
    <div style="margin-top:10px;margin-bottom:0px">
        &nbsp;&nbsp;&nbsp;&nbsp;# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb
    </div>  
    <div style="margin:0 auto">
        <img src="../img/Step1_u1dbPython.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:0px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install python-u1db
    </div>  
    <div style="margin:0 auto">
        <img src="../img/Step2_u1dbPython.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:0px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get -f install
    </div>  
    <div style="margin:0 auto">
        <img src="../img/Step3_u1dbPython.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:0px">
        &nbsp;&nbsp;&nbsp;&nbsp;# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb
    </div>  
    <div style="margin:0 auto">
        <img src="../img/Step4_u1dbPython.jpg" />
    </div>
</p>
<br>
<h3><a name=Pasos>Step by step</a></h3>
<hr>
<div style="margin-bottom:10px;font-family:verdana;font-size:12px;">
    <div style="margin-top:10px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;Check the following files are present in the folder '/var/www/eyeos/serverU1DB':
    </div>
    <div style="margin:0 auto">
        <img src="../img/ListaService.jpg" />
    </div>
    <div style="margin-top:10px;margin-bottom:0px">
        &nbsp;&nbsp;&nbsp;Then run, with administrator privileges, the script 'installServer.sh':
    </div>
    <div style="margin-top:20px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;# ./installServer.sh
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step1_InstallServers.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;During this script execution two services configuration will be requested. This services are:
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:15px">
        &minus;&nbsp;U1DB server. The listening port and the directory where the databases are stored will be asked. If no data is entered, by default, it is configured the port 9000 and the directory /var/lib/u1db/.
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step2_InstallServers.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:15px">
        &minus;&nbsp;Oauth server.  It is needed to configure two things: Mongodb and the server itself.
For the Mongodb the IP, connection port and the database's name are requested. If no data are entered, by default is localhost, 27017 and oauth.  For the Oauth server IP, listening port and  the access token's expiration time are requested. If no data are entered, by default is localhost, 9000 and 24 hours. 
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step3_InstallServers.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        The Oauth server's ip and listening port should be remembered to configure the calendars' synchronization into eyeOS platform.
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        Once the installation is completed, it should be verified that the services are running:
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        # service serverOauth status
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step4_InstallServers.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        # service serverU1DB status
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step5_InstallServers.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        This services generate log files into '/var/log/' with corresponding names serverOauth.log and serverU1DB.log.
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        Finally, make a connection to the database entered previously in the Oauth server's installation (mongodb section), to insert the consumer and request token of the eyeOS consumer.
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # mongo oauth (or the db name entered)
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step1_ConfMongo.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        Enter the consumer following the structure used in the next example, in which you can only change the settings 'key' and 'secret':
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # db.collection.insert({“type”:”consumer”,”key”:”eyeos”,”secret”:”secreteyeos”})
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step2_ConfMongo.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        These data are used to configure the calendars' synchronization into eyeOS platform.
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        Enter the request token following the structure used in the next example, in which you can only change the settings 'key' and 'secret'. The consumerKey must be the same entered in the previous step:
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # db.collection.insert({“type”:”requestToken”,”consumerKey”:”eyeos”,”key”:”requestEyeos”, ”secret”:”requestSecretEyeos”})
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step3_ConfMongo.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        Check the data entered using the sentence 'find':
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # db.collection.find()
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step5_ConfMongo.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        It is possible to perform a selective search from a key, as eg:
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # db.collection.find({“type”:”consumer”})
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step6_ConfMongo.jpg" />
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:25px">
        If you want to remove an existing record you must use the sentence 'remove':
    </div>
    <div style="margin-top:20px;margin-bottom:10px;margin-left:35px">
        # db.collection.remove({“type”:”consumer”}) 
    </div>
    <div style="margin:0 auto">
        <img src="../img/Step7_ConfMongo.jpg" />
    </div>
</div>

