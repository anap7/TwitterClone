<?php 
    namespace MF\Controller;
    
    abstract class Action{
       
        protected $view;

        public function __construct() {
            /*StdClass quando se deseja criar um objeto vazio e ir adicionando as propriedades conforme necessário.*/
            $this->view = new \stdClass();
        }

    
    protected function render($view, $layout = 'layout') {
        $this->view->page = $view;
        require_once "../App/Views/".$layout.".phtml";
    }

    protected function content(){

        $classAtual = get_class($this); 

        $classAtual = str_replace('App\\Controllers\\', '', $classAtual);
        
        $classAtual = strtolower(str_replace('Controller', '', $classAtual)); 
        
       require_once "../App/Views/".$classAtual."/".$this->view->page.".phtml";
    } 
}
?>