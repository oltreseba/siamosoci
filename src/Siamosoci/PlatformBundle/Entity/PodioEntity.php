<?php

namespace Siamosoci\PlatformBundle\Entity;

require_once __DIR__ . '/../podio/API_Podio.php';

class PodioEntity {

    static $appid = null;
    static $appkey = null;
    // TODO, but the next two information in a config file.
    static $devid = 'siamosoci'; //client_id
    static $devkey = '8FfGH1lIPRRhgza7NGz4mveJj9rT6mx5gl8VLiGd7QxaGR56NJgZtllV7ksvClWA'; //client_secret
    static $init = false;

    /*
     * This var represent the podio object. It should not directly read. Basically it's an array containing a podio object.
     */
    protected $podioobject;

    function __constructor($podioobject) {
        $this->podioobject = $podioobject;
    }

    static function init() {
        if (!self::$init) {
            try {
                $className = get_called_class();

                if (!$className::$appid || !$className::$appkey) {

                    throw new \PodioError('You have to inherit PodioEntity class, and declare a static field called $appid and one called $appkey, with these information to get this working.', '', '');
                }
                \Podio::setup($className::$devid, $className::$devkey);
                \Podio::authenticate('app', array('app_id' => $className::$appid, 'app_token' => $className::$appkey)); //TODO check: is this really necessary?
//                echo \Podio::$ch;
                $init = true;
            } catch (\PodioError $e) {
                // Something went wrong. Examine $e->body['error_description'] for a description of the error.
                $e->body;
            }
        }
    }

    public static function getById($id) {
        try {

            self::init();
            $className = get_called_class();
            $podioEntity = new $className();
            $podioEntity->podioobject = \PodioItem::get_basic($id);
            return $podioEntity;
        } catch (\PodioError $e) {
            print_r($e->body);
        }
    }

    public static function updateById($id, $itemData) {
        try {
            self::init();
            $response = \PodioItem::update($id, $itemData, true);
            return $response;
        } catch (\PodioError $e) {
            print_r($e->body);
        }
    }

    /**
     * Create the needed element, 
     * 
     * @param array()  $fields the fiels that need to be inserted during the creation of the object
     * fields must be and array with key, the name of the field, and value the value of that field. lie $fields= array('email'=>'user@example.org').
     * @return the id of the created object. Not the created object, if you need it you have to use PodioEntity::getById 
     */
    public static function create($fields) {
        $entity = array(
            'fields' => $fields,
        );
        try {
            self::init();
            $className = get_called_class();
            return \PodioItem::create($className::$appid, $entity);
        } catch (\PodioError $e) {
            echo $e->body;
        }
    }

    public static function save() {
        //TODO, todo, do this function and decide whether to call it save or update, update maybe is better.
        //it should not be static
    }

}

?>
