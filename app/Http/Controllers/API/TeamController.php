<?php

namespace App\Http\Controllers\API;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    //
    public function fetch(Request $request)
    {
        try {
            $id = $request->input('id');
            $name = $request->input('name');
            $limit = $request->input('limit', 10);

            $teamQuery = Team::query();

            if ($id) {
                $team = $teamQuery->find($id);

                if ($team) {
                    return ResponseFormatter::success($team, 'Data tim berhasil diambil');
                }

                return ResponseFormatter::error(null, 'Data tim tidak ada', 404);
            }

            $teams = $teamQuery->where('company_id', $request->company_id);

            if ($name) {
                $teams->where('name', 'like', '%' . $name . '%');
            }

            return ResponseFormatter::success($teams->paginate($limit), 'Data list tim berhasil diambil');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Gagal mengambil tim', 500);
        }
    }

    public function create(CreateTeamRequest $request)
    {
        try {
            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('assets/teams', 'public');
            }

            // Create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new \Exception('Data perusahaan gagal ditambahkan');
            }

            return ResponseFormatter::success(
                $team,
                'Data tim berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage(),
                'Data tim gagal ditambahkan',
                500
            );
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            $team = Team::find($id);

            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('assets/icons', 'public');
            }

            $team->update([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $team,
                'Data tim berhasil diperbarui'
            );
        } catch (\Throwable $th) {
            //throw $th;
            return ResponseFormatter::error(
                $th->getMessage(),
                'Data tim gagal diperbarui',
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $team = Team::find($id);

            // Check if teams are owned by the user's company

            // Check if team exists
            if (!$team) {
                return ResponseFormatter::error(
                    null,
                    'Data tim tidak ada',
                    404
                );
            }

            $team->delete();

            return ResponseFormatter::success(
                null,
                'Data tim berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage(),
                'Data tim gagal dihapus',
                500
            );
        }
    }
}
