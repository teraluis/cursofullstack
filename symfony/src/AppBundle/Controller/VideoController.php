<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;

class VideoController extends Controller {

    public function newAction(Request $request) {
        $helpers = $this->get("app.helpers");
        $data = array();
        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);
        if ($authCheck) {
            $identity = $helpers->authCheck($hash, true);
            $json = $request->get("json", null);
            if ($json != null) {
                $params = json_decode($json);
                $createdAt = new \DateTime("now");
                $updatedAt = new \DateTime("now");

                $image = null;
                $status = (isset($params->status)) ? $params->status : null;
                $video_path = null;
                $user_id = ($identity->sub != null) ? $identity->sub : null;
                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description)) ? $params->description : null;
                

                if ($user_id != null && $title != null) {
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository("BackendBundle:User")->findOneBy(array("id" => $user_id));

                    $video = new Video();
                    $video->setCreatedAt($createdAt);
                    $video->setUpdatedAt($updatedAt);
                    $video->setImage($image);
                    $video->setStatus($status);
                    $video->setDescription($description);
                    $video->setTitle($title);
                    $video->setUser($user);
                    $video->setDescription($description);
                    $video->setVideoPath($video_path);
                    $em->persist($video);
                    $em->flush();
                    $video = $em->getRepository("BackendBundle:Video")->findOneBy(array(
                        "title" => $title,
                        "user" => $user,
                        "status" => $status,
                        "createdAt" => $createdAt
                    ));
                    $data = array(
                        "status" => "succes",
                        "code" => 200,
                        "data" => $video
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "no user or no title"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "no json"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "message" => "l'authentification a echoue!!!",
            );
        }
        return $helpers->json($data);
    }

}
