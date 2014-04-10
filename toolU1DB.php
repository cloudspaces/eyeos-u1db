<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/04/14
 * Time: 11:40
 */
include_once dirname(__FILE__) . "/eyeos/system/Frameworks/Store/Managers/CodeManager.php";
include_once dirname(__FILE__) . "/eyeos/system/Frameworks/Store/Providers/CodeProvider.php";

if ($_GET["user"] && strlen($_GET["user"])) {
    $codeManager = new CodeManager();
    $user = $codeManager->getEncryption($_GET["user"]);
    listContent($user,$_GET["user"]);
} else {
    echo "Necesario un usuario<br>";
}

function listContent($user,$user_name)
{
    chdir(dirname(__FILE__) . "/eyeos");
    echo "<div style=\"font-family:'Verdana';font-size:15px;\"><p>User:&nbsp;<b><span>$user_name</span></b></p>";

    $jsonSend = '{"type":"selectMetadataUser","lista":[{"user_eyeos":"' . $user . '"}]}';
    $path = "python '/var/www/eyeos/eyeos/extern/u1db/Protocol.py' " . escapeshellarg($jsonSend);
    $datosPython = exec($path);
    $resultado = json_decode($datosPython);

    if (is_array($resultado)) {
        if (is_array($resultado)) {
            echo "<table width=\"50%\" border=\"1\">";
            echo "<tr><td  style=\"font-family:'Verdana';font-size:15px;font-weight:bold;background-color:lightgrey\" align='left' width=\"30%\">Almacenamiento</td><td align='center' width=\"70%\">" . count($resultado) . "</td></tr>";
            echo "</table>";
        }
    } else {
        echo "Se produjo un error: <br>" . $datosPython;
    }

    $jsonSend = '{"type":"selectCalendar" , "lista":[{"type":"calendar","user_eyeos": "' . $user_name . '"}]}';
    $path = "python '/var/www/eyeos/eyeos/extern/u1db/Protocol.py' " . escapeshellarg($jsonSend);
    $datosPython = exec($path);
    $resultado = json_decode($datosPython);

    if (is_array($resultado)) {
        if (is_array($resultado)) {
            for ($i=0; $i < count($resultado); $i++) {
                if ($resultado[$i]->status == 'DELETED') {
                    unset($resultado[$i]);
                    $resultado = array_values($resultado);
                    $i--;
                }
            }
            echo "<div style=\"font-family:'Verdana';font-size:15px;margin-top:30px;\"><p>N&uacute;mero de calendarios disponibles: " .  count($resultado) . "</p></div>";
            if (count($resultado) > 0) {
                echo "<table width=\"50%\" border=\"1\">";
                echo "<tr style=\"font-family:'Verdana';font-size:15px;font-weight:bold;background-color:lightgrey\"><td align='left' width=\"30%\">Calendario</td><td align='center' width=\"70%\">Eventos</td></tr>";
                foreach($resultado as $cal) {
                    $jsonSend = '{"type":"selectEvent","lista":[{"type":"event","user_eyeos":"' . $user_name . '","calendar":"' . $cal->name . '"}]}';
                    $path = "python '/var/www/eyeos/eyeos/extern/u1db/Protocol.py' " . escapeshellarg($jsonSend);
                    $salidaPython = exec($path);
                    $salida = json_decode($salidaPython);
                    if (is_array($salida)) {
                        if (is_array($salida)) {
                            for ($i=0; $i < count($salida); $i++) {
                                if ($salida[$i]->status == 'DELETED') {
                                    unset($salida[$i]);
                                    $salida = array_values($salida);
                                    $i--;
                                }
                            }
                            echo "<tr style=\"font-family:'Verdana';font-size:13px;\"><td align='left' width=\"30%\">" . $cal->name . "</td><td align='center' width=\"70%\">" . count($salida) . "</td></tr>";
                        }
                    } else {
                        echo "Se produjo un error: <br>" . $salidaPython;
                    }
                }
                echo "</table>";
            }
        }
    } else {
        echo "Se produjo un error: <br>" . $datosPython;
    }
    echo "</div>";
}
?>