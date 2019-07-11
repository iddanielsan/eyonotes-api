<?php

namespace App\Middlewares;

use App\Views\Status as Status;
use Illuminate\Database\Capsule\Manager as DB;

class ClientAuth{
	public function init(){
		if(!isset(app('request')->headers["eyoclientkey"])){
			status::render_error(401, "client", "client_key_not_received");
		} else if(!DB::table("eyo_clients")->where('client_key', app('request')->headers["eyoclientkey"])->exists()){
			status::render_error(401, "client", "client_key_does_not_exist");
		}
	}
}