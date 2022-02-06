<?php


class Projeto_model extends AbstractModel {

		
	public $table = "projetos";
	public $fields = ["nome"];
	
	public $manyToMany = [["table"=>"desenvolvedores",
							"key"=>"desenvolvedores_id", 
							"assocTable"=>"desenvolvedoresprojetos"]];

	public function remove_desenvolvedor($id_assoc){
		$obj = R::load("desenvolvedoresprojetos", $id_assoc);
		R::Trash($obj);
	}


}