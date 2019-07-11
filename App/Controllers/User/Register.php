<?php

namespace App\Controllers\User;

use App\Views\Status as Status;
use Illuminate\Database\Capsule\Manager as DB;
use App\Classes\Security\Communication as Communication;

class Register{
	private $user_id;
	private $req;

	public function init(){

		if(!isset(app('request')->body['data'])){
			Status::render_error(400, "client", "invalid_request");
		} else{
			$this->req = app('request')->body['data'];
		}

		$validate = new \Particle\Validator\Validator;
		$validate->required('user_email')->string();
		$validate->required('user_password')->string();
		$validate->required('user_firstname')->string();
		$validate->required('user_lastname')->string();
		$result = $validate->validate($this->req);

		if(!$result->isValid()){
			Status::render_error(400, "client", json_encode($result->getMessages()));
		} else if($this->encryption_is_not_valid($this->req)){
			Status::render_error(400, "client", "invalid_encryption_method");
		} else if(!$this->email_is_valid()){
			Status::render_error(422, "user", "email_already_registered");
		} else{
			$this->register();
		}

	}

	/*
	* Registra o usuário.
	*/
	private function register(){
		$this->user_id = DB::table("eyo_accounts")->insertGetId([
			'user_email' => $this->dec($this->req['user_email']),
			'user_password' => password_hash($this->dec($this->req['user_password']), PASSWORD_DEFAULT),
			'user_firstname' => $this->dec($this->req['user_firstname']),
      	'user_lastname' => $this->dec($this->req['user_lastname']),
      	'account_status' => 1
		]);

		if(!$this->user_id){
			Status::render_error(503, "fatal", "service_register_unavailable");
		} else{
			$this->create_confirmation_code();
		}
	}

	/*
	* Cria o código de ativação.
	*/
	private function create_confirmation_code(){
		$this->activation_code_id = DB::table("eyo_activations_code")->insertGetId([
			'uid' => $this->user_id,
			'code' => bin2hex(random_bytes(3)),
			'duration' => time()+900]
    	);

    	http_response_code(201);
      exit;
	}


	/*
	*	Retorna "true" se o método de encriptação for inválido.
	*/ 
	private function encryption_is_not_valid($params){
		foreach($params as $param){
			if(!Communication::decode($param)){
				return true;
			}
		}
	}

	/*
	* Retorna "true" se o e-mail for válida e se ainda não estiver registrado.
	*/
	private function email_is_valid(){
		if(!filter_var($this->dec($this->req['user_email']), FILTER_VALIDATE_EMAIL)){
			return false;
		} else if(DB::table("eyo_accounts")->where('user_email', $this->dec($this->req['user_email']))->exists()){
			return false;
		} else{
			return true;
		}
	}

	/*
	* Apenas para encurtar.
	*/
	private function dec($p){
		return Communication::decode($p);
	}
}