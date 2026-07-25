<?php

namespace App\Http\Controllers;

use App\Models\SizeRatio;
use App\Models\JobCardEntry;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SizeRatioController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view size-ratio')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $sizeRatios = SizeRatio::orderBy('id','desc')->get();
            $data = [];
            $count = 1;

            foreach ($sizeRatios as $sizeRatio) {

                $checked = $sizeRatio->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input size-ratio-status-toggle"
                        data-id="' . $sizeRatio->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $sizeRatio->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit size-ratio')) {
                    $action .= '<a href="' . url('size_ratio/add/' . $sizeRatio->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete size-ratio')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('size_ratio/delete/' . $sizeRatio->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'size'   => $sizeRatio->size,
                    'ratio'  => $sizeRatio->ratio,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('size_ratio.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit size-ratio')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create size-ratio')) {
                return unauthorizedRedirect();
            }
        }
        $sizeRatio = null;
        $oldData   = null;
        if ($id) {
            $sizeRatio = SizeRatio::findOrFail($id);
            $oldData = $sizeRatio->toArray();
        }
        if (request()->isMethod('post')) {
            $request = request();
            $rules = [
                'size' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[0-9,\s.]+$/'
                ],
                'ratio'  => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[0-9,\s.]+$/'
                ],
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                'size.regex' => 'Size can only contain numbers, commas, and dots.',
                'ratio.regex' => 'Ratio can only contain numbers, commas, and dots.',
            ];
            $validator = validator($request->all(), $rules, $messages);
            $validator->after(function ($validator) use ($request) {
                if (!SizeRatio::validateSizeRatio($request->size, $request->ratio)) {
                    $validator->errors()->add(
                        'ratio',
                        'The number of sizes and ratios must be equal.'
                    );
                }
            });
            $validator->validate();
            $data = [
                'size'   => $request->size,
                'ratio'  => $request->ratio,
                'status' => $request->status
            ];
            if ($id) {
                $data['updated_by'] = auth()->id();
                SizeRatio::where('id', $id)->update($data);
                $newData = SizeRatio::find($id)->toArray();
                addLog('update', 'Size Ratio', 'size_ratios', $id, $oldData, $newData);
                $message = 'Size/Ratio updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $sizeRatio = SizeRatio::create($data);
                $newData = $sizeRatio->toArray();
                addLog('create', 'Size Ratio', 'size_ratios', $sizeRatio->id, null, $newData);
                $message = 'Size/Ratio added successfully';
            }
            return redirect('size_ratio')->with('success', $message);
        }

        return view('size_ratio.add', compact('sizeRatio', 'id'));
    }


    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete size-ratio')) {
            return unauthorizedRedirect();
        }
        $sizeRatio = SizeRatio::findOrFail($id);
        $references = [
            [JobCardEntry::class, 'size_ratio_id', 'Job Card Entries'],
            [Item::class, 'size_ratio_id', 'Items'],
        ];
        foreach ($references as [$model, $column, $label]) {
            if ($model::where($column, $id)->exists()) {
                session()->flash('danger', "This size ratio is currently referenced in {$label} and cannot be deleted.");
                return redirect('size_ratio');
            }
        }
        $oldData = $sizeRatio->toArray();
        $sizeRatio->delete();
        addLog('delete', 'Size Ratio', 'size_ratios', $id, $oldData, null);
        return redirect('size_ratio')->with('success', 'Size/Ratio deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $sizeRatio = SizeRatio::findOrFail($id);
        $oldData = $sizeRatio->toArray();
        $sizeRatio->status = $request->status;
        $sizeRatio->save();
        $newData = $sizeRatio->toArray();
        addLog('update_status', 'Size Ratio Status', 'size_ratios', $id, $oldData, $newData);
        return response()->json(['success' => true, 'status' => $sizeRatio->status]);
    }
}
