<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalPrivate extends Model
{
    use HasFactory;

    public function animal()
    {
        $this->hasOne('App\Models\Animal','id','id');
    }
}
