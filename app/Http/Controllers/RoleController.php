<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use App\Models\User;


class RoleController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view roles')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $roles = Role::orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;

            foreach ($roles as $role) {
                $checked = $role->status === 'Active' ? 'checked' : '';

                $statusSwitch = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input role-status-toggle"
                        data-id="' . $role->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $role->id . '"></div>';

                $actionBtn = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit roles')) {
                    $actionBtn .= '<a href="' . url('roles/add/' . $role->id) . '" class="btn btn-edit">
                                        <i class="icon-base ri ri-edit-box-line"></i>
                                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete roles')) {
                    $actionBtn .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('roles/delete/' . $role->id) . '\')">
                                        <i class="icon-base ri ri-delete-bin-line"></i>
                                    </a>';
                }

                $actionBtn .= '</div>'; 
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name'   => $role->name,
                    'created_at'  => $role->created_at ? $role->created_at->format('d M Y, h:i A') : '-',
                    'status' => $statusSwitch,
                    'action' => $actionBtn
                ];
            }

            return response()->json([
                "data" => $data
            ]);
        }

        return view('roles.view');
    }


    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit roles')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create roles')) {
                return unauthorizedRedirect();
            }
        }
        $role = $id ? Role::with('permissions')->findOrFail($id) : new Role();
        $oldData = $id ? $role->toArray() : null;

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => [
                    'required',
                    'min:3',
                    'max:150',
                    Rule::unique('roles', 'name')->ignore($id)->whereNull('deleted_at'),
                ],
            ], [
                'name.required' => 'This field is required.',
                'name.unique'   => 'This role already exists.',
            ]);

            $role->name = $request->name;
            $role->guard_name = 'web';
            $role->save();

            $role->syncPermissions($request->permissions ?? []);
            $newData = Role::find($role->id)->toArray();

            if ($id) {
                $role->updated_by = auth()->id();
                addLog('update', 'Role', 'roles', $role->id, $oldData, $newData);
                $message = 'Role updated successfully!';
            } else {
                $role->created_by = auth()->id();
                addLog('create', 'Role', 'roles', $role->id, null, $newData);
                $message = 'Role added successfully!';
            }
            return redirect('/roles')->with('success', 'Role saved successfully!');
        }

        $rolePermissions = $id ? $role->permissions->pluck('name')->toArray() : [];

        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode(' ', $perm->name, 2)[1] ?? $perm->name;
        });

        return view('roles.add', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updateStatus(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $oldData = $role->toArray();
        $role->status = $request->status;
        $role->save();
        $newData = $role->toArray();
        addLog('update_status','Role Status','roles', $role->id, $oldData,$newData);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete roles')) {
            return unauthorizedRedirect();
        }
        $role = Role::findOrFail($id);
        if (User::where('role_id', $id)->exists()) {
            return redirect('roles')->with('danger', 'Cannot delete role. It is assigned to employees.');
        }
        $oldData = $role->toArray();
        $role->delete();
        addLog('delete', 'Role', 'roles', $id, $oldData, null);
        return redirect('roles')->with('success', 'Role deleted successfully');
    }
}
