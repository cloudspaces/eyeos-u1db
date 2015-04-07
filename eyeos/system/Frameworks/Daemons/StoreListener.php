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

    private function isCloud($path, $user)
    {
        $cloud = new stdClass();
        $cloud->isCloud = false;
        $cloud->name = "";
        $cloudspaces = 'home://~' . $user . '/Cloudspaces/';
        if (strrpos($path, $cloudspaces) != -1 && $path !== $cloudspaces) {
            $cloud->isCloud = true;
            $auxCloud = substr($path, strlen($cloudspaces));
            $posStartSlash = stripos($auxCloud, '/');
            if ($posStartSlash != -1) {
                $cloud->name = substr($auxCloud, 0, $posStartSlash);
                $cloud->path = $cloudspaces . $cloud->name;
            }
        }
        return $cloud;
    }

    private function cleanCloud($cloud, $user)
    {
        $oauthManager = new OAuthManager();
        $apiManager = new ApiManager();
        $token = new Token();
        $token->setCloudspaceName($cloud);
        $token->setUserID($user->getId());
        $path = '';
        if ($oauthManager->deleteToken($token)) {
            if ($apiManager->deleteMetadataUser($user->getId(), $cloud)) {
                unset($_SESSION['request_token_' . $cloud . '_v2']);
                unset($_SESSION['access_token_' . $cloud . '_v2']);
                $pathOrg = "home://~" . $user->getName() . "/Cloudspaces/" . $cloud;
                $pathDest = "home://~" . $user->getName() . "/Cloudspaces/." . $cloud;
                $folderToRename1 = FSI::getFile($pathOrg);
                $folderToRename2 = FSI::getFile($pathDest);
                shell_exec('mv ' . AdvancedPathLib::getPhpLocalHackPath($folderToRename1->getRealFile()->getAbsolutePath()) . ' ' .
                    AdvancedPathLib::getPhpLocalHackPath($folderToRename2->getRealFile()->getAbsolutePath()));
                $path = AdvancedPathLib::getPhpLocalHackPath($folderToRename2->getRealFile()->getAbsolutePath());
            }
        }
        return $path;
    }

    public function fileWritten(FileEvent $e)
    {
        //Logger::getLogger('sebas')->error('MetadataWritten:' . $e->getSource()->getPath());
        $apiManager = new ApiManager();
        $path = $e->getSource()->getPath();
        $user = ProcManager::getInstance()->getCurrentProcess()->getLoginContext()->getEyeosUser();
        $userName = $user->getName();
        $cloud = $this->isCloud($path, $userName);
        if($cloud->isCloud) {
            $pathU1db = substr($path, strlen($cloud->path));
            $lenfinal = strrpos($pathU1db, $e->getSource()->getName());
            $posfinal = $lenfinal > 1 ? $lenfinal-strlen($pathU1db)-1 : $lenfinal-strlen($pathU1db);
            $pathParent = substr($pathU1db, 0, $posfinal);
            $folder = NULL;
            if ($pathParent !== '/') {
                $pos=strrpos($pathParent,'/');
                $folder = substr($pathParent, $pos+1);
                $pathParent = substr($pathParent, 0, $pos+1);
            }
            $parentId = false;

            if($folder !== NULL) {
                $path = $pathParent . $folder . '/';
                $lista = new stdClass();
                $lista->cloud = $cloud->name;
                $lista->path = $pathParent;
                $lista->filename = $folder;
                $lista->user_eyeos = $user->getId();
                $u1db = json_decode($apiManager->callProcessU1db('parent', $lista));
                if($u1db !== NULL && count($u1db) > 0) {
                    $parentId = $u1db[0]->id;
                }
            } else {
                $parentId = '0';
                $path = $pathParent;
            }

            if($parentId !== false) {
                $pathAbsolute = AdvancedPathLib::getPhpLocalHackPath($e->getSource()->getRealFile()->getAbsolutePath());
                $result = $apiManager->createMetadata($cloud->name, $_SESSION['access_token_' . $cloud->name . '_v2'], $user->getId(), true, $e->getSource()->getName(), $parentId, $path, $pathAbsolute);
                if($result['status'] == 'OK') {
                    $params = array($e->getSource()->getParentPath());
                    $message = new ClientBusMessage('file', 'refreshStackSync', $params);
                    ClientMessageBusController::getInstance()->queueMessage($message);
                } else if($result['error'] == 403) {
                    $path = $this->cleanCloud($cloud->name, $user);
                    $params = array($path, $cloud->name);
                    $message = new ClientBusMessage('file', 'permissionDenied', $params);
                    ClientMessageBusController::getInstance()->queueMessage($message);
                }
            }
        }
    }
}

EyeosGlobalFileEventsDispatcher::getInstance()->addListener(StoreListener::getInstance());
SharingManager::getInstance()->addListener(StoreListener::getInstance());

?>