<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $guarded = ['id']; 

    public function kategori() {
        return $this->belongsTo('App\Models\Kategori', 'kategori_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function images() {
        return $this->hasMany('App\Models\ProdukImage', 'produk_id');
    }

    public function promo() {
        return $this->hasOne('App\Models\ProdukPromo', 'produk_id');
    }
}
