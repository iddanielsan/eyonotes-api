<?php

namespace App\Middlewares;

class ClientAuth{
	public function init(){
		var_dump(app('request')->headers);
	}
}