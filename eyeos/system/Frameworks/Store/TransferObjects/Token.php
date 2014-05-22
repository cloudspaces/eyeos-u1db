<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/14
 * Time: 13:46
 */

class Token
{
    private $userID;
    private $token;

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $userId
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }


}

?>