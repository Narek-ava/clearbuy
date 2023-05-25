<?php namespace App\Traits;

trait NullableFields {

    protected function nullIfEmpty($input)
    {
        if(!is_array($input)) {
            return trim($input) == '' ? null : trim($input);
        }else return $input;
    }

}
