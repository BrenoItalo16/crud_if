<?php

class Grupo_model extends AbstractModel {

		#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
		public $table = "grupos";
        public $fields = ["nome"];
		
		#um grupo pode ter vários usuários
		#um usuário pode ter vários grupos
		#essa associação requer o assocTable com o nome da tabela associativa		
		public $manyToMany = [["table"=>"usuarios","key"=>"pessoa_id", "assocTable"=>"gruposusuarios"]];



		
		public function remove_usuario($id_assoc){
			$obj = R::load("gruposusuarios", $id_assoc);
			R::Trash($obj);
		}



}