<?php

namespace Zf2ServiceBase\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;


abstract class GenericController extends AbstractActionController {

    protected function getSession($name=null){
        if($name == null){
            $config = $this->getServiceLocator()->get('config');
            $name = $config['htsession']['options']['config_options']['name'];
        }
        return new SessionContainer($name);
    }

    public function setFlashMessage($msg, $tipo){
        $flash = $this->flashMessenger();
        if($tipo === 'success'){
            $flash->addSuccessMessage($msg);
        } else if($tipo === 'danger'){
            $flash->addErrorMessage($msg);
        } else if($tipo === 'info'){
            $flash->addInfoMessage($msg);
        } else if($tipo === 'warnig'){
            $flash->addMessage($msg);
        } 
    }
//    
    public function getAllFlashMessages( $destino = 0 ){
        $flash = $this->flashMessenger();
        $msg_controller=[];
        if( $flash->hasSuccessMessages() ){
            $msg_controller['success'] = $flash->getSuccessMessages();
        } 
        
        if( $flash->hasErrorMessages() ){
            $msg_controller['danger'] = $flash->getErrorMessages();
        } 
        
        if( $flash->hasInfoMessages() ){
            $msg_controller['info'] = $flash->getInfoMessages();
        } 
        
        if( $flash->hasMessages() ){
            $msg_controller['warning'] = $flash->getMessages();
        }
        
        if($flash_message_to_layout){
            $this->layout()->setVariable('flash_messages',$msg_controller);   
        } else {
            return $msg_controller;
        }
    }
//    
//    public function superArrayUnique(array $arrays){
//        $merge=[];
//        foreach ($arrays as $array) {
//            $merge = array_merge_recursive($merge, $array); 
//        }
//        
//        $merge_unique = [];
//        foreach ($merge as $key => $array) {
//            $unique = array_unique($array);
//            $merge_unique[$key] = (sizeof($unique)==1)?$unique[0]:$unique;
//        }
//        return $merge_unique;
//    }

}
