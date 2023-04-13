<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Dokter::with('spesialis','jadwal','poliklinik')->where('status', '=', '1')->whereNotIn('kd_sps', ['-','UMUM'])->orderBy('nm_dokter', 'ASC')->get();
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
        $data = Dokter::with('spesialis','jadwal','poliklinik')->where('kd_dokter', '=', $id)->first();
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed retrieving data.');
        }
    }

    public function jadwal($id = null)
    {
        if ($id) {
            $data = DB::table('dokter')
                ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
                ->join('jadwal_web', 'jadwal_web.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jadwal_web.kd_poli')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'spesialis.nm_sps', 'jadwal_web.hari_kerja', 'jadwal_web.jam_mulai', 'jadwal_web.jam_selesai', 'poliklinik.nm_poli')
                ->where('dokter.status', '=', '1')
                ->where('dokter.kd_dokter', '=', $id)
                ->get();
        } else {
            $data = DB::table('dokter')
                ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
                ->join('jadwal_web', 'jadwal_web.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jadwal_web.kd_poli')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'spesialis.nm_sps', 'jadwal_web.hari_kerja', 'jadwal_web.jam_mulai', 'jadwal_web.jam_selesai', 'poliklinik.nm_poli')
                ->where('dokter.status', '=', '1')
                ->get();
        }
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