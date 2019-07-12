<?php

namespace App\Controllers\User;
use App\Views\Status as Status;
use Illuminate\Database\Capsule\Manager as DB;
use App\Classes\Security\Communication as Communication;

class Activation{
    private $req;
    private $uid;
    
    public function init(){
        if(!isset(app('request')->body['data'])){
			Status::render_error(400, "client", "invalid_request");
		} else{
			$this->req = app('request')->body['data'];
		}
        
        $validate = new \Particle\Validator\Validator;
        $validate->required('code')->string();
        $validate->required('user_password')->string();
        $result = $validate->validate($this->req);
        
        if(!$result->isValid()){
			Status::render_error(400, "client", json_encode($result->getMessages()));
		} else if($this->encryption_is_not_valid($this->req)){
			Status::render_error(400, "client", "invalid_encryption_method");
		} else if(!$this->check_code()["status"]){
            Status::render_error(400, $this->check_code()["class"], $this->check_code()["message"]);
        } else{
            echo "okay";
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
        
        // Verifica se o código existe
        if(!DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->exists()){
            return array("message" => "code_does_not_exist", "class" => "user", "status" => false);
            
        // Verifica se o código já expirou.
		} else if(time() > DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->value('duration')){
            return array("message" => "time_has_expired", "class" => "user", "status" => false);
            // Criar um novo?? (Futuro)
        } else{
            $this->uid = DB::table("eyo_activations_code")->where('code', $this->dec($this->req['code']))->value('uid');
            
            // Verifica se a conta pertecente ao código existe.
            if(!DB::table("eyo_accounts")->where('id', $this->uid)->exists()){
                return array("message" => "user_does_not_exist", "class" => "client", "status" => false);
                
            // Verifica se a conta já foi ativada
            } else if(DB::table("eyo_accounts")->where('id', $this->uid)->value('account_status') !== "x1"){
                return array("message" => "account_already_activated", "class" => "user", "status" => false);
                
            // Verifica a senha
            } else if(!password_verify($this->dec($this->req['user_password']), DB::table("eyo_accounts")->where('id', $this->uid)->value('user_password'))){
                return array("message" => "invalid_password", "class" => "user", "status" => false);
                
            // Tudo Ok
            } else{
                return array("status" => true);
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