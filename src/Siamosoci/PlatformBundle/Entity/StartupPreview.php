<?php

namespace Siamosoci\PlatformBundle\Entity;

class PodioAccount extends PodioEntity {

    static $appid = 5724466;
    static $appkey = '148875b6cb1b4c70bfc16c0f69c809f5';

    function getStartup() {
        if ($this->podioobject->field('startup')) {
            return $this->podioobject->field('startup')->humanized_value();
        }
        return null;
    }

}

?>
