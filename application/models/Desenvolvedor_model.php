<?php


class Desenvolvedor_model extends AbstractModel {

		
	public $table = "desenvolvedores";
	public $fields = ["nome"];
	
	public $manyToMany = [["table"=>"projetos",
							"key"=>"projetos_id", 
							"assocTable"=>"desenvolvedoresprojetos"]];

	public function remove_projeto($id_assoc){
		$obj = R::load("desenvolvedoresprojetos", $id_assoc);
		R::Trash($obj);
	}


}