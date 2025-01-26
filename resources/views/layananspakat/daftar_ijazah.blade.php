<?php
use App\enumVar as enum;
use App\Helpers\Get_field;
?>
@extends('landing.index')

@section('content1')
<style>
    @import url(https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700);

    #ijazah-preview-container,
    #ktp-preview-container {
        display: none;
    }

    section {
        margin: -15%;
        text-align: left;
        color: black;
    }

    #legalisir {
        height: 20em;
        /* background-color: rgba(255, 255, 255, 0.5); */
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
    }

    .legalisir {
        /* border-radius: 3%; */
        border: none;
        border-radius: 1em;
        padding-left: 3em;
        padding-right: 3em;
        padding-bottom: 2em;
        padding-top: 2em;
        background-color: white;
    }

    .form-control {
        border-radius: 0.9em;
        /* font-size: 14px; */
        padding: 10px;
    }

    .cari {
        width: 100%;
        border-radius: 0.9em;
        background-color: #299AA7;
        border-color: white;
    }

    .cari:hover {
        background-color: #A2C8A7;
    }

    .swal2-title,
    .swal2-content,
    .swal2-confirm,
    .data h3,
    section,
    .cari:hover,
    .cari,
    .form-control,
    .legalisir,
    #legalisir,
    .custom-select {
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
    }

    /* .select2-container--default .select2-selection--single {
        border-color: #e9ecef;
        height: 20px;
        vertical-align: middle;
    } */

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 45px;
    }
    .select2-container {
        width: 100% !important;
    }

    .select2 .selection .select2-selection--single,
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.9em !important;
        height: 3rem;
        font-family: 'Poppins', sans-serif;
    }

    .select2 .selection .select2-selection--single:focus {
        color: red;
    }

    .select2-search__field {
        padding-left: 0.8em !important;
    }

    .select2-search__field input {
        border: none !important;
    }

    .select2-results__option {
        color: #000;
        padding: 8px 16px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
    }

    .history {
        background-color: black;
        color: white;
        padding: 5px;
    }

    /* wrap text */
    table td {
        word-break: break-word;
        vertical-align: top;
        white-space: normal !important;
    }

    .btn-daftar {
        background-color: #077fd5;
        border-radius: 0.8em;
        width: 100%;
    }

    @media (min-width:460px) and (max-width: 700px) {
        .card {
            padding: 70px;
        }
    }

    @media (min-width:310px) and (max-width: 450px) {
        .card {
            padding: 50px;
        }

        .swal2-popup {
            font-size: 11px;
        }

        section,
        .btn,
        .card,
        .form-control {
            font-size: 11px;
        }

        .alert-danger {
            width: 80%;
        }
    }

    @media (max-width: 300px) {
        .card {
            padding: 10px;
        }

        .swal2-popup {
            font-size: 10px;
        }

        section,
        .btn,
        .card,
        .form-control {
            font-size: 10px;
        }

        .alert-danger {
            width: 80%;
        }
    }

    .form-group {
        margin-top: 0.5em;
    }

</style>
<section class="page-section">
    <div class="container mt-5" id="datasiswa">
        <div class="row ">
            <div class="card data">
                <div class="card-body p-4 mb-5">
                    <div class="row">
                        <div class="col-lg-10 mt-3 mx-auto">
                            <a href="{{ route('legalisir-dashboard') }}" class="btn cari text-white">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                        <h3 class="text-center mt-3">Pendaftaran Permohonan Data Ijazah Siswa</h3>
                        <div class="col-lg-10 mt-3 mx-auto">
                            @if(session('success'))
                            <div class="alert alert-info alert-dismissible fade show col-lg-12 text-center align-center mx-auto"
                                role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            @endif
                            @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show col-lg-12 text-center align-center mx-auto"
                                role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            @endif
                            <form class="pengajuan" action="{{ route('daftar-ijazah.storepengajuan') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row search">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="kotaid"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Provinsi') }}</label>
                                                <select id="provinsiid" class="col-md-12 custom-select form-control"
                                                    name='provinsiid' autofocus required>
                                                    <option value="">-- Pilih Provinsi --</option>
                                                    <option value="{{enum::PROVINSI_KEPRI}}">
                                                        {{  enum::PROVINSI_DESC_KEPRI }}</option>
                                                    <option value="{{enum::PROVINSI_LAINNYA}}">
                                                        {{  enum::PROVINSI_DESC_LAINNYA }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 kepri mt-2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="kotaid"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Kota/Kabupaten') }}</label>
                                                <select id="kotaid" class="col-md-12 custom-select form-control"
                                                    name='kotaid' autofocus>
                                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                                    @foreach ($kota as $item)
                                                    <option value="{{$item->kotaid}}">
                                                        {{  $item->kodekota . ' ' . $item->namakota }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 kepri mt-2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="kecamatanid"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Kecamatan') }}</label>
                                                <select id="kecamatanid" class="col-md-12 custom-select form-control"
                                                    name='kecamatanid' autofocus>
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                    @foreach ($kecamatan as $item)
                                                    <option value="{{$item->kecamatanid}}">
                                                        {{  $item->kodekec . ' ' . $item->namakec }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row search ">
                                    <div class="col-md-6 kepri">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="jenjang"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Jenjang') }}</label>
                                                <select id="jenjang" class="col-md-12 custom-select form-control"
                                                    name='jenjang' autofocus>
                                                    <option value="">-- Pilih Jenjang --</option>
                                                    <option value="{{enum::JENJANG_SMA}}">{{  enum::JENJANG_DESC_SMA }}
                                                    </option>
                                                    <option value="{{enum::JENJANG_SMK}}">{{  enum::JENJANG_DESC_SMK }}
                                                    </option>
                                                    <option value="{{enum::JENJANG_SLB}}">{{  enum::JENJANG_DESC_SLB }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 kepri">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="jenis"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Jenis') }}</label>
                                                <select id="jenis" class="col-md-12 custom-select form-control"
                                                    name='jenis' autofocus>
                                                    <option value="">-- Pilih Jenis --</option>
                                                    <option value="{{enum::JENIS_NEGERI}}">
                                                        {{  enum::JENIS_DESC_NEGERI }}</option>
                                                    <option value="{{enum::JENIS_SWASTA}}">
                                                        {{  enum::JENIS_DESC_SWASTA }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row search ">
                                    <div class="col-md-12 kepri">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="sekolahid"
                                                    class="col-md-12 col-form-label text-md-left">{{ __('Sekolah') }}</label>
                                                <select id="sekolahid"
                                                    class="col-md-12 custom-select form-control @error('sekolahid') is-invalid @enderror"
                                                    name='sekolahid' autofocus required>
                                                    <option value="">-- Pilih Sekolah --</option>
                                                    @foreach ($sekolah as $item)
                                                    <option value="{{$item->sekolahid}}">{{  $item->namasekolah }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('sekolahid')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="namasekolah">Nama Sekolah</label>
                                                <input type="text" class="form-control @error('namasekolah') is-invalid @enderror" id="namasekolah"
                                                    name="namasekolah" value="{{ old('namasekolah') }}" autofocus>
                                                @error('namasekolah')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="namaprov">Provinsi</label>
                                                <input type="text" class="form-control @error('namaprov') is-invalid @enderror" id="namaprov" name="namaprov" value="{{ old('namaprov') }}" autofocus>
                                                @error('namaprov')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="namakab">Kabupatan/Kota</label>
                                                <input type="text" class="form-control @error('namakab') is-invalid @enderror" id="namakab"
                                                    name="namakab" value="{{ old('namakab') }}" autofocus>
                                                @error('namakab')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="namakec">Kecamatan</label>
                                                <input type="text" class="form-control @error('namakec') is-invalid @enderror" id="namakec"
                                                    name="namakec" value="{{ old('namakec') }}" autofocus>
                                                @error('namakec')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="namasiswa">Nama Siswa</label>
                                                <input type="text"
                                                    class="form-control @error('namasiswa') is-invalid @enderror"
                                                    id="namasiswa" name="namasiswa" value="{{ old('namasiswa') }}"
                                                    autofocus required>
                                                @error('namasiswa')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="tempat_lahir">Tempat</label>
                                                <input type="text"
                                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                    id="tempat_lahir" name="tempat_lahir"
                                                    value="{{ old('tempat_lahir') }}" autofocus required>
                                                @error('tempat_lahir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="tgl_lahir">Tanggal Lahir</label>
                                                <input type="date"
                                                    class="form-control @error('tgl_lahir') is-invalid @enderror"
                                                    id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                                                    autofocus required>
                                                @error('tgl_lahir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="namaortu">Nama Orang Tua</label>
                                                <input type="text"
                                                    class="form-control @error('namaortu') is-invalid @enderror"
                                                    id="namaortu" name="namaortu" value="{{ old('namaortu') }}"
                                                    autofocus required>
                                                @error('namaortu')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="nis">Nomor Induk Siswa</label>
                                                <input type="text"
                                                    class="form-control @error('nis') is-invalid @enderror" id="nis"
                                                    name="nis" value="{{ old('nis') }}" autofocus required>
                                                @error('nis')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="noijazah">Nomor Ijazah</label>
                                                <input type="text"
                                                    class="form-control @error('noijazah') is-invalid @enderror"
                                                    id="noijazah" name="noijazah" value="{{ old('noijazah') }}"
                                                    autofocus required>
                                                @error('noijazah')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="tgl_lulus">Tanggal Kelulusan</label>
                                                <input type="date"
                                                    class="form-control @error('tgl_lulus') is-invalid @enderror"
                                                    id="tgl_lulus" name="tgl_lulus" value="{{ old('tgl_lulus') }}"
                                                    autofocus required>
                                                @error('tgl_lulus')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="file_ijazah"
                                                    class="form-label">Upload Ijazah</label>
                                                <br><span class="small text-danger">*File PDF | max : 5 MB</span>
                                                <input class="form-control  @error('file_ijazah') is-invalid @enderror"
                                                    type="file" id="file_ijazah" name="file_ijazah" required>
                                                @error('file_ijazah')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ijazah-preview-container">
                                    <iframe id="ijazah-preview" width="100%" height="600px"></iframe>
                                </div>
                                <div class="form-group row mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="file_ktp" class="form-label">Upload
                                                    KTP</label>
                                                <br><span class="small text-danger">*File PDF | max : 5 MB</span>
                                                <input class="form-control  @error('file_ktp') is-invalid @enderror"
                                                    type="file" id="file_ktp" name="file_ktp" required>
                                                @error('file_ktp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ktp-preview-container">
                                    <iframe id="ktp-preview" width="100%" height="600px"></iframe>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-info btn-daftar"
                                                    onclick="return confirm('Apakah Anda Yakin untuk Pendaftaran Permohonan Ijazah?')">Pendaftaran
                                                    Ijazah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        // $('.custom-select').removeClass('select2-bootstrap');
        $('.custom-select').select2({
            // theme: 'bootstrap'
        });

        $('.kepri').hide();
        $('.lainnya').hide();

        // On change event of the dropdown
        $('#provinsiid').change(function () {
            // Get the selected value
            var selectedValue = $(this).val();
            // Hide both classes initially
            // Show the corresponding class based on the selected value
            if (selectedValue === "{{enum::PROVINSI_KEPRI}}") {
                $('.kepri').show();
                $('.kepri #sekolahid').attr('required', 'required');
                $('#namasekolah').prop('disabled', true).removeAttr('required');
                $('#namaprov').prop('disabled', true).removeAttr('required');
                $('#namakec').prop('disabled', true).removeAttr('required');
                $('#namakab').prop('disabled', true).removeAttr('required');
            } else if (selectedValue === "{{enum::PROVINSI_LAINNYA}}") {
                $('.kepri').hide();
                $('.kepri #sekolahid').removeAttr('required');
                $('#namasekolah').prop('disabled', false).attr('required', 'required');
                $('#namaprov').prop('disabled', false).attr('required', 'required');
                $('#namakec').prop('disabled', false).attr('required', 'required');
                $('#namakab').prop('disabled', false).attr('required', 'required');
            }else{
                $('.kepri').hide();
                $('.lainnya').hide();
                $('#namasekolah').val('').prop('disabled', false).removeAttr('required');
                $('#namaprov').val('').prop('disabled', false).removeAttr('required');
                $('#namakec').val('').prop('disabled', false).removeAttr('required');
                $('#namakab').val('').prop('disabled', false).removeAttr('required');
            }
        });


        // FILTER SEKOLAH - START
        function fetchSchools() {
            let kecamatanValue = $('#kecamatanid').val()
            let jenjangValue = $('#jenjang').val();
            let kotaValue = $('#kotaid').val();
            let jenisValue = $('#jenis').val();
            var link = "{{ route('helper.getSekolahByKota') }}";
            $.ajax({
                url: link,
                data: {
                    kecamatanid: kecamatanValue,
                    jenjang: jenjangValue,
                    kotaid: kotaValue,
                    jenis: jenisValue
                },
                beforeSend: function() {
                // Menampilkan pesan "Loading..."
                    $("#sekolahid").html("");
                    var loadingOption = new Option("Loading...", "");
                        $("#sekolahid").append(loadingOption);
                },
            }).done(function (result) {
                let dataWr = result.data;
                if (dataWr) {
                    $("#sekolahid").html("");
                    var d = new Option("-- Semua Sekolah --", "");
                    $("#sekolahid").append(d);
                    dataWr.forEach((element) => {
                        var text = element.namasekolah; 
                        var o = new Option(text, element.sekolahid);
                        $("#sekolahid").append(o);
                    });
                }
            });
            // set ''
            $('#namasekolah').val('').prop('disabled', true);
            $('#namaprov').val('').prop('disabled', true);
            $('#namakec').val('').prop('disabled', true);
            $('#namakab').val('').prop('disabled', true);
        }
        // end function 
        $('#kotaid').change(function () {
            if (this.value) {
                $.ajax({
                    url: "{{ route('helper.getkecamatan', ':id') }}".replace(':id', this.value),
                }).done(function (result) {
                    let dataWr = result.data;
                    
                    $("#kecamatanid").html("");
                    var d = new Option("-- Semua Kecamatan --", "");
                    $("#kecamatanid").append(d);
            
                    if (dataWr) {
                        dataWr.forEach((element) => {
                            var text = element.kodekec + ' - ' + element.namakec; 
                            var o = new Option(text, element.kecamatanid);
                            $("#kecamatanid").append(o);
                        });
                    }
                });
            }else{

                $("#kecamatanid").html("");
                
                var dd = new Option("-- Pilih Kecamatan --", "");
                $("#kecamatanid").append(dd);
            }
            fetchSchools();
        });
        $('#kecamatanid').change(function () {
            fetchSchools();
        });
        $('#jenjang').change(function () {
            fetchSchools();
        });
        $('#jenis').change(function () {
            fetchSchools();
        });
        // GET NAMA SEKOLAH DLL 
        $('#sekolahid').change(function () {
            // Get the selected value
            var selectedValue = $(this).val();
            if (!selectedValue) {
                // Clear values and enable the input fields
                $('#namasekolah').val('').prop('disabled', true);
                $('#namaprov').val('').prop('disabled', true);
                $('#namakec').val('').prop('disabled', true);
                $('#namakab').val('').prop('disabled', true);
            } else {
                var url = "{{ route('helper.getdatasekolah', ':sekolahid') }}";
                    url = url.replace(':sekolahid', selectedValue);
                // Make an AJAX request to fetch the 'namasekolah' based on the selectedValue
                $.ajax({
                    url: url, // Replace with your actual backend endpoint
                    method: 'GET',
                    data: { sekolahid: selectedValue },
                    dataType: 'json',
                    success: function (response) {
                        // Update the 'namasekolah' input with the fetched data
                        $('#namasekolah').val(response.namasekolah).prop('disabled', true);
                        $('#namaprov').val(response.namaprov).prop('disabled', true);
                        $('#namakec').val(response.namakec).prop('disabled', true);
                        $('#namakab').val(response.namakab).prop('disabled', true);
                    },
                    error: function (error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        });
    });

    // scroll 
    document.addEventListener("DOMContentLoaded", function () {
        var scrollToElement = document.getElementById("datasiswa");
        if (scrollToElement) {
            var navbarHeight = document.querySelector('.navbar').offsetHeight;
            window.scrollTo({
                top: scrollToElement.offsetTop - navbarHeight,
                behavior: "smooth"
            });
        }
    });
    // cek extentions flie
    document.addEventListener('DOMContentLoaded', function () {
        const ijazahInput = document.getElementById('file_ijazah');
        const ijazahPreviewContainer = document.getElementById('ijazah-preview-container');
        const ijazahPreview = document.getElementById('ijazah-preview');
        const ktpInput = document.getElementById('file_ktp');
        const ktpPreviewContainer = document.getElementById('ktp-preview-container');
        const ktpPreview = document.getElementById('ktp-preview');

        ijazahInput.addEventListener('change', function () {
            const file = ijazahInput.files[0];

            if (file) {
                if (file.type === 'application/pdf') {
                    const fileReader = new FileReader();

                    fileReader.onload = function (e) {
                        ijazahPreview.src = e.target.result;
                        ijazahPreviewContainer.style.display =
                            'block'; // Tampilkan kontainer saat sudah ada file
                    };

                    fileReader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF.',
                        icon: 'info',
                    });

                    ijazahInput.value = ''; // Reset the input value
                    ijazahPreviewContainer.style.display = 'none';
                }

            } else {
                ijazahPreviewContainer.style.display =
                    'none'; // Sembunyikan kontainer jika tidak ada file
            }
        });
        ktpInput.addEventListener('change', function () {
            const file = ktpInput.files[0];

            if (file) {
                if (file.type === 'application/pdf') {
                    const fileReader = new FileReader();

                    fileReader.onload = function (e) {
                        ktpPreview.src = e.target.result;
                        ktpPreviewContainer.style.display =
                            'block'; // Tampilkan kontainer saat sudah ada file
                    };

                    fileReader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF.',
                        icon: 'info',
                    });
                    ktpInput.value = ''; // Reset the input value
                    ktpPreviewContainer.style.display = 'none';
                }

            } else {
                ktpPreviewContainer.style.display = 'none'; // Sembunyikan kontainer jika tidak ada file
            }
        });
    });

</script>

@endsection
