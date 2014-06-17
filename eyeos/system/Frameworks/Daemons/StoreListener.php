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
        //Logger::getLogger('sebas')->error('MetadataWritten:' . $e->getSource()->getPath());
        $apiManager = new ApiManager();
        $path = $e->getSource()->getPath();
        $user = ProcManager::getInstance()->getCurrentProcess()->getLoginContext()->getEyeosUser();
        $userName = $user->getName();
        if(strpos($path,"home://~" . $userName . "/Stacksync") !== false) {
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
            $parentId = false;

            if($folder !== NULL) {
                $path = $pathParent . $folder . '/';
                $lista = new stdClass();
                $lista->path = $pathParent;
                $lista->filename = $folder;
                $lista->user_eyeos = $user->getId();
                $u1db = json_decode($apiManager->callProcessU1db('parent',$lista));
                if($u1db !== NULL && count($u1db) > 0) {
                    $parentId = $u1db[0]->id;
                }
            } else {
                $parentId = 'null';
                $path = $pathParent;
            }

            if($parentId !== false) {
                $pathAbsolute = AdvancedPathLib::getPhpLocalHackPath($e->getSource()->getRealFile()->getAbsolutePath());
                $result = $apiManager->createMetadata($_SESSION['access_token_v2'],$user->getId(),true,$e->getSource()->getName(),$parentId,$path,$pathAbsolute);
                if($result['status'] == 'OK') {
                    $params = array($e->getSource()->getParentPath());
                    $message = new ClientBusMessage('file', 'refreshStackSync',$params);
                    ClientMessageBusController::getInstance()->queueMessage($message);
                } else if($result['error'] == 403) {
                    unset($_SESSION['access_token_v2']);
                    $oauthManager = new OAuthManager();
                    $token = new Token();
                    $token->setUserID($user->getId());
                    $oauthManager->deleteToken($token);
                    $message = new ClientBusMessage('file', 'permissionDenied',null);
                    ClientMessageBusController::getInstance()->queueMessage($message);
                }
            }

            /*$message = new ClientBusMessage('file', 'permissionDenied',$e->getSource()->getPath());
            ClientMessageBusController::getInstance()->queueMessage($message);*/
        }

    }
}

EyeosGlobalFileEventsDispatcher::getInstance()->addListener(StoreListener::getInstance());
SharingManager::getInstance()->addListener(StoreListener::getInstance());

?>