<?php


class Disciplina_model extends AbstractModel {

		
	public $table = "disciplinas";
	public $fields = ["nome"];
	
	public $manyToMany = [["table"=>"alunos",
							"key"=>"alunos_id", 
							"assocTable"=>"disciplinasalunos"]];

	public function remove_aluno($id_assoc){
		$obj = R::load("disciplinasalunos", $id_assoc);
		R::Trash($obj);
	}


}