<?php
namespace MF\Model;
//Chamando a classe Connection
use App\Connection;

    class Container{
        public static function getModel($model){
            $class = "\\App\\Model\\".ucfirst($model);

            $conexao = Connection::getDb();
            
            return new $class($conexao);
        }
    }
?>