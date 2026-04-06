<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'supplier_code',
        'gst_in',
        'drug_license',
        'email',
        'credit_period',
        'account_no',
        'ifsc_code',
        'address',
        'template_id'
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }
}