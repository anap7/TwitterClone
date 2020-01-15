<?php 
    namespace App\Model;
    use MF\Model\Model;

    class Tweet extends Model{
        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
           $this->$atributo = $valor;
        }

        public function salvarTweet(){
            $query = "insert into tweets(id_usuario, tweet) values (:id_usuario, :tweet)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));
            $stmt->execute();
            //Retornando o próprio objeto
            return $this;
        }

        public function recuperarTweet(){
            /*Estou usando o DATE_FORMAT para formatar a data com base
            em, por exemplo 14/12/1998 16:20, veja que coloquei como primeiro
            parâmetro a t.data, que é a data registrada e como segundo parâmetro
            o formato que eu quero dela e utilizando o as para atribuir esse formato
            ao data, podendo ser utilizado no array */
            $query = "select t.id, t.id_usuario, us.nome, t.tweet, 
            DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data from tweets 
            as t left join usuarios as us on (t.id_usuario = us.id)
             where id_usuario = :id_usuario 
             or t.id_usuario in (select id_usuario_seguido from usuarios_seguidores where id_usuario = :id_usuario)     
             order by t.data desc";
             /*Veja que estou retornando os ids dos usuários que estão sendo seguidos pelo usuário autenticadp
             a partir do :id_usuario (id do usuário autenticado). Estou selecionando o id, id de usuário, nome
             e tweet ou do usuário autenticado ou do usuário que estou seguindo, veja que o in busca o id
             que está sendo seguido pelo usuário autenticado e caso haja retorno, nós mostramos o tweet do
             usuário seguido.*/
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function removerTweet(){
            $query = "delete from tweets where id = :idTweet";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':idTweet', $this->__get('id'));
            $stmt->execute();
            echo 'deletou';
        }
    }
?>