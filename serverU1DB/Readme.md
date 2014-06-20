Installation U1DB server & Oauth server
================================================================

**Table contents**

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Step by step](#step-by-step)


## Introduction

The installation will be done on a Ubuntu 12.04 operating system, you can choose a higher version, but then the requirements might be different.

### Requirements

+    **MongoDb**

    \# sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
    
    ![](../img/Step1_ImportKey.jpg)

    \# echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | sudo tee /etc/apt/sources.list.d/mongodb.list
    
    ![](../img/Step2_CrearListFile.jpg)

    \# apt-get update
    
    ![](../img/Step3_ReloadPkgDatabase.jpg)        

    \# apt-get install mongodb-org=2.6.0 mongodb-org-server=2.6.0 mongodb-org-shell=2.6.0 mongodb-org-mongos=2.6.0 mongodb-org-tools=2.6.0
    
    ![](../img/Step4_InstalarPkgMongo.jpg)                

    \# echo "mongodb-org hold" | sudo dpkg –set-selections      
    \# echo "mongodb-org-server hold" | sudo dpkg --set-selections      
    \# echo "mongodb-org-shell hold" | sudo dpkg --set-selections      
    \# echo "mongodb-org-mongos hold" | sudo dpkg --set-selections      
    \# echo "mongodb-org-tools hold" | sudo dpkg –set-selections
    
    ![](../img/Step5_Fijarversion.jpg)

    \# mongo –version
    
    ![](../img/Step6_Version.jpg)

    \# service mongod status
    
    ![](../img/Step7_VerifyStatusService.jpg)        
  
 
+   **PyMongo**

    \# apt-get install build-essential python-dev  
    
    ![](../img/Step1_InstallPymongo.jpg)
            
    \# apt-get install python-pip  
    
    ![](../img/Step2_InstallPymongo_2.jpg)

    \# pip install pymongo  
    
    ![](../img/Step3_InstallPymongo.jpg)
    
    

+   **Module Oauth**

    \# pip install oauth  
    
    ![](../img/Step1_OauthPython.jpg)


+   **U1DB**

    Install the package python-u1db_0.1.4-0ubuntu1_all.deb. This package is present in the folder '/var/www/eyeos/eyeos/packages'.  
    
    \# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb  
    
    ![](../img/Step1_u1dbPython.jpg)
    
    \# apt-get install python-u1db  
    
    ![](../img/Step2_u1dbPython.jpg)
    
    \# apt-get -f install  
    
    ![](../img/Step3_u1dbPython.jpg)

    \# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb  
    
    ![](../img/Step4_u1dbPython.jpg)


# Step by step

Check the following files are present in the folder '/var/www/eyeos/serverU1DB':  
    
![](../img/ListaService.jpg)

Then run, with administrator privileges, the script 'installServer.sh':  

\# ./installServer.sh  

![](../img/Step1_InstallServers.jpg)

During this script execution two services configuration will be requested. This services are:  

+   **U1DB server**. The listening port and the directory where the databases are stored will be asked. If no data is entered, by default, it is configured the port 9000 and the directory /var/lib/u1db/.  

    ![](../img/Step2_InstallServers.jpg)

+   **Oauth server**. It is needed to configure two things: Mongodb and the server itself.
For the Mongodb the IP, connection port and the database's name are requested. If no data are entered, by default is localhost, 27017 and oauth.  For the Oauth server IP, listening port and  the access token's expiration time are requested. If no data are entered, by default is localhost, 9000 and 24 hours.

    ![](../img/Step3_InstallServers.jpg)
    
    The Oauth server's ip and listening port should be remembered to configure the calendars' synchronization into eyeOS platform.  

Once the installation is completed, it should be verified that the services are running:  
    
\# service serverOauth status  
    
![](../img/Step4_InstallServers.jpg)
    
\# service serverU1DB status  
    
![](../img/Step5_InstallServers.jpg)

This services generate log files into '/var/log/' with corresponding names serverOauth.log and serverU1DB.log.  

Finally, make a connection to the database entered previously in the Oauth server's installation (mongodb section), to insert the consumer and request token of the eyeOS consumer.  

\# mongo oauth (or the db name entered)  

![](../img/Step1_ConfMongo.jpg)  

Enter the consumer following the structure used in the next example, in which you can only change the settings 'key' and 'secret':  

\# db.collection.insert({“type”:”consumer”,”key”:”eyeos”,”secret”:”secreteyeos”})  

![](../img/Step2_ConfMongo.jpg)

These data are used to configure the calendars' synchronization into eyeOS platform.  

Enter the request token following the structure used in the next example, in which you can only change the settings 'key' and 'secret'. The consumerKey must be the same entered in the previous step:  

\# db.collection.insert({“type”:”requestToken”,”consumerKey”:”eyeos”,”key”:”requestEyeos”, ”secret”:”requestSecretEyeos”})  

![](../img/Step3_ConfMongo.jpg)

Check the data entered using the sentence 'find':  

\# db.collection.find()  

![](../img/Step5_ConfMongo.jpg)

It is possible to perform a selective search from a key, as eg:  

\# db.collection.find({“type”:”consumer”})  

![](../img/Step6_ConfMongo.jpg)

If you want to remove an existing record you must use the sentence 'remove':  

\# db.collection.remove({“type”:”consumer”})  

![](../img/Step7_ConfMongo.jpg)

