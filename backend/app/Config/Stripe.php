<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Stripe extends BaseConfig
{
    /**
     * The Stripe API key.
     */
    public string $testKey = '';
    
}
