<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mat extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function trendingMat(){
        return $this->hasMany(TrendingMats::class,'mat_id','id');
    }
}
