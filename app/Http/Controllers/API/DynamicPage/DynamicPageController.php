<?php

namespace App\Http\Controllers\API\DynamicPage;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    use ApiResponse;

    public function terms()
    {
        $data = DynamicPage::where('status', 'Active')->where('page_slug', 'terms-conditions')->first();
        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Data not found", 500);
    }

    public function privacy()
    {
        $data = DynamicPage::where('status', 'Active')->where('page_slug', 'terms-conditions')->first();

        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }

        return $this->error("Data not found", 500);
    }

}
