<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Get_field
{
  public static function get_data($fieldid, $id, $table, $field)
  {
    $get_data = DB::table($table)->where($fieldid, $id)->first();
    return (isset($get_data->$field) ? $get_data->$field : '');
  }

  public static function format_indo($date)
  {
    date_default_timezone_set('Asia/Jakarta');
    // array hari dan bulan
    $Hari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    $Bulan = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    // pemisahan tahun, bulan, hari, dan waktu
    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl = substr($date, 8, 2);
    $waktu = substr($date, 11, 5);
    $hari = date("w", strtotime($date));
    if (!empty($date)) {
      $result = $tgl . " " . $Bulan[(int)$bulan - 1] . " " . $tahun . " " . $waktu;
    } else {
      $result = '';
    }
    return $result;
  }
  public static function format_indo2($date)
  {
    date_default_timezone_set('Asia/Jakarta');
    // array hari dan bulan
    $Hari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    $Bulan = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

    // pemisahan tahun, bulan, hari, dan waktu
    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl = substr($date, 8, 2);
    $waktu = substr($date, 11, 5);
    $hari = date("w", strtotime($date));
    if (!empty($date)) {
      $result = $tgl . " " . $Bulan[(int)$bulan - 1] . " " . $tahun . " " . $waktu;
    } else {
      $result = '';
    }
    return $result;
  }

  public static function generateRandomString($length = 20)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public static function bulan()
  {
    $bulan = [
      '1' => 'Januari',
      '2' => 'Februari',
      '3' => 'Maret',
      '4' => 'April',
      '5' => 'Mei',
      '6' => 'Juni',
      '7' => 'Juli',
      '8' => 'Agustus',
      '9' => 'September',
      '10' => 'Oktober',
      '11' => 'November',
      '12' => 'Desember',
    ];

    return $bulan;
  }

  public static function getBulan($bulan)
  {
    switch ($bulan) {
      case '1':
        return "Januari";
        break;
      case '2':
        return "Februari";
        break;
      case '3':
        return "Maret";
        break;
      case '4':
        return "April";
        break;
      case '5':
        return "Mei";
        break;
      case '6':
        return "Juni";
        break;
      case '7':
        return "Juli";
        break;
      case '8':
        return "Agustus";
        break;
      case '9':
        return "September";
        break;
      case '10':
        return "Oktober";
        break;
      case '11':
        return "November";
        break;
      case '12':
        return "Desember";
        break;

      default:
        return "-";
        break;
    }
  }

  public static function bulanRomawi()
  {
    $bulanRomawi = [
      '1' => 'I',
      '2' => 'II',
      '3' => 'III',
      '4' => 'IV',
      '5' => 'V',
      '6' => 'VI',
      '7' => 'VII',
      '8' => 'VIII',
      '9' => 'IX',
      '10' => 'X',
      '11' => 'XI',
      '12' => 'XII',
    ];

    return $bulanRomawi;
  }

  public static function getBulanRomawi($romawi)
  {
    switch ($romawi) {
      case '1':
        return "I";
        break;
      case '2':
        return "II";
        break;
      case '3':
        return "III";
        break;
      case '4':
        return "IV";
        break;
      case '5':
        return "V";
        break;
      case '6':
        return "VI";
        break;
      case '7':
        return "VII";
        break;
      case '8':
        return "VIII";
        break;
      case '9':
        return "IX";
        break;
      case '10':
        return "X";
        break;
      case '11':
        return "XI";
        break;
      case '12':
        return "XII";
        break;

      default:
        return "-";
        break;
    }
  }

  public static function triwulan()
  {
    $bulanRomawi = [
      '1' => 'I',
      '2' => 'II',
      '3' => 'III',
      '4' => 'IV',
    ];

    return $bulanRomawi;
  }

  public static function getTriwulan($triwulan)
  {
    switch ($triwulan) {
      case '1':
        return "I";
        break;
      case '2':
        return "II";
        break;
      case '3':
        return "III";
        break;
      case '4':
        return "IV";
        break;

      default:
        return "-";
        break;
    }
  }

  public static function semester()
  {
    $bulanRomawi = [
      '1' => 'I',
      '2' => 'II',
    ];

    return $bulanRomawi;
  }

  public static function getSemester($semester)
  {
    switch ($semester) {
      case '1':
        return "I";
        break;
      case '2':
        return "II";
        break;

      default:
        return "-";
        break;
    }
  }

  public static function rupiah($angka)
  {

    $hasil_rupiah = "Rp " . number_format($angka, 3, ',', '.');
    return $hasil_rupiah;
  }

  public static function currencyFormat($angka)
  {

    $hasil_rupiah = number_format($angka, 2, '.', ',');
    return $hasil_rupiah;
  }

  public static function jenisDaerah($jenis)
  {
    switch ($jenis) {
      case '0':
        return "Provinsi";
        break;
      case '1':
        return "Kabupaten";
        break;
      case '2':
        return "Kota";
        break;

      default:
        return "-";
        break;
    }
  }

  public static function attInsert()
  {
    $attributes = [];
    $attributes['opadd'] = Auth::user()->login;
    $attributes['tgladd'] = date('Y-m-d H:i:s');
    $attributes['dlt'] = 0;

    return $attributes;
  }

  public static function tgl_indo($tanggal)
  {
    $bulan = array(
      1 =>   'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
  }
  public static function tgl_indo2($tanggal)
  {
    $bulan = array(
      1 =>   'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agu',
      'Sep',
      'Okt',
      'Nov',
      'Des'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun
    if (isset($pecahkan[2]) && isset($pecahkan[1]) && isset($pecahkan[0])) {
      return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    } else {
      echo "Index not present";
    }
    // return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
  }
}
