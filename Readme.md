<h1>Tutorial eyeOS 2.5 OpenSource y CloudSpaces</h1>

<b>Tabla de contenidos</b>

<ul style='font-family:verdana;font-size:14px;'>
    <li><a href='#Instalacion' style='text-decoration:none'>Instalación de eyeOS en Ubuntu</a>
        <ul>
            <li><a href='#Requisitos' style='text-decoration:none'>Requisitos</a></li>
            <li><a href='#Eyeos' style='text-decoration:none'>Instalación eyeOS</a></li>
            <li><a href='#ServerU1DB' style='text-decoration:none'>Instalación serverU1DB</a></li>
        </ul>
    </li>
    <li><a href='#Persistencia' style='text-decoration:none'>Persistencia</a>
        <ul>
            <li><a href='#Almacenar' style='text-decoration:none'>Almacenamiento/recuperación de documentos</a></li>
            <li><a href='#Consulta' style='text-decoration:none'>Consulta</a></li>
            <li><a href='#Sincronizar' style='text-decoration:none'>Sincronización</a></li>
            <li><a href='#U1DB' style='text-decoration:none'>Implementar U1DB en calendar de eyeOS</a></li>
            <li><a href='#Oauth' style='text-decoration:none'>Implementar Oauth en calendar de eyeOS</a></li>
        </ul>
    </li>
    <li></li>
</ul>     
      
<h2><a name=Instalacion>Instalación de eyeOS en Ubuntu</a></h2>
<hr>
<h3><a name=Requisitos>Requisitos</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Apache HTTP Server
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install apache2
    </div>
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apache2 -version
    </div>
    <div style='margin:0 auto'>
        <img src="img/apache2_version.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        Escribir en el navegador http://localhost, y verás la página de apache2 (It works!)
    </div>
    <div style='margin:0 auto'>
        <img src="img/navegador_apache.jpg"/>
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Mysql
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install mysql-server mysql-client
    </div>
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# mysql --version
    </div>
    <div style='margin:0 auto'>
        <img src="img/msyql_version.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        El usuario administrador de la base de datos 'root', se solicitará la contraseña durante la instalación de eyeOS 2.5.
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Php
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# apt-get install php5 php5-mysql libapache2-mod-php5 php5-gd php5-mcrypt php5-sqlite php-db php5-curl php-pear php5-dev
    </div>
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# php5 --version
    </div>
    <div style='margin:0 auto'>
        <img src="img/php_version.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        Configurar el php.ini.
    </div>
    <div style="margin-bottom:10px">
        &nbsp;&nbsp;&nbsp;&nbsp;# cd /etc/php5/apache2/<br>
        &nbsp;&nbsp;&nbsp;&nbsp;# nano php.ini
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        Cambiar los siguientes parámetros:
        <ul>
            <li>error_reporting = E_ALL & ~E_NOTICE<br><br><img src="img/phpini_errorreporting.jpg" /></li>
            <li style='margin-top:10px'>display_errors = Off<br><br><img src="img/phpini_displayerrors.jpg" /></li>
            <li style='margin-top:10px'>max_execution_time = 30<br><br><img src="img/phpini_maxexecutiontime.jpg" /></li>
            <li style='margin-top:10px'>max_input_time = 60<br><br><img src="img/phpini_maxinputtime.jpg" /></li>
            <li style='margin-top:10px'>memory_limit = 128M<br><br><img src="img/phpini_memorylimit.jpg" /></li>
            <li style='margin-top:10px'>post_max_size = 200M<br><br><img src="img/phpini_postmaxsize.jpg" /></li>
            <li style='margin-top:10px'>upload_max_filesize = 100M<br><br><img src="img/phpini_uploadmaxfilesize.jpg" /></li>
            <li style='margin-top:10px'>allow_url_fopen = On<br><br><img src="img/phpini_allowurlfopen.jpg" /></li>
            <li style='margin-top:10px'>disable_functions =<br><br><img src="img/phpini_disablefunctions.jpg" /></li>
            <li style='margin-top:10px'>safe_mode = Off<br><br><img src="img/phpini_safemode.jpg" /></li>
            <li style='margin-top:10px'>short_open_tag = On<br><br><img src="img/phpini_shortopentag.jpg" /></li>
            <li style='margin-top:10px'>magic_quotes_runtime = Off<br><br><img src="img/phpini_magicquotesruntime.jpg" /></li>
            <li style='margin-top:10px'>file_uploads = On<br><br><img src="img/phpini_fileuploads.jpg" /></li>
        </ul>
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Java
    <div style="margin-left:15px">
        # apt-get install python-software-properties<br>
        # add-apt-repository ppa:webupd8team/java<br>
        # apt-get update<br>
        # apt-get install oracle-java7-installer<br>
        # update-alternatives --config java<br>
        # java -version<br>
    </div>
    <div style='margin:0 auto'>
        <img src="img/apache2_version.jpg"/>
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;OpenOffice
    <div style="margin-bottom:10px;margin-left:15px">
        # apt-get install openoffice.org
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Python
    <div style="margin-bottom:10px;margin-left:15px">
        # apt-get install python-support python-simplejson python-uno recoll zip unzip libimage-exiftool-perl
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Curl SSL
    <div style="margin-bottom:10px;margin-left:15px">
        Si no tienes certificado de SSL obtenerlo en:<br>
        # wget http://curl.haxx.se/ca/cacert.pem
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        Abrir el php.ini y indicar la ruta donde se ubica el certificado para realizar peticiones a url 	seguras<br>
        # cd /etc/php5/apache2/<br>
        # nano php.ini<br>
        ....<br>
        curl.cainfo='/root/cacert.pem'
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Uploadprogress
    <div style="margin-bottom:10px;margin-left:15px">
        # apt-get install make<br>
        # pecl install uploadprogress
    </div>
    <div style='margin:0 auto'>
        <img src="img/uploadprogress_install.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        # nano /etc/php5/apache2/php.ini  (Add the last two lines) .
    </div>
    <div style='margin:0 auto'>
        <img src="img/phpini_uploadprogress.jpg"/>
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
&minus;&nbsp;&nbsp;Sendmail
    <div style="margin-bottom:10px;margin-left:15px">
        # apt-get install sendmail<br>
        # nano /etc/hosts (Añadir localhost.localdomain a la IP 127.0.0.1)
    </div>
    <div style='margin:0 auto'>
        <img src="img/step2_sendmail.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        # sendmailconfig (Confirmar todas las preguntas)
    </div>
    <div style='margin:0 auto'>
        <img src="img/step3_sendmail.jpg"/>
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;margin-left:15px;font-family:verdana;font-size:12px;">
Configurar mod-rewrite en apache:
    <div style="margin-bottom:10px;margin-left:15px">
        # a2enmod rewrite
    </div>
    <div style='margin:0 auto'>
        <img src="img/apache_config.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        # nano /etc/apache2/sites-available/default
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        Cambiar en &lt;Directory /var/www/&gt; el parámetro AllowOverride  a All
    </div>
    <div style='margin:0 auto'>
        <img src="img/apache_sitesavailable_allowoverwrite.jpg"/>
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        Reiniciar el servicio de apache
    </div>
    <div style="margin-top:10px;margin-bottom:10px;margin-left:15px">
        # /etc/init.d/apache2 restart
    </div>
    <div style='margin:0 auto'>
        <img src="img/apache_reiniciar.jpg"/>
    </div>
</p>
<br><br>
<h3><a name=Eyeos>Instalación eyeOS</a></h3>
<p style="margin-bottom:10px;margin-left:15px;font-family:verdana;font-size:12px;">
Descargar el código en /var/www
    <div style="margin-bottom:10px;margin-left:15px">
        # cd /var/www/<br>
        # git clone https://github.com/cloudspaces/eyeos-u1db eyeos
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Modificar los permisos del directorio eyeos
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # chown -R www-data.www-data eyeos<br>
        # chmod -R 755 eyeos
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Añadir a DocumentRoot el directorio de eyeos:
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # nano /etc/apache2/sites-available/default
    </div>
    <div style='margin:0 auto'>
        <img src="img/DocumentRoot_addEyeos.jpg"/>
    </div>
    <div style="margin-top;15px;margin-bottom:10px;margin-left:15px">
        # service apache2 restart
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Instalar el paquete python-u1db:
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # cd eyeos/eyeos/packages/<br>
        # dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Si al instalar el paquete nos da problemas de dependencias seguir los siguientes pasos:
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # apt-get install python-u1db<br>
        # apt-get -f install<br>
        # dpkg -i python-u1db_0.1.4-0ubuntu1_all.deb
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Entrar en el mysql y crear el schema 'eyeos'. Como se indica a continuación:
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # mysql -uroot -p&lt;password&gt;<br>
        > create database eyeos;
    </div>
    <div style='margin:0 auto'>
        <img src="img/mysql_databaseeyeos.jpg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Abrir desde el navegador la pantalla de configuración de eyeOS (http://localhost/install)
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_config_navegador.jpg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Seleccionar 'Install eyeOS 2 on my server'
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_listrequisitos.jpg"/>
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_listrequisitos2.jpg"/>
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_listrequisitos3.jpg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        No es un error que nos muestre SQLite extension sin instalar.
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Una vez listado todos los requisitos, se continua con la instalación seleccionando 'Continue with the installation'.
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_configdb.jpg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Introducir el usuario y password de la base de datos de mysql (root) y el password que usará el usuario root de la plataforma de eyeOS.
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Una vez configurado las bases de datos y usuario de eyeOS, se cotinua con la instalación seleccionando 'Continue with the installation'.
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_config_end.jpg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        Al finalizar seleccionar 'Go to my new eyeOS' para ver la pantalla de login de la plataforma eyeOS 2.5.
    </div>
    <div style='margin:0 auto'>
        <img src="img/eyeos_login.jpg" />
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        La url de obtención del token de los usuarios de Cloudspaces puede configurarse desde settings.php
    </div>
    <div style="margin-bottom:10px;margin-left:15px">
        # cd /var/www/eyeos/<br>
        # nano settings.php
    </div>
    <div style='margin:0 auto'>
        <img src="img/token_oautUrl.jpg"/>
    </div>
</p> 
<br><br>
<h3><a name=ServerU1DB>Instalación serverU1DB</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
Seguir los pasos del fichero Readme.md que se encuentra en /var/www/eyeos/serverU1DB. Se puede instalar en la misma máquina de eyeos o en otra máquina independiente.
    <div style="margin-bottom:10px;margin-left:0px">
        Si la instalación se hace en una máquina independiente, se debe copiar los directorio /var/www/eyeos/serverU1DB y /var/www/eyeos/eyeos/package.
    </div>
</p>
<br><br>
<h2><a name=Persistencia>Persistencia</a></h2>
<hr>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
El calendario de eyeOS almacena los eventos  en una base de datos U1DB.
    <div style='margin:0 auto'>
        <img src="img/persistencia.jpeg"/>
    </div>
    <div style="margin-top:15px;margin-bottom:10px;margin-left:0px">
        U1DB es una API de base de datos para sincronizar base de datos de documentos JSON creada por Canonical. Permite que las aplicaciones puedan almacenar documentos y sincronizarlos entre máquinas y dispositivos. U1DB es una base de datos diseñada para trabajar en todas partes, ofreciendo un respaldo de almacenamiento de los datos nativos de la plataforma. Esto significa que se puede utilizar en diferentes plataformas, con diferentes lenguajes, con un respaldo y una sincronización entre todas ellas.
    </div>
    <div style="margin-top:15px;margin-left:0px">
        API U1DB tiene tres apartados distintos: almacenamiento/recuperación de documentos, consulta y sincronización.  A continuación se detalla las funciones que se utilizan y una breve explicación de su funcionamiento.
    </div>
</p>
<br>
<h3><a name=Almacenar>Almacenamiento/recuperación de documentos</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
U1DB almacena documentos, básicamente cualquier información que se pueda expresar en JSON.
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>create_doc_from_json(</b><span style='font-style:italic'>json, doc_id=None</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Crea un documento a partir de unos datos JSON.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        Opcionalmente se puede especificar el identificador del documento. El identificador no debe existir  en la base de datos. Si la base de datos especifica un tamaño máximo de documento y el documento excede el mismo, se lanzará una excepción DocumentTooBig.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>json</b> – Datos JSON</li>
                        <li><b>doc-id</b> – Identificador opcional del documento</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Documento</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>put_doc(</b><span style='font-style:italic'>doc</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Actualización de documentos. Si la base de datos especifica un tamaño máximo de documento y el documento excede el mismo, se lanzará una excepción DocumentTooBig
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>doc</b> – Documento con el nuevo contenido</td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Documento</td>
            </tr>
        </table>
    </div>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>set_json(</b><span style='font-style:italic'>json</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Actualiza los datos JSON del documento.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>json</b> – Datos JSON</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>delete_doc(</b><span style='font-style:italic'>doc</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Marca un documento como eliminado
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>doc</b> – Documento a eliminar</td>
            </tr>
        </table>
    </div>
</p>
<br>
<h3><a name=Consulta>Consulta</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
Consultar en U1DB se realiza por medio de índices. Para recuperar sólo algunos documentos de la base de datos en función de determinados criterios, primero debe crear un índice y a continuación consultar dicho índice.
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>create_index(</b><span style='font-style:italic'>index_name, *index_expressions</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Crea un indice que se utilizará para realizar consultas en la base de datos. La creación de un índice que ya existe no provocará ninguna excepcion. La creación de un índice que cambia las index_expressions de un índice creado anteriormente provocará una excepción.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>index_name</b> – Identificador único del índice</li>
                        <li><b>index_expressions</b> – Claves que se utilizarán en las consultas futuras</li>
                    </ul>
                    <br><br>
                    <span>Ejemplos: “nombreClave” o “nombreClave.nombreSubclave”</span>
                </td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>get_from_index(</b><span style='font-style:italic'>index_name, *key_values</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Retorna los documentos que coinciden con las claves especificadas. Se deben especificar el mismo número de valores que número de claves se han definido en el índice.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>index_name</b> – Nombre del índice de la consulta</li>
                        <li><b>key_values</b> – Valores a buscar. Ej: si tiene un índice con 3 campos, entonces tendría get_from_index(index1,val1,val2,val3)</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Lista de documentos</td>
            </tr>
        </table>
    </div>
</p>
<br>
<h3><a name=Sincronizar>Sincronización</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
U1DB es una base de datos sincronizada. Cualquier base de datos U1DB se puede sincronizar con un servidor U1DB. La mayoría de implementaciones de U1DB se pueden ejecutar como servidor. 
La sincronización entre servidor y cliente proporciona la actualización de ambos, de forma que contengan los mismos datos. Los datos se guardan en U1DB locales ya sea online-offline, y luego se sincronizan cuando esté online.
    <div style='margin:0 auto;margin-top:15px'>
        <img src="img/Permanencia.jpeg"/>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>sync(</b><span style='font-style:italic'>url, creds=None, autocreate=True</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Sincroniza documentos con una réplica remota a través de una url.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>url</b> – url de la réplica remota con la cual se va a sincronizar</li>
                        <li><b>creds</b> – (Opcional). Credenciales para autorizar la operación con el servidor. Como por 	ejemplo usar las credenciales para identificarte a traves de OAuth:<br>
                        {‘oauth’:<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{ ‘consumer_key’: ..., ‘consumer_secret’: ...,<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;‘token_key’: ..., ‘token_secret’: ...<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                        }
                        </li>
                        <li><b>autocreate</b> – Si el valor es True se crea la base de datos si no existe. Si el valor es False si la base de datos no existe no se realizará la sincronización.</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">local_gen_before_sync – Es una clave de generación local para controlar la sincronización. Es útil para utilizar con la función whats_changed, si una aplicación quiere saber los documentos que han cambiado durante la sincronización</td>
            </tr>
        </table>
    </div>
</p>
<br>
<h3><a name=U1DB>Implementar U1DB en calendar de eyeOS</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
EyeOS almacena los calendarios y eventos de los usuarios en una base de datos U1DB sincronizada. La gestión de la base de datos se realiza a través de un script Python llamado Protocol.py. Este script se ocupa de la sincronización con el server U1DB y se encuentra en '/var/www/eyeos/eyeos/extern/u1db/', para poder configurarlo se debe acceder al fichero “settings.py”, en el cual se debe modificar los siguientes valores:
    <div style='margin:0 auto;margin-top:15px'>
        <img src="img/Settings_Calendars.jpg"/>
    </div>
    <div style="margin-top:45px;margin-bottom:10px;margin-left:40px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td width="15%">Servers</td>
                <td width="45%" style="padding-left:15px">Dirección IP  donde está activo el server Oauth</td>
            </tr>
            <tr>
                <td>Port</td>
                <td style="padding-left:15px">Puerto donde está activo el server Oauth</td>
            </tr>
            <tr>
                <td colspan="2">urls</td>
            </tr>
            <tr>
                <td valign="middle">CALLBACK_URL</td>
                <td style="padding-left:15px">Sustituir IP y puerto por los valores indicados anteriormente. (Server y port)</td>
            </tr>
            <tr>
                <td colspan="2">consumer</td>
            </tr>
            <tr>
                <td valign="middle">key</td>
                <td style="padding-left:15px">Incluida al configurar el mongodb en la instalación del server Oauth</td>
            </tr>
            <tr>
                <td valign="middle">secret</td>
                <td style="padding-left:15px">Incluido al configurar el mongodb en la instalación del server Oauth</td>
            </tr>
        </table>
    </div>
    <div style="margin-top:25px;margin-bottom:10px;margin-left:0px">
        A continuación se detallan las APIs contenidas en el script Protocol.py:
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>selectCalendar(</b><span style='font-style:italic'>self,data</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Obtienen todos los calendarios del usuario.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>data</b> – Contiene el tipo y el usuario.<br>
                    Ejemplo: {“type”:”calendar”,”user_eyeos”:”eyeos”}
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Vector con todos los calendarios, en formato JSON<br>
                    Ejemplo:<br>
                    [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”,”description”:”personal calendar”,”timezone”: 0,”status”:”NEW”}]
                </td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>insertCalendar(</b><span style='font-style:italic'>self,lista</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Introducir el nuevo calendario generado por el usuario en eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>lista</b> – Contiene un vector  de los  calendarios pendientes de insertar.<br>
                    Ejemplo:<br>
                    [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”,”description”:”personal calendar”,”timezone”:0,”status”:”NEW”}]
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">'true' o excepción en caso de error</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>deleteCalendar(</b><span style='font-style:italic'>self,lista</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Identificar el estado del calendario a eliminado (STATUS=”DELETED”) de un usuario concreto de eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px"><b>lista</b> – Contiene un vector  de los  calendarios a eliminar.<br>
                    Ejemplo:<br>
                    [{“type”:”calendar”,”user_eyeos”:”eyeos”,”name”:”personal”}]
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">'true' o excepción en caso de error</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>selectEvent(</b><span style='font-style:italic'>self,type,user,calendarId</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Obtienen todos los eventos del calendario que pertenece al usuario especificado.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>type</b> – Flag fijo “event”</li>
                        <li><b>user</b> – Usuario de eyeOS</li>
                        <li><b>calendarId</b> – Nombre del calendario</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">
                    Vector con los eventos del calendario, en formato JSON<br>
                    Ejemplo:<br>
                    [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”NEW”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
                </td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>insertEvent(</b><span style='font-style:italic'>self,lista</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Obtienen todos los eventos del calendario que pertenece al usuario especificado.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>lista</b> – Contiene un vector  de los eventos a introducir.<br>
                    Ejemplo:<br>
                    [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”NEW”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">'true' o excepción en caso de error</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>deleteEvent(</b><span style='font-style:italic'>self,lista</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Identificar el estado del evento de un calendario a eliminado de un usuario concreto de eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>lista</b> – Contiene un vector  de los  calendarios a eliminar.<br>
                    Ejemplo:<br>
                    [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”DELETED”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Visita Médico”, “location”: “Barcelona”, “description”: “Llevar justificante”}]
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">'true' o excepción en caso de error</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>updateEvent(</b><span style='font-style:italic'>self,lista</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Modificar los datos de un evento del calendario existente que pertenece a un usuario específico de eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>lista</b> – Contiene un vector  de los eventos a introducir.<br>
                    Ejemplo:<br>
                    [{“type”:”event”,”user_eyeos”:”eyeos”,”calendar”:”personal”,”status”:”CHANGED”, “isallday”: “0”, “timestart”: “201419160000”, “timeend”:”201419170000”, “repetition”: “None”, “finaltype”: “1”, “finalvalue”: “0”, “subject”: “Examen”, “location”: “Tarragona”, “description”: “Llevar libro”}]
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">'true' o excepción en caso de error</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Para visualizar correctamente los calendarios en la plataforma de eyeOS se debe respetar la estructura de los calendarios y los eventos, además de sus índices que son los siguientes:
    <ul>
        <li>calendario: type, user_eyeos y name</li>
        <li>eventos: type, user_eyeos y calendar</li>
    </ul>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Para usar las APIs de Python en eyeOS se ha generado el framework Store, que contiene unas APIs que se componen de una estructura uniforme:
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td>
                    <ul>
                        <li><b>type</b> – Nombre de la función de la API de Python</li>
                        <li><b>lista</b> – Parámetros de la función anterior</li>
                        <li><b>credentials</b> – Credenciales para la identificación en el proceso de sincronización</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
A continuación se listan dichas APIs:
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>synchronizeCalendars(</b><span style='font-style:italic'>user</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Sincronizar todos los calendarios del usuario conectado en la plataforma de eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>user</b> – Contiene el identificador y el nombre del usuario de eyeOS
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Lista de calendarios</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>insertCalendars(</b><span style='font-style:italic'>user,calendar</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Crear un nuevo calendario.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <ul>
                        <li><b>user</b> – Nombre del usuario</li>
                        <li><b>calendar</b> – Es un objeto que contiene nombre, descripción y timezone</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">True o en caso de error null</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>deleteCalendars(</b><span style='font-style:italic'>user,calendar</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Eliminar un calendario existente.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <ul>
                        <li><b>user</b> – Nombre del usuario</li>
                        <li><b>calendar</b> – Nombre del calendario</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">True o en caso de error null</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>synchronizeCalendar(</b><span style='font-style:italic'>calendarId,user</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Sincronizar todos los eventos del calendario especificado del usuario conectado en la plataforma de eyeOS.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <ul>
                        <li><b>calendarId</b> – Identificador del calendario de eyeOS</li>
                        <li><b>user</b> – Nombre del usuario</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">Lista de eventos</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>createEvent(</b><span style='font-style:italic'>event</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Crear un nuevo evento.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>event</b> – Contiene la estructura U1DB del evento
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">True o en caso de error null</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>deleteEvent(</b><span style='font-style:italic'>event</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Eliminar un evento existente.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>event</b> – Contiene la estructura U1DB del evento
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">True o en caso de error null</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
<b>updateEvent(</b><span style='font-style:italic'>event</span><b>)</b>
    <div style="margin-bottom:10px;margin-left:0px">
        Actualizar un evento existente.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="background-color:#C0C0C0">Parámetros:</td>
                <td style="padding-left:30px">
                    <b>event</b> – Contiene la estructura U1DB del evento
                </td>
            </tr>
            <tr>
                <td style="background-color:#C0C0C0">Llamada script:</td>
                <td width="60%" style="padding-left:30px">
                    Ejemplo:<br>
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
                <td style="background-color:#C0C0C0">Retorno:</td>
                <td style="padding-left:30px">True o en caso de error null</td>
            </tr>
        </table>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Al abrir la aplicación del calendario de eyeOS se ejecutan los procesos de sincronización de calendarios y eventos.
    <div style="margin-bottom:10px;margin-left:0px">
        La sincronización de calendarios se realiza cada 20 segundos, si se detectan cambios refresca la lista de calendario y vuelve a iniciarse el tiempo de espera.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        La sincronización de eventos se realiza cada 10 segundos, si se detectan cambios refresca los eventos del período mostrados en pantalla y vuelve a iniciarse el tiempo de espera.
    </div>
</p>
<br>
<h3><a name=Oauth>Implementar Oauth en calendar de eyeOS</a></h3>
<p style="margin-bottom:10px;font-family:verdana;font-size:12px;">
Para sincronizar los calendarios y eventos de eyeOS es necesario autenticarse con el servidor utilizando el protocolo Oauth.
    <div style="margin-bottom:10px;margin-left:0px">
        El servidor Oauth permite acceder  a un único recurso protegido, que es el servidor U1DB.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        La plataforma de eyeOS implementa el script Credentials.py y Protocol.py que contienen las APIs necesarias para comunicar con el server Oauth.
    </div>
    <div style="margin-bottom:10px;margin-left:0px">
        A continuación se muestra el diálogo de comunicación:
    </div>
    <div style='margin:0 auto;margin-top:15px'>
        <img src="img/DiagramaServerOauth.jpeg"/>
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Paso 1:
    <div style="margin-bottom:10px;">
        API getRequestToken() obtiene el consumer key y  consumer secret a partir del settings. Realiza la llamada al server Oauth a través de la url “request_token”.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        El server Oauth recupera el consumer key de la base de datos mongodb, el cual lo compara con el consumer recibido. Si es incorrecto, devuelve un error y en caso contrario, realiza una nueva búsqueda en el mongodb, con el consumer key para obtener el request token.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        El server Oauth responde a la llamada de eyeOS proporcionando el request token y la verificación de acceso para solicitar el access token.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        La plataforma eyeOS almacena el request token y la verificación en variables de sesión para no tener que repetir el proceso en los pasos posteriores.
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Paso 2:
    <div style="margin-bottom:10px;">
        API getAccesToken(token,verifier) realiza la llamada al server Oauth a través de la url “access_token”.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        El server Oauth recupera el consumer y el request token de la base de datos mongodb, los cuales son comparados con los recibidos. Si son incorrectos, devuelve un error y en caso contrario, realiza una nueva búsqueda, por consumer key y request token key en el mongodb para obtener el access token. Si no se obtienen datos o los datos obtenidos están caducados, se debe generar un nuevo access token y almacenarlo en el mongodb, que dejará sin acceso a cualquier token anterior.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        El server Oauth responde a la llamada de eyeOS proporcionando el access token.
    </div>
</p>
<p style="margin-top:20px;margin-bottom:10px;font-family:verdana;font-size:12px;">
Paso 3:
    <div style="margin-bottom:10px;">
        API protocol(params) realiza la llamada al server Oauth a través de la API de sincronización de U1DB.
    </div>
    <div style="margin-top:10px;margin-bottom:10px;">
        El server Oauth recupera el access token del mongodb a partir del consumer y token recibido. Si no son correctos devuelve un error y en caso contrario se procede a realizar la sincronización con el server U1DB.
    </div>
</p>
<p style="margin-top:30px;margin-bottom:10px;font-family:verdana;font-size:12px;">
El paso 1 sólo se aplica en caso de que no se haya solicitado un request token durante la sesión del usuario de eyeOS.
</p>