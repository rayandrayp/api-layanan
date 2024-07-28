<?php

namespace App\Http\Controllers\APIDashboard;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\RegistrasiPeriksa;
use App\Models\KamarInap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dateNow = date("Y-m-d");
        // $data = RegistrasiPeriksa::where('tgl_registrasi', date("Y-m-d"))->where('status_lanjut', 'Ralan')->get()->count();
        $data = array();
        $data['jmlPxRalan'] = $this->JumlahRalan($dateNow,$dateNow);
        $data['PxRalan'] = $this->PenyebaranRalan($dateNow,$dateNow);
        $data['JnsBayarPxRalan'] = $this->JnsBayarRalan($dateNow,$dateNow);
        $data['JnsPxRalan'] = $this->JnsPasienRalan($dateNow,$dateNow);

        $data['jmlPxRanap'] = $this->JumlahRanap();
        $data['PxRanap'] = $this->PenyebaranRanap();
        $data['JnsBayarPxRanap'] = $this->JnsBayarRanap();
        $data['JnsPxRanap'] = $this->JnsPasienRanap();
        
        
        $lamaInapBulan = $this->getLamaInap(date('Y-m'));
        $getHariPerawatan = $this->getHariPerawatan(date('Y'), date('m'), date('d'));
        $jumlahPasien = $this->getJumlahPasienInap(date('Y-m'));
        $jumlahBed = $this->getJumlahBed();
        $jmlhari = date('t', strtotime('-1 month'));

        $borBulan = ($lamaInapBulan / ($jumlahBed * $jmlhari)) * 100;
        $alos = $lamaInapBulan / $jumlahPasien;
        $toi = (($jumlahBed * $jmlhari) - $lamaInapBulan) / $jumlahPasien;
        $data['bor'] = round($borBulan, 2);
        $data['los'] = round($alos, 2);
        $data['toi'] = round($toi, 2);


        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }

    public function range($daterange)
    {
        $data = array();
        $dateArr = explode('_' ,$daterange);

        $data['jmlPxRalan'] = $this->JumlahRalan($dateArr[0],$dateArr[1]);
        $data['PxRalan'] = $this->PenyebaranRalan($dateArr[0],$dateArr[1]);
        $data['JnsBayarPxRalan'] = $this->JnsBayarRalan($dateArr[0],$dateArr[1]);
        $data['JnsPxRalan'] = $this->JnsPasienRalan($dateArr[0],$dateArr[1]);

        $data['jmlPxRanap'] = $this->JumlahRanap();
        $data['PxRanap'] = $this->PenyebaranRanap();
        $data['JnsBayarPxRanap'] = $this->JnsBayarRanap();
        $data['JnsPxRanap'] = $this->JnsPasienRanap();
        
        
        $lamaInapBulan = $this->getLamaInap(date('Y-m'));
        $getHariPerawatan = $this->getHariPerawatan(date('Y'), date('m'), date('d'));
        $jumlahPasien = $this->getJumlahPasienInap(date('Y-m'));
        $jumlahBed = $this->getJumlahBed();
        $jmlhari = date('t', strtotime('-1 month'));

        $borBulan = ($lamaInapBulan / ($jumlahBed * $jmlhari)) * 100;
        $alos = $lamaInapBulan / $jumlahPasien;
        $toi = (($jumlahBed * $jmlhari) - $lamaInapBulan) / $jumlahPasien;
        $data['bor'] = round($borBulan, 2);
        $data['los'] = round($alos, 2);
        $data['toi'] = round($toi, 2);

        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }
    
    public function JumlahRalan($date1,$date2)
    {
        $result = RegistrasiPeriksa::whereBetween('tgl_registrasi', [$date1,$date2])
            ->where('status_lanjut', 'Ralan')
            ->count();
        $data = [
            'data' => $result
        ];
        return $data;
    }

    public function PenyebaranRalan($date1,$date2)
    {
        $dataQuery = RegistrasiPeriksa::join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->groupBy('poliklinik.nm_poli')
                ->selectRaw('
                        poliklinik.nm_poli, 
                        COUNT(reg_periksa.no_rawat) AS jml_pasien
                    ')
                ->where('status_lanjut', 'Ralan')
                ->whereBetween('tgl_registrasi', [$date1,$date2])
                ->orderBy('poliklinik.nm_poli')
                ->get();
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function JnsBayarRalan($date1,$date2)
    {
        $dataQuery = RegistrasiPeriksa::join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
                ->groupBy('penjab.png_jawab')
                ->selectRaw('
                        penjab.png_jawab, 
                        COUNT(reg_periksa.no_rawat) AS jml_pasien
                    ')
                ->where('status_lanjut', 'Ralan')
                ->whereBetween('tgl_registrasi', [$date1,$date2])
                ->orderBy('penjab.png_jawab')
                ->get();
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function JnsPasienRalan($date1,$date2)
    {
        $dataQuery = RegistrasiPeriksa::join('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
                ->groupBy('bridging_sep.peserta')
                ->selectRaw('
                        bridging_sep.peserta, 
                        COUNT(reg_periksa.no_rawat) AS jml_pasien
                    ')
                ->where('status_lanjut', 'Ralan')
                ->whereBetween('tgl_registrasi', [$date1,$date2])
                ->orderBy('bridging_sep.peserta')
                ->get();
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function JumlahRanap()
    {
        $result = KamarInap::where('stts_pulang', '-')
            ->count();
    
        $data = [
            'data' => $result
        ];
        return $data;
    }

    public function PenyebaranRanap()
    {
        $dataQuery = KamarInap::join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
                ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
                ->groupBy('bangsal.nm_bangsal')
                ->selectRaw('
                        bangsal.nm_bangsal, 
                        COUNT(kamar_inap.no_rawat) AS jml_pasien
                    ')
                ->where('stts_pulang', '-')
                ->orderBy('bangsal.nm_bangsal')
                ->get();
        
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function JnsBayarRanap()
    {
        $dataQuery = RegistrasiPeriksa::join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
                ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->groupBy('penjab.png_jawab')
                ->selectRaw('
                        penjab.png_jawab, 
                        COUNT(kamar_inap.no_rawat) AS jml_pasien
                    ')
                ->where('stts_pulang', '-')
                ->orderBy('penjab.png_jawab')
                ->get();
        
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function JnsPasienRanap()
    {
        $dataQuery = RegistrasiPeriksa::join('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->groupBy('bridging_sep.peserta')
                ->selectRaw('
                        bridging_sep.peserta, 
                        COUNT(kamar_inap.no_rawat) AS jml_pasien
                    ')
                ->where('jnspelayanan', '1')
                ->where('stts_pulang', '-')
                ->orderBy('bridging_sep.peserta')
                ->get();
        $data = [
            'data' => $dataQuery
        ];
        return $data;
    }

    public function getLamaInap($date)
    {
        // $dataQuery = KamarInap::selectRaw('SUM(lama) AS lama')
        //         ->where('tgl_masuk','LIKE', $date.'%')
        //         ->get();
        $dataQuery = DB::select(DB::raw("SELECT SUM(lama) AS lama FROM kamar_inap where tgl_masuk LIKE '$date%'"));
        // $data = [
        //     'data' => $dataQuery
        // ];
        return $dataQuery[0]->lama;
    }

    public function getHariPerawatan($year, $month, $days)
    {
        $d = '';
        $lama = 0;
        for ($i = 1; $i <= $days; $i++) {
            if($i < 10){
                $d = '0'.$days;
            } else {
                $d = $days;
            }
            $m = ($month < 10) ? '0'.$month : $month;
            $dataQuery = KamarInap::selectRaw('COUNT(kd_kamar) AS jml')
                ->where('tgl_masuk', '<', $year.'-'.$m.'-'.$d)
                ->where('tgl_keluar', '>=', $year.'-'.$m.'-'.$d)
                ->get()
                ->first();
            $lama += $dataQuery['jml'];

        }
        
        // $data = [
        //     'data' => $lama
        // ];
        return $lama;
    }

    public function getJumlahPasienInap($date)
    {
        $dataQuery = DB::select(DB::raw("SELECT SUM(jml) as jml FROM (select count(no_rawat) AS jml from kamar_inap where tgl_masuk LIKE '$date%' group by no_rawat) A"));
        // $data = [
        //     'data' => $dataQuery
        // ];
        return $dataQuery[0]->jml;
    }

    public function getJumlahBed()
    {
        $dataQuery = DB::select(DB::raw("select count(*) as jmlbed from kamar where statusdata='1'"));
        // $data = [
        //     'data' => $dataQuery
        // ];
        return $dataQuery[0]->jmlbed;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
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