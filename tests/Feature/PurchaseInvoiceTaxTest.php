<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceCharge;
use App\Models\Charge;
use App\Models\RawMaterial;
use App\Models\Uom;
use App\Models\StoreCategory;
use App\Models\StoreType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseInvoiceTaxTest extends TestCase
{
    use DatabaseTransactions;

    public function test_purchase_invoice_tax_and_charges_persistence()
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

        // Create inter-state supplier (other_state = Y)
        $supplier = Supplier::firstOrCreate(['name' => 'Test Inter-State Supplier'], [
            'code' => 'SUPP-TEST',
            'status' => 'Active',
            'state_id' => $companyStateId + 1, // distinct state
            'created_by' => 1,
        ]);

        $uom = Uom::firstOrCreate(['uom_code' => 'MTR'], ['uom_name' => 'Meter', 'status' => 'Active', 'created_by' => 1]);
        $storeCategory = StoreCategory::firstOrCreate(['category_name' => 'Fabric'], ['status' => 'Active', 'created_by' => 1]);
        $storeType = StoreType::firstOrCreate(['store_type_name' => 'Main Store'], ['status' => 'Active', 'created_by' => 1]);
        $rawMaterial = RawMaterial::firstOrCreate(['name' => 'Cotton Fabric'], [
            'code' => 'RM-TEST',
            'store_category_id' => $storeCategory->id,
            'uom_id' => $uom->id,
            'status' => 'Active',
            'hsn_code' => '5208',
            'created_by' => 1,
        ]);

        // Create a Purchase Order
        $po = PurchaseOrder::create([
            'po_number' => 'PO-TEST-' . time(),
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
            'other_state' => true,
            'igst_percent' => 18,
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

        // Find or create the charges
        $logoCharge = Charge::firstOrCreate(['charge_name' => 'LOGO EMBROIDERY CHARGE'], ['status' => 'Active', 'created_by' => 1]);
        $tcsCharge = Charge::firstOrCreate(['charge_name' => 'TCS'], ['status' => 'Active', 'created_by' => 1]);

        // 3. Step 1: Add Purchase Invoice (Logo = Pre-GST, TCS = Post-GST)
        $invoiceNo = 'SINV-TEST-' . time();
        $payload1 = [
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
            'other_state' => 'Y',
            'igst_percent' => 18,
            'igst_amount' => 198, // (1000 + 100 Logo) * 18% = 198
            'taxable_amount' => 1100, // 1000 subtotal + 100 logo
            'tax_amount' => 198,
            'round_off_type' => 'Add',
            'round_off' => 0,
            'grand_total' => 1398, // 1000 subtotal + 100 Logo + 198 IGST + 100 TCS = 1398
            'invoice_status' => 'Unpaid/Credit',
            'charges' => [
                'charge_id' => [$logoCharge->id, $tcsCharge->id],
                'name' => ['LOGO EMBROIDERY CHARGE', 'TCS'],
                'amount' => [100.00, 100.00],
                'tax_type' => ['Pre-GST', 'Post-GST'],
            ]
        ];

        $response = $this->post('/purchase_invoices/add', $payload1);
        $response->assertRedirect('/purchase_invoices');

        // Verify it was saved correctly in database
        $invoice = PurchaseInvoice::where('invoice_no', $invoiceNo)->firstOrFail();
        $this->assertEquals(true, $invoice->other_state);

        $savedCharges = $invoice->charges()->orderBy('charge_id')->get();
        $this->assertCount(2, $savedCharges);

        // Map by charge name for safety
        $logoInvoiceCharge = $savedCharges->where('charge_name', 'LOGO EMBROIDERY CHARGE')->first();
        $tcsInvoiceCharge = $savedCharges->where('charge_name', 'TCS')->first();

        $this->assertEquals('Pre-GST', $logoInvoiceCharge->tax_type);
        $this->assertEquals('Post-GST', $tcsInvoiceCharge->tax_type);

        // 4. Step 2: Edit Purchase Invoice (swap Pre-GST / Post-GST)
        // TCS -> Pre-GST, Logo -> Post-GST
        $payload2 = $payload1;
        $payload2['charges']['tax_type'] = ['Post-GST', 'Pre-GST']; // corresponding to Logo and TCS
        $payload2['taxable_amount'] = 1100; // 1000 subtotal + 100 TCS
        $payload2['igst_amount'] = 198; // (1000 + 100 TCS) * 18%
        $payload2['grand_total'] = 1398; // 1000 + 100 Logo + 198 IGST + 100 TCS

        $response2 = $this->post("/purchase_invoices/add/{$invoice->id}", $payload2);
        $response2->assertRedirect('/purchase_invoices');

        // 5. Step 3: Reopen Edit Page and Verify Selection & Calculations
        $invoice = $invoice->fresh();
        
        // Ensure other_state was NOT changed to false/0
        $this->assertEquals(true, $invoice->other_state, 'other_state should remain true (IGST)');

        $savedCharges2 = $invoice->charges()->orderBy('charge_id')->get();
        $logoInvoiceCharge2 = $savedCharges2->where('charge_name', 'LOGO EMBROIDERY CHARGE')->first();
        $tcsInvoiceCharge2 = $savedCharges2->where('charge_name', 'TCS')->first();

        $this->assertEquals('Post-GST', $logoInvoiceCharge2->tax_type, 'Logo Embroidery Charge should now be Post-GST');
        $this->assertEquals('Pre-GST', $tcsInvoiceCharge2->tax_type, 'TCS should now be Pre-GST');
    }
}
