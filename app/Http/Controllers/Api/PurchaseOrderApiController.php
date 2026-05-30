<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseCommissionAgent;
use App\Models\Supplier;
use App\Models\StoreType;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\Brand;
use App\Models\Style;
use App\Models\Color;
use App\Models\SizeRatio;
use App\Models\Setting;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class PurchaseOrderApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/purchase-orders",
     *     summary="List Purchase Executive's Orders",
     *     description="Returns a paginated list of Purchase Orders belonging to the authenticated user. Supports filtering by status and date range.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Universal search (po_number, date, amount, supplier, agent)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Draft", "Approved", "Dispatched", "Received"})
     *     ),
     *     @OA\Parameter(
     *         name="po_date_range",
     *         in="query",
     *         description="Filter by date range (format: d-m-Y to d-m-Y)",
     *         required=false,
     *         @OA\Schema(type="string", example="01-03-2024 to 31-03-2024")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $query = PurchaseOrder::with(['purchaseCommissionAgent:id,name', 'supplier:id,name', 'storeType:id,store_type_name'])->where('purchase_executive_id', $user->id)->orderBy('created_at', 'desc');

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('po_date_range') && !empty($request->po_date_range)) {
                $dates = explode(' to ', $request->po_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('po_date', [$startDate, $endDate]);
                } else if (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('po_date', $startDate);
                }
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('po_number', 'LIKE', "%{$search}%")
                        ->orWhere('po_date', 'LIKE', "%{$search}%")
                        ->orWhere('total_amount', 'LIKE', "%{$search}%")
                        ->orWhereHas('supplier', function ($sq) use ($search) {
                            $sq->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('purchaseCommissionAgent', function ($aq) use ($search) {
                            $aq->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $perPage = $request->input('per_page', 15);
            $purchaseOrders = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => $purchaseOrders
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/purchase-orders/{id}",
     *     summary="Get Detailed Purchase Order",
     *     description="Returns full information for a specific Purchase Order including line items and attachment URLs.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Purchase Order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Purchase Order not found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show($id)
    {
        try {
            $user = auth()->user();
            $purchaseOrder = PurchaseOrder::with([
                'purchaseCommissionAgent:id,name',
                'supplier:id,name',
                'storeType:id,store_type_name',
                'items.storeCategory:id,category_name',
                'items.rawMaterial:id,name,uom_id',
                'items.uom:id,uom_name',
                'items.color:id,color_name',
                'items.style:id,style_name',
                'items.brand:id,brand_name',
                'items.fabricWidth'
            ])->where('purchase_executive_id', $user->id)->where('id', $id)->first();

            if (!$purchaseOrder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Purchase Order not found'
                ], 404);
            }

            if ($purchaseOrder->additional_attachments) {
                $attachments = $purchaseOrder->additional_attachments;
                $formattedAttachments = [];
                foreach ($attachments as $attachment) {
                    $formattedAttachments[] = url('uploads/purchase_orders/' . $attachment);
                }
                $purchaseOrder->additional_attachments = $formattedAttachments;
            }

            foreach ($purchaseOrder->items as $item) {
                if ($item->attached_file) {
                    $item->attached_file = url('uploads/purchase_orders/' . $item->attached_file);
                }
            }

            return response()->json([
                'status' => true,
                'data' => $purchaseOrder
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/purchase-orders/{id}/update-status",
     *     summary="Change Purchase Order Status",
     *     description="Updates the status of a specific Purchase Order. Status updates cannot go backwards (e.g., from Dispatched back to Approved).",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Purchase Order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", enum={"Draft", "Approved", "Dispatched", "Received"}, description="New Status")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status updated successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Purchase Order not found"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update_status(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Draft,Approved,Dispatched,Received'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $purchaseOrder = PurchaseOrder::where('purchase_executive_id', $user->id)->where('id', $id)->first();

            if (!$purchaseOrder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Purchase Order not found'
                ], 404);
            }

            $statusLevels = [
                'Draft' => 1,
                'Approved' => 2,
                'Dispatched' => 3,
                'Received' => 4
            ];

            $currentLevel = $statusLevels[$purchaseOrder->status];
            $newLevel = $statusLevels[$request->status];

            if ($newLevel < $currentLevel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot revert status from ' . $purchaseOrder->status . ' to ' . $request->status
                ], 400);
            }

            $purchaseOrder->status = $request->status;
            $purchaseOrder->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/purchase-orders/create_po",
     *     summary="Get Initial Data for Creating PO",

     *     description="Generates the next PO number and fetches lists of active agents, suppliers, and store types.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="next_po_number", type="string", example="PO0001"),
     *                 @OA\Property(property="po_date", type="string", format="date", example="27-03-2024"),
     *                 @OA\Property(property="purchase_commission_agents", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="suppliers", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="store_types", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create_po()
    {
        try {
            $nextPoNumber = '';
            $setting = Setting::first();
            if ($setting && $setting->po_prefix) {
                $prefix = $setting->po_prefix;
                $lastPo = PurchaseOrder::where('po_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();

                if ($lastPo) {
                    $lastNumberStr = substr($lastPo->po_number, strlen($prefix));
                    $lastNumber = intval($lastNumberStr);
                    $nextNumber = str_pad($lastNumber + 1, max(strlen($lastNumberStr), 4), '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }
                $nextPoNumber = $prefix . $nextNumber;
            }

            $purchaseCommissionAgents = PurchaseCommissionAgent::active()->get(['id', 'name', 'code']);

            $suppliers = Supplier::active()->with(['state:id,state_name'])->get(['id', 'name', 'code', 'state_id']);

            $storeTypes = StoreType::get(['id', 'store_type_name']);

            return response()->json([
                'status' => true,
                'data' => [
                    'po_number' => $nextPoNumber,
                    'po_date' => Carbon::today()->format('d-m-Y'),
                    'purchase_commission_agents' => $purchaseCommissionAgents,
                    'suppliers' => $suppliers,
                    'store_types' => $storeTypes,
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/purchase-orders/item_details",
     *     summary="Get Item Details Metadata for PO",
     *     description="Fetches metadata needed for adding an item to a Purchase Order. Filters raw materials if store_category_id is provided.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_category_id",
     *         in="query",
     *         description="Optional ID to filter raw materials by category",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="store_categories", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="brands", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="styles", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="fabric_widths", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="colors", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="raw_materials", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="validation", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function item_details(Request $request)
    {
        try {
            $storeCategories = StoreCategory::active()->get(['id', 'category_name', 'code']);
            $brands = Brand::active()->get(['id', 'brand_name', 'code']);
            $styles = Style::active()->get(['id', 'style_name', 'code']);
            $colors = Color::active()->get(['id', 'color_name']);

            $sizeRatios = SizeRatio::active()->get();
            $fabricWidths = $sizeRatios->map(function ($ratio) {
                return [
                    'id' => $ratio->id,
                    'display' => "({$ratio->size}) - ({$ratio->ratio})",
                ];
            });

            $rawMaterialQuery = RawMaterial::active()->with(['uom:id,uom_name,uom_code']);

            if ($request->has('store_category_id') && !empty($request->store_category_id)) {
                $rawMaterialQuery->where('store_category_id', $request->store_category_id);
            }

            $rawMaterials = $rawMaterialQuery->get(['id', 'name', 'code', 'uom_id']);

            return response()->json([
                'status' => true,
                'data' => [
                    'store_categories' => $storeCategories,
                    'brands' => $brands,
                    'raw_materials' => $rawMaterials,
                    'styles' => $styles,
                    'fabric_widths' => $fabricWidths,
                    'colors' => $colors,
                    'validation' => [
                        'file_upload' => [
                            'max_size' => '2MB',
                            'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp'],
                            'rules' => 'image|mimes:jpeg,jpg,png,webp|max:2048'
                        ]
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**

     * @OA\Get(
     *     path="/api/purchase-orders/additional_info",
     *     summary="Get Additional Information Metadata for PO",
     *     description="Fetches current status options, default payment terms, and attachment constraints.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="order_statuses", type="array", @OA\Items(type="string", example="Draft")),
     *                 @OA\Property(property="payment_terms_info", type="string", example="No special characters allowed (<, >)"),
     *                 @OA\Property(property="attachment_rules", type="object",
     *                     @OA\Property(property="max_files", type="integer", example=5),
     *                     @OA\Property(property="max_size_per_file", type="string", example="2MB"),
     *                     @OA\Property(property="allowed_mimes", type="array", @OA\Items(type="string", example="pdf"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function additional_info()
    {
        try {
            $orderStatuses = ['Draft', 'Approved', 'Dispatched', 'Received'];

            return response()->json([
                'status' => true,
                'data' => [
                    'order_statuses' => $orderStatuses,
                    'payment_terms_info' => '',
                    'attachment_rules' => [
                        'max_files' => 5,
                        'max_size_per_file' => '2MB',
                        'allowed_mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp'],
                        'rules' => 'nullable|array|max:5',
                        'file_rules' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048'
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**

     * @OA\Get(
     *     path="/api/purchase-orders/tax_summary",
     *     summary="Get Tax Summary Metadata for PO",
     *     description="Fetches default tax percentages (CGST, SGST, IGST) from system settings.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cgst", type="number", example=9),
     *                 @OA\Property(property="sgst", type="number", example=9),
     *                 @OA\Property(property="igst", type="number", example=18),
     *                 @OA\Property(property="round_off_types", type="array", @OA\Items(type="string"), example={"Add", "Less"})
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function tax_summary()
    {
        try {
            $setting = Setting::first();

            return response()->json([
                'status' => true,
                'data' => [
                    'cgst' => $setting ? (float) $setting->cgst : 0,
                    'sgst' => $setting ? (float) $setting->sgst : 0,
                    'igst' => $setting ? (float) $setting->igst : 0,
                    'round_off_types' => ['Add', 'Less']
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**



     * @OA\Post(
     *     path="/api/purchase-orders/store",
     *     summary="Submit a New Purchase Order",
     *     description="Creates a new Purchase Order with multiple items and optional attachments. Automatically sets purchase_executive_id.",
     *     tags={"Purchase Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="po_number", type="string", example="PO0001"),
     *                 @OA\Property(property="po_date", type="string", format="date", example="27-03-2024"),
     *                 @OA\Property(property="supplier_id", type="integer", example=1),
     *                 @OA\Property(property="reference_no", type="string", example="REF123"),
     *                 @OA\Property(property="reference_date", type="string", format="date", example="27-03-2024"),
     *                 @OA\Property(property="due_date", type="string", format="date", example="10-04-2024"),
     *                 @OA\Property(property="store_type_id", type="integer", example=1),
     *                 @OA\Property(property="purchase_commission_agent_id", type="integer", example=1),
     *                 @OA\Property(property="commission", type="number", example=5.00),
     *                 @OA\Property(property="payment_terms", type="string", example="Net 30"),
     *                 @OA\Property(property="status", type="string", example="Draft"),
     *                 @OA\Property(property="discount_percent", type="number", example=0),
     *                 @OA\Property(property="other_state", type="string", example="no"),
     *                 @OA\Property(property="igst_percent", type="number", example=0),
     *                 @OA\Property(property="cgst_percent", type="number", example=9),
     *                 @OA\Property(property="sgst_percent", type="number", example=9),
     *                 @OA\Property(property="round_off_type", type="string", example="Add"),
     *                 @OA\Property(property="round_off", type="number", example=0.00),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="additional_attachments[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Purchase Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Purchase Order created successfully"),
     *             @OA\Property(property="po_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function store(Request $request)
    {
        $rules = [
            'po_number' => 'required|string|unique:purchase_orders,po_number',
            'po_date' => 'required',
            'purchase_commission_agent_id' => 'nullable|exists:purchase_commission_agents,id',
            'commission' => 'nullable|numeric|min:0|max:100',
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_no' => 'required|string|min:3|max:100',
            'reference_date' => 'required',
            'due_date' => 'required',
            'store_type_id' => 'required|exists:store_types,id',
            'payment_terms' => 'nullable|string|max:255|regex:/^[^<>]*$/',
            'status' => 'required|in:Draft,Approved,Dispatched,Received',
            'items' => 'required|array|min:1',
            'items.*.store_category_id' => 'required|exists:store_categories,id',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.uom_id' => 'required|exists:uoms,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string|max:255',
            'items.*.attached_file' => 'nullable|mimes:jpeg,jpg,png,webp|max:2048',
            'discount_percent' => 'nullable|numeric|min:0',
            'additional_attachments' => 'nullable|array|max:5',
            'additional_attachments.*' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048',
            'round_off_type' => 'nullable|in:Add,Less',
            'round_off' => 'nullable|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();

            $po_number = $request->po_number;


            $totalQty = 0;
            $subTotal = 0;
            foreach ($request->items as $item) {
                $totalQty += floatval($item['quantity']);
                $subTotal += (floatval($item['quantity']) * floatval($item['rate']));
            }

            $discountPercent = floatval($request->discount_percent ?? 0);
            $discountAmount = round(($subTotal * $discountPercent) / 100, 2);
            $taxableAmount = $subTotal - $discountAmount;

            $isOtherState = $request->other_state === 'yes';
            $igstPercent = $isOtherState ? floatval($request->igst_percent ?? 0) : 0;
            $cgstPercent = !$isOtherState ? floatval($request->cgst_percent ?? 0) : 0;
            $sgstPercent = !$isOtherState ? floatval($request->sgst_percent ?? 0) : 0;

            $taxAmount = 0;
            if ($isOtherState) {
                $taxAmount = round(($taxableAmount * $igstPercent) / 100, 2);
            } else {
                $taxAmount = round(($taxableAmount * $cgstPercent) / 100, 2) + round(($taxableAmount * $sgstPercent) / 100, 2);
            }

            $totalBeforeRoundOff = round($taxableAmount + $taxAmount, 2);
            $roundOffAmount = floatval($request->round_off ?? 0);
            $roundOffType = $request->round_off_type ?? 'Add';
            $finalTotal = $roundOffType === 'Add' ? ($totalBeforeRoundOff + $roundOffAmount) : ($totalBeforeRoundOff - $roundOffAmount);

            $poData = [
                'purchase_executive_id' => $user->id,
                'po_number' => $po_number,
                'po_date' => Carbon::parse($request->po_date)->format('Y-m-d'),
                'purchase_commission_agent_id' => $request->purchase_commission_agent_id,
                'commission' => $request->commission ?? 0,
                'supplier_id' => $request->supplier_id,
                'reference_no' => $request->reference_no,
                'reference_date' => Carbon::parse($request->reference_date)->format('Y-m-d'),
                'due_date' => Carbon::parse($request->due_date)->format('Y-m-d'),
                'store_type_id' => $request->store_type_id,
                'payment_terms' => $request->payment_terms,
                'status' => $request->status,
                'total_qty' => $totalQty,
                'sub_total' => $subTotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'taxable_amount' => $taxableAmount,
                'other_state' => $isOtherState,
                'igst_percent' => $igstPercent,
                'cgst_percent' => $cgstPercent,
                'sgst_percent' => $sgstPercent,
                'tax_amount' => $taxAmount,
                'round_off_type' => $roundOffType,
                'round_off' => $roundOffAmount,
                'total_amount' => $finalTotal,
                'created_by' => $user->id,
            ];

            $purchaseOrder = PurchaseOrder::create($poData);

            $attachments = [];
            if ($request->hasFile('additional_attachments')) {
                $uploadPath = public_path('uploads/purchase_orders');
                if (!file_exists($uploadPath))
                    mkdir($uploadPath, 0755, true);

                foreach ($request->file('additional_attachments') as $file) {
                    $fileName = 'additional_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadPath, $fileName);
                    $attachments[] = $fileName;
                    if (count($attachments) >= 5)
                        break;
                }
            }
            $purchaseOrder->update(['additional_attachments' => $attachments]);

            foreach ($request->items as $index => $item) {
                $itemData = [
                    'purchase_order_id' => $purchaseOrder->id,
                    'store_category_id' => $item['store_category_id'],
                    'raw_material_id' => $item['raw_material_id'],
                    'uom_id' => $item['uom_id'],
                    'color_id' => $item['color_id'] ?? null,
                    'style_id' => $item['style_id'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'fabric_width_id' => $item['fabric_width_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'supplier_design_name' => $item['supplier_design_name'] ?? null,
                    'rate' => $item['rate'],
                    'amount' => floatval($item['quantity']) * floatval($item['rate']),
                    'remarks' => $item['remarks'] ?? null,
                ];

                if ($request->hasFile("items.{$index}.attached_file")) {
                    $file = $request->file("items.{$index}.attached_file");
                    $fileName = 'item_' . time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $uploadPath = public_path('uploads/purchase_orders');
                    $file->move($uploadPath, $fileName);
                    $filePath = $uploadPath . '/' . $fileName;
                    $this->compressImage($filePath, $filePath, 60);
                    $itemData['attached_file'] = $fileName;
                }

                PurchaseOrderItem::create($itemData);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase Order created successfully',
                'po_id' => $purchaseOrder->id
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    private function compressImage($sourcePath, $destinationPath, $quality = 60)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $info = getimagesize($sourcePath);

        if (!$info || !isset($info['mime'])) {
            return false;
        }

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];
        $max_width = 1000;
        $max_height = 1000;

        if ($width > $max_width || $height > $max_height) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $max_width;
                $new_height = round($max_width / $ratio);
            } else {
                $new_height = $max_height;
                $new_width = round($max_height * $ratio);
            }
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        $resize = function($src) use ($new_width, $new_height, $width, $height) {
            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            return $dst;
        };

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = $resize($image);
                    imagedestroy($image);
                    $image = $resized;
                }
                imagejpeg($image, $destinationPath, $quality);
                imagedestroy($image);
                return true;

            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = imagecreatetruecolor($new_width, $new_height);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagedestroy($image);
                    $image = $resized;
                } else {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                imagepng($image, $destinationPath, 6);
                imagedestroy($image);
                return true;

            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    return false;
                }
                $image = imagecreatefromwebp($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = $resize($image);
                    imagedestroy($image);
                    $image = $resized;
                }
                imagewebp($image, $destinationPath, $quality);
                imagedestroy($image);
                return true;
        }

        return false;
    }
}

