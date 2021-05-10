<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Animal extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable= [
        'type_id',
        'name',
        'birthday',
        'area',
        'fix',
        'description',
        'personality'
    ];

    public function type()
    {
        //belongTo(class_name,table_name,key)
        return $this->belongsTo('App\Models\Type');
    }

    public function animalprivate()
    {
        $this->belongsTo('App\Models\AnimalPrivate');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }  

    /**
     *  many to many relation animal and user likes
     */
    public function likes()
    {
        return $this->belongsToMany('App\Models\User','animal_user_likes')->withTimestamps();
    }
    
    public function getAgeAttributes()
    {
        $diff = Carbon::now()->diff($this->birthday);
        return "{$diff->y}岁{$diff->m}月";
    }
}
