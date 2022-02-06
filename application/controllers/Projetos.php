<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projetos  extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		
		
		$this->load->model("Projeto_model");

		
		if (!isset($_SESSION["email"])){
			redirect("login/index/");
		}

		$this->seguranca->permitir("Comum");

	}
	
	
	//página principal
	public function index($id=null) {

		if (isset($_GET['page'])){
			$page = $_GET['page'];
		} else {
			$page = 1;
		}


		
		//busca todos os registros para a listagem
		$listaPaginada = $this->Projeto_model->pagination($this->config->item("per_page"), $page, val($_GET,"busca"));

		//se for para abrir algum registro
		$dados = $this->Projeto_model->get($id);

		//Seleciona todas as pessoas que ainda nao estao naquele setor
		$this->load->model("Desenvolvedor_model");
		$desenvolvedores = $this->Desenvolvedor_model->options("nome");
		
		
		$this->load->view('Projetos', ["listaPaginada"=>$listaPaginada,
										"dados"=>$dados,
										"desenvolvedores"=>$desenvolvedores]);
		
	}
	
	
	
	public function salvar(){
		$this->form_validation->set_rules('nome', 'Nome', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata("error","<div class='ui red message'>Corrija os erros no formulário.</div>");
			$this->index();
		} else {
			$obj = $this->Projeto_model->save();

			#mensagem de confirmação
			if ($obj == ""){
				$this->session->set_flashdata("error","<div class='ui red message'>Falha ao salvar.</div>");
			} else {
				$this->session->set_flashdata("success","<div class='ui green message'>Salvo com sucesso.</div>");
			}

			
			redirect("projetos/index/" . $obj );
		}
	}
	
	
	
	
	public function deletar($id){
		$this->Projeto_model->delete($id);

		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");
		
		redirect("projetos/index");
		
	}

	
}
