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
        if ($request->issetGET('token')) {
            return true;
        }

        return false;
    }

    public function processRequest(MMapRequest $request, MMapResponse $response) {
        $oauth_verifier = null;
        $oauth_token = null;
        
        if($request->issetGET('verifier')) {
            $oauth_verifier = $request->getGET('verifier');
        }

        if($request->issetGET('token')) {
            $oauth_token = $request->getGET('token');
        }

        if($oauth_verifier && $oauth_token) {
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

            $token = new stdClass();
            $token->oauth_verifier = $oauth_verifier;
            $token->oauth_token = $oauth_token;

            $group = UMManager::getInstance()->getGroupByName('users');
            $users = UMManager::getInstance()->getAllUsersFromGroup($group);

            foreach ($users as $user) {
                $NetSyncMessage = new NetSyncMessage('cloud', 'token', $user->getId(), $token);
                NetSyncController::getInstance()->send($NetSyncMessage);
            }
        }
    }
}

?>