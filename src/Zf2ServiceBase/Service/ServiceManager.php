<?php

namespace Zf2ServiceBase\Service;

class ServiceManager {

    private $em;
    
    private $modulo;

    private $servico;

    private $service_namespace;
    
    private $type_service;
    
    const TYPE_ENTITY = 'entity';
    
    const TYPE_SERVICE = 'service';

    public function __construct(\ZeDb\DatabaseManager $em) {
        $this->em = $em;
    }

    public function getModulo() {
        return $this->modulo;
    }

    public function setModulo($modulo) {
        $this->modulo = $modulo;
        return $this;
    }

    public function getServico() {
        return $this->servico;
    }

    public function setServico($servico) {
        $this->servico = $servico;
        return $this;
    }

    public function getService($modulo, $servico, $type_service = self::TYPE_SERVICE) {
        
        $this->modulo = $modulo;
        $this->servico = $servico;
        $this->type_service = $type_service;
        $this->service_namespace = str_replace('/', '\\', $this->obtemNamespace() ) ;
        
        if ( ! class_exists($this->service_namespace) ){
            throw new \Exception("Erro ao carregar a classe $this->service_namespace, verique se a classe existe ou se os parametros foram passados corretamente!");
        }
        
        $instancia = null;
        if($this->type_service == self::TYPE_ENTITY){            
            $this->configuraZeDb();
            $instancia = $this->em->get($this->service_namespace);
        } else {
            $this->configuraZeDb();
            $instancia = new $this->service_namespace($this->em);
        }
 
        return $instancia;
    }

    private function configuraZeDb(){
        $config = $this->em->getServiceLocator()->get('Configuration');
        $config = isset($config['zendexperts_zedb']) && (is_array($config['zendexperts_zedb']) || $config['zendexperts_zedb'] instanceof ArrayAccess)
        ? array_merge($config['zendexperts_zedb'], $this->geraArrayConfig($this->service_namespace))
        : array();
        
        $this->em->setConfig($config);
    }

    private function obtemNamespace(){
        
        $iterator = $this->getIterator();
        
        $recursiveIterator = new \RecursiveIteratorIterator($iterator);
        $service = FALSE;
        foreach ( $recursiveIterator as $entry ) {
            if($entry->getFilename() == $this->servico.'.php'){
                
                if($this->type_service == self::TYPE_SERVICE){
                    $service = "$this->modulo\\Service\\$this->servico";
                } else {
                    $service = "$this->modulo\\Entity\\$this->servico";
                }
                
                
                if(PHP_OS != 'WINNT'){
                    $service = str_replace('\\', '/', $service);
                }
                break;
            }
        }
        
        return $service;
    }
    
    private function getIterator(){
        
        $path = getcwd()."\\module\\$this->modulo\\src";         
        if(PHP_OS != 'WINNT'){ $path = str_replace('\\', '/', $path); } 
        
        try {
            $iterator = new \RecursiveDirectoryIterator($path);
        } catch (\Exception $exc) {

            $path = getcwd()."\\vendor"; 
            if(PHP_OS != 'WINNT'){ $path = str_replace('\\', '/', $path); }
            
            try {
                $iterator = new \RecursiveDirectoryIterator($path);
            } catch (\Exception $exc) {
                echo '<pre>';
                print_r($exc->getTraceAsString());
                die();
            }

        }

        return $iterator;
        
    }
    
    private function geraArrayConfig($namespace){
        $model_class = str_replace('Entity', 'Model', $namespace);
        $tabela = explode('\\', $namespace);
        
        $tabela = array_pop($tabela);
        $table_name = strtolower($tabela[0]);
        
        for($i=1; $i<strlen($tabela);$i++){
            $char = $tabela[$i];
            $table_name .= (preg_match('/[A-Z]/', $char)) ? '_'.strtolower($char) : $char ;
        }
        $table_name = str_replace('._', '.', $table_name);
        
        return [
                'models' => [
                    $model_class => [
                        'tableName' => $table_name,
                        'entityClass' => $namespace,
                    ]
                ]
            ];
    }
    
}

?>