<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gmail',
        'app_password',
        'mobile',
        'broker_code',
        'status',
    ];

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
