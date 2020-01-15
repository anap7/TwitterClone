<?php 
    namespace App\Model;
    use MF\Model\Model;

    class Usuario extends Model{
        /*Essa classe vai permitir salvar, validar se um cadastro
        pode ser realizado, recuperar usuário por e-mail (verificar
        existência de e-mail) */

        /*A Classe Model precisa ser estendida pois
        ela que realiza a conexão com o banco a partir
        do construtor*/

        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
           $this->$atributo = $valor;
        }

        public function salvar(){
            $query = "insert into usuarios(nome, email, senha) values (:nome, :email, :senha)";
            /*Veja que estou acessando o atributo db, ele pertence à classe Model e posso
            utilizá-lo pois está sendo herdado*/
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha')); //Utilizar o método md5

            //executandp
            $stmt->execute();
            //retornado o próprio objeto
            return $this;
        }

        public function validarCadastro(){
            $valido = true;
            /*Verificando se o e-mail, senha e nome tem no mínimo 3
            caracteres 
            
            O strlen conta o número de caracteres de uma variável string,
            caso seja menor que 3 caracteres, será retornado um boolean 
            false */
            if(strlen($this->__get('nome')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('email')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('senha')) < 3){
                $valido = false;
            }

            return $valido;
        }

        //Verificar a existência do email
        public function getEmail(){
            $query = "select nome, email, senha from usuarios where email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();
            /*Utilizando o fetchAll para retornar todos os registros e utilizando o 
            FETCH_ASSOC para trazer um array associativo */
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);            
        }

        public function autenticar(){
            $query = "select id, nome, email, senha from usuarios where email = :email and senha = :senha";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha')); 
            $stmt->execute();

            $usuarioAutentic = $stmt->fetch(\PDO::FETCH_ASSOC);
            //retorna um array associativo

            if($usuarioAutentic['id'] != '' && $usuarioAutentic['nome'] != ''){
                /*Se for retornado o id e nome preenchidos, ou seja, não for de vazio,
                nós vamos iniciar o processo de autenticação, inserindo o nome e o 
                id dentro do objeto*/
                $this->__set('id', $usuarioAutentic['id']);
                $this->__set('nome', $usuarioAutentic['nome']);
            }
            
            //retornando o usuário
            return $this;
        }
        /*Função responsável por fazer a buscas de usuário que estão cadastrados no twitter
        para que possam ser seguidos */
        public function getAllUsers(){
            //Utilizando o like pois buscamos com algo parecido com o nome pesquisado 
            //$query = "select id, nome, email from usuarios where nome like :nome and id != :id_usuario";
            $query = "
			select 
				u.id, 
				u.nome, 
				u.email,
				(
					select
						count(*)
					from
						usuarios_seguidores as us 
					where
						us.id_usuario = :id_usuario and us.id_usuario_seguido = u.id
				) as seguindo_sn
			from  
				usuarios as u
			where 
				u.nome like :nome and u.id != :id_usuario
			";
            /*Por que id != :id_usuario? Porque eu não quero filtrar o próprio perfil autenticado na 
            lista de pesquisa, pois seria estranho o usuário seguir ele mesmo, portanto, o banco deve
            retornar usuários cujo o id seja diferente do id do usuário autenticado */
            $stmt = $this->db->prepare($query);
            /*Lembrando que como utilizamos o like, precisamos do % entre o nome
            procurado porque pode haver algo na direita ou na esquerda do nome */
            $stmt->bindValue(':nome', '%'. $this->__get('nome').'%');
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function seguirUsuario($id_usuario_seguindo){
            $query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguido)
            values (:id_usuario, :id_usuario_seguindo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }

        public function deixarSeguirUsuario($id_usuario_seguindo){
            $query = "delete from usuarios_seguidores where id_usuario = :id_usuario
            and id_usuario_seguido = :id_usuario_seguindo";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();
            return true;
        }

        //Método, recuperar as informações do usuário para exibir no perfil
        public function getInfoUser(){
            $query = "select nome from usuarios where id = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Recuperar o total de tweets
         public function getTotalTweet(){
            $query = "select count(*) as total_tweet from tweets where id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Recuperar o total de usuário que estamos seguindo
        public function getTotalSeguindo(){
            $query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Recuperar o total de seguidores
        public function getTotalSeguidores(){
            $query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguido = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
?>