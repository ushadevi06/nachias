<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Charge;
use App\Models\RawMaterial;
use App\Models\Uom;
use App\Models\StoreCategory;
use App\Models\StoreType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseInvoiceNegativeValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_purchase_invoice_blocks_negative_taxable_total()
    {
        // 1. Authenticate as admin (user ID 1)
        $user = User::firstOrCreate(['id' => 1], [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        // 2. Prepare dependencies
        $setting = Setting::firstOrCreate([], [
            'state_id' => 1,
            'cgst' => 9,
            'sgst' => 9,
            'igst' => 18,
        ]);
        $companyStateId = $setting->state_id;

        $supplier = Supplier::firstOrCreate(['name' => 'Test Supplier'], [
            'code' => 'SUPP-NEG',
            'status' => 'Active',
            'state_id' => $companyStateId,
            'created_by' => 1,
        ]);

        $uom = Uom::firstOrCreate(['uom_code' => 'MTR'], ['uom_name' => 'Meter', 'status' => 'Active', 'created_by' => 1]);
        $storeCategory = StoreCategory::firstOrCreate(['category_name' => 'Fabric'], ['status' => 'Active', 'created_by' => 1]);
        $storeType = StoreType::firstOrCreate(['store_type_name' => 'Main Store'], ['status' => 'Active', 'created_by' => 1]);
        $rawMaterial = RawMaterial::firstOrCreate(['name' => 'Cotton Fabric'], [
            'code' => 'RM-NEG',
            'store_category_id' => $storeCategory->id,
            'uom_id' => $uom->id,
            'status' => 'Active',
            'hsn_code' => '5208',
            'created_by' => 1,
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-NEG-' . time(),
            'po_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'supplier_id' => $supplier->id,
            'store_type_id' => $storeType->id,
            'status' => 'Approved',
            'total_qty' => 100,
            'sub_total' => 1000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'taxable_amount' => 1000,
            'other_state' => false,
            'cgst_percent' => 9,
            'sgst_percent' => 9,
            'tax_amount' => 180,
            'total_amount' => 1180,
            'created_by' => 1,
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'store_category_id' => $storeCategory->id,
            'raw_material_id' => $rawMaterial->id,
            'uom_id' => $uom->id,
            'quantity' => 100,
            'rate' => 10,
            'amount' => 1000,
        ]);

        // Attempt to create a purchase invoice with 150% discount (resulting in negative taxable total)
        $invoiceNo = 'SINV-NEG-' . time();
        $payload = [
            'invoice_no' => $invoiceNo,
            'invoice_date' => now()->format('d-m-Y'),
            'purchase_order_id' => $po->id,
            'purchase_order_no' => $po->po_number,
            'supplier_id' => $supplier->id,
            'items' => [
                $poItem->id => [
                    'selected' => '1',
                    'purchase_order_item_id' => $poItem->id,
                    'raw_material_id' => $rawMaterial->id,
                    'uom_id' => $uom->id,
                    'quantity' => 100,
                    'rate' => 10,
                    'hsn_code' => '5208',
                ]
            ],
            'other_state' => 'N',
            'discount_percent' => 60, // 60% discount
            'discount_amount' => 600,
            'commission' => 50, // 50% commission (combined 110% causes negative taxable total)
            'commission_amount' => 500,
            'cgst_percent' => 9,
            'sgst_percent' => 9,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'taxable_amount' => -100, // 1000 subtotal - 600 discount - 500 commission
            'tax_amount' => 0,
            'round_off_type' => 'Add',
            'round_off' => 0,
            'grand_total' => -100,
            'invoice_status' => 'Unpaid/Credit',
        ];

        $response = $this->post('/purchase_invoices/add', $payload);
        
        // Assert that we are redirected back with errors
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['taxable_amount', 'grand_total']);
        
        // Assert the exact validation error message is set
        $errors = session('errors')->getBag('default');
        $this->assertEquals(
            'Taxable Total cannot be negative. Please check the discount, commission, and Pre-GST charges.',
            $errors->first('taxable_amount')
        );
        $this->assertEquals(
            'Grand Total cannot be negative. Please check the round off and other charges.',
            $errors->first('grand_total')
        );
    }
}
