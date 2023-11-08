<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table="password_reset";
    protected
 
    $fillable = [
            'otp',
            'expires_at',
        ];
    
        protected $dates = [
            'expires_at',
        ];
    
        public function user()
        {
            return $this->belongsTo(User::class);
        }
}
