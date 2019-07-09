<?php

namespace App\Middlewares;
use App\Classes\Security\Communication as Communication;

class ClientAuth{
	public function init(){
		var_dump(app('request')->headers);
	}
}