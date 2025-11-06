<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterCountry;
use App\Models\MasterDistrict;
use App\Models\MasterIndustry;
use App\Models\MasterLink;
use App\Models\MasterState;
use Illuminate\Http\Request;

class MasterApiController extends Controller
{
    public function getMasterLinks()
    {
        $links = MasterLink::select('id', 'name', 'icon_class', 'title')->where('status', 1)->get();
        return response()->json($links);
    }

    public function getCountry()
    {
        $cty = MasterCountry::select('id', 'ctry_name')->get();
        return response()->json($cty);
    }

    public function getState(Request $r)
    {
        $state = MasterState::select('id', 'state_name')->where('c_id', $r->country_id)->get();
        return response()->json($state);
    }

    public function getDistrict(Request $r)
    {
        $district = MasterDistrict::select('id', 'district_name')->where('s_id', $r->state_id)->get();
        return response()->json($district);
    }
    public function getIndustryTypeList()
    {
        $indus = MasterIndustry::select('id', 'title', 'icons')->where('status', 1)->get();
        return response()->json($indus);
    }
}
