<?php

namespace App\Http\Controllers\Web\Backend\Enquiry;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
           $data = Enquiry::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("status", function ($data) {
                    if ($data->status == 'Active') {
                        return "<span class='badge bg-warning'>Pending</span>";
                    }elseif ($data->status == 'Inactive') {
                        return "<span class='badge bg-danger'>Enquiry Withdrawed</span>";
                    }
                    elseif ($data->status == 'Answered') {
                        return "<span class='badge bg-success'>Answered</span>";
                    }else{
                        return "<span class='badge bg-secondary'>$data->status</span>";
                    }
                })
                ->addColumn('action', function ($data) {
                    $buttons = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                    // Edit button
                    $buttons .= '<a href="'.route('enquiry.edit', ['id' => $data->id]).'" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                    <i class="fe fe-edit"></i>
                    </a>';
                     $buttons .= '<a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                    <i class="fe fe-trash"></i>
                    </a>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action', 'status'])
                ->make();
        }
        return view('backend.layouts.enquiry.index');
    }

    public function edit($id)
    {
        $data = Enquiry::find($id);
        if (!$data) {
            return redirect()->route('enquiry.index')->with('t-error', 'No availability found for this store.');
        }

        return view('backend.layouts.enquiry.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'=> 'required|string',
            'email'=> 'required|email',
            'answer'=> 'required',
            'status'=> 'required',
            'description'=> 'required',
        ]);

        $data = Enquiry::find($request->id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->answer = $request->answer;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->save();

        return redirect()->route('enquiry.index')->with('t-success', 'Status updated to '.$request->status.' successfully!');
    }
    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        if ($enquiry) {
            $enquiry->delete();
        }
        return response()->json([
            't-success' => true,
            'message' => 'Data deleted successfully'
        ], 200);
    }
}
