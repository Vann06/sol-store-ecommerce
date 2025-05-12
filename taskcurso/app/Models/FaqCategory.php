<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }
}
