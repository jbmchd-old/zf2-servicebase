<?php

namespace Zf2ServiceBase\Service;

use ZfServiceBase\Service\ServiceManager;

abstract class GenericService {

    protected $em;
  
    const MESCLAR_DUPLICADOS = 1;
    
    public function __construct(\ZeDb\DatabaseManager $em) {
        $this->em = $em;
    }

    public function getEntity($module, $entity){
        try {
            return $this->em->get( ServiceManager::class )->getService($module, $entity, ServiceManager::TYPE_ENTITY);
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            die();
        }
    }
    
    public function objetosParaArray($objetos, $mesclar_duplicados=false){
        if( ! is_array($objetos)){
            $objetos = [];
        }
        $array = [];
        foreach ($objetos as $objeto) {
            if(is_object($objeto) && method_exists($objeto, 'toArray')){
                $array[] = $objeto->toArray($objeto);
            }
        }
        
        if($mesclar_duplicados){
            $array = $this->super_array_unique($array);
        }
        
        return $array;
        
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
