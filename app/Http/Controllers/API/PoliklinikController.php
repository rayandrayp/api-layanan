<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Poliklinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoliklinikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Poliklinik::all();
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
    public function show($id)
    {
        $data = Poliklinik::where('kd_poli', '=', $id)->get();
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed retrieving data.');
        }
    }

    public function jadwal($kd_poli = null)
    {
        if ($kd_poli) {
            $data = DB::table('dokter')
                ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
                ->join('jadwal', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jadwal.kd_poli')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'spesialis.nm_sps', 'jadwal.hari_kerja', 'jadwal.jam_mulai', 'jadwal.jam_selesai', 'poliklinik.nm_poli')
                ->where('dokter.status', '=', '1')
                ->where('poliklinik.kd_poli', '=', $kd_poli)
                ->get();
        } else {
            $data = DB::table('dokter')
                ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
                ->join('jadwal', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jadwal.kd_poli')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'spesialis.nm_sps', 'jadwal.hari_kerja', 'jadwal.jam_mulai', 'jadwal.jam_selesai', 'poliklinik.nm_poli')
                ->where('dokter.status', '=', '1')
                ->get();
        }
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed retrieving data.');
        }
    }

    public function dokterPoli($kd_poli)
    {
        $data = DB::table('dokter')
            ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
            ->join('jadwal', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jadwal.kd_poli')
            ->select('poliklinik.kd_poli', 'poliklinik.nm_poli', 'dokter.kd_dokter', 'dokter.nm_dokter', 'spesialis.nm_sps')
            ->where('dokter.status', '=', '1')
            ->where('poliklinik.kd_poli', '=', $kd_poli)
            ->distinct()
            ->get();

        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed retrieving data.');
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
