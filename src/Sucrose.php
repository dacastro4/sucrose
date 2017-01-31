<?php

namespace AgileElement;

use Illuminate\Database\Eloquent\Model;

class Sucrose extends Model {

    const CREATED_AT = 'date_entered';

    const UPDATED_AT = 'date_modified';

    const DELETED_AT = 'deleted';

    public $incrementing = false;

    public function setDeletedAttribute()
    {
        if(!is_null($this->attribute['deleted'])) {
          $this->attribute['deleted'] = 1;
        }
    }

}
