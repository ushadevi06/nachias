<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DocumentRepository;
use Carbon\Carbon;

class DocumentRepositoryController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view document-repository')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $query = DocumentRepository::with('department')->orderBy('id', 'desc');

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('document_name', 'like', "%{$search}%")
                        ->orWhere('document_type', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($q2) use ($search) {
                            $q2->where('department', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $documents = $query->get();
            $data = [];
            $i = $start + 1;
            foreach ($documents as $row) {
                $file = '-';
                if ($row->file) {
                    $ext = strtolower(pathinfo($row->file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $imgUrl = url('uploads/documents/' . $row->file);
                    } else {
                        $img = ($ext == 'pdf') ? 'pdf_image.jpg' : 'word_image.png';
                        $imgUrl = url('assets/images/' . $img);
                    }
                    $file = '<a href="' . url('uploads/documents/' . $row->file) . '" target="_blank"><img src="' . $imgUrl . '" alt="file" class="table-img"></a>';
                }

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view document-repository')) {
                    $action .= '<a href="' . url('document_repository/view/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit document-repository')) {
                    $action .= '<a href="' . url('document_repository/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete document-repository')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete delete-btn" onclick="delete_data(\'' . url('document_repository/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }
                $action .= '</div>';

                $status = '<span class="badge bg-success">Active</span>';
                if ($row->validity_date) {
                    if (Carbon::parse($row->validity_date)->isPast() && !Carbon::parse($row->validity_date)->isToday()) {
                        $status = '<span class="badge bg-danger">Expired</span>';
                    }
                }

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'document_name' => $row->document_name,
                    'document_type' => $row->document_type,
                    'department' => $row->department->department ?? '-',
                    'validity_date' => $row->validity_date ? date('d-m-Y', strtotime($row->validity_date)) : '-',
                    'status' => $status,
                    'file' => $file,
                    'action' => $action,
                ];
            }
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        return view('document_repository/view');
    }
    public function add(Request $request,$id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit document-repository')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create document-repository')) {
                return unauthorizedRedirect();
            }
        }
        $document = $id ? DocumentRepository::findOrFail($id) : null;
        $departments = Department::active()->orderBy('id','desc')->get();
        if ($request->isMethod('post')) {
            $rules = [
                'document_name' => 'required|min:3|max:100',
                'document_type' => 'required|in:Certification,HR,Compliance,Policy',
                'department_id' => 'required|exists:departments,id',
                'validity_date' => 'nullable|date_format:d-m-Y',
                'remarks' => 'nullable|string',
                'file' => ($id ? 'nullable' : 'required') . '|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048',
            ];
            $messages = [
                '*.required' => 'This field is required', 
                '*.min'      => 'This field must be at least :min characters.',
                'file.max' => 'Uploaded file cannot exceed 2MB.',
                '*.max'      => 'This field should not be more than :max characters.',
                'file.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
            ];
            $request->validate($rules, $messages);
            $data = [
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'department_id' => $request->department_id,
                'validity_date' => $request->validity_date ? Carbon::createFromFormat('d-m-Y', $request->validity_date)->format('Y-m-d') : null,
                'remarks' => $request->remarks,
            ];
            if($request->file){
                if(!empty($document->file)){
                    $oldFilePath = public_path('uploads/documents/' . $document->file);
                    if(file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $uploadPath = public_path('uploads/documents/');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $filename);
                $data['file'] = $filename;
            }
            if($id) {
                $oldData = DocumentRepository::find($id)->toArray();
                $document_respository = DocumentRepository::findOrFail($id);
                $data['updated_by'] = auth()->id();
                $document->update($data); 
                $newData = $document->fresh()->toArray();
                addLog('update', 'Document Repository', 'document_repositories', $id, $oldData, $newData);
                $message = 'Document Repository updated successfully';
            }else{
                $data['created_by'] = auth()->id();
                $document_respository = DocumentRepository::create($data);
                $newData = $document_respository->toArray();
                addLog('create', 'Document Repository', 'document_repositories', $document_respository->id, null, $newData);
                $message = 'Document Repository added successfully';
            }
            return redirect('document_repository')->with('success', $message);
        }
        return view('document_repository/add',compact('document','departments'));
    }
    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view document-repository')) {
            return unauthorizedRedirect();
        }
        $document = DocumentRepository::with('department')->findOrFail($id);
        return view('document_repository/view_details', compact('document'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete document-repository')) {
            return unauthorizedRedirect();
        }
        $document = DocumentRepository::findOrFail($id);
        $oldData = $document->toArray();
        if(!empty($document->file)){
            $oldFilePath = public_path('uploads/documents/' . $document->file);
            if(file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }
        $document->delete();
        addLog('delete', 'Document Repository', 'document_repositories', $id, $oldData, null);
        return redirect('document_repository')->with('success', 'Document Repository deleted successfully');
    }
}
