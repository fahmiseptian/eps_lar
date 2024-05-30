<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapRequestLog extends Model
{
    use HasFactory;
    protected $table = 'sap_request_log';
    public $timestamps = false;
    protected $fillable = [
        'payload',
        'response',
    ];
}
