<?php

namespace App\Http\Controllers\Web\Backend\Dynamic;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\DynamicPage as ModelsDynamicPage;

class DynamicPage extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = ModelsDynamicPage::latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <a href="' . route('page.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                        <i class="fe fe-edit"></i>
                                    </a>
                                    <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                        <i class="fe fe-trash"></i>
                                    </a>
                                </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.layouts.dynamic.index');
    }


    public function create()
    {
        return view('backend.layouts.dynamic.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_title' => 'required|string',
            'page_content' => 'required|string',
            'page_slug' => 'required|string',
            'status' => 'required|in:Active,Inactive',

        ]);

        try {
            ModelsDynamicPage::create($validated);
            return redirect()->route('page.index')->with('t-success', 'Page created successfully');
        } catch (\Exception $exception) {
            return redirect()->route('page.index')->with('t-error', 'Page creation failed: ' . $exception->getMessage());
        }
    }

    public function edit($id)
    {
        $data= ModelsDynamicPage::findOrFail($id);
        return view('backend.layouts.dynamic.edit', compact('data'));
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'page_title' => 'required|string',
            'page_content' => 'required|string',
            'page_slug' => 'required|string',
            'status' => 'required|in:Active,Inactive',

        ]);
        try {
            ModelsDynamicPage::findOrFail($id)->update($validated);
            return redirect()->route('page.index')->with('t-success','Page Updated Successfully');
        } catch (\Exception $exception) {
            return redirect()->route('page.index')->with('t-error', 'Failed');
        }
    }

    public function destroy($id){
        ModelsDynamicPage::findOrFail($id)->delete();
        return redirect()->route('page.index')->with('t-succes','Page Deleted Successfully!');
    }
}
