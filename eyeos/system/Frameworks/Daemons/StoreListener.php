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
        $path = $e->getSource()->getPath();
        $userName = ProcManager::getInstance()->getCurrentProcess()->getLoginContext()->getEyeosUser()->getName();
        if(strpos($path,"home://~" . $userName . "/Stacksync") !== false) {
            $pathAbsolute = AdvancedPathLib::getPhpLocalHackPath($e->getSource()->getRealFile()->getAbsolutePath());
            $apiManager = new ApiManager();
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
                Logger::getLogger('sebas')->error('PathStore:' . $pathParent);
                Logger::getLogger('sebas')->error('PathStore:' . $folder);

                $apiManager->createFile($e->getSource()->getName(),$file,filesize($pathAbsolute),$pathParent,$folder);


                $params = array($e->getSource()->getParentPath());
                $message = new ClientBusMessage('file', 'refreshStackSync',$params);
                ClientMessageBusController::getInstance()->queueMessage($message);

                fclose($file);
            }
        }
    }

    public function directoryCreated(FileEvent $e) {
        /*$path = $e->getSource()->getPath();
        $userName = ProcManager::getInstance()->getCurrentProcess()->getLoginContext()->getEyeosUser()->getName();
        if(strpos($path,"home://~" . $userName . "/Stacksync") !== false) {
            $apiManager = new ApiManager();
            $len = strlen("home://~" . $userName . "/Stacksync");
            $pathU1db = substr($path,$len);
            $lenfinal = strrpos($pathU1db,$e->getSource()->getName());
            $posfinal = $lenfinal > 1?$lenfinal-strlen($pathU1db)-1:$lenfinal-strlen($pathU1db);
            $pathParent = substr($pathU1db,0,$posfinal);
            $folder = NULL;
            if ($lenfinal > 1) {
                $pos=strrpos($pathParent,'/');
                $folder = substr($pathParent,$pos+1);
                $pathParent = substr($pathParent,0,$pos+1);
            }

            Logger::getLogger('sebas')->error('PathStore:' . $pathParent);
            Logger::getLogger('sebas')->error('PathStore:' . $folder);

            $apiManager->createFolder($e->getSource()->getName(),$pathParent,$folder);
        }*/
    }
}

EyeosGlobalFileEventsDispatcher::getInstance()->addListener(StoreListener::getInstance());
SharingManager::getInstance()->addListener(StoreListener::getInstance());

?>