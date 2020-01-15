<?php 
    //Para que o autoload funcione corretamente, nós devemos definir o namespace
    namespace App\Controllers;
     //-----Recursos do miniframework 
    use MF\Controller\Action;
    use MF\Model\Container;
    //---Recursos da model

    class AppController extends Action{
       
        public function timeline(){
            //Recuperando a sessão de AuthController
            session_start();
            
            $this->validaAutenticacao();
        
            //Instanciando a classe tweet
            $tweet = Container::getModel('Tweet');
            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            $this->view->total_seguidores = $usuario->getTotalSeguidores();
            $this->view->total_seguindo = $usuario->getTotalSeguindo();
            $this->view->total_tweets = $usuario->getTotalTweet();
            $this->view->info_usuario = $usuario->getInfoUser();

            //Recuperando o id do usuário para utilizar na query
            $tweet->__set('id_usuario', $_SESSION['id']);

            $tweets = $tweet->recuperarTweet();
                
            $this->view->tweets = $tweets;
            /*Por que estou recuperando os tweets e listando a partir desse método?
            Bom, é no momento em que carregamos a timeline, nós nos deparamos com o
            perfil e os respectivos tweets já postados, portanto, é na action timeline
            que os tweets são carregados e exibidos */
                
            //Enviando para redenrizar a nossa view
            $this->render('timeline');
        }

        public function tweet(){
            $this->validaAutenticacao();
            //Instanciando o $tweet
            $tweet = Container::getModel('Tweet');

            $tweet->__set('tweet', $_POST['tweet']);
            //Recuperando o id de usuário pela sessão
            $tweet->__set('id_usuario', $_SESSION['id']);

            $tweet->salvarTweet();

            header('Location: /timeline');       
        }

        /*Função responsável por validar a autenticação do usuário através do preenchimento
        do id e do nome na Sessão */
        public function validaAutenticacao() {
            //Recuperando a sessão de AuthController
            session_start();
            /*Inserindo novamente a lógica de verificação dos dados, pois vamos sempre 
            conferir se todo o processo de autenticação foi feito */

            /*Antes de acessar a timeline, nós vamos verificar se a
                sessão contém o id e o nome preencidos, caso não seja,
                vamos redirecionar para o erro novamente.
                
                Isso é utilizado para verificar se todo o processo de
                autenticação foi realizado perfeitamente. Caso a pessoa
                tente acessar o /timeline pelo navegador anônimo, por exemplo,
                nós iremos redirecionar para a página de login com a mensagem de erro. */
            if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
                header('Location: /?login=erro');
            }	
    
        }

        public function quemSeguir(){
            //Validando
           $this->validaAutenticacao();

           $tweet = Container::getModel('Tweet');
           $usuario = Container::getModel('Usuario');
           $usuario->__set('id', $_SESSION['id']);

           $this->view->total_seguidores = $usuario->getTotalSeguidores();
           $this->view->total_seguindo = $usuario->getTotalSeguindo();
           $this->view->total_tweets = $usuario->getTotalTweet();
           $this->view->info_usuario = $usuario->getInfoUser();

            $pesquisarPor = '';
            /*Primeiro verificando se a variável pesquisaPor foi inserida
            na URL */
           if(isset($_GET['pesquisarPor'])){
               //Caso pesquisaPor esteja inserida na url, iremos atribuir seu valor à variável pesquisarPor
                $pesquisarPor = $_GET['pesquisarPor'];
           }else{
               //Caso não tenha sido inserida na url, a variável recebe o vazio
            $pesquisarPor = '';
           }
           //Iniciando a variável
           $usersList = Array();

           if($pesquisarPor != ''){
               /*Caso a nossa pesquisa seja diferente de vazio, ou seja,
               caso o campo de busca seja preenchido, nós iremos realizar
               a pesquisa no banco com base no que foi preenchido no campo */

               /*Precisamos setar o nome que estamos buscando e este está
               setado em $pesquisarPor, depois de ser verificado se está
               vazio ou não */
               $usuario->__set('nome', $pesquisarPor);
               /*Inserindo o o id do usuário que está atribuido na sessão no objeto
               usuario. Esse id setado será utilizado para identificar qual usuário está
               autenticado e fazer com que ele não seja filtrado na busca, pois seria estranho
               o usuário seguir ele mesmo.  */
        
               //Com o atributo nome setado, iremos pesquisar por esse nome no banco de dados
               //Lembrando que no método getAllUsers, precisamos do nome setado 
               $usersList = $usuario->getAllUsers();
 
           }
            $this->view->userlist = $usersList;
            //Rederizando o página
            $this->render('quemSeguir');
        }

        public function acao(){
            $this->validaAutenticacao();
            $acao = '';
            $id_usuario_seguindo = '';
            if(isset($_GET['acao']) && isset($_GET['id_usuario']))
            {
                $acao = $_GET['acao'];
                $id_usuario_seguindo = $_GET['id_usuario'];
            }else{
                $acao = '';
                $id_usuario_seguindo = '';
            }
            //Instanciando usuário
            $usuario = Container::getModel('Usuario');
            /*Veja que acima que temos a acao(seguir ou não seguir) e o id_usuario
            que é o usuário que vamos seguir e vamos setar o id do usuário da sessão,
            pois na tabela usuarios_seguidores temos: id, id_usuario(usuário da sessão)
            e id_usuario_seguido (id do usuário que o usuário da sessão está seguindo,
            lembrando que isso está setado na sessão) */
            $usuario->__set('id', $_SESSION['id']);

            if($acao == 'seguir'){
                //Caso a ação seja seguir, iremos achamar o método seguirUsuario, passando o id dele como parâmetro
                $usuario->seguirUsuario($id_usuario_seguindo);
            }else if($acao == 'deixar_de_seguir'){
                $usuario->deixarSeguirUsuario($id_usuario_seguindo);
            }
            header('Location: /quem_seguir');
        }

        public function remover(){
           $idTweet =  $_GET['idTweet'];
           $tweet = Container::getModel('Tweet');
           $tweet->__set('id', $idTweet);
           $tweet->removerTweet();
           header('Location: /timeline');
        }
    }

?>