<?php

namespace App\Traits;

trait CodeIndexedTrait
{
    public function generateCode()
    {
        return rand(pow(10, 8-1), pow(10, 8)-1);
    }
}