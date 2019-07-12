<?php

namespace App\Controllers\User;
use App\Views\Status as Status;
use Illuminate\Database\Capsule\Manager as DB;
use App\Classes\Security\Communication as Communication;

class Activation{
    private $req;
    
    public function init(){
        if(!isset(app('request')->body['data'])){
			Status::render_error(400, "client", "invalid_request");
		} else{
			$this->req = app('request')->body['data'];
		}
        
        $validate = new \Particle\Validator\Validator;
        $validate->required('code')->string();
        $validate->required('user_password')->string();
        
        if(!$result->isValid()){
			Status::render_error(400, "client", json_encode($result->getMessages()));
		} else if($this->encryption_is_not_valid($this->req)){
			Status::render_error(400, "client", "invalid_encryption_method");
		}
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
    * Checa se o código de ativação é valido.
    */
    
    private function check_code(){
        if(!DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->exists()){
            return array("message" => "code_does_not_exist", "status" => false);
		} else if(DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->value('duration') > time()){
            return array("message" => "time_has_expired", "status" => false);
            // Criar um novo??
        } else{
            $uid = DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->value('uid');
            
            if(!DB::table("eyo_accounts")->where('id', $uid)->exists()){
                return array("message" => "user_does_not_exist", "status" => false);
            } else if(DB::table("eyo_accounts")->where('id', $uid)->value('account_status') !== 1){
                return array("message" => "account_already_activated", "status" => false);
            }
        }
    }

	/*
	* Apenas para encurtar.
	*/
	private function dec($p){
		return Communication::decode($p);
	}
}