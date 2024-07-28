<?php

namespace App\Http\Controllers\APIDashboard;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\RegistrasiPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RalanController extends Controller
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
        $data['jmlPxRalan'] = $this->Jumlah($dateNow,$dateNow);
        $data['jmlPxRalanBaru'] = $this->JumlahPxBaru($dateNow,$dateNow);
        $data['jmlPxRalanBatal'] = $this->JumlahPxBatal($dateNow,$dateNow);
        $data['jmlPxRalanSelesai'] = $this->JumlahPxSelesai($dateNow,$dateNow);
        $data['PxRalan'] = $this->Penyebaran($dateNow,$dateNow);
        $data['JnsBayarPxRalan'] = $this->JnsBayar($dateNow,$dateNow);
        $data['JnsPxRalan'] = $this->JnsPasien($dateNow,$dateNow);
        $data['TrendKunjungan'] = $this->TrendKunjungan();
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

        $data['jmlPxRalan'] = $this->Jumlah($dateArr[0],$dateArr[1]);
        $data['jmlPxRalanBaru'] = $this->JumlahPxBaru($dateArr[0],$dateArr[1]);
        $data['jmlPxRalanBatal'] = $this->JumlahPxBatal($dateArr[0],$dateArr[1]);
        $data['jmlPxRalanSelesai'] = $this->JumlahPxSelesai($dateArr[0],$dateArr[1]);
        $data['PxRalan'] = $this->Penyebaran($dateArr[0],$dateArr[1]);
        $data['JnsBayarPxRalan'] = $this->JnsBayar($dateArr[0],$dateArr[1]);
        $data['JnsPxRalan'] = $this->JnsPasien($dateArr[0],$dateArr[1]);
        $data['TrendKunjungan'] = $this->TrendKunjungan();
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }
    
    public function Jumlah($date1,$date2)
    {
        $result = RegistrasiPeriksa::whereBetween('tgl_registrasi', [$date1,$date2])
            ->where('status_lanjut', 'Ralan')
            ->count();
        $data = [
            'data' => $result
        ];
        return $data;
    }

    public function JumlahPxBaru($date1,$date2)
    {
        $result = RegistrasiPeriksa::whereBetween('tgl_registrasi', [$date1,$date2])
            ->where('status_lanjut', 'Ralan')
            ->where('stts_daftar', 'Baru')
            ->count();
        $data = [
            'data' => $result
        ];
        return $data;
    }

    public function JumlahPxBatal($date1,$date2)
    {
        $result = RegistrasiPeriksa::whereBetween('tgl_registrasi', [$date1,$date2])
            ->where('status_lanjut', 'Ralan')
            ->where('stts', 'Batal')
            ->count();
        $data = [
            'data' => $result
        ];
        return $data;
    }


    public function JumlahPxSelesai($date1,$date2)
    {
        $result = RegistrasiPeriksa::whereBetween('tgl_registrasi', [$date1,$date2])
            ->where('status_lanjut', 'Ralan')
            ->where('stts', 'Sudah')
            ->count();
        $data = [
            'data' => $result
        ];
        return $data;
    }

    public function Penyebaran($date1,$date2)
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

    public function JnsBayar($date1,$date2)
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

    public function JnsPasien($date1,$date2)
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

    public function TrendKunjungan()
    {
        $dataQuery = RegistrasiPeriksa::selectRaw('
                        LEFT(tgl_registrasi, 7),
                        MONTHNAME(tgl_registrasi)  AS bulan, 
                        COUNT(no_rawat) AS jml
                    ')
                ->where('tgl_registrasi','LIKE', date('Y').'%')
                ->groupBy('LEFT(tgl_registrasi, 7)')
                ->get();
        $data = [
            'data' => $dataQuery
        ];
        return $data;
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