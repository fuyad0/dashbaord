<?php

namespace App\Http\Controllers\API\Review;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function index(){
        $reviews = Review::with(['users:id,first_name,last_name'])->where('status', 'Active') ->take(5) // limit to 5 reviews
        ->get();
        if($reviews->count() > 0){
            return $this->ok('Reviews founds', $reviews);
        }else{
            return $this->success('No Data found', [], 200);
        }
        
    }

    public function store(Request $request){
        $validated = $request->validate([
            'users_id'=> 'required',
            'ratings'=> 'required',
            'comment'=> 'required',
            'company'=> 'nullable'
        ]);

       $review = Review::create($validated);
        return $this->success('Review successfully stored', $review);
    }

    public function update(Request $request, $id){
       $validated = $request->validate([
            'users_id'=> 'required',
            'ratings'=> 'required',
            'comment'=> 'required',
            'company'=> 'nullable'
        ]);

        $review= Review::findOrFail($id);
        $this->authorize('update', $review);
        $review->update($validated);
        return $this->success('Review updated successfully!', $review);
    }

    public function destroy($id){
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);
        $review->delete();
        return $this->success('Review deleted successfully', $review);
    }


}
