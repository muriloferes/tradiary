<?php

require_once(__DIR__."/Connection.class.php");
require_once(__DIR__."/DBColumnDate.class.php");
require_once(__DIR__."/DBColumnDateTime.class.php");
require_once(__DIR__."/DBColumnDecimal.class.php");
require_once(__DIR__."/DBColumnInteger.class.php");
require_once(__DIR__."/DBColumnString.class.php");
require_once(__DIR__."/DBColumnTime.class.php");

define("DATABASE_TYPE_PUBLIC", 0);
define("DATABASE_TYPE_PRIVATE", 1);

abstract class DBTable{

	protected $database_type; // Nome do banco que deve ser conectado (ids do postgresql no heroku)
	protected $connection; // Conexao com o banco de dados
    protected $table; // Nome da tabela no banco de dados
    protected $primarykey; // Nome da chave primaria da tabela
	public $columns; // Array com as colunas relativas da tabela (como public apenas por causa do metodo "search")

	function __construct(String $id = null){
		global $connection_public, $connection_private;

		// Identifica o tipo de conexao a ser feita
		switch($this->database_type){
			case DATABASE_TYPE_PUBLIC:
				if(!is_object($connection_public)){
					$connection_public = new Connection("DATABASE");
				}
				$connection = $connection_public;
				break;
			case DATABASE_TYPE_PRIVATE:
				if(!is_object($connection_private)){
					if(strlen($_SESSION["current-database"]) === 0){
						throw new Error("Não é possível iniciar um objeto ligado a um banco de dados privado sem antes estar conectado a um.");
					}
					$connection_private = new Connection($_SESSION["current-database"]);
				}
				$connection = $connection_private;
				break;
		}
        $this->connection = $connection;
        
        // Monta o nome do campo de chave primaria ("id" + $tabela)
        $this->primarykey = "id{$this->table}";

		// Verifica se a chave primaria foi informada, e caso sim, ja busca o registro no banco de dados
		if(strlen($id) > 0){
            if($object = $this->searchFirst("{$this->primarykey} = '{$id}'")){
                foreach($this->columns as $column){
                    call_user_func([$this, "set{$column->getname()}"], call_user_func([$object, "get{$column->getname()}"]));
                }
            }
		}
	}

	// Retorna o objeto em formato array
	function as_array(Bool $formated = false, Array $columns = null){
		$arr = [];
		foreach($this->columns as $column){
			if(is_array($columns) && !in_array($column->getname(), $columns)){
				continue;
			}
			$arr[$column->getname()] = $column->getvalue($formated);
		}
		return $arr;
	}

	// Retorna o objeto em formato JSON
	function as_json(Bool $formated = false, Array $columns = null){
		return json_encode($this->as_array($formated, $columns));
	}

	// Retorna uma lista com o nome de todas as colunas da tabela
	function column_names(){
		$arr_name = [];
		foreach($this->columns as $column){
			$arr_name[] = $column->getname();
		}
		return $arr_name;
	}

	// Deleta o registro atual do banco de dados (necessita a chave primaria)
	function delete(){

		// Captura valor da chave primaria
		$primarykey_value = call_user_func([$this, "get{$this->primarykey}"]);

		// Trava para nao prosseguir se a chave nao estiver preenchida
		if(strlen($primarykey_value) === 0){
			throw new Exception("Não é possível excluír um registro sem antes informar o ID.");
		}

		// Cria a instrucao para apagar o registro
		$sql = "DELETE FROM \"{$this->table}\" WHERE {$this->primarykey} = '{$primarykey_value}'";
		
		// Executa o SQL
		if($this->connection->query($sql) === false){
            $error = $this->connection->errorInfo();
            throw new Exception("Houve uma falha ao excluír o registro da tabela '{$this->table}':\n{$error[2]}");
		}

		// Retorna com sucesso
		return true;
	}

	// Verifica se o registro atual existe no banco de dados
	function exists(){
        $column = $this->columns[$this->primarykey];
        if(strlen($column->getvalue()) == 0){
            return false;
        }else{
            $res = $this->connection->query("SELECT COUNT({$column->getname()}) FROM \"{$this->table}\" WHERE {$column->getname()} = '{$column->getvalue()}'");
            return ($res->fetchColumn() > 0);
        }
	}

	// Mesmo medoto: searchFist
	static function findFirst($where = null, $order = null){
		return self::searchFirst($where, $order);
	}

	// Recarrega os dados no objeto atual a partir do banco de dados
	function refresh(){
        $this->__construct($this->columns[$this->primarykey]->getvalue());
	}

	// Salva o registro atual no banco de dados, identificando se deve executar um INSERT ou UPDATE
	// Em seguida ja atualiza o objeto com o valores do banco de dados
	function save(){
		// Verifica se deve preencher o contrato
		if($this->database_type === DATABASE_TYPE_PRIVATE){
			if(key_exists("idcontract", $this->columns) && strlen($this->getidcontract()) === 0){
				$this->setidcontract($_SESSION["idcontract"]);
			}
		}

		// Define a acao
		$column_pk = $this->columns[$this->primarykey];
        $action = (strlen($column_pk->getvalue()) === 0 ? "INSERT" : "UPDATE");

		// Verifica que tipo de acao deve tomar na gravacao do registro
		switch($action){
			case "INSERT":
				$arr_name = [];
				$arr_value = [];
				foreach($this->columns as $column){
					if(strlen($column->getvalue()) > 0){
						$arr_name[] = $column->getname();
						if(in_array($column->gettype(), array("date", "datetime", "string", "time"))){
							$arr_value[] = "'".str_replace("'", "''", $column->getvalue())."'";
						}else{
							$arr_value[] = $column->getvalue();
						}
					}
				}
                $sql = "INSERT INTO \"{$this->table}\" (".implode(", ", $arr_name).") VALUES (".implode(", ", $arr_value).") ";
				break;
			case "UPDATE":
				if(isset($this->columns["update"])){
					$this->columns["update"]->setvalue(date("Y-m-d H:i:s"));
				}

				$arr_update = array();
				foreach($this->columns as $column){
					if(strlen($column->getvalue()) > 0){
						if(in_array($column->gettype(), array("date", "datetime", "string", "time"))){
							$value = "'".str_replace("'", "''", $column->getvalue())."'";
						}else{
							$value = $column->getvalue();
						}
					}else{
						$value = "NULL";
					}
					$arr_update[] = $column->getname()." = ".$value;
				}
				$sql = "UPDATE \"{$this->table}\" SET ".implode(", ", $arr_update)." WHERE {$column_pk->getname()} = '{$column_pk->getvalue()}' ";
				break;
		}
		$sql .= "RETURNING *";

		// Executa o SQL
		$res = $this->connection->query($sql);
		if($res !== false){
			// Preenche o objeto atual com os valores vindo do banco de dados
			// Isso faz com que o objeto receba os valores "defaults" do banco
			$row = $res->fetch(2);
			foreach($row as $column => $value){
				if(is_object($this->columns[$column])){
					call_user_func(array($this, "set{$column}"), $value);
				}
			}
		}else{
            $error = $this->connection->errorInfo();
            throw new Error("Houve uma falha ao gravar o registro da tabela '{$this->table}':\n{$error[2]}");
		}

		// Retorna com sucesso
		return true;
	}

	// Localiza os registros no banco de dados
	// Caso o parametro $where nao seja preenchido, sera utilizado os valores ja informados no objeto
	function search($where = null, $order = null, $limit = null, $offset = null, $as_array = false, $formated = true, $columns = null){
		if(is_array($where)){
			$where = implode(" AND ", $where);
		}

		if(is_null($where)){
			$where = [];
			foreach($this->columns as $column){
				if(strlen($column->getvalue()) > 0){
					if(in_array($column->gettype(), ["date", "string", "time"])){
						$where[] = $column->getname()." = '".str_replace("'", "''", $column->getvalue())."'";
					}else{
						$where[] = $column->getname()." = ".$column->getvalue();
					}
				}
			}
			$where = implode(" AND ", $where);
		}elseif(is_array($where)){
			if(count($where) === 0){
				return [];
			}else{
                $firstCondition = reset($where);
                $terms = [" ", ",", "=", "<", ">", " in", " like", " ilike"];
                $found = false;
                foreach($terms as $term){
                    if(strpos($firstCondition, $term) !== false){
                        $found = true;
                        break;
                    }    
                }
				if(!$found){
					$where = "{$this->primarykey} IN (".implode(", ", $where).")";
				}else{
					$where = implode(" AND ", $where);
				}
			}
		}

		$arr_object = [];

		$query = "SELECT ";
		if(is_array($columns) && count($columns) > 0){
			$query .= implode(", ", $columns);
		}else{
			$query .= "*";
		}
		$query .= " FROM \"".$this->tablename()."\" ";
		if(strlen($where) > 0){
			$query .= "WHERE {$where} ";
		}
		if(strlen($order) > 0){
			$query .= "ORDER BY {$order} ";
		}
		if(strlen($limit) > 0){
			$query .= "LIMIT {$limit} ";
		}
		if(strlen($offset) > 0){
			$query .= "OFFSET {$offset} ";
		}
		
		$res = $this->connection->query($query);
		if($res === false){
            $error = $this->connection->errorInfo();
            throw new Error("Houve uma falha ao consultar os registros na tabela '{$this->table}':\n{$error[2]}");
		}
		$arr = $res->fetchAll();
		foreach($arr as $row){
			$object = new $this();
			foreach($row as $column => $value){
				$object->columns[$column]->value = $value;
			}
			$arr_object[$object->columns[$this->primarykey]->getvalue()] = $object;
		}
		if($as_array){
			$arr_object_aux = [];
			foreach($arr_object as $object){
				$object_aux = $object->json(true, $formated);
				if(is_array($columns) && count($columns) > 0){
					foreach($object_aux as $column => $value){
						if(!in_array($column, $columns)){
							unset($object_aux[$column]);
						}
					}
				}
				$arr_object_aux[] = $object_aux;
			}
			$arr_object = $arr_object_aux;
		}
		return $arr_object;
	}

	// Localiza o primeiro registro no banco de dados
	// Caso o parametro $where nao seja preenchido, sera utilizado os valores ja informados no objeto
	static function searchFirst($where = null, $order = null){
		$object = new static();
		$arr_object = $object->search($where, $order, 1);
		if(count($arr_object) === 0){
			return false;
		}
		return reset($arr_object);
	}

	// Retorna o nome da tabela
	function tablename(){
		return $this->table;
	}

}