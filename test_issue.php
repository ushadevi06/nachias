<?php
$req = new \Illuminate\Http\Request();
$req->merge(['ids' => ['28']]);
$ctrl = new \App\Http\Controllers\JobCardEntryController();
echo json_encode($ctrl->getStockEntryDetails($req));
