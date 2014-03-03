<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 12:46
 */

class OAuthProvider
{
    private $accessorProvider;

    public function __construct(AccessorProvider $accessorProvider=NULL){
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
    }

    public function verifyUser($settings)
    {
        $response = json_decode($this->accessorProvider->sendMessage($settings));

        if(isset($response->access)) {
            if($response->access->token) {
                if($response->access->token->id && strlen($response->access->token->id) > 0) {
                    $token = $response->access->token->id;
                }

                if($response->access->serviceCatalog && count($response->access->serviceCatalog) > 0) {
                    if($response->access->serviceCatalog[0]->endpoints && count($response->access->serviceCatalog[0]->endpoints[0]) > 0) {
                        if($response->access->serviceCatalog[0]->endpoints[0]->publicURL && strlen($response->access->serviceCatalog[0]->endpoints[0]->publicURL) > 0) {
                            $url = $response->access->serviceCatalog[0]->endpoints[0]->publicURL;
                        }
                    }
                }

                if($response->access->token->expires && strlen($response->access->token->expires) > 0) {
                    $dateExpires = $this->getDateExpires($response->access->token->expires);

                }
            }
        }
        if(isset($token) && isset($url) && isset($dateExpires)) {
            return new Token($url,$token,$dateExpires);
        } else {
            throw new EyeCurlException();
        }
    }

    public function verifyDateExpireToken($dateExpire,$currentDate,$settings)
    {
        $expire = strtotime($dateExpire);
        $current = strtotime($currentDate);

        if($current < $expire){
            return false;
        } else {

            return $this->verifyUser($settings);
        }

    }

    private function getDateExpires($date) {
        $aux = explode("T",$date);
        $dateExpires = null;

        if(count($aux) == 2) {
            if(strlen($aux[0]) == 10 && strlen($aux[1]) >= 8) {
                $aux = new DateTime($aux[0] . " " . substr($aux[1],0,8));
                $dateExpires = date_format($aux, 'Y-m-d H:i:s');
            }
        }

        return $dateExpires;
    }
}


?>