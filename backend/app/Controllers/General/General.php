<?php 

namespace App\Controllers\General;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class General extends LoadController {

    public function getLevels() {
        return Routing::success(getLevels());
    }
}