<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    public function loginAction(Request $request){
        $helpers = $this->get("app.helpers");
        $jwt_auth = $this->get("app.jwt_auth");
        //recevoir du json
        $json = $request->get("json",null);
        $params= json_decode($json);
        if($json !=null){
            $params = json_decode($json);
            
            //params reçus
            $email = (isset($params->email))? $params->email :null; 
            $password = (isset($params->password)) ? $params->password : null; 
            $getHash = (isset($params->gethash)) ? $params->gethash : null; 
            
            $email_constraint = new Email();
            $email_constraint->message="El email no es valido";
            $validate_email = $this->get("validator")->validate($email,$email_constraint);
            if(count($validate_email)==0 && $password!=NULL){
                $pwd=hash("sha256",$password);
                if($getHash == null){
                    $signup = $jwt_auth->signup($email,$pwd); 
                }else{
                    $signup = $jwt_auth->signup($email,$pwd,true); 
                }
                return new JsonResponse($signup);
            }else {
                return $helpers->json(array(
                    "param"=>$getHash,
                   "status" => "error",
                    "data" => "mail not valid"
                ));
            }
        }else{
                return $helpers->json(array(
                   "status" => "error",
                    "data" => "send json with post"
                ));
        }
    }

    public function pruebasAction(Request $request)
    {
        $helpers = $this->get("app.helpers");
        //$jwt_auth = $this->get("app.jwt_auth");
        $hash = $request->get("authorization",null);
        $check=$helpers->authCheck($hash,true);
        var_dump($check);
        die();
//        $em = $this->getDoctrine()->getManager();
//        $users = $em->getRepository("BackendBundle:User")->findAll();
//        //$pruebas =array("id"=>1, "name"=>"Victor");
//        return $helpers->json($users);
    }

}
