<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

/**
 * Description of Sav
 *
 * @author ClaraLuis
 */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Sav;

class SavController extends Controller {

    //put your code here
    public function newAction(Request $request) {
        $helpers = $this->get("app.helpers");
        $json = $request->get("json", null);
        //$json="{'titre':'casse','commentaire':'c est casse'}";
        $params = json_decode($json);
        var_dump($params);die();
        $data=array();
        if ($json != null) {
            $date = new \DateTime("now");
            $titre = (isset($params->titre)) ? $params->titre : null;
            $commentaire = (isset($params->commentaire)) ? $params->commentaire : null;
            if ($titre != null && $commentaire != null) {
                $sav = new Sav();
                $sav->setCommentaire($commentaire);
                $sav->setDate($date);
                $sav->setTittre($tittre);
                $em = $this->getDoctrine()->getManager();
                $em->persist($sav);
                $em->flush();
                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "message" => "new sav create!!"
                );
            }else {
                $data = array(
                    "status" => "echec",
                    "code" => 401,
                    "message" => "le titre ou le message est vide"
                );
            }
        }else{
                $data = array(
                    "status" => "echec",
                    "code" => 400,
                    "message" => "no json!"
                );
        }
        return $helpers->json($data);
    }

}
