<?php

namespace App\Controllers\User;

use App\Views\Status as Status;
use Illuminate\Database\Capsule\Manager as DB;
use App\Classes\Security\Communication as Communication;

class Register{
	private $req;

	public function init(){

		// Verifica se os dados brutos foram recebidos
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

		/*
		* Checa se os dados acima foram recebidos no formato string.
		*/
		if(!$result->isValid()){
			Status::render_error(400, "client", json_encode($result->getMessages()));
		}

		/*
		* Checa se o método de criptografia é válido.
		*/
		else if($this->encryption_is_not_valid($this->req)){
			Status::render_error(400, "client", "invalid_encryption_method");
		}

		/*
		* Checa se o e-mail é válida e se já está registrado.
		*/
		else if(!$this->email_is_valid()){
			Status::render_error(422, "user", "email_already_registered");
		}

	}

	private function encryption_is_not_valid($params){
		foreach($params as $param){
			if(!Communication::decode($param)){
				return true;
			}
		}
	}

	private function email_is_valid(){
		if(!filter_var($this->dec($this->req['user_email']), FILTER_VALIDATE_EMAIL)){
			return false;
		} else if(DB::table("eyo_accounts")->where('user_email', $this->dec($this->req['user_email']))->exists()){
			return false;
		} else{
			return true;
		}
	}

	private function dec($q){
		return Communication::decode($p);
	}
}