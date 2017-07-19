<?php

namespace Zf2ServiceBase\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\ServiceManager\ServiceManager;

abstract class GenericController extends AbstractActionController {
    
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $sm;
    
    protected $config;

    private $namespace = __NAMESPACE__;
    
    const FLASH_MESSAGE_TO_LAYOUT = 1;
    
    public function __construct(ServiceManager $sm, $namespace = __NAMESPACE__) {
        $this->sm = $sm;
        $this->config = $this->sm->get('config');
        $this->setNamespace($namespace);
    }

    public function __invoke() {
        die(__NAMESPACE__.__CLASS__);
    }
    
    public function getServiceManager(){return $this->sm;}
    
    public function setNamespace($namespace){
        $this->namespace = $namespace;
    }
    
    public function getNamespace(){
        return $this->namespace;
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
    
    public function superArrayUnique(array $arrays){
        $merge=[];
        foreach ($arrays as $array) {
            $merge = array_merge_recursive($merge, $array); 
        }
        
        $merge_unique = [];
        foreach ($merge as $key => $array) {
            $unique = array_unique($array);
            $merge_unique[$key] = (sizeof($unique)==1)?$unique[0]:$unique;
        }
        return $merge_unique;
    }

}
