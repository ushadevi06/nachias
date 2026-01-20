<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ServiceProvider;
use App\Models\ProductionService;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view resources')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $resources = Resource::with(['serviceProvider'])->latest()->get();

            $data = [];
            $i = 1;

            foreach ($resources as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input resource-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit resources')) {
                    $action .= '<a href="' . url('resources/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete resources')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('resources/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'        => $i++,
                    'resource_code'      => $row->resource_code,
                    'resource_name'      => $row->resource_name,
                    'service_provider'   => $row->serviceProvider->name ?? '-',
                    'status'             => $status,
                    'action'             => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('resources.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit resources')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create resources')) {
                return unauthorizedRedirect();
            }
        }

        $resource = $id ? Resource::findOrFail($id) : null;
        
        // Get Service Providers where is_plant = 1
        $serviceProviders = ServiceProvider::where('is_plant', 1)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        if ($request->isMethod('post')) {
            $rules = [
                'resource_code'      => 'required|string|max:100|unique:resources,resource_code,' . $id . ',id,deleted_at,NULL',
                'resource_name'      => 'required|string|max:255',
                'service_provider_id' => 'required|exists:service_providers,id',
                'status'             => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['resource_code', 'resource_name', 'service_provider_id', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Resource::where('id', $id)->update($data);
                addLog('update', 'Resource', 'resources', $id, null, $data);
                $msg = 'Resource updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newResource = Resource::create($data);
                addLog('create', 'Resource', 'resources', $newResource->id, null, $data);
                $msg = 'Resource added successfully';
            }

            return redirect('resources')->with('success', $msg);
        }

        return view('resources.add', compact('resource', 'serviceProviders'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete resources')) {
            return unauthorizedRedirect();
        }

        $resource = Resource::findOrFail($id);
        $oldData = $resource->toArray();
        $resource->delete();
        addLog('delete', 'Resource', 'resources', $id, $oldData, null);
        return redirect('resources')->with('success', 'Resource deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        $oldData = $resource->toArray();
        $resource->status = $request->status;
        $resource->save();
        $newData = $resource->toArray();
        addLog('update_status', 'Resource Status', 'resources', $resource->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $resource->status
        ]);
    }
}
