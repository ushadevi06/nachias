@extends('layouts.common')
@section('title', 'View Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-4">
                <h4>View Job Card Entry</h4>
                <a href="{{ url('job_card_entries') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="text-center py-3 border-bottom">
                        <h3 class="mb-0 fw-bold">CASINO FORMAL</h3>
                    </div>

                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            {{-- Row 1 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1" style="width: 10%;">CUTTING NO</td>
                                <td class="py-1" style="width: 10%;">CF0156/23</td>
                                <td class="fw-bold bg-light py-1" style="width: 6%;">FIT</td>
                                <td class="py-1 text-center" style="width: 32%;">TAILOR FIT</td>
                                <td class="fw-bold bg-light py-1" style="width: 6%;">CUFF</td>
                                <td class="py-1 text-center" style="width: 10%;">CROSS</td>
                                <td class="fw-bold bg-light py-1" style="width: 12%;">CUTTING MASTER</td>
                                <td class="py-1" style="width: 10%;"></td>
                                <td class="fw-bold bg-light py-1" style="width: 10%;">MARK CHECKER</td>
                                <td class="py-1" style="width: 14%;"></td>
                            </tr>

                            {{-- Row 2 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">F.ISSUE DATE</td>
                                <td class="py-1">16/12/2023</td>
                                <td class="fw-bold bg-light py-1">N.PATTI</td>
                                <td class="py-1 text-center">28 MM AMERICAN PATTI</td>
                                <td class="fw-bold bg-light py-1">POCKET</td>
                                <td class="py-1 text-center">CROSS</td>
                                <td class="fw-bold bg-light py-1">CUTTING DATE</td>
                                <td class="py-1"></td>
                                <td colspan="2" class="bg-light"></td>
                            </tr>

                            {{-- Row 3 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">DELIVERY DATE</td>
                                <td class="py-1">01/01/2024</td>
                                <td class="fw-bold bg-light py-1">COLLAR</td>
                                <td class="py-1 text-center">REGULAR DOUBLE CANVAS</td>
                                <td class="fw-bold bg-light py-1">BOT.CUT</td>
                                <td class="py-1 text-center">AERO CUT</td>
                                <td class="fw-bold bg-light py-1">CUTTING ISSUE UNIT</td>
                                <td class="py-1"></td>
                                <td class="fw-bold bg-light py-1">H.O.D.C NO</td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 4 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">WASHING</td>
                                <td class="py-1">NO</td>
                                <td colspan="6" class="bg-light text-center fw-bold py-1" style="font-size: 0.75rem; border-bottom: 2px solid #dee2e6;">CUTTING SIZE RATIO</td>
                                <td class="fw-bold bg-light py-1">H.O.D.C DATE</td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 5 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">WIDTH</td>
                                <td class="py-1">58</td>
                                <td class="fw-bold bg-light py-1">SIZE</td>
                                <td colspan="4" class="p-0">
                                    <table class="table table-bordered mb-0" style="border: none;">
                                        <tr class="text-center bg-light fw-bold" style="font-size: 0.8rem;">
                                            <td class="py-1" style="width: 25%; border-top: none; border-left: none;">36</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">38</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">40</td>
                                            <td class="py-1" style="width: 25%; border-top: none; border-right: none;">42</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0" colspan="2">
                                    <div class="bg-light fw-bold text-center py-1" style="font-size: 0.75rem; border-bottom: 1px solid #dee2e6;">CUTTING MARK AND LAY</div>
                                    <div class="row g-0">
                                        <div class="col-6 bg-light fw-bold text-center py-1 border-end" style="font-size: 0.7rem;">SIZE</div>
                                        <div class="col-6 bg-light fw-bold text-center py-1" style="font-size: 0.7rem;">MARK</div>
                                    </div>
                                </td>
                                <td class="fw-bold bg-light py-1">UNIT D.C NO</td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 6 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">MRP</td>
                                <td class="py-1"></td>
                                <td class="fw-bold bg-light py-1">QTY - F/S</td>
                                <td colspan="4" class="p-0">
                                    <table class="table table-bordered mb-0" style="border: none;">
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            <td class="py-1" style="width: 25%; border-top: none; border-left: none;">8</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">8</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">8</td>
                                            <td class="py-1" style="width: 25%; border-top: none; border-right: none;">-</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 7 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">F/S</td>
                                <td class="py-1"></td>
                                <td class="fw-bold bg-light py-1">QTY - F/S</td>
                                <td colspan="4" class="p-0">
                                    <table class="table table-bordered mb-0" style="border: none;">
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            <td class="py-1" style="width: 25%; border-top: none; border-left: none;">-</td>
                                            <td colspan="3" class="py-1" style="border-top: none; border-right: none;">(38,40,42) காலிபண்ணவும்</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 8 --}}
                            <tr>
                                <td class="fw-bold bg-light py-1">H/S</td>
                                <td class="py-1"></td>
                                <td class="fw-bold bg-light py-1">QTY - H/S</td>
                                <td colspan="4" class="p-0">
                                    <table class="table table-bordered mb-0" style="border: none;">
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            <td class="py-1" style="width: 25%; border-top: none; border-left: none;">-</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">8</td>
                                            <td class="py-1" style="width: 25%; border-top: none;">8</td>
                                            <td class="py-1" style="width: 25%; border-top: none; border-right: none;">8</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                                <td class="py-1"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row g-0 border-bottom">
                        <div class="col text-center p-3 border-end">
                            <div class="border rounded p-4 bg-light" style="min-height: 100px;">
                                <small class="text-muted">Fabric Swatch 1</small>
                            </div>
                        </div>
                        <div class="col text-center p-3 border-end">
                            <div class="border rounded p-4 bg-light" style="min-height: 100px;">
                                <small class="text-muted">Fabric Swatch 2</small>
                            </div>
                        </div>
                        <div class="col text-center p-3 border-end">
                            <div class="border rounded p-4 bg-light" style="min-height: 100px;">
                                <small class="text-muted">Fabric Swatch 3</small>
                            </div>
                        </div>
                        <div class="col text-center p-3 border-end">
                            <div class="border rounded p-4 bg-light" style="min-height: 100px;">
                                <small class="text-muted">Fabric Swatch 4</small>
                            </div>
                        </div>
                        <div class="col text-center p-3">
                            <div class="border rounded p-4 bg-light" style="min-height: 100px;">
                                <small class="text-muted">Fabric Swatch 5</small>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="text-center bg-light">
                                <th>ART NO</th>
                                <th>CF20077-1</th>
                                <th>ART NO</th>
                                <th>CF20077-2</th>
                                <th>ART NO</th>
                                <th>CF20078-1</th>
                                <th>ART NO</th>
                                <th>CF20078-2</th>
                                <th>ART NO</th>
                                <th>CF20079-1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="fw-bold">WIDTH</td>
                                <td>58"</td>
                                <td class="fw-bold">WIDTH</td>
                                <td>58"</td>
                                <td class="fw-bold">WIDTH</td>
                                <td>58"</td>
                                <td class="fw-bold">WIDTH</td>
                                <td>58"</td>
                                <td class="fw-bold">WIDTH</td>
                                <td>58"</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fw-bold">M/B.M</td>
                                <td>103.5</td>
                                <td class="fw-bold">M/B.M</td>
                                <td>91</td>
                                <td class="fw-bold">M/B.M</td>
                                <td>112</td>
                                <td class="fw-bold">M/B.M</td>
                                <td>109</td>
                                <td class="fw-bold">M/B.M</td>
                                <td>95</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fw-bold">I/O</td>
                                <td>YES</td>
                                <td class="fw-bold">I/O</td>
                                <td>YES</td>
                                <td class="fw-bold">I/O</td>
                                <td>YES</td>
                                <td class="fw-bold">I/O</td>
                                <td>YES</td>
                                <td class="fw-bold">I/O</td>
                                <td>YES</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="text-center bg-light">
                                <th rowspan="2">ART NO</th>
                                <th colspan="6">F/S</th>
                                <th colspan="4">H/S</th>
                                <th rowspan="2">TOTAL</th>
                            </tr>
                            <tr class="text-center bg-light">
                                <th>36</th>
                                <th>38</th>
                                <th>40</th>
                                <th>42</th>
                                <th>44</th>
                                <th>46</th>
                                <th>38</th>
                                <th>40</th>
                                <th>42</th>
                                <th>44</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>CF20077-1</td>
                                <td>10</td>
                                <td>15</td>
                                <td>20</td>
                                <td>15</td>
                                <td>10</td>
                                <td>5</td>
                                <td>12</td>
                                <td>18</td>
                                <td>12</td>
                                <td>8</td>
                                <td class="fw-bold">135</td>
                            </tr>
                            <tr class="text-center">
                                <td>CF20077-2</td>
                                <td>8</td>
                                <td>12</td>
                                <td>18</td>
                                <td>12</td>
                                <td>8</td>
                                <td>4</td>
                                <td>10</td>
                                <td>15</td>
                                <td>10</td>
                                <td>6</td>
                                <td class="fw-bold">111</td>
                            </tr>
                            <tr class="text-center">
                                <td>CF20078-1</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="fw-bold">-</td>
                            </tr>
                            <tr class="text-center">
                                <td>CF20078-2</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="fw-bold">-</td>
                            </tr>
                            <tr class="text-center">
                                <td>CF20079-1</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="fw-bold">-</td>
                            </tr>
                            <tr class="text-center">
                                <td>CF20079-2</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="fw-bold">-</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="text-center fw-bold bg-light">
                                <td>TOTAL</td>
                                <td>18</td>
                                <td>27</td>
                                <td>38</td>
                                <td>27</td>
                                <td>18</td>
                                <td>9</td>
                                <td>22</td>
                                <td>33</td>
                                <td>22</td>
                                <td>14</td>
                                <td>246</td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold bg-light" style="width: 20%;">DATE</td>
                                <td style="width: 30%;">FABRIC INCHARGE</td>
                                <td class="fw-bold bg-light" style="width: 20%;">DATE</td>
                                <td>AUTHORIZED SIGNATURE</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>FABRIC ISSUED BY</td>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>UNIT SUPERVISOR</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>UNIT INCHARGE</td>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>PRODUCTION UNIT SEND BY</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>CUTTING SUPERVISOR</td>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>READY SECTION</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>CUTTING SEND BY</td>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>READY STORE</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>CUTTING RECEIVED BY</td>
                                <td class="fw-bold bg-light">DATE</td>
                                <td>H.O RECEIVED BY</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td colspan="3">KAJA & BUTTON</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td colspan="3">TRIMMING & CHECKING</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td colspan="3">IRONING</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">DATE</td>
                                <td colspan="3">PACKING & DELIVERY</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
