<?php

namespace Siamosoci\PlatformBundle\Entity;

class PodioAccount extends PodioEntity {

    static $appid = 5724466;
    static $appkey = '148875b6cb1b4c70bfc16c0f69c809f5';

    protected function __construct() {
        
    }

    public function getFirstName() {
        if ($this->podioobject->field('firstname')) {
            return $this->podioobject->field('firstname')->humanized_value();
        }
        return null;
    }

    public function getMobile() {
        if ($this->podioobject->field('mobile')) {
            return $this->podioobject->field('mobile')->humanized_value();
        }
        return null;
    }
    
    public function getLastName() {
        if ($this->podioobject->field('lastname')) {
            return $this->podioobject->field('lastname')->humanized_value();
        }
        return null;
    }

}

?>
