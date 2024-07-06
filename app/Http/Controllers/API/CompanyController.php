<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    //
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        //powerhuman.com/api/company?id=1
        if ($id) {
            $company = Company::with(['users'])->find($id);

            if ($company) {
                return ResponseFormatter::success(
                    $company,
                    'Data perusahaan berhasil diambil'
                );
            }

            return ResponseFormatter::error(
                null,
                'Data perusahaan tidak ada',
                404
            );
        }

        //powerhuman.com/api/company
        $companies = Company::with(['users']);

        // powerhuman.com/api/company?name=PT%20Power%20Human
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Data list perusahaan berhasil diambil'
        );
    }
}
