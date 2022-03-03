<?php

class Agenda_model extends AbstractModel {

	#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
	public $table = "agenda";
	public $fields = ["nome","email","senha", "tipo", "area"=>"array", "foto", "nivel"=>"secure"];
	public $searchFields = ["nome","email"];
	public $tiposUsuarios = [1=>"Professor", 2=>"Técnico", 3=>"Bolsista"];
	public $areasUsuarios = [1=>"Programação", 2=>"Banco de dados", 3=>"Redes", 4=>"Manutenção"];
	
	#Seguranca
	#variável que contem os níveis
	public $secureLevels = [1=>"Admin", 2=>"Comum", 3=>"Convidado"];
	

	#varios usuarios podem ter o mesmo setor
	#a key é o campo no formulario que contem a id do setor
	public $manyToOne = [["table"=>"setores", "key"=>"setores_id"]];

	public $manyToMany = [["table"=>"grupos","key"=>"grupo_id", "assocTable"=>"gruposusuarios"]];
	

	public function login($data){
		$data["senha"] = sha1($data["senha"]);
		return $this->findOne($data);
	}

	public function preSave($obj, $data) {

		#Caso alguem tente salvar como Admin, seta para a permissao padrao
		if (($obj["nivel"] == "Admin" || $obj["nivel"] == "") && !Seguranca::temPermissao("Admin")){
			$obj["nivel"] = 3;
		}

		
		if ($obj["senha"] != ""){
			//criptografa a senha
			$obj["senha"] = sha1($obj["senha"]);
		} else {
			//caso a senha venha em branco, nao vai modificar
			unset($obj["senha"]);
		}

		//A função preSave sempre deve retornar o $obj
		return $obj;
	}
	

	public function findNotInSetor($idSetor){

		return R::findAll($this->table,"setores_id <> ? or setores_id is null ",[$idSetor]);			
	}

	public function findNotInGrupo($idGrupo){

		$sql = "select * from usuarios where 
				id not in (select usuarios_id from gruposusuarios where grupos_id = ? )";
		return R::getAll($sql,[$idGrupo]);
	}

	public function resetAdmin(){

		$email = "admin@admin.com";
		$senha = "123456";

		#deleta todos os admins
		$sql = "delete from gruposusuarios
			where usuarios_id in (select id from usuarios where email = '$email')";
		R::exec($sql);
		$sql = "delete from {$this->table} where email = '$email'";
		R::exec($sql);
			

		#recria o admin
		$admin = R::dispense($this->table);
		$admin->nome = "admin";
		$admin->email = $email;
		$admin->senha = sha1($senha);
		$admin->nivel = 1;
		R::Store($admin);
		return ["id"=>$admin->id, "email"=>$email, "senha"=>$senha];
	}


}
