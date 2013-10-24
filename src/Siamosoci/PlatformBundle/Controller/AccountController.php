<?php

namespace Siamosoci\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Siamosoci\PlatformBundle\Entity\User;
use Siamosoci\PlatformBundle\Form\Registration;
use Siamosoci\PlatformBundle\Form\Forgot;
use Symfony\Component\HttpFoundation\Request;
use Siamosoci\PlatformBundle\Entity\PodioAccount;


class AccountController extends Controller {

    /**
     * @Route("/login",name="login")
     * @Template("PlatformBundle:Login:login.html.twig")
     */
    public function loginAction() {

        $request = $this->getRequest();
        $session = $request->getSession();
        // ok get request and session data.
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        }

        return array('last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,);
    }

    /**
     * @Route("/registration",name="registration")
     * @Template("PlatformBundle:Account:registration.html.twig")
     */
    public function registrationAction() {
        $user = new User();
        $form = $this->createForm(new Registration(), $user, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return array('form' => $form->createView());
    }

    /**
     * @Route("/registration/create",name="account_create")
     * @Template("PlatformBundle:Account:registration.html.twig")
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new Registration(), new User());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setUsername('');
            $userDataForPodio = array(
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
            );

            $podioAccountId = PodioAccount::create($userDataForPodio);
            $user->setPodioid($podioAccountId);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('index'));
        }

        return array('form' => $form->createView());
    }
    
     /**
     * @Route("/forgot",name="forgot")
     * @Template("PlatformBundle:Account:forgot.html.twig")
     */
    public function forgotAction() {
        $user = new User();
        $form = $this->createForm(new Forgot(), $user, array(
            'action' => $this->generateUrl('forgot_check'),
        ));
       
        return array('form' => $form->createView());
    }
    
    
    
    /**
     * @Route("/forgot_check",name="forgot_check")
     * @Template("PlatformBundle:Account:forgotPwdSent.html.twig")
     */
    public function forgotCheckAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new Forgot(), new User());
        $form->handleRequest($request);
        $frmData = $form->getData();
        $email = $frmData->getEmail();
        $message = '';
        $savedEmail='';
        
        $user = $em->getRepository('PlatformBundle:User')->findOneByEmail($email);
        if($user){
            $savedEmail = $user->getEmail();
        }
  
        if((empty($savedEmail)||$savedEmail=='')){
            
            $message = "Could'nt find your email address";
            
        }else{
            
            //$this->sendResetLink();//send password reset link to user
            $message = "Please check your email. Your reset password link has been sent.";
            
        }
           
        return array('message'=>$message);
    }
    
    
    /**
     * @Route("/podiodata/{podioId}")
     * @Template("PlatformBundle:Account:forgotPwdSent.html.twig")
     * This is only for testing purpose
     */
    public function podioDataAction($podioId) {
        
        $pdData=array('firstname'=>'Raj','lastname'=>'Kumar');
        $x = PodioAccount::updateById($podioId,$pdData);
        var_dump($x);
        return array('message'=>'testing');
        //$podio= PodioAccount::getById($podioId);
        //return array('message'=>$podio->getLastName());
        
    }
    
    
    
    
    /**
     * 
     * @return boolean
     */
     private function sendResetLink(){
                     
        $message = \Swift_Message::newInstance()
        ->setSubject('Password reset')
        ->setFrom('binu305@gmail.com')
        ->setTo('b.pillai@noonic.com')
        ->setBody(
            $this->renderView(
                'PlatformBundle:Account:resetLink.html.twig',
                array('name' => 'binu')
            )
        );

        try{
        $this->get('mailer')->send($message);
        }catch(Swift_TransportException $e){
            
            
        }

        return true;
    } 
    

    /**
     * If $user_id isset and exsist, we'll use it as user id, otherwise we will return
     * user own profile.
     * @Route("/profile/",name="personal_profile_view")
     * @Route("/profile/{user_id}",name="profile_view")
     * @Template("PlatformBundle:Account:profileView.html.twig")
     */
    public function ProfileAction($user_id = null) {
        
        $user    = null;
        $profile = null;
        
        if ($user_id) {
            $em   = $this->getDoctrine()->getManager();
            $user = $em->getRepository('PlatformBundle:User')->find($user_id);
        }
        
        if (!$user) {
            $user = $this->get('security.context')->getToken()->getUser();
        }
             
            //check whether the profile is viewed by the logged in user
            if( $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
                $auth = $this->getUser();
                if ($user->getId() == $auth->getId()) {
                    $profile = 'PROFILE_OWNER';
                }   else {
                    $profile = 'VISITOR';
                }
            }
            
        return array('profile'   => $profile,
                     'firstName' => $user->getAccountFirstName(),
                     'mobile'    => $user->getMobile()
        );
    }

    /**
    * Creates a form to edit a User entity.
    * @param User $entity The entity
    * @Route("/edit/",name="profile_edit")
    * @Template("PlatformBundle:Account:profileEdit.html.twig")
    */
    
    public function EditAction() {
        
        return array();
     
    }
    
}
