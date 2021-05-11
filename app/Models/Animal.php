<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
/**
 * @OA\Schema(
 *     schema="Animal",
 *     required={"id", "name", "fix", "user_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="type_id",
 *         type="integer",
 *         description="動物的分類ID(需參照types資料表)",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="type_name",
 *         type="string",
 *         description="關聯type名稱",
 *         example="狗"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="動物名稱",
 *         example="黑藤"
 *     ),
 *     @OA\Property(
 *         property="birthday",
 *         type="date",
 *         description="生日",
 *         example="2019-01-01"
 *     ),
 *     @OA\Property(
 *         property="age",
 *         type="string",
 *         description="年齡(系統自動計算)",
 *         example="1歲1月"
 *     ),
 *     @OA\Property(
 *         property="area",
 *         type="string",
 *         description="所在區域",
 *         example="台北"
 *     ),
 *     @OA\Property(
 *         property="fix",
 *         type="integer",
 *         description="是否結紮(輸入1或0)",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="text",
 *         description="簡易描述",
 *         example="黑狗，胸前有白毛！宛如台灣黑熊"
 *     ),
 *     @OA\Property(
 *         property="personality",
 *         type="text",
 *         description="動物個性",
 *         example="非常親人！很可愛～"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="date",
 *         description="建立時間",
 *         example="2020-10-01 00:00:00"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="date",
 *         description="更新時間",
 *         example="2020-10-01 00:00:00"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="所屬會員ID",
 *         example=1
 *     )
 * )
 */
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
