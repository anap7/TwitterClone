<?php 
    //Para que o autoload funcione corretamente, nós devemos definir o namespace
    namespace App\Controllers;
     //-----Recursos do miniframework 
    use MF\Controller\Action;
    use MF\Model\Container;
    //---Recursos da model

    class IndexController extends Action{
        public function index() {     
            $this->render('index');
        }

        public function inscreverse() {  
            $this->view->erroCadastro = false;  
            //verificando se posso mandar outros atributos 
            //$this->view->teste = "Olá, estou testando";
            
            /*Isso será utilizado para verificar se o cadastro
            foi realizado com sucesso ou não, estou atribuindo
            diretamente na action inscreverse para que eu possa
            utilizá-la na sua própria página */
            $this->render('inscreverse');
        }

        public function registrar(){
            //receber os dados do formulário
            /*O método de envio do formulário da página inscrever-se é
            post, portando, basta recuperar o método. */
            $usuario = Container::getModel('Usuario');
            /*Lembrando que o Contianer (MF/Model/Container) é responsável por
            com base em uma string passado como parâmetro (Usuario) instanciar
            o modelo/classe e instanciar a classe de conexão com o banco, 
            retornando o objeto instanciado e a conexão com o banco estabelecida */

            $usuario->__set('nome', $_POST['nome']);
            $usuario->__set('email', $_POST['email']);
            $usuario->__set('senha', md5($_POST['senha']));
            /*A função md5 para trasformar a senha é um hash de 32 caracters */

            //Cadastrando no banco
            if($usuario->validarCadastro() && count($usuario->getEmail()) == 0){
                /*Utilizando método count para contar quantos registros 
                há na lista, se for 0, significa que não há nenhum
                e-mail parecido com o que foi cadastrado, caso contrário
                será retornada uma mensagem de erro */
                   //Se não houver nenhum email registrado, iremos cadastrar o usuário
                    $usuario->salvar();
                    /*Quando o cadastro for realizado, ele será direcionado para a 
                    Action.php (MF>Controller>Action) e irá redirecionar para a página
                    de sucesso */
                    $this->render('cadastro');
               }else{
                /*Através do atributo view (que pertence à classe Action e como estamos herdando
                ela, podemos utilizá-la), vamos criar um novo atributo chamado erroCadastro e 
                atribuir o valor true */
                   $this->view->erroCadastro = true;
                    $this->render('inscreverse');
               }           
        }
        
    }
?>