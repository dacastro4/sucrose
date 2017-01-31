<?php

namespace AgileElement;

use Illuminate\Database\Eloquent\Model;

class Sucrose extends Model {

    const CREATED_AT = 'date_entered';

    const UPDATED_AT = 'date_modified';
    
    public $incrementing = false;

}
