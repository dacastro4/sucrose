<?php

namespace AgileElement;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Sucrose extends Authenticatable {

    const CREATED_AT = 'date_entered';
    const UPDATED_AT = 'date_modified';
    public $incrementing = false;

}
