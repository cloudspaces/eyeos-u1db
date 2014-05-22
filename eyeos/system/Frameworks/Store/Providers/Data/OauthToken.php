<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/02/14
 * Time: 17:03
 */

class OauthToken
{
    private $url;
    private $id;
    private $expire;

    public function __construct($url=NULL,$id=NULL,$expire=NULL)
    {
        if($url) $this->url = $url;
        if($id) $this->id = $id;
        if($expire) $this->expire = $expire;
    }

    /**
     * @param mixed $expire
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    /**
     * @return mixed
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }



}