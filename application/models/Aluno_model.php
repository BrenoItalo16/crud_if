<?php


class Aluno_model extends AbstractModel {

		
	public $table = "alunos";
	public $fields = ["nome","matricula","periodo"];
	
	public $manyToMany = [["table"=>"disciplinas",
							"key"=>"disciplinas_id", 
							"assocTable"=>"disciplinasalunos"]];
	


}