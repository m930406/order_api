<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderByCurrency extends Model
{
    use HasFactory;

    //default table name, use twd table
    protected $table = 'orders_twd';
    protected $tablePrefix = 'orders_';

    protected $primaryKey = 'order_id';
    protected $keyType = 'string';
    protected $fillable = [
        'order_id',
        'name',
        'address',
        'price'
    ];

    public $timestamps = true;

    // Set the table name based on the currency
    public function setTableCurrency($currency)
    {
        $this->table = $this->tablePrefix . strtolower($currency);
    }
}