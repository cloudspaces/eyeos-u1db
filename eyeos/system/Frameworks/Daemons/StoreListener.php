<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/03/14
 * Time: 11:55
 */

class StoreListener extends AbstractFileAdapter implements ISharingListener {
    private static $Instance = null;

    protected function __construct() {}

    public function collaboratorPermissionUpdated(SharingEvent $e) {}

    public function collaboratorAdded(SharingEvent $e) {}

    public function collaboratorRemoved(SharingEvent $e) {}

    public static function getInstance() {
        if (self::$Instance === null) {
            self::$Instance = new StoreListener();
        }
        return self::$Instance;
    }

    public function fileWritten(FileEvent $e)
    {
        $oauthManager = new OAuthManagerOld();
        $codeManager = new CodeManager();
        $settings = new Settings();
        $settings->setUrl(URL_CLOUDSPACE);
        $settings->setCustomRequest("POST");
        $postfields = array();
        $postfields['auth'] = array();
        $postfields['auth']['passwordCredentials'] = array();
        $postfields['auth']['passwordCredentials']['username'] = $codeManager->getDecryption($_SESSION['user']);
        $postfields['auth']['passwordCredentials']['password'] = $codeManager->getDecryption($_SESSION['password']);
        $postfields['auth']['tenantName'] = $codeManager->getDecryption($_SESSION['user']);
        $settings->setPostFields(json_encode($postfields));
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader(array("Content-Type: application/json"));
        $settings->setHeader(false);
        $settings->setSslVerifyPeer(false);
        $date = new DateTime();
        $token = $oauthManager->verifyDateExpireToken($_SESSION['dateExpires'],date_format($date, 'Y-m-d H:i:s'),$settings);
        if($token !== false) {
            $_SESSION['token'] = $codeManager->getEncryption($token->getId());
            $_SESSION['url'] = $codeManager->getEncryption($token->getUrl());
            $_SESSION['dateExpires'] = $token->getExpire();
        }

        $path = $e->getSource()->getPath();
        $userName = ProcManager::getInstance()->getCurrentProcess()->getLoginContext()->getEyeosUser()->getName();
        if(strpos($path,"home://~" . $userName . "/Stacksync") !== false) {
            $pathAbsolute = AdvancedPathLib::getPhpLocalHackPath($e->getSource()->getRealFile()->getAbsolutePath());
            $apiManager = new ApiManagerOld();
            $file = fopen($pathAbsolute,"r");
            if($file) {
                $len = strlen("home://~" . $userName . "/Stacksync");
                $pathU1db = substr($path,$len);
                $lenfinal = strrpos($pathU1db,$e->getSource()->getName());
                $posfinal = $lenfinal > 1?$lenfinal-strlen($pathU1db)-1:$lenfinal-strlen($pathU1db);
                $pathParent = substr($pathU1db,0,$posfinal);
                $folder = NULL;
                if ($pathParent !== '/') {
                    $pos=strrpos($pathParent,'/');
                    $folder = substr($pathParent,$pos+1);
                    $pathParent = substr($pathParent,0,$pos+1);
                }

                $apiManager->createFile($e->getSource()->getName(),$file,filesize($pathAbsolute),$pathParent,$folder);


                $params = array($e->getSource()->getParentPath());
                $message = new ClientBusMessage('file', 'refreshStackSync',$params);
                ClientMessageBusController::getInstance()->queueMessage($message);

                fclose($file);
            }
        }
    }
}

EyeosGlobalFileEventsDispatcher::getInstance()->addListener(StoreListener::getInstance());
SharingManager::getInstance()->addListener(StoreListener::getInstance());

?>