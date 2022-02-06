<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	#nome do campo de upload no formulario
	private $upload_name = "foto";
	#a variavel que vai receber o nome do arquivo que foi feito o upload
	private $upload_result = null;
	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model de usuario
		$this->load->model("Usuario_model");

		#verifica se o usuário fez o login corretamente
		if (!isset($_SESSION["email"])){
			redirect("login/index/");
		}

		$this->seguranca->permitir("Convidado");
	}
	
	
	//página principal
	public function index($id=null) {

		if (isset($_GET['page'])){
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		//busca todos os registros para a listagem
		$listaPaginada = $this->Usuario_model->pagination($this->config->item("per_page"), $page, val($_GET,"busca"));
		
		//se for para abrir algum registro
		//se tiver dado erro de validacao, o Model automaticamente pega os
		//dados que foram enviados via POST
		$dados = $this->Usuario_model->get($id);


		//recupera todos os setores para o select de setor
		$this->load->model("Setor_model");
		$setores = $this->Setor_model->options("nome");
		$this->load->model("Grupo_model");
		$grupos = $this->Grupo_model->options("nome");

		//recupera os tipos possiveis de usuarios
		$tiposUsuarios = $this->Usuario_model->tiposUsuarios;
		$areasUsuarios = $this->Usuario_model->areasUsuarios;
		$niveis = [""] + $this->Usuario_model->secureLevels;

		
		$this->load->view('usuarios', ["listaPaginada"=>$listaPaginada,
										"dados"=>$dados,
										"setores"=>$setores,
										"grupos"=>$grupos,
										"niveis"=>$niveis,
										"tiposUsuarios"=>$tiposUsuarios,
										"areasUsuarios"=>$areasUsuarios]);
		
	}


	private function cleanString($text) {
		$utf8 = array(
			'/[áàâãªä]/u'   =>   'a',
			'/[ÁÀÂÃÄ]/u'    =>   'A',
			'/[ÍÌÎÏ]/u'     =>   'I',
			'/[íìîï]/u'     =>   'i',
			'/[éèêë]/u'     =>   'e',
			'/[ÉÈÊË]/u'     =>   'E',
			'/[óòôõºö]/u'   =>   'o',
			'/[ÓÒÔÕÖ]/u'    =>   'O',
			'/[úùûü]/u'     =>   'u',
			'/[ÚÙÛÜ]/u'     =>   'U',
			'/ç/'           =>   'c',
			'/Ç/'           =>   'C',
			'/ñ/'           =>   'n',
			'/Ñ/'           =>   'N',
			'/\./'           =>   '_',
			'/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
			'/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
			'/[“”«»„]/u'    =>   ' ', // Double quote
			'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
		);
		return preg_replace(array_keys($utf8), array_values($utf8), $text);
	}
	

	

	public function upload_check($v){

		#local onde salvará o arquivo sqlite/uploads/
		$config['upload_path']          = "./uploads/";
		$config['allowed_types']        = 'jpg|jpeg|png';
		$config['max_size']             = 1000;
		#limpa os caracteres especiais do nome do arquivo
		$config['file_name']            = $this->cleanString($_FILES[$this->upload_name]["name"]);

		$this->load->library('upload', $config);
		#faz o upload
		if ( ! $this->upload->do_upload($this->upload_name)){
			$this->form_validation->set_message('upload_check', $this->upload->display_errors());
			return false;
		} else {
			#salva o novo nome do arquivo
			$this->upload_result = $this->upload->data();
			return true;
		}
	}
	
	
	public function salvar(){

		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");

		#upload de foto com validacao
		if (isset($_FILES[$this->upload_name]) && $_FILES[$this->upload_name]["name"] != ""){
			#o arquivo será salvo e feita a validacao
			$this->form_validation->set_rules($this->upload_name, 'Foto', 'callback_upload_check');
		}


		#a senha so sera validada se for num novo registro
		#ou se enviado algo diferente de branco
		#pq quando ela nao é enviada, significa que nao é pra alterar
		if (val($_POST,"senha") != "" || val($_POST,"id") == ""){
			$this->form_validation->set_rules('senha', 'Senha', 'min_length[6]',
					array('min_length' => 'Defina uma senha com no mínimo 6 dígitos.')
			);
			$this->form_validation->set_rules('senhaConfirm', 'Confirmação da senha', 'required|matches[senha]');
		}
		$this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
		$this->form_validation->set_rules('setores_id', 'Setor', 'required');

		#A permissao só será obrigatória quando a pessoa que estiver cadastrando
		#tiver o nivel de acesso necessário
		if (Seguranca::temPermissao("Admin"))
			$this->form_validation->set_rules('nivel', 'Nível de acesso', 'required');

		
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata("error","<div class='ui red message'>Corrija os erros no formulário.</div>");
			$this->index();
		} else {

			#insere o nome do arquivo que foi feito o upload
			#para ser salvo no banco de dados
			#nesse momento o arquivo ja se encontra em sqlite/uploads/
			$_POST["foto"] = $this->upload_result["file_name"];


			$obj = $this->Usuario_model->save();

			#mensagem de confirmação
			if ($obj == ""){
				$this->session->set_flashdata("error","<div class='ui red message'>Falha ao salvar.</div>");
			} else {
				$this->session->set_flashdata("success","<div class='ui green message'>Salvo com sucesso.</div>");
			}

			redirect("usuarios/index/" . $obj );
		}
	}
	
	
	
	
	public function deletar($id){
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");

		$this->Usuario_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");
		redirect("usuarios/index");
	}


	public function remover_grupo($this_id, $assoc_id){
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");

		$this->load->model("Grupo_model");
		$this->Grupo_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}

	public function remover_permissao($this_id, $assoc_id){
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");
		
		$this->load->model("Permissao_model");
		$this->Permissao_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}
}
