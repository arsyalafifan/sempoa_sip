<?php
   use App\apptemplate as apps;
   $mode = config('app.app_mode', '-');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', '-') }}</title>
        <!-- Favicon-->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/default/assets/images/'.apps::gettemplate($mode, 'favicon')) }}">
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light%7CPlayfair+Display:400" rel="stylesheet" type="text/css">
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('/dist/plugins/bootstrap-extension/css/bootstrap-extension.css') }}" rel="stylesheet"> --}}
        {{-- <link href="css/styles.css" rel="stylesheet" /> --}}
        <link rel="stylesheet" href="{{asset('dist/landing/css/styles.css')}}">
        <!-- select2 -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

        <script src="{{ asset('/assets/js/globalFunction.js') }}" type="text/javascript"></script>
        {{-- <link href="{{ asset('/dist/plugins/bootstrap-extension/css/bootstrap-extension.css') }}" rel="stylesheet"> --}}
        <style>
            * {
                padding:0;
                margin:0;
                -webkit-box-sizing:border-box;
                -moz-box-sizing:border-box;
                -box-sizing:border-box;
            }
            /* .mega-menu, .mega-menu >ul {
                position: relative;
                background-color: #333;
                height: 50px;
            } */
            .mega-menu >ul >li {
                display: inline-block;
                /* padding:15.5px 0; */
                overflow: hidden;
            }
            a{
                text-decoration: none;
            }
            .mega-menu >ul >li >a {
                /* padding:17px; */
                color:#eee;
                text-decoration: none !important;
            }
            /* .mega-menu >ul >li:hover {
                background-color: #10496f;
                -webkit-transition:ease 0.3s;
            } */
            .mega-menu .menu-detail {
                height: 0;
                visibility: hidden;
                opacity: 0;
                position: absolute;
            }
            .mega-menu >ul >li:hover >div.menu-detail {
                opacity: 1;
                height: 300px;
                width:100%;
                height: 100vh;
                visibility: visible;
                /* top:200px; */
                right:100;
                left: 0;
                z-index: 99;
                background-color: transparent;
                color:#fff;
                -webkit-transition:height 1s;
                -moz-transition:height 1s;
                transition:height 1s;
                /* border-top:3px solid #3399dd; */
                overflow: hidden;
            }

            .menu-detail .section {
                padding:20px;
            }

            .card-custom{
                background-color: #DDFDFC !important;
                color: #3500FF;
                height: 110px;
            }

            /* .dropdown-custom{
                visibility: hidden;
            } */
        </style>
        <!-- modify selected row datatable color -->
        <style>
            @media (min-width: 1549px){
                #page-wrapper {
                    margin-right: 0;
                }
            }
            @media (max-width: 540px){
                #page-wrapper {
                    margin-left: 40px;
                }
            }
            table.dataTable tbody>tr.selected,table.dataTable tbody>tr>.selected {
                background-color: #9dabc7 !important;
                color: white;
                /* background-color: #b0bed9 !important; */
            }
            table.dataTable tbody>tr:hover,table.dataTable tbody>tr>:hover {
                /* background-color: #9dabc7 !important; */
                /* color: white; */
                box-shadow: inset 0 0 0 9999px #e9edf3 !important;
                /* background-color: #b0bed9 !important; */
            }
            /* .swal2-container {
                transform: scale(1.3);
            } */

            label{
                font-weight: 600;
            }

            .progress {
                background-color: rgb(18 21 23 / 13%);
            }

        </style>

        {{-- <style>
            .navbar {
                background-color: #333; /* Warna latar belakang navbar */
            }

            .navbar-brand {
                padding: 0.5rem 1rem;
            }

            .navbar-nav .nav-link {
                padding: 0.5rem 1rem;
                color: #fff; /* Warna teks link */
            }

            .navbar-nav .nav-link:hover {
                color: #fff; /* Warna teks link saat dihover */
            }

            /* Navbar Toggler Icon */
            .navbar-toggler {
                color: #fff; /* Warna ikon toggler */
            }

            /* Navbar Toggler Icon Hover */
            .navbar-toggler:hover {
                color: #fff; /* Warna ikon toggler saat dihover */
            }

            /* Navbar Toggler Icon */
            .navbar-toggler {
                color: #fff; /* Warna ikon toggler */
            }

            /* Navbar Toggler Icon Hover */
            .navbar-toggler:hover {
                color: #fff; /* Warna ikon toggler saat dihover */
            }

            /* Mega Menu */
            .mega-menu {
                position: static !important; /* Override position agar dropdown tetap tampil */
                flex-wrap: wrap; /* Agar dropdown dapat menampilkan item secara vertikal */
            }

            .nav-item {
                position: relative; /* Agar dropdown tetap muncul ketika dihover */
            }

            .menu-detail {
                display: none; /* Sembunyikan menu detail secara default */
                position: absolute;
                left: 0;
                top: 100%;
                background-color: #333; /* Warna latar belakang menu detail */
                z-index: 999; /* Atur indeks z agar muncul di atas elemen lain */
                width: 100%;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Efek bayangan */
            }

            .menu-detail .row {
                margin-bottom: 10px;
            }

            .nav-item:hover .menu-detail {
                display: block; /* Tampilkan menu detail ketika parent nav-item dihover */
            }

            .card-custom {
                background-color: #444; /* Warna latar belakang kartu */
            }

            .card-custom:hover {
                background-color: #555; /* Warna latar belakang kartu saat dihover */
            }

            .card-custom .card-title {
                color: #fff; /* Warna teks judul kartu */
            }

            /* Media queries */
            @media (max-width: 992px) {
                .nav-width {
                    width: 100%; /* Atur lebar navbar item menjadi 100% */
                }

                .nav-link {
                    display: flex;
                    align-items: center;
                }

                .nav-link img {
                    margin-right: 10px; /* Atur margin kanan gambar */
                }

                .menu-detail {
                    position: static; /* Override posisi menu detail agar tampil seperti dropdown biasa */
                    display: none; /* Sembunyikan menu detail */
                    background-color: transparent; /* Hapus latar belakang */
                    padding: 0; /* Hapus padding */
                    box-shadow: none; /* Hapus bayangan */
                }

                .nav-item:hover .menu-detail {
                    display: none; /* Sembunyikan menu detail */
                }

                .navbar-nav {
                    flex-direction: column; /* Tampilkan item navbar secara vertikal */
                }
            } --}}
        {{-- </style> --}}


        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <!--<link href="{{ asset('/datatables/datatables.min.css') }}" rel="stylesheet">
        <script src="{{ asset('/datatables/datatables.js') }}"></script>-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchpanes/1.2.1/css/searchPanes.dataTables.min.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/searchpanes/1.2.1/js/dataTables.searchPanes.min.js"></script>
        <link type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.3/css/rowGroup.dataTables.min.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/rowgroup/1.1.3/css/rowGroup.dataTables.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.3/js/dataTables.rowGroup.min.js"></script>
        <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
        <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha512-Rdk63VC+1UYzGSgd3u2iadi0joUrcwX0IWp2rTh6KXFoAmgOjRS99Vynz1lJPT8dLjvo6JZOqpAHJyfCEZ5KoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>

        <!-- pie chart -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            {{-- <a class="navbar-brand" href="#">Navbar</a> --}}
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="{{route('index')}}">
                            <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="/">
                            <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                            Profile
                        </a>
                    </li>
                    <li class="nav-item dropdown" style="margin-right: 20px">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_spakat.png') }}" alt="">
                            <div style="max-width: 60px">
                                Layanan 
                                <br/>
                                Spakat
                            </div>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Legalisir Pindah Rayon</a>
                            <a class="dropdown-item" href="{{ route('legalisir-dashboard') }}">Legalisir Ijazah</a>
                            <a class="dropdown-item" href="#">Legalisir Surat Keterangan Pengganti Ijazah</a>
                            <a class="dropdown-item" href="#">Rekomendasi Perizinan Sekolah</a>
                            <a class="dropdown-item" href="#">PPDB</a>
                            <a class="dropdown-item" href="#">Kenaikan JABFUNG Guru</a>
                            <a class="dropdown-item" href="{{ route('caristatuspegawai.index') }}">Kenaikan Gaji Berkala</a>
                            <a class="dropdown-item" href="">Penilaian Angka Kredit</a>
                            <a class="dropdown-item" href="">Pensium Guru</a>
                        </div>
                    </li>
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="{{ route('sarpras-dashboard') }}">
                            <img width="60px" src="{{ asset('/images/icon/landing/sarpras.png') }}" alt="">
                            Sarpras
                        </a>
                    </li> 
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_ptk.png') }}" alt="">
                            PTK
                        </a>
                    </li> 
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_dokumen_kurikulum.png') }}" alt="">
                            Layanan Dokumen Kurikulum
                        </a>
                    </li> 
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                            Peta Sebaran Sekolah
                        </a>
                    </li> 
                    <li class="nav-item nav-width" style="margin-right: 20px">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                            Forum Interaksi
                        </a>
                    </li> 
                    <li class="nav-item nav-width"  style="margin-right: 20px">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/bangunan.png') }}" alt="">
                            Data Pembangunan
                            {{-- <p style="overflow: auto">
                                Data pembangunan
                            </p> --}}
                        </a>
                    </li> 
                </ul>
            </div>
        </nav>
        {{-- <nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-static" id="mainNav" style="{{ isset($p_nav_bg_color) ? 'background-color: '.$p_nav_bg_color.';' : ''}}">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMobile" aria-controls="navbarMobile" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars ms-1"></i>
                </button>
                <div class="collapse navbar-collapse flex-column mega-menu" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto py-4 py-lg-0">
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="{{route('index')}}">
                                <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                                Beranda
                            </a>
                        </li>
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="/">
                                <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="/">
                                <img width="60px" src="{{ asset('/images/icon/landing/layanan_spakat.png') }}" alt="">
                                Layanan Spakat
                            </a>
                            <div class="menu-detail">
                                <div class="container-sm mt-5 text-center">
                                    <div class="row">
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Legalisir Pindah Rayon</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="{{ route('legalisir-dashboard') }}">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Legalisir Ijazah</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Legalisir Surat Keterangan Pengganti Ijazah</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Rekomendasi Perizinan Sekolah</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">PPDB</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Kenaikan JABFUNG Guru</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm m-2">
                                            <a href="{{ route('caristatuspegawai.index') }}">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Kenaikan Gaji Berkala</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Penilaian Angka Kredit</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm m-2">
                                            <a href="">
                                                <div class="card p-3 card-custom">
                                                    <div class="card-body">
                                                      <h5 class="card-title">Pensium Guru</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="{{ route('sarpras-dashboard') }}">
                                <img width="60px" src="{{ asset('/images/icon/landing/sarpras.png') }}" alt="">
                                Sarpras
                            </a>
                        </li> 
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="">
                                <img width="60px" src="{{ asset('/images/icon/landing/layanan_ptk.png') }}" alt="">
                                PTK
                            </a>
                        </li> 
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="">
                                <img width="60px" src="{{ asset('/images/icon/landing/layanan_dokumen_kurikulum.png') }}" alt="">
                                Layanan Dokumen Kurikulum
                            </a>
                        </li> 
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="">
                                <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                                Peta Sebaran Sekolah
                            </a>
                        </li> 
                        <li class="nav-item nav-width">
                            <a class="nav-link" href="">
                                <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                                Forum Interaksi
                            </a>
                        </li> 
                        <li class="nav-item nav-width mr-2">
                            <a class="nav-link" href="">
                                <img width="60px" src="{{ asset('/images/icon/landing/bangunan.png') }}" alt="">
                                <p style="overflow: auto">
                                    Data pembangunan
                                </p>
                            </a>
                        </li> 
                        
                    </ul>
                </div>
            </div>
            <div class="container" id="navbarMobile">
                <div class="row">
                    <div class="col-4">
                        <a class="nav-link" href="{{route('index')}}">
                            <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                            Beranda
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="nav-link" href="/">
                            <img width="60px" src="{{ asset('/images/icon/landing/beranda.png') }}" alt="">
                            Profile
                        </a>
                    </div>
                    <div class="col-4 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_spakat.png') }}" alt="">
                            Layanan Spakat
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Legalisir Pindah Rayon</a>
                            <a class="dropdown-item" href="{{ route('legalisir-dashboard') }}">Legalisir Ijazah</a>
                            <a class="dropdown-item" href="#">Legalisir Surat Keterangan Pengganti Ijazah</a>
                            <a class="dropdown-item" href="#">Rekomendasi Perizinan Sekolah</a>
                            <a class="dropdown-item" href="#">PPDB</a>
                            <a class="dropdown-item" href="#">Kenaikan JABFUNG Guru</a>
                            <a class="dropdown-item" href="{{ route('caristatuspegawai.index') }}">Kenaikan Gaji Berkala</a>
                            <a class="dropdown-item" href="">Penilaian Angka Kredit</a>
                            <a class="dropdown-item" href="">Pensium Guru</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <a class="nav-link" href="{{ route('sarpras-dashboard') }}">
                            <img width="60px" src="{{ asset('/images/icon/landing/sarpras.png') }}" alt="">
                            Sarpras
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_ptk.png') }}" alt="">
                            PTK
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/layanan_dokumen_kurikulum.png') }}" alt="">
                            Layanan Dokumen Kurikulum
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                            Peta Sebaran Sekolah
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/forum_interaksi.png') }}" alt="">
                            Forum Interaksi
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="nav-link" href="">
                            <img width="60px" src="{{ asset('/images/icon/landing/bangunan.png') }}" alt="">
                            <p style="overflow: auto">
                                Data pembangunan
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        </nav> --}}

        @yield('content')

        <!-- Footer-->
        <footer class="footer py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 text-lg-start">Copyright &copy; Riqcom Services 2023</div>
                    <div class="col-lg-4 my-3 my-lg-0">
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        {{-- <a class="link-dark text-decoration-none me-3" href="#!">Privacy Policy</a>
                        <a class="link-dark text-decoration-none" href="#!">Terms of Use</a> --}}
                    </div>
                </div>
            </div>
        </footer>
        
        <script>
            // document.addEventListener("DOMContentLoaded", function() {
            //     const layananSpakatLink = document.querySelector('.layanan-spakat');
            //     const dropdownCustom = document.querySelector('.dropdown-custom');

            //     layananSpakatLink.addEventListener('click', function(event) {
            //         event.preventDefault(); // Prevent default link behavior

            //         // Toggle 'show' class on dropdownCustom
            //         dropdownCustom.classList.toggle('show');
            //     });
            // });
        </script>

        {{-- <!-- boostrap js --> 
        <script src="{{asset('/bootstrap/dist/js/bootstrap.min.js')}}"></script> --}}
        <!-- bootstrap exstension -->
        {{-- <script src="{{asset('/dist/plugins/bower_components/bootstrap-extension/js/bootstrap-extension.min.js')}}"></script> --}}
        {{-- <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> --}}
        <!-- Core theme JS-->
        <script src="{{asset('dist/landing/js/scripts.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
        <!-- tether -->
        <script src="{{asset('/bootstrap/dist/js/tether.min.js')}}"></script>
        <!-- boostrap js --> 
        <script src="{{asset('/bootstrap/dist/js/bootstrap.min.js')}}"></script>

         <!-- touchspin -->
         <script src="{{asset('/dist/plugins/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js')}}" type="text/javascript"></script>

         <!-- switcher -->
         <script src="{{asset('/dist/plugins/bower_components/styleswitcher/jQuery.style.switcher.js')}}"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script> -->

    </body>
</html>