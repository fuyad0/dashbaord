<?php

namespace App\Http\Controllers\Web\Plan;

use App\Models\Plan;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of all users.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse | View {
        if ($request->ajax()) {
            $data = Plan::latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('price', function ($data) {
                    $price = $data->price ?? 0;

                    return '$' . number_format($price, 2);
                })
                ->addColumn('duration', function ($data) {
                    $duration = $data->duration ?? 0;

                    return $duration . ' days';
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor  = $data->status == "Active" ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == "Active" ? '26px' : '2px';
                    $sliderStyles     = "position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX($sliderTranslateX);";

                    $status = '<div class="form-check form-switch" style="margin-left:40px; position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="' . $sliderStyles . '"></span>';
                    $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('plan.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status','action'])
                ->make();
        }
        return view('backend.layouts.plan.index');
    }

    public function edit(int $id): View {
        $data = Plan::with('planOptions')->findOrFail($id);

        return view('backend.layouts.plan.edit', compact('data'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $data = Plan::findOrFail($id);

            // Validate input
            $validator = Validator::make($request->all(), [
                'title'              => 'nullable|string|max:100',
                'description'        => 'nullable|string|max:100',
                'price'              => 'nullable|numeric|min:0',
                'type'               => 'nullable|string|max:50',
                'duration'           => 'nullable|integer|min:1',
                'stripe_product_id'  => 'required|string|unique:plans,stripe_product_id,' . $id,
                'stripe_price_id'    => 'required|string|unique:plans,stripe_price_id,' . $id,
                'interval'           => 'required|in:month,year',

                // Options validation (optional)
                'options'            => 'nullable|array',
                'options.*.name'     => 'required_with:options|string|max:255',
                'options.*.type'     => 'required_with:options|in:Yes,No',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Update main plan info
            $data->update([
                'title'             => $request->title,
                'description'       => $request->description,
                'price'             => $request->price,
                'type'              => $request->type,
                'duration'          => $request->duration,
                'stripe_product_id' => $request->stripe_product_id,
                'stripe_price_id'   => $request->stripe_price_id,
                'interval'          => $request->interval,
                'status'            => 'Active',
            ]);

            // --- Update Package Options ---
            if ($request->filled('options')) {
                // Delete old options
                $data->planOptions()->delete();

                // Recreate all options
                foreach ($request->options as $option) {
                    if (!empty($option['name'])) {
                        $data->planOptions()->create([
                            'name' => $option['name'],
                            'type' => $option['type'] ?? 'Yes',
                        ]);
                    }
                }
            }

            return redirect()->route('plan.index')->with('t-success', 'Package updated successfully.');

        } catch (\Exception $exception) {
            return redirect()->route('plan.index')->with('t-error', 'Failed to update plan: ' . $exception->getMessage());
        }
    }

    /**
     * Change the status of the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse {
        $data = Plan::findOrFail($id);
        if ($data->status == 'Active') {
            $data->status = 'Inactive';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'Active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Plan::findOrFail($id);
        $user->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
