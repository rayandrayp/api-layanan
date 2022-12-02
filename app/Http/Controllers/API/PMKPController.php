<?php

namespace App\Http\Controllers\API;


use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PMKPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function indikatorMutuNasional($year)
    {
        $data = array();
        $this->data['data_pmkp'] = $this->KebersihanTangan($year);
        $this->data['data_pmkp'] = $this->PenggunaanAPD($year);
        $this->data['data_pmkp'] = $this->IdentifikasiPasien($year);
        $this->data['data_pmkp'] = $this->WaktuTanggapOperasiSC($year);
        $this->data['data_pmkp'] = $this->WaktuTungguRalan($year);
        $this->data['data_pmkp'] = $this->PenundaanOK($year);
        $this->data['data_pmkp'] = $this->VisiteDokter($year);
        $this->data['data_pmkp'] = $this->HasilKritisLab($year);
        $this->data['data_pmkp'] = $this->PenggunaanFornas($year);
        $this->data['data_pmkp'] = $this->WaktuTanggapKomplain($year);
        $this->data['data_pmkp'] = $this->KepuasanPasien($year);
        $this->data['data_pmkp'] = $this->PencegahanRisikoPasienJatuh($year);
        if ($data) {
            return ApiFormatter::createAPI(200, 'Success', $data);
        } else {
            return ApiFormatter::createAPI(400, 'Failed loading data.');
        }
    }

    public function KebersihanTangan($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan,
                    ((
                        SUM(CASE WHEN sebelum_kontak_pasien = "HW" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN sebelum_tindakan_aseptik = "HW" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_pasien = "HW" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_cairan = "HW" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_alat = "HW" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN sebelum_kontak_pasien = "HR" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN sebelum_tindakan_aseptik = "HR" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_pasien = "HR" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_cairan = "HR" THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN setelah_kontak_alat = "HR" THEN 1 ELSE 0 END) 
                ) / (COUNT(nama_petugas) * 5) )
                    AS prosentase')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function PenggunaanAPD($year)
    {
        $dataQuery = DB::statement(DB::raw('SELECT bulan, 
            AVG((CASE WHEN level_apd = "1" THEN level_1 WHEN level_apd = "2" THEN level_2 WHEN level_apd = "3" THEN level_3 END)) AS prosentase
            FROM ( 
                SELECT DATE_FORMAT(tanggal, "%M") AS bulan, level_apd,
                (( 
                    SUM(CASE WHEN level_apd = "1" AND  masker_bedah = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "1" AND  gaun = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "1" AND  handscoon = "IYA" THEN 1 ELSE 0 END) 
                ) / (COUNT(ruangan) * 3)) AS level_1,
                (( 
                    SUM(CASE WHEN level_apd = "2" AND  masker_n95 = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "2" AND  gaun = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "2" AND  faceshield = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "2" AND  nursecap = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "2" AND  handscoon = "IYA" THEN 1 ELSE 0 END) 
                )  / (COUNT(ruangan) * 5)) AS level_2,
                (( 
                    SUM(CASE WHEN level_apd = "3" AND  masker_n95 = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  hazmat = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  handscoon = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  nursecap = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  faceshield = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  goggle = "IYA" THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN level_apd = "3" AND  sepatuboot = "IYA" THEN 1 ELSE 0 END) 
                )  / (COUNT(ruangan) * 7))  AS level_3
                FROM mutu_kepatuhanapd
                WHERE tanggal LIKE ":year%"
                GROUP BY LEFT(tanggal, 7), level_apd
            ) A
            GROUP BY bulan'), array('year' => $year));
        $data = [
            'namaIndikator' => 'Kepatuhan Penggunaan APD',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function IdentifikasiPasien($year)
    {
        $dataQuery = DB::table('mutu_identifikasipasien')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan,
                (
                    SUM(CASE WHEN pemberian_obat = "IYA" 
                        AND pemberian_nutrisi = "IYA" 
                        AND pemberian_darah = "IYA" 
                        AND pengambilan_specimen = "IYA" 
                        AND sebelum_diagnostik = "IYA" THEN 1 ELSE 0 
                        END) / COUNT(nm_pasien)
                ) AS prosentase')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Identifikasi Pasien',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function WaktuTanggapOperasiSC($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tgl_registrasi, 7)')
            ->selectRaw('DATE_FORMAT(tgl_registrasi, "%M") AS bulan, AVG(skor) AS prosentase')
            ->where('tgl_registrasi', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Tanggap Operasi SC Emergency',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function ClinicalPathway($year)
    {
        $dataQuery = DB::table('mutu_kepatuhanclinicalpathway')
            ->groupByRaw('LEFT(tgl_data, 7)')
            ->selectRaw('DATE_FORMAT(tgl_data, "%M") AS bulan,
            ((SUM(CASE WHEN patuh = "IYA" THEN 1 ELSE 0 END))/COUNT(nm_pasien)) AS prosentase')
            ->where('tgl_data', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Clinical Pathway',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function WaktuTungguRalan($year)
    {
        $dataQuery = \App\Models\RegistrasiPeriksa::join('temporary2', 'temporary2.temp2', '=', 'reg_periksa.no_rawat')
            ->groupByRaw('LEFT(reg_periksa.tgl_registrasi, 7)')
            ->selectRaw(' DATE_FORMAT(r.tgl_registrasi, "%M") AS bulan,
                    ((SUM(CASE WHEN ROUND(((TIME_TO_SEC(temporary2.temp4) - TIME_TO_SEC(reg_periksa.jam_reg))/60) ,0) <= 180  THEN 1 ELSE 0 END))/COUNT(reg_periksa.no_rawat)) AS prosentase')
            ->where('reg_periksa.tgl_registrasi', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Waktu Tunggu Ralan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function PenundaanOK($year)
    {
        $dataQuery = \App\Models\BookingOperasi::join('operasi', 'operasi.no_rawat', '=', 'booking_operasi.no_rawat')
            ->groupByRaw('LEFT(booking_operasi.tanggal, 7)')
            ->selectRaw('DATE_FORMAT(LEFT(booking_operasi.tanggal, 10), "%M") AS bulan,
            // ((SUM(CASE WHEN DATEDIFF(LEFT(operasi.tgl_operasi, 10),LEFT(booking_operasi.tanggal, 10)) > 1 THEN 1 ELSE 0 END))/COUNT(booking_operasi.tanggal)) AS prosentase')
            ->where('booking_operasi.tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Penundaan Operasi Elektif',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function VisiteDokter($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function HasilKritisLab($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function PenggunaanFornas($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function WaktuTanggapKomplain($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function KepuasanPasien($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }
    public function PencegahanRisikoPasienJatuh($year)
    {
        $dataQuery = DB::table('mutu_kebersihantangan')
            ->groupByRaw('LEFT(tanggal, 7)')
            ->selectRaw('DATE_FORMAT(tanggal, "%M") AS bulan, (COUNT(nama_petugas) * 5) AS opp,
                (
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HW\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN sebelum_tindakan_aseptik = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_pasien = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_cairan = \'HR\' THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN setelah_kontak_alat = \'HR\' THEN 1 ELSE 0 END) 
                ) AS hwhr')
            ->where('tanggal', 'LIKE', '"' . $year . '%"')
            ->get();
        $data = [
            'namaIndikator' => 'Kepatuhan Kebersihan Tangan',
            'data' => $dataQuery
        ];
        return $data;
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }


    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
