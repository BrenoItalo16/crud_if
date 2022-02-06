<?php


class Setor_model extends AbstractModel {

		#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
		public $table = "setores";
		public $fields = ["nome"];
		
		#um setor pode ter varios usuarios
		#a key é o campo no formulário que contém a id do usuário
		public $oneToMany = [["table"=>"usuarios", "key"=>"pessoa_id"]];


		
		public function remove_usuario($id_setor,$id_usuario){
			$obj = R::load($this->table, $id_setor);
			unset($obj->ownUsuariosList[$id_usuario]);
			R::Store($obj);

		}


}