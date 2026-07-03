@extends('layouts.common')
@section('title', ($fgMinStock ? 'Edit' : 'Add') . ' FG Minimum Stock - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $fgMinStock ? 'Edit' : 'Add' }} FG Minimum Stock</h4>
                    </div>
                    <form action="{{ url('fg-min-stocks/add' . ($fgMinStock ? '/' . $fgMinStock->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-12 col-xl-12">
                                @php
                                    $fgSearchVal = '';
                                    $artNoVal = '';
                                    $sizeVal = '';
                                    $sleeveVal = '';
                                    
                                    $currentStockItemId = old('stock_entry_item_id', $fgMinStock->stock_entry_item_id ?? null);
                                    
                                    if($currentStockItemId) {
                                        $item = \App\Models\StockEntryItem::with('item')->find($currentStockItemId);
                                        if($item) {
                                            $itemName = $item->finished_item_code ?: ($item->item ? $item->item->name : 'Unknown Item');
                                            $fgSearchVal = $itemName;
                                            $artNoVal = $item->art_no;
                                            $sizeVal = $item->size;
                                            $sleeveVal = $item->sleeve_type;
                                        }
                                    }
                                @endphp
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('stock_entry_item_id') is-invalid @enderror" id="fg_search" placeholder="Search Finished Good (Item, Art No, Size, Sleeve)" value="{{ old('fg_search', $fgSearchVal) }}" autocomplete="off">
                                    <label for="fg_search">Search Finished Good <span class="text-danger">*</span></label>
                                </div>
                                <input type="hidden" name="stock_entry_item_id" id="stock_entry_item_id" value="{{ old('stock_entry_item_id', $fgMinStock->stock_entry_item_id ?? '') }}">
                                @error('stock_entry_item_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 col-xl-12">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control bg-light" id="disp_art_no" placeholder="Art No" value="{{ $artNoVal }}" readonly tabindex="-1">
                                            <label for="disp_art_no">Art No</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control bg-light" id="disp_size" placeholder="Size" value="{{ $sizeVal }}" readonly tabindex="-1">
                                            <label for="disp_size">Size</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control bg-light" id="disp_sleeve" placeholder="Sleeve" value="{{ $sleeveVal }}" readonly tabindex="-1">
                                            <label for="disp_sleeve">Sleeve Type</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" placeholder="Enter Minimum Stock" name="min_stock" value="{{ old('min_stock', $fgMinStock->min_stock ?? '0.00') }}">
                                    <label for="min_stock">Minimum Stock <span class="text-danger">*</span></label>
                                </div>
                                @error('min_stock')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $fgMinStock->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $fgMinStock->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('fg-min-stocks') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#fg_search').autocomplete({
            source: function(request, response) {
                $.getJSON("{{ url('fg-min-stocks/search') }}", {
                    term: request.term
                }, function(data) {
                    if (data.length === 0) {
                        response([{
                            label: 'No Finished Good found',
                            value: '',
                            noResult: true
                        }]);
                        return;
                    }
                    response(data);
                });
            },
            minLength: 1,
            select: function(event, ui) {
                if (ui.item.noResult) {
                    event.preventDefault();
                    return false;
                }
                
                $('#fg_search').val(ui.item.item_name);
                $('#stock_entry_item_id').val(ui.item.id);
                $('#disp_art_no').val(ui.item.art_no);
                $('#disp_size').val(ui.item.size);
                $('#disp_sleeve').val(ui.item.sleeve_type === '-' ? '' : ui.item.sleeve_type);
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            if (item.noResult) {
                return $("<li>")
                    .append(`<div class="ui-menu-item-wrapper text-danger fw-bold">${item.label}</div>`)
                    .appendTo(ul);
            }

            return $("<li>")
                .append(`<div class="ui-menu-item-wrapper">
                    <span class="fw-bold">${item.item_name}</span><br>
                    <small class="text-muted">Art No: ${item.art_no} | Size: ${item.size} ${item.sleeve_type !== '-' ? '| Sleeve: '+item.sleeve_type : ''}</small>
                </div>`)
                .appendTo(ul);
        };

        $('#fg_search').on('input', function() {
            if (!$(this).val()) {
                $('#stock_entry_item_id').val('');
                $('#disp_art_no').val('');
                $('#disp_size').val('');
                $('#disp_sleeve').val('');
            }
        });
    });
</script>
<style>
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999;
    }
    .ui-menu-item-wrapper {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }
    .ui-menu-item-wrapper:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
