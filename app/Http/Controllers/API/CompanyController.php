<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    //
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        ///api/company?id=1
        if ($id) {
            $company = Company::with(['users'])->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })->find($id);

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

        ///api/company
        $companies = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::user()->id);
        });

        ///api/company?name=PT%20Power%20Human
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Data list perusahaan berhasil diambil'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('assets/company', 'public');
            }

            // Create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if (!$company) {
                throw new \Exception('Data perusahaan gagal ditambahkan');
            }

            // Attach company to user
            $user = User::find(Auth::user()->id);
            $user->companies()->attach($company->id);


            return ResponseFormatter::success(
                $company,
                'Data perusahaan berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage(),
                'Data perusahaan gagal ditambahkan',
                500
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Company::whereHas('users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($id);

            if (!$company) {
                throw new \Exception('Data perusahaan tidak ditemukan');
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('assets/company', 'public');
            }

            $company->update([
                'name' => $request->name,
                'logo' => $path,
            ]);

            return ResponseFormatter::success(
                $company,
                'Data perusahaan berhasil diperbarui'
            );
        } catch (\Throwable $th) {
            //throw $th;
            return ResponseFormatter::error(
                $th->getMessage(),
                'Data perusahaan gagal diperbarui',
                500
            );
        }
    }
}
