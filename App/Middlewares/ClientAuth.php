<?php

namespace App\Middlewares;
use App\Views\Status as Status;
use App\Classes\Security\Communication as Communication;
use Illuminate\Database\Capsule\Manager as DB;

class ClientAuth{
    private $signature;
    
	public function init(){
        $this->signature = Communication::decode(app('request')->headers['eyosignature']);
        
        if(!$this->signature){
            Status::render_error(401, "client", "signature_unreadable");
        } else if(!DB::table("eyo_clients")->where('client_key', json_decode($this->signature, true)['key'])->exists()){
            status:render_error(401, "client", "invalid_signature");
        }
	}
    

}