Tutorial eyeOS 2.5 OpenSource & CloudSpaces
=====================================================================================

**Table contents**

- [eyeOS 2.5 installation on Ubuntu](#eyeos-2.5-installation-on-ubuntu)
    - [Requeriments](#requeriments)
    - [eyeOS installation](#eyeos-installation)
    - [serverU1DB installation](#serverU1DB-installation)
- [Persistence](#persistence)
    - [Storage/recovering documents](#storage/recovering-documents)
    - [Queries](#queries)
    - [Synchronization](#synchronization)
    - [Implementation of U1DB into eyeOS calendar](#implementation-of-u1db-into-eyeos-calendar)
    - [Implementation of Oauth into eyeOS calendar](#implementation-of-oauth-into-eyeos-calendar)
- [Implementation of StackSync API into eyeOS](#implementation-of-stacksync-api-into-eyeos)
- [Collaborative tool between eyeOS and StackSync](#collaborative-tool-between-eyeos-and-stacksync)
- [Implementation of Share API into eyeOS](#implementation-of-share-api-into-eyeos)


## eyeOS 2.5 installation on Ubuntu

### Requeriments

+   **Apache HTTP Server**

    \# apt-get install apache2  
    \# apache2 -version  
    
    ![](img/apache2_version.jpg)
    
    Type in the navegation bar of the browser http://localhost, and you'll see apache2 page (It works!)  
    
    ![](img/navegador_apache.jpg)
  
  
+   **Mysql**

    \# apt-get install mysql-server mysql-client  
    \# mysql --version  
    
    ![](img/msyql_version.jpg)
    
    Remember the password of database's administrator ('root'), because it'll be requested during the eyeOS 2.5 installation.
    
+   **Php**

    \# apt-get install php5 php5-mysql libapache2-mod-php5 php5-gd php5-mcrypt php5-sqlite php-db php5-curl php-pear php5-dev  
    \# php5 --version  
    
    ![](img/php_version.jpg)
    
    Configure the php.ini.  
  
    \# cd /etc/php5/apache2/  
    \# nano php.ini  
  
    Change the next parameters:  

    +   error_reporting = E_ALL & ~E_NOTICE  
    
        ![](img/phpini_errorreporting.jpg)
    
    +   display_errors = Off  
    
        ![](img/phpini_displayerrors.jpg)
        
    +   max_execution_time = 30  
    
        ![](img/phpini_maxexecutiontime.jpg)
        
    +   max_input_time = 60  
    
        ![](img/phpini_maxinputtime.jpg)
        
    +   memory_limit = 128M  
    
        ![](img/phpini_memorylimit.jpg)
        
    +   post_max_size = 200M  
    
        ![](img/phpini_postmaxsize.jpg)
    
    +   upload_max_filesize = 100M  
    
        ![](img/phpini_uploadmaxfilesize.jpg)
        
    +   allow_url_fopen = On  
        
        ![](img/phpini_allowurlfopen.jpg)
        
    +   disable_functions =  
    
        ![](img/phpini_disablefunctions.jpg)
        
    +   safe_mode = Off  
    
        ![](img/phpini_safemode.jpg)
        
    +   short_open_tag = On  
    
        ![](img/phpini_shortopentag.jpg)
        
    +   magic_quotes_runtime = Off  
    
        ![](img/phpini_magicquotesruntime.jpg)
        
    +   file_uploads = On
    
        ![](img/phpini_fileuploads.jpg)
        
+   **Java**

    \# apt-get install python-software-properties  
    \# add-apt-repository ppa:webupd8team/java  
    \# apt-get update  
    \# apt-get install oracle-java7-installer  
    \# update-alternatives --config java  
    \# java -version  
    
    ![](img/apache2_version.jpg)

+   **OpenOffice**

    \# apt-get install openoffice.org  
    
+   **Python**

    \# apt-get install python-support python-simplejson python-uno recoll zip unzip libimage-exiftool-perl  
    \# pip install requests requests_oauthlib  
    
+   **Curl SSL**

    If you don't have SSL certificate get it by typing:  
    \# wget http://curl.haxx.se/ca/cacert.pem  
      
    Open the php.ini and introduce the path where the certificate to make requests to secure url is located.  
    \# cd /etc/php5/apache2/  
    \# nano php.ini  
    ....  
    curl.cainfo='/root/cacert.pem'

+   **Uploadprogress**

    \# apt-get install make  
    \# pecl install uploadprogress  
    
    ![](img/uploadprogress_install.jpg)
    
    \# nano /etc/php5/apache2/php.ini  (Add the last two lines) .  
    
    ![](img/phpini_uploadprogress.jpg)

+   **Sendmail**

    \# apt-get install sendmail  
    \# nano /etc/hosts (Add localhost.localdomain to IP 127.0.0.1)  
    
    ![](img/step2_sendmail.jpg)
    
    \# sendmailconfig (Confirmar todas las preguntas)  
    
    ![](img/step3_sendmail.jpg)
  
+   **Apache**

    Configure mod-rewrite in apache:  
    
    \# a2enmod rewrite  

    ![](img/apache_config.jpg)

    \# nano /etc/apache2/sites-available/default  

    Change into <Directory /var/www/\> the parameter AllowOverride to All  

    ![](img/apache_sitesavailable_allowoverwrite.jpg)

    Restart the apache service  

    \# /etc/init.d/apache2 restart  

    ![](img/apache_reiniciar.jpg)


### eyeOS installation

Download the code into /var/www  

\# cd /var/www/  
\# git clone https://github.com/cloudspaces/eyeos-u1db eyeos  

Change the eyeos directory's permissions  

\# chown -R www-data.www-data eyeos  
\# chmod -R 755 eyeos  

Add to DocumentRoot the eyeos directory:  

\# nano /etc/apache2/sites-available/default  

![](img/DocumentRoot_addEyeos.jpg)

\# service apache2 restart  

Install python-u1db package:  

\# cd eyeos/eyeos/packages/  
\# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb  

If you have dependency problems during package installation follow the next steps:  

\# apt-get install python-u1db  
\# apt-get -f install  
\# dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb  

Get into mysql and create the schema 'eyeos'. As showed bellow:  

\# mysql -uroot -p&lt;password&gt;  
\> create database eyeos;

![](img/mysql_databaseeyeos.jpg)

Open into the browser the eyeOS configuration screen (http://localhost/install)  

![](img/eyeos_config_navegador.jpg)

Select 'Install eyeOS 2 on my server'  

![](img/eyeos_listrequisitos.jpg)

![](img/eyeos_listrequisitos2.jpg)

![](img/eyeos_listrequisitos3.jpg)

The error "SQLite extension not installed" can be ignored.

Once all requirements are listed, go on with the installation selecting 'Continue with the installation'.

![](img/eyeos_configdb.jpg)

Introduce user and password of mysql database's administrator (root), and in addition, the eyeOS platform root user's password.

Once configured the database and eyeOS user, go on with the installation selecting 'Continue with the installation'.

![](img/eyeos_config_end.jpg)

After the installation is finished select 'Go to my new eyeOS' to see the eyeOS 2.5 platform's login screen.

![](img/eyeos_login.jpg)

The url of StackSync resources requests can be configured in the file settings.php

\# cd /var/www/eyeos/  
\# nano settings.php

![](img/token_oautUrl.jpg)


### serverU1DB installation

Follow the steps explained in the Readme.md file, located in /var/www/eyeos/serverU1DB. The serverU1DB can be installed in the same machine than eyeOS or in another independent machine.

If installed in another independent machine the directories /var/www/eyeos/serverU1DB and /var/www/eyeos/eyeos/package must be copied in that machine.


## Persistence

EyeOS calendar saves the events into a U1DB database.

![](img/persistencia.jpeg)

U1DB is an database API to synchronize JSON documents databases created by Canonical. Its allows that applications can storage documents and synchronize then between machines and devices. U1DB is a database design to work in every enviroment, offering a backup of platform's native data. This means that can be use in differents platforms, with differents languages, with backup and synchronization between all of them.

API U1DB has three different parts: storage/recover documents, queries and synchronization. Next are detailed the functions that are used and a short explanation of operation.


### Storage/recovering documents

U1DB storages documents, basically any information that can be expressed in JSON.


**created_doc_from_json(**_json,doc\_id=None_**)**

Creates a document from JSON data.

Alternatively can be especified the document's identifier. The identifier mustn't exist in the database. If database especifies a maximum document size and the document exceeds, the exception DocumentTooBig will be thrown.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>json</b> – JSON data</li>
                    <li><b>doc-id</b> – Optional document identificator</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Document</td>
        </tr>
    </table>
</div>


**put_doc(**_doc_**)**

Document update. If database especifies a maximum document size and the document exceeds, the exception DocumentTooBig will be thrown.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>doc</b> – Document with new content</td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Document</td>
        </tr>
    </table>
</div>


**set_json(**_json_**)**

Updates document's JSON data.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>json</b> – JSON data</td>
        </tr>
    </table>
</div>


**delete_doc(**_doc_**)**

Mark a document as deleted.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>doc</b> – Document to delete</td>
        </tr>
    </table>
</div>


### Queries

Quering in U1DB is performed by means of indexes. To recover some documents from the database, based on some criteria, first an index must be created and then get the data using it.

**create_index(**_index\_name,\*index_expressions_**)**

Creates an index that will be used to make queries in the database. The creation of an existing index  will not throw any exception. The creation of an existing index with different index_expressions than the original one will throw an exception.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>index_name</b> – Unique index identifier</li>
                    <li><b>index_expressions</b> – Keys used in future queries</li>
                </ul>
                <br><br>
                <span>Examples: “nameKey” o “nameKey.nameSubKey”</span>
            </td>
        </tr>
    </table>
</div>


**get_from_index(**_index\_name,\*key\_values_**)**

Returns the documents that match the specified keys.  It must be specified the same number of values than keys defined in the index.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>index_name</b> – Name of index</li>
                    <li><b>key_values</b> – Values to seek. Ex: if index1 has 3 fields, then get_from_index(index1, val1, val2, val3)</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">List of documents</td>
        </tr>
    </table>
</div>


### Synchronization

U1DB is a synchronized database.  An U1DB database can be used both as client or server, where one or several clients can be synchronized with the server.

The synchronization between server and client provides the update of both, so that they have got the same data. The data are saved into local U1DB no matter if online or offline, and then, the synchronization takes place when it's online.

![](img/Permanencia.jpeg)


**sync(**_url,creds=None,autocreate=True_**)**

Synchronizes documents with a remote backup by means of an url.


<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>url</b> – url of remote backup the synchronization is going to take place with</li>
                    <li><b>creds</b> – (Optional). Credentials to authorize the operation with the server.  For instance using the credentials to identificate by means of OAuth:<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{ ‘consumer_key’: ..., ‘consumer_secret’: ...,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;‘token_key’: ..., ‘token_secret’: ...<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    }
                    </li>
                    <li><b>autocreate</b> – If the value is True, is created database if it not exists. If the value is False, the database doesn't exists and it don't synchronize.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">local_gen_before_sync – It's a local generation key to control the synchronization. It's useful to use with the function whats_changed, if an aplication wants to know the documents that have changed during synchronization</td>
        </tr>
    </table>
</div>


### Implementation of U1DB into eyeOS calendar

EyeOS storages the user's calendars and events into U1DB synchronized database. The database management is performed by means of a Python script called Protocol.py. This script handles the synchronization with the U1DB server and is located in '/var/www/eyeos/eyeos/extern/u1db/', to configure it the “settings.py” file must be modified in the following values:

![](img/Settings_Calendars.jpg)

<div style="margin-top:45px;margin-bottom:10px;margin-left:40px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td width="15%">Server</td>
            <td width="45%" style="padding-left:15px">IP address where the Oauth server is active</td>
        </tr>
        <tr>
            <td>Port</td>
            <td style="padding-left:15px">Port where the Oauth server is active</td>
        </tr>
        <tr>
            <td colspan="2">urls</td>
        </tr>
        <tr>
            <td valign="middle">CALLBACK_URL</td>
            <td style="padding-left:15px">Replace IP and port with the values stablished in the previous parameters (server and port)</td>
        </tr>
        <tr>
            <td colspan="2">consumer</td>
        </tr>
        <tr>
            <td valign="middle">key</td>
            <td style="padding-left:15px">Included when mongodb was configured in the Oauth server installation</td>
        </tr>
        <tr>
            <td valign="middle">secret</td>
            <td style="padding-left:15px">Included when mongodb was configured in the Oauth server installation</td>
        </tr>
    </table>
</div>

Next is detailed the APIs contained in the script Protocol.py:

**selectCalendar(**_data_**)**

Get all calendars of the user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>data</b> – Contains the type and the user.<br>
                Example: {“type”:”calendar”,”user_eyeos”:”eyeos”}
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Array in JSON format<br>
                Example:<br>
                [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”,”description”:”personal calendar”,”timezone”: 0,”status”:”NEW”}]
            </td>
        </tr>
    </table>
</div>


**insertCalendar(**_lista_**)**

Introduce the new calendar created by the eyeOS user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>lista</b> – Contains an array of the calendars pending to insert.<br>
                Example:<br>
                [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”,”description”:”personal calendar”,”timezone”:0,”status”:”NEW”}]
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">'true' or exception in case of error</td>
        </tr>
    </table>
</div>


**deleteCalendar(**_lista_**)**

Identifies the status of the calendar as deleted (STATUS=”DELETED”) of a particular eyeOS user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>lista</b> – Contains an array of the calendars identied as deleted.<br>
                Example:<br>
                [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”}]
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">'true' or exception in case of error</td>
        </tr>
    </table>
</div>


**selectEvent(**_type,user,calendarId_**)**


<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>type</b> – Fixed value “event”</li>
                    <li><b>user</b> – EyeOS user</li>
                    <li><b>calendarId</b> – Name of the calendar</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                Array in JSON format<br>
                Example:<br>
                [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”NEW”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
            </td>
        </tr>
    </table>
</div>


**insertEvent(**_self,lista_**)**

Introduce a new event into the especified user's calendar.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>lista</b> – Contains an array with the news events.<br>
                Example:<br>
                [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”NEW”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">'true' or exception in case of error</td>
        </tr>
    </table>
</div>


**deleteEvent(**_lista_**)**

Identifies the status of the event as deleted (STATUS=”DELETED”) of a particular eyeOS user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>lista</b> – Contains an array with the events identified as deleted.<br>
                Example:<br>
                [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”DELETED”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">'true' or exception in case of error</td>
        </tr>
    </table>
</div>


**updateEvent(**_lista_**)**

Update the data of an event.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>lista</b> – Contains an array with the events to update.<br>
                Example:<br>
                [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”CHANGED”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Examen”, “location”: “Tarragona”, “description”: “Llevar libro”}]
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">'true' or exception in case of error</td>
        </tr>
    </table>
</div>


To see correctly the calendars into eyeOS platform it must be respected the estructure of the calendars and the events, also its indexes that are the next:  
- calendar: type, user_eyeos, name and status.  
- events: type, user_eyeos, calendar and status.  

To use the Python APIs into eyeOS it has been generated the framework Store, containing some APIs with an uniform structure:

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>type</b> – Python API name</li>
                    <li><b>lista</b> – Python API parameters</li>
                    <li><b>credentials</b> – Credentials for the identification into synchronization process</li>
                </ul>
            </td>
        </tr>
    </table>
</div>

Next these APIs are listed:

**synchronizeCalendars(**_user_**)**

Synchronize all calendars of the user connected in eyeOS platform.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>user</b> – Contains the eyeOS user's identifier and name
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"selectCalendar" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"calendar",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">List of calendars</td>
        </tr>
    </table>
</div>


**insertCalendars(**_user,calendar_**)**

Create a new calendar.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <ul>
                    <li><b>user</b> – User name</li>
                    <li><b>calendar</b> – It's an object that contains name, description and timezone</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"insertCalendar" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"calendar",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status": "NEW"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"description": "personal calendar"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timezone": 0<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">True or in case of error null</td>
        </tr>
    </table>
</div>


**deleteCalendars(**_user,calendar_**)**

Delete an existing calendar.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <ul>
                    <li><b>user</b> – User name</li>
                    <li><b>calendar</b> – Calendar name</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"deleteCalendar" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"calendar",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">True or in case of error null</td>
        </tr>
    </table>
</div>


**synchronizeCalendar(**_calendarId,user_**)**

Synchronize all the events of the especified calendar of the user connected in eyeOS platform.


<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <ul>
                    <li><b>calendarId</b> – Calendar identifier in eyeOS</li>
                    <li><b>user</b> – User name</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"selectEvent" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"event",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">List of events</td>
        </tr>
    </table>
</div>


**createEvent(**_event_**)**

Create a new event

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>event</b> – Contains the event's U1DB estructure
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"insertEvent" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"event",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status": "NEW"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"isallday": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timestart": "201419160000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timeend": "201419170000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"repetition": "None"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finaltype": "1"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finalvalue": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"subject": "Visita Médico"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"location": "Barcelona"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"description": "Llevar justificante"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">True or in case of error null</td>
        </tr>
    </table>
</div>


**deleteEvent(**_event_**)**

Delete an existing event.


<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>event</b> – Contains the event's U1DB structure
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"deleteEvent" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"event",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status": "DELETED"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"isallday": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timestart": "201419160000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timeend": "201419170000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"repetition": "None"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finaltype": "1"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finalvalue": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"subject": "Visita Médico"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"location": "Barcelona"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"description": "Llevar justificante"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">True or in case of error null</td>
        </tr>
    </table>
</div>


**updateEvent(**_event_**)**

Update an existing event.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>event</b> – Contains the event's U1DB structure
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"updateEvent" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"lista":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"event",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user_eyeos": "eyeos"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "personal"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status": "CHANGED"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"isallday": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timestart": "201419160000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"timeend": "201419170000"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"repetition": "None"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finaltype": "1"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"finalvalue": "0"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"subject": "Visita Médico"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"location": "Barcelona"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"description": "Llevar justificante"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">True or in case of error null</td>
        </tr>
    </table>
</div>


When the eyeOS calendar application starts the calendar and events synchronization processes are executed.

The calendar sinchronization is performed every 20 seconds, if changes are detected the calendar list is refreshed and the wait time starts again.

The event sinchronization is performed every 10 seconds, if changes are detected the events of the period showed on the screen is refreshed and the wait time starts again.


### Implementation of Oauth into eyeOS calendar

To synchronize eyeOS calendars and  events is needed authentication with the server using the Oauth protocol.

The Oauth server permits access to only one protected resource, that is the U1DB server.

The eyeOS platform implements the Credentials.py and Protocol.py scripts that contain the necessary APIs to comunicate with the Oauth server.

Next is shown the communication dialog:

![](img/DiagramaServerOauth.jpeg)

Step 1:

API getRequestToken() get the consumer key and consumer secret from the settings file. Performs the call to the Oauth server by means of the “request_token” url.

The Oauth server get the consumer key from mongodb database, an compares it with the consumer received from getRequestToken(). If it isn't correct, return an error and otherwise, performs a new search in mongodb, with the consumer key to get the request token.

The Oauth server answers to the eyeOS call providing the request token and the access verification to ask for the access token.

The eyeOS platform storage the request token and the verifier in the session variables in order not to repeat the process previously explained.

Step 2:

API getAccessToken(token,verifier) performs the Oauth server call by means of “access_token” url.

The Oauth server get the consumer and the request token from mongodb database, an compares it with the data received from getAccessToken(). If it isn't correct, returns an error and otherwise, performs a new search, with consumer key and request token key in mongodb to get the access token. If no data are adquired or the adquired data are expired, a new access token must be generated and stored it in mongodb, that  will invalidate any previous token.

The Oauth server ask for the eyeOS call providing the access token.

Step 3:

API protocol(params) performs the Oauth server call by means of the U1DB synchonization API.

The Oauth server get the access token from mongodb database using the consumer and token received. If they aren't correct returns an error and otherwise proceed to perform the synchronization with the U1DB server.

The step 1 is only applied in case that a request token hasn't been requested during the eyeOS user session.


## Implementation of StackSync API into eyeOS

EyeOS accesses the private document storage of a cloudspace user using StackSync API's from the application “File Manager”.

The StackSync API calls are made via a Python script called OAuthCredentials.py. This script can be configured from the file “settings.py” located at '/var/www/eyeos/eyeos/extern/u1db/'. Next are shown the values to modify:

![](img/settings_connectStacksync.jpg)

<div style="margin-top:45px;margin-bottom:10px;margin-left:40px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td colspan="2">Urls</td>
        </tr>
        <tr>
            <td valign="middle">CALLBACK_URL</td>
            <td style="padding-left:15px">Callback url when the user has been validated in StackSync</td>
        </tr>
        <tr>
            <td colspan="2">consumer</td>
        </tr>
        <tr>
            <td valign="middle" width="10%">key</td>
            <td style="padding-left:15px">Key provided by StackSync</td>
        </tr>
        <tr>
            <td valign="middle">secret</td>
            <td style="padding-left:15px">Secret provided by StackSync</td>
        </tr>
    </table>
</div>


Next are detailed the APIs contained in the script OAuthCredentials.py:

**getRequestToken(**_oauth_**)**

Ask for the consumer eyeos's request token.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use REQUEST_TOKEN_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                Plaintext
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key, secret and CALLBACK_URL from the configuration file
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Key and secret of the request token or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"oauth_token":"token1234","oauth_token_secret":"secret1234"}<br>
            {"error":401, "description": "Authorization required."}
            </td>
        </tr>
    </table>
</div>

**getAccessToken(**_oauth_**)**

Ask for the consumer eyeos's access token from the storaged request token.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use ACCESS_TOKEN_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                Plaintext
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the request token and verifier.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Key and secret of the access token or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"oauth_token":"token1234","oauth_token_secret":"secret1234"}<br>
            {"error":401, "description": "Authorization required."}
            </td>
        </tr>
    </table>
</div>

**getMetadata(**_oauth,file,id,contents_**)**

Get the metadata of a directory and/or files.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token<br>
                <b>file</b> – True, if it is a file or False, is it a directory<br>
                <b>Id</b> – Element identifier number (directory or file)<br>
                <b>contents</b> – True, to list the metadatas depending on the id or None, to inactive the list. Used when 'Id' is a directory. (Optional)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata/s of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"filename":"clients",<br>
            &nbsp;"id":9873615,<br>
            &nbsp;"status":"NEW",<br>
            &nbsp;"version":1,<br>
            &nbsp;"parent_id":”null”,<br>
            &nbsp;"user":"eyeos",<br>
            &nbsp;"client_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;"server_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;“is_root”: false,<br>
            &nbsp;"is_folder":true,<br>
            &nbsp;"contents":[{<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"Client1.pdf",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"size":775412,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"mimetype":"application/pdf",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status":"NEW",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":-348534824681,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user":"eyeos",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"client_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"server_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_root":false,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_folder":false<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
            }<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**updateMetadata(**_oauth,file,id,name,parent_**)**

Update the metadata of the element in the actions rename and move.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                PUT
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token<br>
                <b>file</b> – True, if it is a file or False, is it a directory<br>
                <b>Id</b> – Element identifier number (directory or file)<br>
                <b>name</b> – Element name<br>
                <b>parent</b> – Id of the destination directory (Optional)               
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"status": "CHANGED",<br>
            &nbsp;"is_folder": false,<br>
            &nbsp;"user": "eyeos",<br>
            &nbsp;"server_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"id": 32565632156, <br>
            &nbsp;"size": 775412,<br>
            &nbsp;"mimetype": "application/pdf",<br>
            &nbsp;"client_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"filename": "Client1.pdf",<br>
            &nbsp;"parent_id": 789456,<br>
            &nbsp;“is_root”: false,<br>
            &nbsp;"version": 3}<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**createMetadata(**_oauth,file,name,parent,path_**)**

Create a new element (file or directory)

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                POST
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OAuthRequest. Contains the values key and secret from the configuration file. Also the access token.<br>
                <b>file</b> – True, if it is a file or False, is it a directory<br>
                <b>name</b> – Element name<br>
                <b>parent</b> – Id of the destination directory (Optional)<br>
                <b>path</b> – Absolute file path (Optional)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"status": "NEW",<br>
            &nbsp;"is_folder": false,<br>
            &nbsp;"user": "eyeos",<br>
            &nbsp;"server_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"id": 32565632155, <br>
            &nbsp;"size": 775412,<br>
            &nbsp;"mimetype": "application/pdf",<br>
            &nbsp;"client_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"filename": "Client.pdf",<br>
            &nbsp;"parent_id": 789456,<br>
            &nbsp;“is_root”: false,<br>
            &nbsp;"version": 1}<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**uploadFile(**_oauth,id,path_**)**

Upload the contents of an existing file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                PUT
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file<br>
                <b>id</b> – File identifier number<br>
                <b>path</b> – Absolute file path (Optional)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**downloadFile(**_oauth,id,path_**)**

Download the contents of an existing file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token<br>
                <b>id</b> – File identifier number<br>
                <b>path</b> – Absolute file path
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            True or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**deleteMetadata(**_oauth,file,id_**)**

Delete an element (file or directory)

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                DELETE
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token.<br>
                <b>file</b> – True, if it is a file or False, is it a directory<br>
                <b>Id</b> – Element identifier number (directory or file)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"status": "DELETED",<br>
            &nbsp;"is_folder": false,<br>
            &nbsp;"user": "eyeos",<br>
            &nbsp;"server_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"id": 32565632156, <br>
            &nbsp;"size": 775412,<br>
            &nbsp;"mimetype": "application/pdf",<br>
            &nbsp;"client_modified": "2013-03-08 10:36:41.997",<br>
            &nbsp;"filename": "Client1.pdf",<br>
            &nbsp;"parent_id": 789456,<br>
            &nbsp;“is_root”: false,<br>
            &nbsp;"version": 3}<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**getFileVersions(**_oauth,id_**)**

Get the list of versions of a specific file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token.<br>
                <b>Id</b> – File identifier number
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata of the element or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"status": "CHANGED",<br>
            &nbsp;"is_folder": false,<br>
            &nbsp;"chunks": [],<br>
            &nbsp;"id": "155",<br>            
            &nbsp;"mimetype": "text/plain",<br>
            &nbsp;"versions": [<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{"status":"CHANGED",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_folder": false,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"chunks": [],<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id": "155",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"size": 61,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"mimetype": "text/plain",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"checksum": 2499810342,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"modified_at": "2014-06-20 10:11:11.031",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"welcome.txt",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":"null",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"version":"2"},<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{"status":"NEW",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_folder": false,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"chunks": [],<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id": "155",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"size": 59,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"mimetype": "text/plain",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"checksum": 2499810342,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"modified_at": "2014-06-20 10:11:11.031",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"welcome.txt",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":"null",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"version":"1"}],<br>
            &nbsp;"checksum": 2499810342,<br>
            &nbsp;"modified_at": "2014-06-20 10:11:11.031",<br>
            &nbsp;"filename": "welcome.txt",<br>
            &nbsp;"parent_id": "null",<br>
            &nbsp;"version":2}<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>

**getFileVersionData(**_oauth,id,version,path_**)**

Download the contents of a specific version of an existing file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL from the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>oauth</b> – Object OauthRequest. Contains the values key and secret from the configuration file. Also the access token.<br>
                <b>id</b> – File identifier number<br>
                <b>version</b> – Version pending to download<br>
                <b>path</b> – Absolute file path
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            True or in case of error returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>


Next are listed the APIs contained in the framework Store, which comunicate with the Python APIs previously explained:

**getRequestToken()**

Ask for the consumer eyeos's request token.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                No parameters
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Object token or in case of error null<br>
            Example:<br>
            {<br>
            &nbsp;"key": "token1234",<br>
            &nbsp;"secret": "secret1234"<br>
            }
            </td>
        </tr>
    </table>
</div>

**getAccessToken(**_token_**)**

Ask for the consumer eyeos's access token from the request token.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the request token and user verification
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"verifier":"userVerifier"<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Object token or in case of error null<br>
            Example:<br>
            {<br>
            &nbsp;"key": "access1234",<br>
            &nbsp;"secret": "access1234"<br>
            }
            </td>
        </tr>
    </table>
</div>

**getMetadata(**_token,id,path,user_**)**

Get the metadatas of the current element. Generate its file and/or directory structure in eyeOS.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>id</b> – Element identifier number in StackSync<br>
                <b>path</b> – eyeOS path<br>
                <b>user</b> – User identifier in eyeOS                
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"get",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":false,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":155241412,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contents":true<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata or in case of error returns an error structure:<br>
            - error: Error number<br>
            Example:<br>
            {"filename":"clients",<br>
            &nbsp;"id":155241412,<br>
            &nbsp;"status":"NEW",<br>
            &nbsp;"version":1,<br>
            &nbsp;"parent_id":”null”,<br>
            &nbsp;"user":"eyeos",<br>
            &nbsp;"client_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;"server_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;“is_root”: false,<br>
            &nbsp;"is_folder":true,<br>
            &nbsp;"contents":[{<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"Client1.pdf",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"size":775412,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"mimetype":"application/pdf",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"status":"NEW",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":155241412,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user":"eyeos",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"client_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"server_modified":"2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_root":false,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"is_folder":false<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
            }<br>
            {"error":401}
            </td>
        </tr>
    </table>
</div>

**getSkel(**_token,file,id,metadatas,path,pathAbsolute_**)**

Get recursively the metadatas depending of the current element. Used in the copy and move action in eyeOS.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>file</b> – True, if it is a file or False, if it is a directory<br>                
                <b>id</b> – Element identifier number in StackSync<br>
                <b>metadatas</b> – Metadats accumulative array<br>                
                <b>path</b> – Current element's relative path<br>
                <b>pathAbsolute</b> – eyeOS path               
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"get",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":false,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":155241412,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contents":true<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadatas array or in case of error returns an error structure:<br>
            - error: Error number<br>
            Example:<br>
            [{"filename":"Client1.pdf",<br>
            &nbsp;&nbsp;"id":32565632156,<br>
            &nbsp;&nbsp;"size":775412,<br>            
            &nbsp;&nbsp;"mimetype":"application/pdf",<br>
            &nbsp;&nbsp;"status":"NEW",<br>
            &nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;"parent_id":155241412,<br>
            &nbsp;&nbsp;"user":"eyeos",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;"is_root":false,<br>            
            &nbsp;&nbsp;"is_folder":false},<br>
            &nbsp;{"filename":"clients",<br>
            &nbsp;&nbsp;"id":155241412,<br>
            &nbsp;&nbsp;"status":"NEW",<br>
            &nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;"parent_id":"null",<br>
            &nbsp;&nbsp;"user":"eyeos",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;"is_root":false,<br>
            &nbsp;&nbsp;"is_folder":true}]<br>
            {"error":401}
            </td>
        </tr>
    </table>
</div>

**createMetadata(**_token,user,file,name,parent_id,path,pathAbsolute_**)**

Create a new file or directory.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>user</b> – User identifier in eyeOS<br>
                <b>file</b> – True, if it is a file or False, if it is a directory<br>
                <b>name</b> – Element name<br>
                <b>parent_id</b> – Id of the destination directory<br>
                <b>path</b> – Current element's relative path<br>
                <b>pathAbsolute</b> – Absolute path. Mandatory when the element is a file
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"create",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":true,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"Client.pdf",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":254885,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"path":"/home/eyeos/Documents/Client.pdf"<br>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadata or in case of error returns an error structure:<br>
            - error: Error number<br>
            Example:<br>
            {"filename":"Client.pdf",<br>
            &nbsp;&nbsp;"id":32565632111,<br>
            &nbsp;&nbsp;"size":775412,<br>            
            &nbsp;&nbsp;"mimetype":"application/pdf",<br>
            &nbsp;&nbsp;"status":"NEW",<br>
            &nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;"parent_id":254885,<br>
            &nbsp;&nbsp;"user":"eyeos",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;"is_root":false,<br>            
            &nbsp;&nbsp;"is_folder":false}<br>
            {"error":401}
            </td>
        </tr>
    </table>
</div>

**downloadMetadata(**_token,id,path_**)**

Download the contents of a file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>id</b> – File identifier number in Stacksync<br>
                <b>path</b> – Absolute path
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"download",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632111,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"path":"/home/eyeos/Documents/Client.pdf"<br>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Result structure:<br>
            - status: 'OK' correct case or 'KO' error case<br>
            - error: Error number. Only exists in case of error<br>
            Example:<br>
            {"status":"OK"}<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>

**deleteMetadata(**_token,file,id,user_**)**

Delete an existing file or directory.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>file</b> – True, if it is a file or False, if it is a directory<br>
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>user</b> – User identifier in eyeOS
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"delete",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":true,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632111<br>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Result structure:<br>
            - status: 'OK' correct case or 'KO' error case<br>
            - error: Error number. Only exists in case of error<br>
            Example:<br>
            {"status":"OK"}<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>

**renameMetadata(**_token,file,id,name,path,user,parent_**)**

Rename a file or directory.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>file</b> – True, if it is a file or False, if it is a directory<br>
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>name</b> – Element new name<br>
                <b>path</b> – Current element's relative path<br>
                <b>user</b> – User identifier in eyeOS<br>
                <b>parent</b> – Id of the destination directory (Optional)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"update",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":true,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"Client2.pdf",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":155241412<br>             
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Result structure:<br>
            - status: 'OK' correct case or 'KO' error case<br>
            - error: Error number. Only exists in case of error<br>
            Example:<br>
            {"status":"OK"}<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>

**moveMetadata(**_token,file,id,pathOrig,pathDest,user,parent,filenameOld,filenameNew_**)**

Move a file or directory.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>file</b> – True, if it is a file or False, if it is a directory<br>
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>pathOrig</b> – EyeOS path in source<br>
                <b>pathDest</b> – EyeOS path in destination<br>
                <b>user</b> – User identifier in eyeOS<br>
                <b>parent</b> – Id of the destination directory<br>
                <b>filenameOld</b> – Element name in the source path<br>                
                <b>filenameNew</b> – Element name in the destination path if destination is different to source (Optional)
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"update",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"file":true,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"filename":"Client2.pdf",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"parent_id":0<br>             
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Result structure:<br>
            - status: 'OK' correct case or 'KO' error case<br>
            - error: Error number. Only exists in case of error<br>
            Example:<br>
            {"status":"OK"}<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>

**listVersions(**_token,id_**)**

Get the list of versions of a specific file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>id</b> – File identifier number in Stacksync
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"listVersions",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Metadatas array or in case of error returns an error structure:<br>
            - error: Error number<br>
            Example:<br>
            [{"name":"Winter2015.jpg",<br>
            &nbsp;&nbsp;"path":"\/documents\/clients\/Winter2015.jpg",<br>
            &nbsp;&nbsp;"id":32565632156,<br>            
            &nbsp;&nbsp;"size":775412,<br>
            &nbsp;&nbsp;"mimetype":"image\/jpg",<br>
            &nbsp;&nbsp;"status":RENAMED,<br>
            &nbsp;&nbsp;"version":3,<br>
            &nbsp;&nbsp;"parent_id":12386548974,<br>
            &nbsp;&nbsp;“user”: "Adrian",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;"enabled":true},<br>            
            &nbsp;{"name":"Winter2015.jpg",<br>
            &nbsp;&nbsp;"path":"\/documents\/clients\/Winter2015.jpg",<br>
            &nbsp;&nbsp;"id":32565632156,<br>            
            &nbsp;&nbsp;"size":7482,<br>
            &nbsp;&nbsp;"mimetype":"image\/jpg",<br>
            &nbsp;&nbsp;"status":CHANGED,<br>
            &nbsp;&nbsp;"version":2,<br>
            &nbsp;&nbsp;"parent_id":12386548974,<br>
            &nbsp;&nbsp;“user”: "Cristian",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997"},<br>
            &nbsp;{"name":"Winter2015.jpg",<br>
            &nbsp;&nbsp;"path":"\/documents\/clients\/Winter2015.jpg",<br>
            &nbsp;&nbsp;"id":32565632156,<br>            
            &nbsp;&nbsp;"size":775412,<br>
            &nbsp;&nbsp;"mimetype":"image\/jpg",<br>
            &nbsp;&nbsp;"status":NEW,<br>
            &nbsp;&nbsp;"version":1,<br>
            &nbsp;&nbsp;"parent_id":12386548974,<br>
            &nbsp;&nbsp;“user”: "Adrian",<br>
            &nbsp;&nbsp;“client_modified”: "2013-03-08 10:36:41.997",<br>
            &nbsp;&nbsp;“server_modified”: "2013-03-08 10:36:41.997"}]<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>

**getFileVersionData(**_token,id,version,path_**)**

Download the contents of a specific version of an existing file.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Contains the access token's key and secret<br>
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>version</b> – Version pending to download<br>
                <b>path</b> – Absolute file path
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                &nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"getFileVersion",<br> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"version":2,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"path":"\/documents\/clients\/Winter2012.jpg",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            Result structure:<br>
            - status: 'OK' correct case or 'KO' error case<br>
            - error: Error number. Only exists in case of error<br>
            Example:<br>
            {"status":"OK"}<br>
            {"status":"KO","error":-1}
            </td>
        </tr>
    </table>
</div>


The user uses the application “File Manager”, to manage his storaged files in StackSync via the previously explained APIs, as shown in the next point:

+ If the eyeOS user doesn't have the StackSync's access token the next screen will be shown.  

    ![](img/Stacksync_1.jpg)

+ If the user selects the option 'No', it will be shown the files structure without access to StackSync.  

    ![](img/Stacksync_exc_1.jpg)

+ Otherwise, when 'Yes' it selected the connection to StackSync is started to get the access token.  

    ![](img/Stacksync_2.jpg)

+ A new window in the browser is opened, where the user is asked to login in StackSync.

    ![](img/Stacksync_3.jpg)

+ Once the user is logged correctly it is shown the redirect screen to eyeOS platform  

    ![](img/Stacksync_4.jpg)

+ The access token is saved attaching it to the current user. From now the user has access to StackSync's files structure.  

    ![](img/Stacksync_5.jpg)


During the previous process some exceptions could be thrown, which are listed bellow:  

+ Communication error.

    ![](img/Stacksync_exc_2.jpg)

+ Timeout to login the StackSync exhausted. This timeout is configurated to 1 minute.  

    ![](img/Stacksync_exc_3.jpg)


The access token is permanently stored in eyeOS and has no expiration. If the user wants to delete the link, must do it from his StackSync intranet, after that, once it is done the first call to StackSync API a 403 error will be received, that indicates access denied.  
  
![](img/Stacksync_exc_4.png)


## Collaborative tool between eyeOS and StackSync

EyeOS allows from the application “File Manager” the next actions: list, insert and delete comments of a specific element, file or directory are located in the personal cloudspace of the user.

EyeOS storages the comments into U1DB synchronized database. The database management is performed by means of a Python script called Commnents.py. This script handles the synchronization with the U1DB server and is located in '/var/www/eyeos/eyeos/extern/u1db/', to configure it the “settings.py” file must be modified in the following values:

![](img/Settings_Calendars.jpg)

<div style="margin-top:45px;margin-bottom:10px;margin-left:40px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td width="15%">Server</td>
            <td width="45%" style="padding-left:15px">IP address where the Oauth server is active</td>
        </tr>
        <tr>
            <td>Port</td>
            <td style="padding-left:15px">Port where the Oauth server is active</td>
        </tr>
        <tr>
            <td colspan="2">urls</td>
        </tr>
        <tr>
            <td valign="middle">CALLBACK_URL</td>
            <td style="padding-left:15px">Replace IP and port with the values stablished in the previous parameters (server and port)</td>
        </tr>
        <tr>
            <td colspan="2">consumer</td>
        </tr>
        <tr>
            <td valign="middle">key</td>
            <td style="padding-left:15px">Included when mongodb was configured in the Oauth server installation</td>
        </tr>
        <tr>
            <td valign="middle">secret</td>
            <td style="padding-left:15px">Included when mongodb was configured in the Oauth server installation</td>
        </tr>
    </table>
</div>

Next is detailed the APIs contained in the script Comments.py:

**getComments(**_id_**)**

Get all the comments of a specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px"><b>id</b> – Element identifier number in Stacksync</td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Array in JSON format or in case of error returns an error structure:<br>
                - error: Error number<br>
                Example:<br>
                [{“id”:”1245789”,”user”:”eyeos”,”time_created”:”20140702124055”,”status”:”NEW”,”text”: ”Comment 1”}]<br>
                [{“error”:-1,”description”:”Error getComments”}]
            </td>
        </tr>
    </table>
</div>

**createComments(**_id,user,text,time_created_**)**

Create a new comment  associated with an specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>user</b> – Username that inserts the comment<br>
                <b>text</b> – Text of comment<br>
                <b>time_created</b> – Date and hour when is created  the comment. Format  YYYYmmddHHMMSS.(Optional)                
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Metadata in JSON format or in case of error returns an error structure:<br>
                - error: Error number<br>
                Example:<br>
                {“id”:”1245789”,”user”:”eyeos”,”time_created”:”20140702124055”,”status”:”NEW”,”text”: ”Comment 1”}<br>
                {“error”:-1,”description”:”Error create comment”}
            </td>
        </tr>
    </table>
</div>

**deleteComments(**_id,user,time_created_**)**

Delete a comment associated a un specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>user</b> – Username that inserts the comment<br>
                <b>time_created</b> – Date and hour when is created  the comment. Format  YYYYmmddHHMMSS.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">Result structure:<br>
                - status:  'OK' correct case or 'KO' error case<br>
                - error: Error number. Only exists in case of error<br>
                Example:<br>
                {“status”: “OK”}<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“status”: “KO”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“error”: -1<br>
                }
            </td>
        </tr>
    </table>
</div>

To use the Python APIs into eyeOS it has been generated the framework Store, containing some APIs with an uniform structure:

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td>
                <ul>
                    <li><b>type</b> – Python API name</li>
                    <li><b>metadata</b> – Python API parameters</li>
                    <li><b>credentials</b> – Credentials for the identification into synchronization process</li>
                </ul>
            </td>
        </tr>
    </table>
</div>

Next these APIs are listed:

**getComments(**_id_**)**

Ask for all the comments of a specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>id</b> – Element identifier number in Stacksync
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"get" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"metadata":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":"124578",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                Array in JSON format or in case of error returns an error structure:<br>
                - error: Error number<br>
                Example:<br>
                [{“id”:”124578”,”user”:”eyeos”,”time_created”:”20140702124055”,”status”:”NEW”,”text”: ”Comment 1”}]<br>
                [{“error”:-1,”description”:”Error getComments”}]
            </td>
        </tr>
    </table>
</div>

**createComments(**_id,user,text_**)**

Create a new comment  associated with an specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>user</b> – Username that inserts the comment<br>
                <b>text</b> – Text of comment
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"create" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"metadata":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":"124578",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user":"eyeos",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"text":"Comment 1",<br>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                Metadata in JSON format or in case of error returns an error structure:<br>
                - error: Error number<br>
                Example:<br>
                {“id”:”124578”,”user”:”eyeos”,”time_created”:”20140702124055”,”status”:”NEW”,”text”: ”Comment 1”}<br>
                {“error”:-1,”description”:”Error create comment”}
            </td>
        </tr>
    </table>
</div>

**deleteComments(**_id,user,time_created_**)**

Delete a comment associated a un specific element of StackSync.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>id</b> – Element identifier number in Stacksync<br>
                <b>user</b> – Username that inserts the comment<br>
                <b>time_created</b> – Date and hour when is created  the comment. Format  YYYYmmddHHMMSS
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td width="60%" style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"type":"delete" ,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"metadata":[{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":"124578",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"user":"eyeos",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"time_created":"20140702124944",<br>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"credentials":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;”oauth”:{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_key”:”eyeos”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“consumer_secret”:”eyeosABC”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_key”:”eyeostoken”,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“token_secret”:”eyeosDEF”<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                Result structure:<br>
                - status:  'OK' correct case or 'KO' error case<br>
                - error: Error number. Only exists in case of error<br>
                Example:<br>
                {“status”: “OK”}<br>
                {<br>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“status”: “KO”,<br>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“error”: -1<br>
                }
            </td>
        </tr>
    </table>
</div>
  
  
## Implementation of Share API into eyeOS

To share folders and their contents among users of the same or different Personal Clouds, the Share tool is implemented in the file manager.  

The user can access the tool by selecting a Personal Cloud directory and then clicking the “Activity” tab in the right toolbar (social bar).  

![](img/FilesManager_Share_1.jpg)

This tab lists all users that can access and manage the active directory. Furthermore it indicates who is the owner of the directory.  

Where the directory is not shared, only the directory owner is displayed.   

![](img/FilesManager_Share_2.jpg)

If the user wants to share or add more users to the sharing list, they must right click on the directory to open a contextual menu and then select the “Share” option. When they select this option, a form appears in which they need to enter the email addresses of the people with whom they want to share the directory. Once the form has been completed, the data is sent to StackSync. If the operation is done successfully, the form closes. When the user accesses the “Activity” tab of the directory again, they will see the new users added to the list.  

![](img/FilesManager_Share_3.jpg)  

![](img/FilesManager_Share_4.jpg)

The list of users sharing the directory is not refreshed, as there is no background process that enables new users to be displayed automatically.  

Directory sharing is implemented in eyeOS according to the diagram below:

![](img/diagrama_Share.jpg)

The user performs an action in the file manager, such as lists the users who have access to the directory. The Manager using the getListUsersShare function retrieves the user’s access token and the id of the directory in StackSync. These values are sent to the API, which is responsible for requesting the resource from StackSync using the getListUsersShare function. It receives the list of users and then it notifies the Manager, which will update the eyeOS interface.  

The getListUsersShare of the Share Manager and the Share API, as well as the other actions performed by the Share tool. Now are described in more detail, respectively:

- **_Share Manager_**

**getListUsersShare(**_token,id_**)**

Gets all users with access to a StackSync directory.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Includes key and secret of the access token<br>
                <b>id</b> – Id of the StackSync element
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"listUsersShare",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                List of users or, in the event of error, returns an error structure:<br>
                - error: Error number<br>
                - description: Error description<br>
                Example:<br>
                [<br>
		&nbsp;&nbsp;&nbsp;{“name”:”tester1”,<br>
		&nbsp;&nbsp;&nbsp;”email”:”tester1@test.com”,<br>
		&nbsp;&nbsp;&nbsp;”modified_at”:”2014-04-11 10:02:33.432”,<br>
		&nbsp;&nbsp;&nbsp;”is_owner”:true},<br>
		&nbsp;&nbsp;&nbsp;{“name”:”tester2”,<br>
		&nbsp;&nbsp;&nbsp;”email”:”tester2@test.com”,<br>
		&nbsp;&nbsp;&nbsp;”modified_at”:”2014-05-30 19:39:21.044”,<br>
		&nbsp;&nbsp;&nbsp;”is_owner”:false},<br>
		&nbsp;&nbsp;&nbsp;{“name”:”tester3”,<br>
		&nbsp;&nbsp;&nbsp;”email”:”tester3@test.com”,<br>
		&nbsp;&nbsp;&nbsp;”modified_at”:”2014-06-06 15:42:41.852”,<br>
		&nbsp;&nbsp;&nbsp;”is_owner”:false},<br>
		]<br>
                {“error”:401,”description”:”Error list members”}
            </td>
        </tr>
    </table>
</div>  
  
  
**shareFolder(**_token,id,list_**)**

Shares a directory with another user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
                <b>token</b> – Includes key and secret of the access token<br>
                <b>id</b> – Id of the StackSync element<br>
                <b>list</b> – Users’ email addresses
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Script call:</td>
            <td style="padding-left:30px">
                Example:<br>
                {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"token":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"key":"token1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"secret":"secret1234",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"metadata":{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type":"shareFolder",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id":32565632156,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"list":[<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"tester1@test.com",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"tester2@test.com",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"tester3@test.com"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
                Result structure:<br>
                - status: ‘OK’ if correct or ‘KO’ in the event of error<br>
                - error: Error number. Only exists in the event of error<br>
                Example:<br>
                {“status”:”OK” }<br>
		{“status”:”KO”, “error”: -1}
            </td>
        </tr>
    </table>
</div>  
  
  
- **_Share API_**

The configuration file of Share API is found at “/var/www/eyeos/eyeos/extern/u1db/” and is called “settings.py”.

**getListUsersShare(**_oauth,id_**)** 

Gets all users with access to a StackSync directory.  

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL of the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                GET
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
		<ul>
		   <li><b>oauth</b> – OauthRequest object. Includes the values of the consumer key and secret of the configuration file. And also the access token</li>
		   <li><b>id</b> – Id of the StackSync element</li>
		</ul>
	    </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            List of users or, in the event of error, returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            [<br>
	    &nbsp;&nbsp;&nbsp;{“name”:”tester1”,<br>
	    &nbsp;&nbsp;&nbsp;”email”:”tester1@test.com”,<br>
	    &nbsp;&nbsp;&nbsp;”modified_at”:”2014-04-11 10:02:33.432”,<br>
	    &nbsp;&nbsp;&nbsp;”is_owner”:true},<br>
	    &nbsp;&nbsp;&nbsp;{“name”:”tester2”,<br>
	    &nbsp;&nbsp;&nbsp;”email”:”tester2@test.com”,<br>
	    &nbsp;&nbsp;&nbsp;”modified_at”:”2014-05-30 19:39:21.044”,<br>
	    &nbsp;&nbsp;&nbsp;”is_owner”:false},<br>
	    &nbsp;&nbsp;&nbsp;{“name”:”tester3”,<br>
	    &nbsp;&nbsp;&nbsp;”email”:”tester3@test.com”,<br>
	    &nbsp;&nbsp;&nbsp;”modified_at”:”2014-06-06 15:42:41.852”,<br>
	    &nbsp;&nbsp;&nbsp;”is_owner”:false},<br>
	    ]<br>
            {“error”:401,”description”:”Error list members”}
            </td>
        </tr>
    </table>
</div>  
  
   
**shareFolder(**_oauth,id,list_**)** 

Shares a directory with another user.

<div style="margin-bottom:10px;margin-left:0px">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="background-color:#C0C0C0">Url:</td>
            <td style="padding-left:30px">
                Use RESOURCE_URL of the configuration file.
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Method:</td>
            <td style="padding-left:30px">
                POST
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Signature:</td>
            <td style="padding-left:30px">
                HMAC-SHA1
            </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Parameters:</td>
            <td style="padding-left:30px">
		<ul>
		   <li><b>oauth</b> – OauthRequest object. Includes the values of the consumer key and secret of the configuration file. And also the access token</li>
		   <li><b>id</b> – Id of the StackSync element</li>
		   <li><b>list</b> – List of users</li>
		</ul>
	    </td>
        </tr>
        <tr>
            <td style="background-color:#C0C0C0">Return:</td>
            <td style="padding-left:30px">
            True or, in the event of error, returns an error structure:<br>
            - error: Error number<br>
            - description: Error description<br>
            Example:<br>
            {"error":403, "description": "Forbidden ."}
            </td>
        </tr>
    </table>
</div>
   
