<table>
    <thead>
        <tr>
            <th colspan="{{ !empty($isDhotiBrand) ? 28 : 25 }}" style="font-size: 14px; font-weight: bold; text-align: center; background-color: #f1f5f9; height: 35px; border: 1px solid #000000;">
                {{ $title }}
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">S.NO</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">PRODUCT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">ORDER FABRIC</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">FABRIC STOCK</th>
            @if(!empty($isDhotiBrand))
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">DHOTI STOCK</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">DHOTI REORDER</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">DHOTI PENDING</th>
            @endif
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">STOCK</th>
            <th colspan="9" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #cbd5e1;">F/S</th>
            <th colspan="9" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">H/S</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #cbd5e1;">GROSS TOT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">UNIT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">C.NO</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">REMARKS</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">36</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">38</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">40</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">42</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">44</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">46</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">48</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">50</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cbd5e1;">T/L</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">36</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">38</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">40</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">42</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">44</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">46</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">48</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f8fafc;">50</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cbd5e1;">T/L</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $item)
        @php
            $wips = !empty($item['matrix']['wips']) ? $item['matrix']['wips'] : [[
                'label' => 'WIP', 'unit' => '-', 'c_no' => '-', 'remarks' => '-',
                'fs' => [], 'hs' => [], 'fs_tl' => 0, 'hs_tl' => 0, 'gross_total' => 0
            ]];
            $min = $item['matrix']['min'];
            $fg = $item['matrix']['fg'];
            $total = $item['matrix']['total'];
            $totalRowspan = 2 + count($wips) + 1;
            $sizes = [36, 38, 40, 42, 44, 46, 48, 50];
        @endphp
        <tr>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">{{ $index + 1 }}</td>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: left; vertical-align: middle; font-weight: bold;">{{ $item['art_no'] }}</td>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $item['order_fabric'] }}</td>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $item['fabric_stock'] }}</td>
            @if(!empty($isDhotiBrand))
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $item['dhoti_stock'] }}</td>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $item['dhoti_reorder'] }}</td>
            <td rowspan="{{ $totalRowspan }}" style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $item['dhoti_pending'] }}</td>
            @endif
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f8fafc;">MIN</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($min['fs'][$sz]) ? $min['fs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($min['fs_tl']) ? $min['fs_tl'] : '' }}</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($min['hs'][$sz]) ? $min['hs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($min['hs_tl']) ? $min['hs_tl'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($min['gross_total']) ? $min['gross_total'] : '' }}</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f8fafc;">FG</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($fg['fs'][$sz]) ? $fg['fs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($fg['fs_tl']) ? $fg['fs_tl'] : '' }}</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($fg['hs'][$sz]) ? $fg['hs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($fg['hs_tl']) ? $fg['hs_tl'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($fg['gross_total']) ? $fg['gross_total'] : '' }}</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
        @foreach($wips as $wip)
        <tr>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f8fafc;">{{ $wip['label'] }}</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($wip['fs'][$sz]) ? $wip['fs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($wip['fs_tl']) ? $wip['fs_tl'] : '' }}</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center;">{{ !empty($wip['hs'][$sz]) ? $wip['hs'][$sz] : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f1f5f9;">{{ !empty($wip['hs_tl']) ? $wip['hs_tl'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($wip['gross_total']) ? $wip['gross_total'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $wip['unit'] ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $wip['c_no'] ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: left;">{{ $wip['remarks'] ?? '-' }}</td>
        </tr>
        @endforeach
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">TOTAL</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ !empty($total['fs'][$sz]) ? $total['fs'][$sz] : 0 }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($total['fs_tl']) ? $total['fs_tl'] : 0 }}</td>
            @foreach($sizes as $sz)
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ !empty($total['hs'][$sz]) ? $total['hs'][$sz] : 0 }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($total['hs_tl']) ? $total['hs_tl'] : 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #cbd5e1;">{{ !empty($total['gross_total']) ? $total['gross_total'] : 0 }}</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
    @endforeach
    </tbody>
</table>
