<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OpenStockEntryLog extends Model
{
    use SoftDeletes;

    protected $table = 'open_stock_entry_logs';

    protected $fillable = [
        'entry_no',
        'entry_date',
        'warehouse_id',
        'store_type_id',
        'brand_id',
        'product_name',
        'art_no',
        'style_id',
        'size',
        'color_id',
        'sleeve_type',
        'qty',
        'price',
        'store_location_id',
        'sku',
        'uom_id',
        'remarks',
        'header_remarks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'qty' => 'float',
        'price' => 'float',
    ];

    /**
     * Auto-ensure the table exists so it works safely on Live environments
     * even before running php artisan migrate.
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('open_stock_entry_logs')) {
            Schema::create('open_stock_entry_logs', function (Blueprint $table) {
                $table->id();
                $table->string('entry_no', 50)->index();
                $table->date('entry_date')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->unsignedBigInteger('store_type_id')->nullable()->index();
                $table->unsignedBigInteger('brand_id')->nullable()->index();
                $table->string('product_name', 255);
                $table->string('art_no', 100)->index();
                $table->unsignedBigInteger('style_id')->nullable()->index();
                $table->string('size', 50);
                $table->unsignedBigInteger('color_id')->nullable();
                $table->string('sleeve_type', 20)->nullable();
                $table->decimal('qty', 10, 2)->default(0);
                $table->decimal('price', 12, 2)->nullable();
                $table->unsignedBigInteger('store_location_id')->nullable()->index();
                $table->string('sku', 100)->index();
                $table->unsignedBigInteger('uom_id')->nullable();
                $table->string('remarks', 255)->nullable();
                $table->string('header_remarks', 500)->nullable();
                $table->string('status', 50)->default('Logged')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'store_location_id');
    }

    public function style()
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

