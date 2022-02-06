<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function __construct(){
			parent::__construct();
			
			//Carrega o Model de usuario
			$this->load->model("Usuario_model");			
	}
	
	//página de login
	public function index() {

		#verifica se o usuário já fez o login
		if (isset($_SESSION["email"])){
			redirect("usuarios/index/");
		}
		
		$this->load->view('login');
	}
	
	
	
	public function login(){
			
		$this->form_validation->set_rules('email', 'E-mail', 'required');
		$this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]',
				array('min_length' => 'A senha contém no mínimo 6 caracteres.')
		);

		if ($this->form_validation->run() == FALSE) {
			$this->index();
		} else {
			$user = $this->Usuario_model->login(["email"=>$_POST["email"],
												"senha"=>$_POST["senha"]]);

			if ($user != null){
				$_SESSION["nome"] = $user->nome;
				$_SESSION["email"] = $user->email;
				$_SESSION["user_id"] = $user->id;

				redirect("usuarios/index/");
			} else {
				$this->session->set_flashdata("message","E-mail ou senha incorretos.");
				
				redirect("login/index/");
			}
		}
		
	}
	
	
	
	public function logout(){
		session_destroy();
		redirect("login/index/");
	}


	public function cria_usuario(){
		$admin = $this->Usuario_model->resetAdmin();
		if ($admin['id'] != null) {
			$this->session->set_flashdata("message","O usuário {$admin['email']}, senha {$admin['senha']} foi criado. Tente fazer o login.");
		} else {
			$this->session->set_flashdata("message","Houve uma falha ao criar o usuário padrão.");
		}

		$this->load->view('login');
	}
}
