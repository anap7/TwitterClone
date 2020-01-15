<?php 
    //Para que o autoload funcione corretamente, nós devemos definir o namespace
    namespace App\Controllers;
     //-----Recursos do miniframework 
    use MF\Controller\Action;
    use MF\Model\Container;
    //---Recursos da model

    class AuthController extends Action{
        public function autenticar() {     
            /*O Container faz a instância automática da
            classe passada como parâmetro no getModel e 
            conecta automaticamente com o banco */
           $usuario = Container::getModel('usuario');
           $usuario->__set('email', $_POST['email']);
           $usuario->__set('senha', md5($_POST['senha']));

           $usuario->autenticar();

           /*Após o método autenticar ser chamado, ele irá retornar
           ou não os atributos id e nome preenchidos no objeto,
           caso ambos os atributos estejam diferentes de vazios,
           iremos autenticar, caso contrário, iremos responder com
           uma mensagem de erro */
           if($usuario->__get('id') != '' && $usuario->__get('nome')){
               //Iniciando a sessão
               session_start();
               /*Inserindo o id do usuário e o nome na sessão, veja
               que estou criando os índices da session (id e nome) */
               $_SESSION['id'] = $usuario->__get('id');
               $_SESSION['nome'] = $usuario->__get('nome');

               /*Redirecionando para timeline do usuário */
               header('Location: /timeline');
           }else{
               //Caso ocorra algum erro, será direcionado para o diretório raiz 
               header('Location: /?login=erro');
           }
        }

        public function sair(){
            session_start();
            session_destroy();
            header('Location: /');
        }

        
        
    }
?>