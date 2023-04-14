<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Spesialis;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpesialisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arrspesialis=array('Sp.A','Sp.An','Sp.B','Sp.BM','Sp.BP','Sp.BS','Sp.OT','Sp.DV','Sp.JP','Sp.KF','Sp.KJ','Sp.KK','Sp.M','Sp.OG','Sp.OT','Sp.P','Sp.PA','Sp.PD','Sp.PK','Sp.Pr','Sp.Ra','Sp.S','Sp.TH','Sp.U','SpRad','TKV');
        $data = Spesialis::whereIn('kd_sps', $arrspesialis)->get();
        // $data = Spesialis::all();
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
        $arrspesialis=array('Sp.PD','Sp.P');
        if($id == "konsulan"){
            $arr_dokter = ['D0000141','201706004','D0000142'];
            $spesialis['kd_sps'] = "konsulan";
            $spesialis['nm_sps'] = "Dokter Konsulan";
            $spesialis['dokter'] = Dokter::whereIn('kd_dokter', $arr_dokter)
                                ->with('jadwal','poliklinik')
                                ->get();
            if ($spesialis) {
                return ApiFormatter::createAPI(200, 'Success', $spesialis);
            }else{
                return ApiFormatter::createAPI(400, 'Failed retrieving data.');
            }
        }else{
            $data = Spesialis::with('dokter.jadwal','dokter.poliklinik')->where('kd_sps', $id)->first();
            if ($data) {
                return ApiFormatter::createAPI(200, 'Success', $data);
            } else {
                return ApiFormatter::createAPI(400, 'Failed retrieving data.');
            }
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