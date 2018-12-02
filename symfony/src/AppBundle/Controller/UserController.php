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

class UserController extends Controller {

    //crear usuario
    //actualisar usuario
    //modificar usuario
    //subir una imagen
    //pasarlos al modelo de usuario
    //devolverlo al cliente
    public function newAction(Request $request) {
        $helpers = $this->get("app.helpers");
        $json = $request->get("json", null);
        var_dump($json);die();
        $params = json_decode($json);
        if ($json != null) {
            $createdAt = new \DateTime("now");
            $image = null;
            $role = "user";
            $data = array();
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $password = (isset($params->password) && $params->password) ? $params->password : null;
            $email_constraint = new Email();
            $validate_email = $this->get("validator")->validate($email, $email_constraint);

            if ($email != NULL && count($validate_email) == 0 && $name != null && $surname != null && $password != null) {
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surname);
                $user->setRole($role);
                //chifrement
                $pwd = hash("sha256", $password);
                $user->setPassword($pwd);


                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository("BackendBundle:User")->findBy(
                        array(
                            "email" => $email
                        )
                );
                if (count($isset_user) == 0) {
                    $em->persist($user);
                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "new user create!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "messsage" => "user is not created,duplicated"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 405,
                    "messsage" => "il manque des champs ou l eamil est incorect"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "message" => "parce que y a pas de json mec"
            );
        }

        return $helpers->json($data);
    }
    public function getAllAction(Request $request){
        $helpers = $this->get("app.helpers");
        $json = $request->get("json", null);


        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("BackendBundle:User")->findAll();
        return $helpers->json($user);
    }

    public function editAction(Request $request) {
        $helpers = $this->get("app.helpers");
        $json = $request->get("json", null);
        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);
        $identity = $helpers->authCheck($hash, true);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("BackendBundle:User")->findOneBy(array("id" => $identity->sub));
        if ($authCheck) {
            $params = json_decode($json);
            if ($json != null) {
                $createdAt = new \DateTime("now");
                $image = null;
                $role = "user";
                $data = array();
                $email = (isset($params->email)) ? $params->email : null;
                $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
                $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
                $password = (isset($params->password) && $params->password) ? $params->password : null;
                $email_constraint = new Email();
                $validate_email = $this->get("validator")->validate($email, $email_constraint);

                if ($email != NULL && count($validate_email) == 0) {

                    $user->setCreatedAt($createdAt);
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);
                    $user->setRole($role);
                    //chifrement
                    if ($password != null) {
                        $pwd = hash("sha256", $password);
                        $user->setPassword($pwd);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $isset_user = $em->getRepository("BackendBundle:User")->findBy(
                            array(
                                "email" => $email
                            )
                    );
                    if (count($isset_user) == 0 || $identity->email == $email) {
                        $em->persist($user);
                        $em->flush();

                        $data = array(
                            "status" => "success",
                            "code" => 200,
                            "message" => "User was updated!!"
                        );
                    } else {
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "messsage" => "user is not created,duplicated"
                        );
                    }
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 405,
                        "messsage" => "il manque des champs ou l email est incorrect"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "message" => "parce que y a pas de json mec"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "auth not valid"
            );
        }
        return $helpers->json($data);
    }

    public function uploadImageAction(Request $request) {
        $helpers = $this->get("app.helpers");
        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(array(
                "id" => $identity->sub
            ));

            //ecoger el fichero por post y guardarlo en DiscoDuro
            $file = $request->files->get('image');
            if (!empty($file) && $file != null) {
                $ext = $file->getClientOriginalExtension();
                $ext = strtolower($ext);
                
                if (in_array($ext, array("jpeg", "jpg", "png", "bitmap"))) {
                    $file_name = time() . "." . $ext;
                    $file->move("uploads/users", $file_name);
                    $user->setImage($file_name);
                    $em->persist($user);
                    $em->flush();
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "image upload succes"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "file not valid"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "image not upload!!"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "auhorization not valid"
            );
        }
        return $helpers->json($data);
    }

}
