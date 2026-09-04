<?php

namespace App\Http\Controllers;

use App\Services\EInvoiceService;
use Illuminate\Http\Request;

class TaxProCredentialController extends Controller
{
    protected $eInvoiceService;

    public function __construct(EInvoiceService $eInvoiceService)
    {
        $this->eInvoiceService = $eInvoiceService;
    }

    /**
     * Display TaxPro API Credentials, Balance, and Support Information
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect('login');
        }

        $isAdmin = $user->id == 1 
            || $user->hasRole(['Admin', 'Super Admin', 'ADMIN', 'SUPER ADMIN', 'MANAGING DIRECTOR', 'IT INCHARGE']) 
            || ($user->role && in_array(strtoupper($user->role->name), ['ADMIN', 'SUPER ADMIN', 'MANAGING DIRECTOR', 'IT INCHARGE']));

        $isAccountant = $user->hasRole(['ACCOUNTANT', 'Accountant', 'accountant']) 
            || ($user->role && strtoupper($user->role->name) === 'ACCOUNTANT');

        // Only Admin and Accountant roles can access this page
        if (!$isAdmin && !$isAccountant) {
            return unauthorizedRedirect();
        }

        $authUrl = env('EINV_AUTH_URL', '');
        $isSandbox = str_contains($authUrl, 'sandbox');

        $credentials = [
            'asp_id' => env('EINV_ASP_ID', '-'),
            'asp_password' => env('EINV_ASP_PASSWORD', ''),
            'gstin' => env('EINV_GSTIN', '-'),
            'username' => env('EINV_USERNAME', '-'),
            'password' => env('EINV_PASSWORD', ''),
            'auth_url' => $authUrl ?: 'https://einvapi.charteredinfo.com/eivital/dec/v1.04/auth',
            'api_url' => env('EINV_API_URL', 'https://einvapi.charteredinfo.com/eicore/dec/v1.03/Invoice'),
            'ewaybill_auth_url' => env('EINV_EWAYBILL_AUTH_URL', 'https://einvapi.charteredinfo.com/v1.03/dec/auth'),
            'ewaybill_url' => env('EINV_EWAYBILL_URL', 'https://einvapi.charteredinfo.com/v1.03/dec/ewayapi'),
            'balance_url' => env('EINV_BALANCE_URL', $isSandbox ? 'https://gstsandbox.charteredinfo.com/aspapi/v1.1/getapibalance' : 'https://einvapi.charteredinfo.com/aspapi/v1.1/getapibalance'),
            'environment' => $isSandbox ? 'Sandbox (Testing)' : 'Production (Live)',
            'is_sandbox' => $isSandbox,
        ];

        $helpline = [
            'central_helpdesk' => '0712-663 8888',
            'central_helpdesk_lines' => '100 Lines',
            'emails' => [
                'support' => 'support@taxpro.co.in',
                'gsp_info' => 'info@taxpro.co.in',
                'sales' => 'sales@taxpro.co.in',
                'corporate' => 'corporatesales@taxpro.co.in',
            ],
            'offices' => [
                [
                    'city' => 'Nagpur (Corporate Office)',
                    'address' => '"Chartered House", West of Lata Mangeshkar Musical Park, Bhandara Road, Nagpur – 440 035, India',
                    'phone' => '0712-663 8888',
                ],
                [
                    'city' => 'Mumbai Office',
                    'address' => '#110, 1st Floor, Building No. 3, Hari OM Plaza, M. G. Road, Near National Park, Borivali (E), Mumbai – 400 066',
                    'phone' => '022-28955888 / 9766491466',
                    'email' => 'taxpromumbai@taxpro.co.in',
                ],
                [
                    'city' => 'Delhi Office',
                    'address' => '#222-223, Durga Chambers, 1333-34, D.B. Gupta Road, Karol Bagh, New Delhi – 110 005',
                    'phone' => '011-45037177 / 45020850',
                    'email' => 'taxprodelhi@taxpro.co.in',
                ],
                [
                    'city' => 'Bengaluru Office',
                    'address' => 'Bengaluru Regional Branch',
                    'phone' => '080-40921639',
                ],
            ],
            'websites' => [
                'portal' => 'https://www.taxpro.co.in',
                'gsp' => 'https://www.charteredinfo.com',
            ],
        ];

        return view('taxpro_credentials.index', compact('credentials', 'helpline', 'isAdmin', 'isAccountant'));
    }

    /**
     * AJAX endpoint to check live API balance
     */
    public function checkBalance()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $isAdmin = $user->id == 1 
            || $user->hasRole(['Admin', 'Super Admin', 'ADMIN', 'SUPER ADMIN', 'MANAGING DIRECTOR', 'IT INCHARGE']) 
            || ($user->role && in_array(strtoupper($user->role->name), ['ADMIN', 'SUPER ADMIN', 'MANAGING DIRECTOR', 'IT INCHARGE']));

        $isAccountant = $user->hasRole(['ACCOUNTANT', 'Accountant', 'accountant']) 
            || ($user->role && strtoupper($user->role->name) === 'ACCOUNTANT');

        if (!$isAdmin && !$isAccountant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $result = $this->eInvoiceService->getApiBalance();
        return response()->json($result);
    }
}
