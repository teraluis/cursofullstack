<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of Helpers
 *
 * @author ClaraLuis
 */
class Helpers {
    public $jwt_auth; 
    
    public function __construct($jwt_auth) {
        $this->jwt_auth=$jwt_auth;
    }

    public function authCheck($hash,$getIdentity = false){
        $jwt_auth = $this->jwt_auth;
        //par defaut
        $auth=false;
        //so hash diff null
        if($hash!=null){
            if($getIdentity == false){
                $check_token = $jwt_auth->checkToken($hash);
                if($check_token == true){
                    $auth = true;
                }
            }else {
                $check_token=$jwt_auth->checkToken($hash,true);
                if(is_object($check_token)){
                   $auth = $check_token;
                }
            }
        }
        
        return $auth;
    }

    public function json($data){
        $normalizers = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
        $encoders = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers,$encoders);
        $json = $serializer->serialize($data, "json");
        $reponse = new Response();
        $reponse->setContent($json);
        $reponse->headers->set("Content-Type", "application/json");
        return $reponse;
    }
}
