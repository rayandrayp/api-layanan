<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = Kamar::all();

        $data = \App\Models\Bangsal::join('kamar', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->groupBy('kamar.kelas', 'bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->selectRaw('
                        bangsal.kd_bangsal, 
                        bangsal.nm_bangsal, 
                        COUNT(kamar.kd_bangsal) AS jml_kamar, 
                        kamar.kelas,
                        SUM(CASE WHEN kamar.`status` = "ISI" THEN 1 ELSE 0 END) kamar_isi,
                        SUM(CASE WHEN kamar.`status` = "KOSONG" THEN 1 ELSE 0 END) kamar_kosong
                    ')
            ->where('statusdata', '1')
            ->orderBy('bangsal.nm_bangsal')
            ->get();
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($kd_bangsal)
    {
        $data = \App\Models\Bangsal::join('kamar', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->groupBy('kamar.kelas', 'bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->selectRaw('
                        bangsal.kd_bangsal, 
                        bangsal.nm_bangsal, 
                        COUNT(kamar.kd_bangsal) AS jml_kamar, 
                        kamar.kelas,
                        SUM(CASE WHEN kamar.`status` = "ISI" THEN 1 ELSE 0 END) kamar_isi,
                        SUM(CASE WHEN kamar.`status` = "KOSONG" THEN 1 ELSE 0 END) kamar_kosong
                    ')
            ->where('statusdata', '1')
            ->where('bangsal.kd_bangsal', $kd_bangsal)
            ->orderBy('bangsal.nm_bangsal')
            ->get();
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }

    public function kelas($kelas = null)
    {
        if ($kelas) {
            $data = \App\Models\Bangsal::join('kamar', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->groupBy('kamar.kelas', 'bangsal.kd_bangsal', 'bangsal.nm_bangsal','kamar.trf_kamar')
                ->selectRaw('
                        bangsal.kd_bangsal, 
                        bangsal.nm_bangsal, 
                        COUNT(kamar.kd_bangsal) AS jml_kamar, 
                        kamar.kelas,
                        kamar.trf_kamar,
                        SUM(CASE WHEN kamar.`status` = "ISI" THEN 1 ELSE 0 END) kamar_isi,
                        SUM(CASE WHEN kamar.`status` = "KOSONG" THEN 1 ELSE 0 END) kamar_kosong
                    ')
                ->where('statusdata', '1')
                ->where('kamar.kelas', $kelas)
                ->orderBy('bangsal.nm_bangsal')
                ->get();
        } else {
            $data = \App\Models\Kamar::where('statusdata', '1')
                ->groupBy('kelas')
                ->selectRaw('kelas, COUNT(kelas) AS jml_kamar, 
                            SUM(CASE WHEN `status` = \'ISI\' THEN 1 ELSE 0 END) kamar_isi,
                            SUM(CASE WHEN `status` = \'KOSONG\' THEN 1 ELSE 0 END) kamar_kosong')
                ->get();
        }
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //
    // }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }
}