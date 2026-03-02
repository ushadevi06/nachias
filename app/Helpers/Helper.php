<?php

use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

if (!function_exists('createdByName')) {

    function createdByName($userId)
    {
        if (!$userId) {
            return 'System';
        }

        $user = User::find($userId);

        return $user?->name ?? 'Unknown';
    }
}
if (!function_exists('addLog')) {
    function addLog($action, $module, $table, $recordId, $oldData = null, $newData = null, $description = null)
    {
        \App\Models\Log::create([
            'user_id'     => auth()->id(),
            'action_type' => $action,
            'module'      => $module,
            'table_name'  => $table,
            'record_id'   => $recordId,
            'old_values'  => $oldData ? json_encode($oldData) : null,
            'new_values'  => $newData ? json_encode($newData) : null,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);
    }
}

if (!function_exists('unauthorizedRedirect')) {
    function unauthorizedRedirect($message = 'Unauthorized action.')
    {
        return redirect(url(''))->with('danger', $message);
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $no = round($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            '0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $paise = ($point > 0) ?
            "and " . ($words[floor($point / 10) * 10] . " " . $words[$point % 10]) . ' Paise' : '';
        return strtoupper(($result ? $result . "Rupees " : "") . $paise . " Only");
    }
}