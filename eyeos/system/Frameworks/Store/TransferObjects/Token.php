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
    private $tkey;
    private $tsecret;

    /**
     * @param mixed $token
     */
    public function setTkey($tkey)
    {
        $this->tkey = $tkey;
    }

    /**
     * @return mixed
     */
    public function getTkey()
    {
        return $this->tkey;
    }

    public function setTsecret($tsecret)
    {
        $this->tsecret = $tsecret;
    }

    public function getTsecret()
    {
        return $this->tsecret;
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