<?php 
    namespace App; 
    use MF\Init\Bootstrap;
    
    class Route extends Bootstrap{
        protected function initRoutes(){

            $routes['home'] = array(
                'route' => '/', 
                'controller' => 'indexController', //Para qual controladora irá
                'action' => 'index'
            );

            $routes['inscreverse'] = array(
                'route' => '/inscreverse', 
                'controller' => 'indexController', //Para qual controladora irá
                'action' => 'inscreverse'
            );

            $routes['registrar'] = array(
                'route' => '/registrar', 
                'controller' => 'indexController', //Para qual controladora irá
                'action' => 'registrar'
            );

            $routes['autenticar'] = array(
                'route' => '/autenticar', 
                'controller' => 'AuthController', //Para qual controladora irá
                'action' => 'autenticar'
            );

            $routes['timeline'] = array( //Responsável por carregar o perfil do usuário com controle de acesso
                'route' => '/timeline', 
                'controller' => 'AppController', //Para qual controladora irá
                'action' => 'timeline'
            );

            $routes['sair'] = array( //Responsável pela destruição da sessão do perfil
                'route' => '/sair', 
                'controller' => 'AuthController', //Para qual controladora irá
                'action' => 'sair'
            );

            $routes['tweet'] = array(  //Responsável por inserir tweets na página timeline
                'route' => '/tweet', 
                'controller' => 'AppController', //Para qual controladora irá
                'action' => 'tweet'
            );

            $routes['quem_seguir'] = array( 
                'route' => '/quem_seguir', 
                'controller' => 'AppController', //Para qual controladora irá
                'action' => 'quemSeguir'
            );

            /*A route acao será responsável por definir se devemos acrescentar ou decrementar
            um seguir com base no botão clicado na página quemSeguir.phtml */
            $routes['acao'] = array(  
                'route' => '/acao', 
                'controller' => 'AppController', //Para qual controladora irá
                'action' => 'acao'
            );

            $routes['remover'] = array(  
                'route' => '/remover', 
                'controller' => 'AppController', //Para qual controladora irá
                'action' => 'remover'
            );
    
             $this->setRoutes($routes);
        }
        
    }
?>