<?php

class AbstractModel extends CI_Model {

	#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
	public $table = "";
	public $fields = [""];
	public $searchFields = [];

	#para fazer associacoes entre tabelas
	#defina uma matriz com os relacionamentos [['table'=>'usuarios', 'key'=>'usuario_id'], ...]
	public $oneToMany = false;
	public $manyToOne = false;
	#a matriz do manyToMany requer 3 itens
	#[['table'=>'usuarios', 'key'=>'usuario_id', 'assocTable'=>'gruposusuarios'], ...]
	public $manyToMany = false;

	public $secureField = false;
	public $secureLevels = false;

	public $arrayFields = false;

	public function __construct(){
		parent::__construct();

		if ($this->arrayFields == false)
			$this->arrayFields = [];
			
		foreach($this->fields as $key=>$field){
			if (!is_numeric($key)){
				if ($field == "array"){
					array_push($this->arrayFields,$key);
				} else
				if ($field == "secure"){
					$this->secureField = $key;
				}
			}
		}
		$arr = array_merge($this->arrayFields);
		if ($this->secureField != false){
			array_push($arr, $this->secureField);
		}
		foreach($arr as $field){
			unset($this->fields[$field]);
			array_push($this->fields, $field);
		}
		if (count($this->arrayFields) == 0)
			$this->arrayFields = false;
		
		

		#se tiver campo de seguranca
		if ($this->secureField != false){
			$_SESSION["model_seguranca"] = get_class($this);
		}
	}


	function decamelize($string) {
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
	}

	public function parseArrayFields($obj){
		if ($this->arrayFields != false){
			foreach($this->arrayFields as $field){
				if (isset($obj->$field) && !is_array($obj->$field))
					$obj->$field = explode(";",$obj->$field);
					/*if (is_array($obj->$field) && count($obj->$field) == 1 && $obj->$field[0] == ""){
						$obj->$field = [];
					}*/
			}
		}
		return $obj;
	}
	
	//recebo a id do elemento que quero abrir
	public function get($id = null){
		if ($id == null){
			//se a id for nula, retorna um array vazio

			//se houver id no POST, provavelmente é porque houve erro de validacao
			//retorna os dados que foram submetidos pelo formulário
			if (isset($_POST['id'])){
				$data = $_POST;
				if ($_POST['id'] != ""){
					$state = $this->get($_POST['id']);
		
					foreach($_POST as $key=>$val){
						if ( isset($state[$key]) ){
							$state[$key] = $_POST[$key];
						}
					}
					
					$data = $state;
				}
				return $this->parseArrayFields($data);
			}

			return [];
		} else {
			//caso contrário, busca no banco
			//aquele elemento da tabela que tem aquela id
			return $this->parseArrayFields(R::load($this->table,$id));
		}
	}

	public function findOne($data){
		$where = [];
		$whereData = [];
		foreach($data as $key=>$value){
			array_push($where, "$key = ?");
			array_push($whereData, $value);
		}
		$where = implode(" and ", $where);

		return R::findOne($this->table,$where,$whereData);
	}

	//recebe o número da página que será exibida, inicia na 1
	public function pagination($per_page, $page, $busca = null){

		//Quantidade de itens por pagina
		$loc = ($page-1) * $per_page;

		$where = [];
		$values = [];
		if ($busca != null){
			#se o searchFields tiver sido preenchido, usa ele, se nao, usa o fields
			$fields = (count($this->searchFields) == 0) ? $this->fields : $this->searchFields;
			
			foreach($fields as $field ){
				array_push($where, $this->decamelize($field)." like ?");
				array_push($values,"%".$busca."%");
			}
		}
		$where = implode(" or ", $where);
		
		//Seleciona todos os dados, ordenando pelo primeiro campo da tabela
		$list = R::findAll($this->table , $where . " ORDER BY " . $this->decamelize($this->fields[0]) 
						. " LIMIT $loc,$per_page ", $values );
		
		//Recupera a quantidade total de itens na tabela
		$qtd = R::count($this->table, $where, $values );
		
		//Retorna um array com os dados, total de registros,
		//qtd de itens por pagina e a quantidade máxima de páginas
		return ["data"=>$list,"total_rows"=>$qtd, "per_page"=>$per_page, "page_max"=>ceil($qtd/$per_page)];
	}
		
	public function options($field){
		$all = R::getAll("select id, {$field} from {$this->table}");
		return [""] + toOptions($all);
	}
	
	public function all(){
		
		return R::findAll($this->table);
		
	}
	
	public function delete($id){
		
		$obj = R::load($this->table,$id);
		R::trash($obj);
		
	}


	public function preSave($obj, $data){
		return $obj;
	}

	public function posSave($obj, $data){
		
	}


	public function getOrSave($data){
		$obj = $this->findOne($data);
		if ($obj == null){
			return $this->save($data);
		} else {
			return $obj;
		}
	}


	public function save($data=null) {
		
		if ($data == null){
			$data = $_POST;
		}

		$fields = prepare_fields($this->fields, $data);
		
		$obj = R::load($this->table, val($data,'id'));

		
		foreach($fields as $key=>$val){
			if (is_array($val)){
				$obj[$key] = implode(";",$val);
			} else {
				$obj[$key] = $val;
			}
		}

		#verifica se existe relacionamentos
		if ($this->oneToMany){
			foreach($this->oneToMany as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"], $data[$rel["key"]]);
					#para relacionar um para muitos utilize essa notacao
					#own+NomeDaTabela+List
					#essa propriedade sera um array com varios daquele model que voce adicionar
					$tblName = "own" . ucfirst($rel["table"]) . "List";
					array_push($obj->$tblName, $another);
				}
			}
		}

		if ($this->manyToOne){
			foreach($this->manyToOne as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"],$data[$rel["key"]]);
					$tblName = $rel["table"];
					#associa o item
					$obj->$tblName = $another;
				}
			}
		}

		if ($this->manyToMany){
			foreach($this->manyToMany as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"], $data[$rel["key"]]);
					#cria a tabela de associacao
					$assoc = R::dispense($rel["assocTable"]);

					#associa os itens
					$tbl1 = $rel["table"];
					$tbl2 = $this->table;

					$found = R::find($rel["assocTable"], "{$tbl1}_id = ? and {$tbl2}_id = ?", [$another->id, $obj->id]);
					
					#para evitar que cadastre duas vezes
					#antes de salvar, verifica se ja foram relacionados
					if ($found == null){
						$assoc->$tbl1 = $another;
						$assoc->$tbl2 = $obj;
						R::Store($assoc);
					}
				}
			}
		}

		$obj = $this->preSave($obj, $data);
		
		$id = R::store($obj);

		$this->posSave($obj, $data);

		return $id;
	}


}