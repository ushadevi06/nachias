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
        if (empty($description)) {
            $actionWord = ucwords(str_replace('_', ' ', $action));
            $moduleWord = ucwords(str_replace(['_', '-'], ' ', $module));
            $userName = createdByName(auth()->id());
            
            $recordIdentifier = "#{$recordId}";
            if ($table && $recordId) {
                try {
                    $record = \Illuminate\Support\Facades\DB::table($table)->find($recordId);
                    if ($record) {
                        if (isset($record->order_no)) $recordIdentifier = $record->order_no;
                        elseif (isset($record->inv_no)) $recordIdentifier = $record->inv_no;
                        elseif (isset($record->name)) $recordIdentifier = $record->name;
                        elseif (isset($record->job_card_no)) $recordIdentifier = $record->job_card_no;
                    }
                } catch (\Exception $e) {}
            }
            
            $description = "{$moduleWord} {$recordIdentifier} {$actionWord} by {$userName}";
        }

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
        $previous = url()->previous();
        $baseUrl = url('/');
        
        if ($previous && $previous !== url()->current() && $previous !== $baseUrl . '/login') {
            return redirect()->back()->with('danger', $message);
        }
        
        return redirect($baseUrl)->with('danger', $message);
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

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($number)
    {
        if (!is_numeric($number)) {
            return $number;
        }
        $isNegative = $number < 0;
        $number = abs($number);

        $parts = explode('.', sprintf('%0.2f', $number));
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? $parts[1] : '00';

        $len = strlen($integerPart);
        if ($len > 3) {
            $lastThree = substr($integerPart, -3);
            $remaining = substr($integerPart, 0, -3);
            $reversedRemaining = strrev($remaining);
            $chunks = str_split($reversedRemaining, 2);
            $formattedRemaining = strrev(implode(',', $chunks));
            
            $formattedInteger = $formattedRemaining . ',' . $lastThree;
        } else {
            $formattedInteger = $integerPart;
        }

        $formatted = $formattedInteger . '.' . $decimalPart;
        return $isNegative ? '-' . $formatted : $formatted;
    }
}

if (!function_exists('formatStockItemName')) {
    function formatStockItemName($categoryName)
    {
        if (empty($categoryName)) {
            return '';
        }

        $formatted = strtoupper(trim($categoryName));

        // Use cache to avoid querying styles on every call if this is in a loop
        $styles = \Illuminate\Support\Facades\Cache::remember('active_styles_for_formatting', 3600, function () {
            return \App\Models\Style::active()->whereNotNull('code')->get();
        });

        foreach ($styles as $style) {
            if (empty($style->style_name) || empty($style->code)) {
                continue;
            }
            $styleNameUpper = strtoupper($style->style_name);
            $styleCodeUpper = strtoupper($style->code);
            // Replace the full word style name with the code
            $formatted = str_replace($styleNameUpper, $styleCodeUpper, $formatted);
        }

        // Replace spaces with hyphens (e.g., CW-WHT H/S -> CW-WHT-H/S)
        $formatted = str_replace(' ', '-', $formatted);
        // Clean up any double hyphens if they occur
        $formatted = str_replace('--', '-', $formatted);

        return $formatted;
    }
}


if (!function_exists('formatLocationLogData')) {
    function formatLocationLogData($data)
    {
        if (!$data) return $data;

        if (isset($data['state_id'])) {
            $state = \App\Models\State::find($data['state_id']);
            $data['state'] = $state ? $state->state_name : '';
            unset($data['state_id']);
        }

        if (isset($data['state_ids'])) {
            $stateIds = array_filter(explode(',', $data['state_ids']));
            $data['states'] = \App\Models\State::whereIn('id', $stateIds)->pluck('state_name')->implode(', ');
            unset($data['state_ids']);
        }

        if (isset($data['city_id'])) {
            $city = \App\Models\City::find($data['city_id']);
            $data['city'] = $city ? $city->city_name : '';
            unset($data['city_id']);
        }

        if (isset($data['city_ids'])) {
            $cityIds = array_filter(explode(',', $data['city_ids']));
            $data['cities'] = \App\Models\City::whereIn('id', $cityIds)->pluck('city_name')->implode(', ');
            unset($data['city_ids']);
        }

        if (isset($data['place_id'])) {
            $place = \App\Models\Place::find($data['place_id']);
            $data['place'] = $place ? $place->place_name : '';
            unset($data['place_id']);
        }

        if (isset($data['place_ids'])) {
            $placeIds = array_filter(explode(',', $data['place_ids']));
            $data['places'] = \App\Models\Place::whereIn('id', $placeIds)->pluck('place_name')->implode(', ');
            unset($data['place_ids']);
        }

        if (isset($data['zone_id'])) {
            $zone = \App\Models\Zone::find($data['zone_id']);
            $data['zone'] = $zone ? $zone->zone_name : '';
            unset($data['zone_id']);
        }

        if (isset($data['zone_ids'])) {
            $zoneIds = array_filter(explode(',', $data['zone_ids']));
            $data['zones'] = \App\Models\Zone::whereIn('id', $zoneIds)->pluck('zone_name')->implode(', ');
            unset($data['zone_ids']);
        }

        return $data;
    }
}
