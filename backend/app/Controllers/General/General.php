<?php 

namespace App\Controllers\General;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class General extends LoadController {

    public function utilities() {
        $result = [
            'levels' => getLevels(),
            'eventTypes' => getEventTypes(),
            'industries' => getIndustries()
        ];
        return Routing::success($result);
    }

}