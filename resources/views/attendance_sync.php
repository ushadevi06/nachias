<?php
include 'config.php';

function getStatus($conn, $date, $inTime, $outTime, $hours)
{
    $isSunday = date('w', strtotime($date)) == 0;

    $holidayCheck = $conn->query("
        SELECT id FROM declared_holidays
        WHERE date = '$date' LIMIT 1
    ");
    $isHoliday = $holidayCheck && $holidayCheck->num_rows > 0;

    // No punch at all
    if (empty($inTime) && empty($outTime)) {
        if ($isHoliday) return 'Holiday';
        if ($isSunday)  return 'Week Off';
        return 'Absent';
    }

    // Worked on Sunday or Holiday → Overtime
    if (($isSunday || $isHoliday) && (!empty($inTime) || !empty($outTime))) {
        return 'Overtime';
    }

    // Has in_time but no out_time → Missing Time Card
    if (empty($inTime) && !empty($outTime)) {
        return 'Missing Time Card';
    }

    // Worked more than 9 hours → Overtime
    if ($hours > 9) {
        return 'Overtime';
    }

    // Late entry
    if (!empty($inTime)) {
        $lateLimit  = strtotime($date . ' 09:05:00');
        $employeeIn = strtotime($inTime);
        if ($employeeIn > $lateLimit) {
            return 'Late';
        }
    }

    return 'Present';
}

// $selectedDate = date('Y-m-d');
// $from = $selectedDate . ' 00:00:00';
// $to   = $selectedDate . ' 23:59:59';
$fromDate = date('Y-m-d', strtotime('-6 days'));
$toDate   = date('Y-m-d');
$from     = $fromDate . ' 00:00:00';
$to       = $toDate . ' 23:59:59';
$allGrouped = [];
$allSerials      = [];
$devicesQuery = $conn->query("
    SELECT serial_number
    FROM devices
");
while ($deviceRow = $devicesQuery->fetch_assoc()) {
    $device       = $deviceRow['serial_number'];
    $allSerials[] = $device;

    echo "<h3>Fetching Device: $device</h3>";

    $xml = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetTransactionsLog xmlns="http://tempuri.org/">
                <FromDateTime>' . $from . '</FromDateTime>
                <ToDateTime>'   . $to   . '</ToDateTime>
                <SerialNumber>' . $device . '</SerialNumber>
                <UserName>test</UserName>
                <UserPassword>Test@123</UserPassword>
                <strDataList>123</strDataList>
            </GetTransactionsLog>
        </soap:Body>
    </soap:Envelope>';

    $url = "http://106.51.22.181:85/iclock/webAPIservice.asmx";
    $ch  = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $xml,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "http://tempuri.org/GetTransactionsLog"',
        ],
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 120,
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error for Device $device: $error<br><br>";
        continue;
    }

    preg_match('/<strDataList>(.*?)<\/strDataList>/s', $response, $matches);

    if (!isset($matches[1]) || empty(trim($matches[1]))) {
        echo "No logs found for Device: $device<br><br>";
        continue;
    }

    $rows = preg_split('/\r\n|\r|\n/', trim($matches[1]));

    foreach ($rows as $row) {
        $row = trim($row);
        if (empty($row)) continue;

        $parts = preg_split('/\s+/', $row);
        if (count($parts) < 3) continue;

        $empCode  = trim($parts[0]);
        $datetime = trim($parts[1] . ' ' . $parts[2]);
        $logDate  = date('Y-m-d', strtotime($datetime));

        // Merge punch times (avoid duplicates)
        $allGrouped[$empCode][$logDate]['devices'][$device] = true;

        if (!in_array($datetime, $allGrouped[$empCode][$logDate]['times'] ?? [])) {
            $allGrouped[$empCode][$logDate]['times'][] = $datetime;
        }
    }

    echo "Fetched logs for Device: $device<br>";
}

echo "<h3>Processing merged attendance...</h3>";

foreach ($allGrouped as $emp => $dates) {
      $deviceQuery = $conn->query("
        SELECT d.serial_number
        FROM users u
        LEFT JOIN devices d
            ON d.serial_number COLLATE utf8mb4_general_ci =
               u.device COLLATE utf8mb4_general_ci
        WHERE u.emp_id = '$emp'
        LIMIT 1
    ");

    $deviceData = $deviceQuery->fetch_assoc();
    $serialNumber = $deviceData['serial_number'] ?? '';
    foreach ($dates as $date => $data) {

        $times         = $data['times'] ?? [];

        sort($times);

        $in  = null;
        $out = null;

        if (count($times) == 1) {
            $cutoffTime = strtotime($date . ' 13:00:00');
            if (strtotime($times[0]) >= $cutoffTime) {
                $out = $times[0];
            } else {
                $in  = $times[0];
            }
        } else {
            $in  = reset($times);
            $out = end($times);
        }

        $checkQuery = $conn->query("SELECT id, in_time, out_time, is_manual, device_serial_number FROM attendances WHERE emp_code = '$emp' AND date = '$date' LIMIT 1");

        $existing = $checkQuery ? $checkQuery->fetch_assoc() : null;
        if ($existing && $existing['is_manual']) {
            continue;
        }

        $dbIn = $existing ? $existing['in_time'] : null;
        $dbOut = $existing ? $existing['out_time'] : null;
        $allTimes = array_values(array_unique(array_filter([$in, $out, $dbIn, $dbOut])));
        if (!empty($allTimes)) {
            sort($allTimes);
            if (count($allTimes) == 1) {
                $cutoffTime = strtotime($date . ' 13:00:00');
                if (strtotime($allTimes[0]) >= $cutoffTime) {
                    $in = null;
                    $out = $allTimes[0];
                } else {
                    $in = $allTimes[0];
                    $out = null;
                }
            } else {
                $in = $allTimes[0];
                $out = end($allTimes);
            }
        }

        $deviceSerial = $serialNumber;
        if (empty($deviceSerial) && $existing) {
            $deviceSerial = $existing['device_serial_number'];
        }

        $workHours = 0;
        if ($in && $out) {
            $workHours = round((strtotime($out) - strtotime($in)) / 3600, 2);
        }

        // Permission hours
        $permissionMinutes = 0;
        if ($in) {
            $allowedTime = strtotime($date . ' 09:06:00');
            $inTimestamp = strtotime($in);
            if ($inTimestamp > $allowedTime) {
                $lateMinutes       = ceil(($inTimestamp - $allowedTime) / 60);
                $permissionMinutes = ceil($lateMinutes / 15) * 15;
            }
        }
        $permissionHours = round($permissionMinutes / 60, 2);

        $status = getStatus($conn, $date, $in, $out, $workHours);

        $inVal  = $in  ? "'$in'"  : "NULL";
        $outVal = $out ? "'$out'" : "NULL";

        $sql = "
            INSERT INTO attendances
                (emp_code, device_serial_number, date, in_time, out_time,
                 work_hours, permission_hours, status, is_manual)
            VALUES
                ('$emp', '$deviceSerial', '$date', $inVal, $outVal,
                 '$workHours', '$permissionHours', '$status', 0)
            ON DUPLICATE KEY UPDATE
                device_serial_number = VALUES(device_serial_number),
                in_time              = VALUES(in_time),
                out_time             = VALUES(out_time),
                work_hours           = VALUES(work_hours),
                permission_hours     = VALUES(permission_hours),
                status               = VALUES(status)
        ";

        if ($conn->query($sql)) {
            echo "Synced: $emp - $date (Devices: $serialNumber)<br>";
        } else {
           echo "DB Error: " . $conn->error . "<br>";
        }
    }
}

$employees = [];

$usersQuery = $conn->query("
    SELECT
        u.emp_id,
        d.serial_number
    FROM users u
    LEFT JOIN devices d
        ON d.serial_number COLLATE utf8mb4_general_ci =
           u.device COLLATE utf8mb4_general_ci
    WHERE u.id != 1
");

while ($user = $usersQuery->fetch_assoc()) {
    $employees[] = $user;
}

// --- NEW: Handle absent marking for each of the last 7 days safely ---
$datesToMark = [];
for ($i = 6; $i >= 0; $i--) {
    $datesToMark[] = date('Y-m-d', strtotime("-$i days"));
}

foreach ($datesToMark as $dateKey) {
    foreach ($employees as $employee) {
        $emp = $employee['emp_id'];
        $deviceSerialNumber = $employee['serial_number'];

        // Skip if already processed in this execution
        if (isset($allGrouped[$emp][$dateKey])) {
            continue;
        }

        $check = $conn->query("
            SELECT id, in_time, out_time, is_manual
            FROM attendances
            WHERE emp_code = '$emp'
              AND date = '$dateKey'
            LIMIT 1
        ");

        if ($check && $check->num_rows > 0) {
            $existing = $check->fetch_assoc();
            
            // Skip marking absent if record has manual edits or existing punches
            if ($existing['is_manual'] || !empty($existing['in_time']) || !empty($existing['out_time'])) {
                continue;
            }

            $status = getStatus($conn, $dateKey, null, null, 0);
            $sql = "
                UPDATE attendances
                SET in_time = NULL,
                    out_time = NULL,
                    work_hours = 0,
                    permission_hours = 0,
                    status = '$status'
                WHERE id = " . $existing['id'];
            $conn->query($sql);
        } else {
            $status = getStatus($conn, $dateKey, null, null, 0);
            $sql = "
                INSERT INTO attendances
                    (
                        emp_code,
                        device_serial_number,
                        date,
                        in_time,
                        out_time,
                        work_hours,
                        permission_hours,
                        status,
                        is_manual
                    )
                VALUES
                    (
                        '$emp',
                        '$deviceSerialNumber',
                        '$dateKey',
                        NULL,
                        NULL,
                        0,
                        0,
                        '$status',
                        0
                    )
            ";

            if ($conn->query($sql)) {
                echo "Marked $emp as $status for $dateKey (Device: $deviceSerialNumber)<br>";
            } else {
                echo "DB Error: " . $conn->error . "<br>";
            }
        }
    }
}

echo "<hr><h2>Attendance Sync Completed</h2>";