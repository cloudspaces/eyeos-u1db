<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/05/14
 * Time: 10:33
 */

class MMapStacksync extends Kernel implements IMMap {
    private static $Logger = null;

    public static function getInstance() {
        self::$Logger = Logger::getLogger('system.services.MMap.MMapStacksync');
        return parent::getInstance(__CLASS__);
    }

    public function checkRequest(MMapRequest $request) {
        if ($request->issetGET('userId')) {
            return true;
        }

        return false;
    }

    public function processRequest(MMapRequest $request, MMapResponse $response) {
        $userId=$request->getGET('userId');
        $oauth_verifier = null;
        //eyeID_EyeosUser_453

        if($request->issetGET('oauth_verifier')) {
            $oauth_verifier = $request->getGET('oauth_verifier');
        }

        if($oauth_verifier) {
            Logger::getLogger('sebas')->error('MMapStackSync.userId:' . $userId . "  ; oauthverifier:" . $oauth_verifier);
            $response->getHeaders()->append('Content-type: text/html');

            $body = '<html>
                            <div id="logo_eyeos" style="margin: 0 auto;width:350"> <img src="eyeos/extern/images/logo-eyeos.jpg"/></div>
                            <div style="margin: 0 auto;width:350;text-align:center"><span style="font-family:Verdana;font-size:20px;">Successful authentication.<br>Back to Eyeos.</span></div>
                     </html>';

            $response->getHeaders()->append('Content-Length: ' . strlen($body));
            $response->getHeaders()->append('Accept-Ranges: bytes');
            $response->getHeaders()->append('X-Pad: avoid browser bug');
            $response->getHeaders()->append('Cache-Control: ');
            $response->getHeaders()->append('pragma: ');

            $response->setBody($body);

            try {
                $userRoot = UMManager::getInstance()->getUserByName('root');
            } catch(EyeNoSuchUserException $e) {
                throw new EyeFailedLoginException('Unknown user root"' . '". Cannot proceed to login.', 0, $e);
            }

            $subject = new Subject();
            $loginContext = new LoginContext('eyeos-login', $subject);
            $cred = new EyeosPasswordCredential();
            $cred->setUsername('root');
            $cred->setPassword($userRoot->getPassword(),false);
            $subject->getPrivateCredentials()->append($cred);
            $loginContext->login();

            Kernel::enterSystemMode();

            $appProcess = new Process('stacksync');
            $appProcess->setPid('31338');
            $mem = MemoryManager::getInstance();
            $processTable = $mem->get('processTable', array());
            $processTable[31338] = $appProcess;
            $mem->set('processTable', $processTable);

            $appProcess->setLoginContext($loginContext);
            ProcManager::getInstance()->setCurrentProcess($appProcess);
            kernel::exitSystemMode();

            /*$procManager = ProcManager::getInstance();
            $procManager->setProcessLoginContext($procManager->getCurrentProcess()->getPid(), $loginContext);*/


            $NetSyncMessage = new NetSyncMessage('stacksync', 'token',$userId, $oauth_verifier);
            NetSyncController::getInstance()->send($NetSyncMessage);

            /*$message = new ClientBusMessage('stacksync', 'token', $token);
            ClientMessageBusController::getInstance()->queueMessage($message);
            Logger::getLogger('sebas')->error('MMapStackSync.userId:Mensaje enviado');*/

            //echo "<html>Autenticacion correcta</html>";

            //exit;
        }
    }
}

?>