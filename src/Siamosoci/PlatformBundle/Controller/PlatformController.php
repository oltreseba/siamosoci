<?php

namespace Siamosoci\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siamosoci\PlatformBundle\Entity\User;

require_once __DIR__ . '/../podio/API_Podio.php';

class PlatformController extends Controller {

    /**
     * @Route("/", name="index")
     * @Template() 
     */
    function defaultAction() {
        
    }

}

?>
