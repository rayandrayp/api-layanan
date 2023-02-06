<?php
namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\LaporanSPI;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class LaporanSPIController extends Controller
{
    private function validator(array $data)
    {
        return Validator::make($data, [
            'nama' => ['required', 'string', 'max:255'],
            'pangkat' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255'],
            'lingkup' => ['required', 'string', 'max:255'],
            'unit_dilaporkan' => ['required', 'string', 'max:255'],
            'personel_dilaporkan' => ['required', 'string', 'max:255'],
            'laporan' => ['required'],
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laporan = LaporanSPI::all();
        return ApiFormatter::createAPI(200, 'Success', $laporan);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return ApiFormatter::createAPI(400, 'Failed', 'Silahkan isi semua data yang diperlukan.');
        }
        $today = date('Y-m-d');
        $nrp = $request->nrp;
        $previous_laporan = LaporanSPI::where('nrp', $nrp)->where('created_at', 'like', $today . '%')->first();
        if ($previous_laporan) {
            return ApiFormatter::createAPI(400, 'Failed', 'Anda sudah mengirim laporan hari ini.');
        }
        try{
            $laporan = LaporanSPI::create($request->all());
        } catch (Exception $e) {
            return ApiFormatter::createAPI(400, 'Failed', 'Laporan gagal diunggah.');
        }
        return ApiFormatter::createAPI(200, 'Success', 'Laporan berhasil diunggah.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LaporanSPI  $laporanSPI
     * @return \Illuminate\Http\Response
     */
    public function show(LaporanSPI $laporanSPI)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LaporanSPI  $laporanSPI
     * @return \Illuminate\Http\Response
     */
    public function edit(LaporanSPI $laporanSPI)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LaporanSPI  $laporanSPI
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaporanSPI $laporanSPI)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LaporanSPI  $laporanSPI
     * @return \Illuminate\Http\Response
     */
    public function destroy(LaporanSPI $laporanSPI)
    {
        //
    }
}