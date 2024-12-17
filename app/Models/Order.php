<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'order_id';
    protected $keyType = 'string';
    protected $fillable = [
        'order_id',
        'currency',
    ];

    public $timestamps = true;
}