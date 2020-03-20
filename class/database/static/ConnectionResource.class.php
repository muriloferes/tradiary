<?php

final class ConnectionResource {

    private $result; // Objeto: PDOStatement

    public function __construct(PDOStatement $result){
        $this->result = $result;
    }

    function fetchAll(Bool $verifyTypes = false){
        // Verifica se deve fazer as verificacoes de tipo das colunas
        if(!$verifyTypes){
            return $this->result->fetchAll(PDO::FETCH_ASSOC);
        }

        // Dados que serao retornados
        $data = [];
        
        // Verifica quais campos devem ser convertidos
        $convert_float = [];
        $n = $this->result->columnCount();
        for($i = 0; $i < $n; $i++){
            $meta = $this->result->getColumnMeta($i);
            if(in_array($meta["native_type"], ["decimal", "float", "integer", "numeric"])){
                $convert_float[] = $meta["name"];
            }
        }

        // Captura os dados
        while($row = $this->result->fetch(PDO::FETCH_ASSOC)){
            foreach($convert_float as $name){
                $row[$name] = (float) $row[$name];
            }
            $data[] = $row;
        }

        // Retorna os dados
        return $data;
    }

    function __call($name, $arguments){
        return call_user_func_array([$this->result, $name], $arguments);
    }

}