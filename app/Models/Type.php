<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort'
    ];

    public function animals()
    {
        //hasMany(class_name,table_name,key)
        return $this->hasMany('App\Models\Animal','type_id','id');
    }
}
