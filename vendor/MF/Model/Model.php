<?php 
    namespace MF\Model;
    
    abstract class Model{
        protected $db; //Vai receber a conexão do banco

        public function __construct(\PDO $db){
            $this->db = $db;
        }
    }
?>