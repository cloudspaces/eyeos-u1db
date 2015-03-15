<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/14
 * Time: 13:46
 */

class Token
{
    private $cloudspaceName;
    private $userID;
    private $tkey;
    private $tsecret;

    /**
     * @return mixed
     */
    public function getCloudspaceName()
    {
        return $this->cloudspaceName;
    }

    /**
     * @param mixed $cloudspaceName
     */
    public function setCloudspaceName($cloudspaceName)
    {
        $this->cloudspaceName = $cloudspaceName;
    }

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