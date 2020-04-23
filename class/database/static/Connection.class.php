<?php

require_once(__DIR__."/ConnectionResource.class.php");

final class Connection extends PDO {
    
    private $transaction = 0; // Controlador do profundidade de transacoes

    public function __construct(){
		// Verifica o sistema operacional
		if(strtolower(substr(PHP_OS, 0, 3)) === "win"){
			// Desenvolvimento
			$db = parse_url("postgres://qzlohtnurokogr:9533e686953c10bff4c4ca9adb12d209c1e478f6564eddfffcbe4b1e03b35b75@ec2-23-22-156-110.compute-1.amazonaws.com:5432/d6ekvhp80qlpnp");
		}else{
			// Producao
			$db = parse_url(getenv("DATABASE_URL"));
		}

		$dbname = ($db["path"] ? ltrim($db["path"], "/") : $db["dbname"]);
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
