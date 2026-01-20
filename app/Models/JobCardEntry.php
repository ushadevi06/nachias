<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\JobCardCuttingSizeRatio;
use App\Models\JobCardImage;
use App\Models\JobCardOperation;
use App\Models\PurchaseOrder;
use App\Models\JobCardFabricDetail;
use App\Models\Brand;
use App\Models\Season;
use App\Models\ProcessGroup;
use App\Models\SizeRatio;
use App\Models\User;
use App\Models\JobCardIssueItem;

class JobCardEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_no', 'reference_no', 'purchase_order_id', 'service_provider_id', 'issue_store_id', 'receipt_store_id',
        'brand_id', 'season_id', 'process_group_id', 'job_card_date', 'delivery_date', 'washing', 'width', 'mrp',
        'fs_qty', 'hs_qty', 'receipt_store', 'fit', 'patti_type', 'collar_type',
        'cuff_type', 'pocket_type', 'bottom_cut', 'cutting_master_id',
        'cutting_date', 'cutting_issue_unit', 'price_fs', 'price_hs',
        'total_qty_fs', 'total_qty_hs', 'grand_total_qty', 'average', 'remarks', 'status', 
        'created_by', 'updated_by', 'size_ratio_id', 'ex_1_label', 'ex_2_label'
    ];

    public function cuttingMaster()
    {
        return $this->belongsTo(User::class, 'cutting_master_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function issueStore()
    {
        return $this->belongsTo(StoreType::class, 'issue_store_id');
    }

    public function receiptStoreMapping()
    {
        return $this->belongsTo(StoreType::class, 'receipt_store_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function processGroup()
    {
        return $this->belongsTo(ProcessGroup::class);
    }

    public function sizeRatio()
    {
        return $this->belongsTo(SizeRatio::class);
    }

    public function cuttingSizeRatios()
    {
        return $this->hasMany(JobCardCuttingSizeRatio::class);
    }

    public function images()
    {
        return $this->hasMany(JobCardImage::class);
    }

    public function operations()
    {
        return $this->hasMany(JobCardOperation::class);
    }

    public function fabricDetails()
    {
        return $this->hasMany(JobCardFabricDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issueItems()
    {
        return $this->hasMany(JobCardIssueItem::class);
    }
}
