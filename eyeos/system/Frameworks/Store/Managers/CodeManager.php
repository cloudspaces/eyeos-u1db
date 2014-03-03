<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 24/10/13
 * Time: 10:33
 * To change this template use File | Settings | File Templates.
 */

class CodeManager
{
    private $CodeProvider;

    public function __construct(CodeProvider $CodeProvider=NULL)
    {
        if(!$CodeProvider) $CodeProvider = new CodeProvider();
        $this->CodeProvider=$CodeProvider;
    }

    public function getEncryption($data)
    {
        return $this->CodeProvider->getEncryption($data);
    }

    public function getDecryption($data)
    {
        return $this->CodeProvider->getDecryption($data);
    }

}