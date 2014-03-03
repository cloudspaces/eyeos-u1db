<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 24/10/13
 * Time: 10:46
 * To change this template use File | Settings | File Templates.
 */

class CodeProvider
{
    private $key = 'AB123456AAA89Z078B83837';

    public function getEncryption($data)
    {
        return ($data !== null && strlen($data) > 0)?$this->strToHex(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->key), $data, MCRYPT_MODE_CBC, md5(md5($this->key))))):$data;
    }

    public function getDecryption($data)
    {
        return ($data !== null && strlen($data) > 0)?rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($this->hexToStr($data)), MCRYPT_MODE_CBC, md5(md5($this->key))), "\0"):$data;
    }

    private function strToHex($string)
    {
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    private function hexToStr($hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

}