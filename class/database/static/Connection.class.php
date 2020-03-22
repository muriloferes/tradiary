<?php

require_once(__DIR__."/ConnectionResource.class.php");

final class Connection extends PDO {
    
    private $transaction = 0; // Controlador do profundidade de transacoes

    public function __construct(){
		// Verifica o sistema operacional
		if(strtolower(substr(PHP_OS, 0, 3)) === "win"){
			// Desenvolvimento
			$db = ["host" => "localhost", "port" => "5432", "user" => "postgres", "pass" => "postgres", "dbname" => "tradiary"];
		}else{
			// Producao
			$db = parse_url(getenv("DATABASE_URL"));
		}

		$dbname = ($db["path"] ? ltrim($db["path"], "/") : $db["dbname"]);
		die(var_dump($db));
        parent::__construct("pgsql: host={$db["host"]}; port={$db["port"]}; user={$db["user"]}; password={$db["pass"]}; dbname={$dbname}");
    }

    function exec($statement){
		return $this->query($statement);
	}

	function query($statement){
		if(is_array($statement)){
			$statement = implode(" ", $statement);
		}

		$result = parent::query($statement);
		if($result === false){
            $error = $this->errorInfo();
            throw new Exception("Houve uma falha ao executar a instrução SQL:\n{$statement}\n\nErro:\n{$error[2]}");
		}
		return new ConnectionResource($result);
	}

    function start_transaction(){
		if($this->transaction === 0){
			parent::beginTransaction();
		}
		$this->transaction++;
    }
    
    function commit(){
		if($this->transaction === 1){
			parent::commit();
		}elseif($this->transaction === 0){
            throw new Exception("Não é possível confirmar uma transação que não foi iniciada no banco de dados.");
		}
		$this->transaction--;
    }
    
    function rollback(){
		if($this->transaction === 1){
			parent::rollBack();
		}elseif($this->transaction === 0){
            throw new Exception("Não é possível retroceder uma transação que não foi iniciada no banco de dados.");
		}
		$this->transaction--;
	}

}
