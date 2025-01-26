<?php
use App\enumVar as enum;
use App\Helpers\Get_field;
?>
@extends('layouts.master')

@section('content')
<style>

    .imageThumb {
    max-height: 75px;
    border: 2px solid;
    padding: 1px;
    cursor: pointer;
    }
    .pip {
    display: inline-block;
    margin: 10px 10px 0 0;
    }
    .remove {
    display: block;
    background: #444;
    border: 1px solid black;
    color: white;
    text-align: center;
    cursor: pointer;
    }
    .remove:hover {
    background: white;
    color: black;
    }

    .param_img_holder {
    display: none;  
    }

    .param_img_holder img.img-fluid {
    width: 110px;
    height: 70px;
    margin-bottom: 10px;
    }

    .dataTables_filter {
        display: none;
    }

    div.dt-buttons {
        float: right;
    }

    /* #detail-laporan-table {
        display: none;
    } */
    .btn-view-pengajuan:hover{
        background-color: rgb(24, 106, 154);
    }
    .modal {
        overflow-y:auto;
    }

    /* tbody {
        display:block;
        height:80vh;
        overflow:auto;
    }
    thead, tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;
    }
    thead {
        width: calc( 100% - 1em )
    } */
</style>
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">DAFTAR PROGRES FISIK</h5>
        <hr />
        <form class="form-material">
            <div class="form-group row">
                <div class="col-md-6 ">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="perusahaanid" class="col-md-12 col-form-label text-md-left">{{ __('Perusahaan') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="perusahaanid" class="col-md-12 custom-select form-control" name='perusahaanid' autofocus {{ $isPerusahaan ? 'disabled' : '' }}>
                                <option  value="{{ $isPerusahaan ? $userPerusahaan->perusahaanid : ''}}">{{ $isPerusahaan ? $userPerusahaan->nama : '-- Pilih Perusahaan --' }}</option>
                                @foreach ($perusahaan as $item)
                                    <option value="{{$item->perusahaanid}}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="kotaid" class="col-md-12 col-form-label text-md-left">{{ __('Kota/Kabupaten') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="kotaid" class="col-md-12 custom-select form-control" name='kotaid' autofocus>
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                @foreach ($kota as $item)
                                    <option value="{{$item->kotaid}}">{{  $item->kodekota . ' ' . $item->namakota }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="jenjang" class="col-md-12 col-form-label text-md-left">{{ __('Jenjang') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="jenjang" class="col-md-12 custom-select form-control" name='jenjang' autofocus>
                                <option value="">-- Pilih Jenjang --</option>
                                    <option value="{{enum::JENJANG_SMA}}">{{  enum::JENJANG_DESC_SMA }}</option>
                                    <option value="{{enum::JENJANG_SMK}}">{{  enum::JENJANG_DESC_SMK }}</option>
                                    <option value="{{enum::JENJANG_SLB}}">{{  enum::JENJANG_DESC_SLB }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="jenis" class="col-md-12 col-form-label text-md-left">{{ __('Jenis') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="jenis" class="col-md-12 custom-select form-control" name='jenis' autofocus>
                                <option value="">-- Pilih Jenis --</option>
                                    <option value="{{enum::JENIS_NEGERI}}">{{  enum::JENIS_DESC_NEGERI }}</option>
                                    <option value="{{enum::JENIS_SWASTA}}">{{  enum::JENIS_DESC_SWASTA }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="sekolahid" class="col-md-12 col-form-label text-md-left">{{ __('Sekolah') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="sekolahid" class="col-md-12 custom-select form-control" name='sekolahid' autofocus {{ $isSekolah ? 'disabled' : '' }}>
                                <option value="{{ $isSekolah ? $userSekolah->sekolahid : ''}}">{{ $isSekolah ? $userSekolah->namasekolah : '-- Pilih Sekolah --' }}</option>
                                @foreach ($sekolah as $item)
                                    <option value="{{$item->sekolahid}}">{{  $item->namasekolah }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="search" class="col-md-12 col-form-label text-md-left">{{ __('Filter') }}</label>
                        </div>
                        <div class="col-md-9">
                            <input id="search" type="text" class="col-md-12 form-control" name="search" value="{{ old('search') }}" maxlength="100" autocomplete="search" placeholder="-- Filter --">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="form-group row">
            <div class="col-md-12">
                @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                <p class="alert alert-danger alert-dismissible fade show" role="alert">{{ $error }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </p>
                @endforeach
                @endif

                @if (session()->has('success'))
                <p class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="overflow-x: auto">
                {{-- <h3 class="card-title text-uppercase">PENGANGGARAN</h3> --}}
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="kebutuhan-sarpras-table">
                        <thead>
                            <tr>
                                <th>Jenis Kebutuhan</th>
                                <th>Nama Sekolah</th>
                                <th>Jenis Pagu</th>
                                <th>Perusahaan</th>
                                <th>Sub Kegiatan</th>
                                <th>Sub Detail Kegiatan</th>
                                <th>No Kontrak</th>
                                <th>Nilai Kontrak</th>
                                <th>Progres</th>
                                <th>Selesai</th>
                            </tr>
                        </thead>
                        <tbody style="overflow-y: auto">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card p-4 mt-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <h3 class="card-title text-uppercase">DETAIL LAPORAN</h3>
                    <hr>
                    <table class="table table-bordered yajra-datatable table-striped" id="detail-laporan-table">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="2">Minggu</th>
                                <th class="text-center" colspan="2">Waktu Pengerjaan</th>
                                <th class="text-center" rowspan="2">Progres</th>
                                <th class="text-center" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="text-center">Dari</th>
                                <th class="text-center">Sampai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal tambah -->
<div class="modal" id="modal-detail-laporan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-laporan"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="detailLaporan-form" name="detailLaporan-form" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    <input type="hidden" id="detailpaguanggaranid" name="detailpaguanggaranid">
                    <input type="hidden" name="detail_mode" id="detail_mode"/>
                    <div class="form-group">
                        <label for="bulan" class="control-label">Bulan:</label>
                        <select id="detail_bulan" class="custom-select-edit-detail form-control" name='bulan'>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach (enum::listBulan() as $id)
                            <option value="{{ $id }}">{{ enum::listBulan('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="minggu" class="control-label">Minggu:</label>
                        <select id="detail_minggu" class="custom-select-edit-detail form-control" name='minggu'>
                            <option value="">-- Pilih Minggu --</option>
                            @foreach (enum::listMinggu() as $id)
                            <option value="{{ $id }}">{{ enum::listMinggu('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="daritgl" class="control-label">Dari Tanggal:</label>
                        <input id="detail_daritgl" type="date" class="form-control @error('daritgl') is-invalid @enderror" name="daritgl" value="{{ (old('daritgl')) }}" maxlength="100" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="sampaitgl" class="control-label">Sampai Tanggal:</label>
                        <input id="detail_sampaitgl" type="date" class="form-control @error('sampaitgl') is-invalid @enderror" name="sampaitgl" value="{{ (old('sampaitgl')) }}" maxlength="100" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="target" class="control-label">Target (%):</label>
                        <input id="detail_target" type="number" step="any" class="form-control @error('target') is-invalid @enderror" name="target" value="{{ (old('target')) }}" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="progres" class="control-label">Progres (%):</label>
                        <input id="detail_progres" type="number" step="any" class="form-control @error('progres') is-invalid @enderror" name="progres" value="{{ (old('progres')) }}" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="progres" class="control-label">Keterangan:</label>
                        <textarea class="form-control @error('progres') is-invalid @enderror" name="keterangan" id="detail_keterangan" cols="30" rows="10"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button value="btnSubmit" type="submit" id="btnSubmit" class="btn btn-primary btnSubmit"><i class="icon wb-plus" aria-hidden="true"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal detail jumlah -->
<div class="modal" id="modal-detail-parent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px; overflow-y:auto;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-parent">Lihat Data Peralatan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="detail-parent-form" name="detail-parent-form" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    <input type="hidden" id="detailpaguanggaranid" name="detailpaguanggaranid">
                    <input type="hidden" name="detail_mode_parent" id="detail_mode_parent"/>

                    <table class="table table-bordered yajra-datatable table-striped" id="detailjumlah-table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Peralatan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">jenisperalatanid</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="modal-footer" id="btn-selesai-container">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Selesai</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal form detail jumlah -->
<div class="modal" id="modal-detail-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px; overflow-y:auto;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-form"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="detail-form" name="detail-form" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    <input type="hidden" id="detailpaguanggaranid" name="detailpaguanggaranid">
                    <input type="hidden" name="detail_jumlah_mode" id="detail_jumlah_mode"/>
                    <input type="hidden" name="detail_jumlah_data" id="detail_jumlah_data"/>
                    <input type="hidden" name="detail_jumlah_datadel" id="detail_jumlah_datadel"/>
                    <div class="form-group">
                        <label for="jenisperalatanid" class="control-label">Jenis Peralatan:</label>
                        <select id="detail_jumlah_jenisperalatanid" class="custom-select-detail-jumlah form-control" name='jenisperalatanid'>
                            <option value="">-- Jenis Peralatan --</option>
                            @foreach ($jenisperalatan as $item)
                            <option value="{{ $item->jenisperalatanid }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah" class="control-label">Jumlah:</label>
                        <input id="detail_jumlah_jumlah" type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ (old('jumlah')) }}" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="satuan" class="control-label">Satuan:</label>
                        <input id="detail_jumlah_satuan" type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan" value="{{ (old('satuan')) }}" autocomplete="name">
                    </div>
                    <div id="form-file-container" class="form-group">
                        <label for="file" class="control-label">File:</label>
                        <input id="detail_jumlah_file" type="file" class="form-control file-input" name="file[]" multiple /><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 2MB</span>
                        <div class="param_img_holder d-flex justify-content-center align-items-center">
                        </div>
                    </div>
                    <div id="edit-peralatan-table-container">
                        <table class="table table-bordered yajra-datatable table-striped" id="detail-edit-peralatan-table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">File</th>
                                    <th class="text-center" id="th-detail-form">Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-save" id="">OK</button>
                        {{-- <button type="button" id="btn_simpan_detail" class="btn btn-primary"><i class="icon wb-plus"></i>Simpan --}}
                        {{-- </button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal detail peralatan -->
<div class="modal" id="modal-detail-peralatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px; overflow-y:auto;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-peralatan"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered yajra-datatable table-striped" id="detail-peralatan-table" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">File</th>
                            <th class="text-center" id="th-detail-peralatan">Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- modal detail foto peralatan -->
<div class="modal" id="modal-detail-foto-peralatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px; overflow-y:auto;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-foto-peralatan"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="detail-foto-peralatan-form" name="detail-foto-peralatan-form" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="detail_foto_mode" id="detail_foto_mode">
                    <div class="form-group">
                        <label for="file" class="control-label">File:</label>
                        <input id="file" type="file" class="form-control file-input" name="file" /><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 5MB</span>
                        <div class="param_img_holder d-flex justify-content-center align-items-center">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Simpan</button>
                        {{-- <button type="button" id="btn_simpan_detail" class="btn btn-primary"><i class="icon wb-plus"></i>Simpan --}}
                        {{-- </button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- foo table -->
<script src="{{asset('/dist/js/pages/footable-init.js')}}"></script>
<script src="{{asset('/dist/plugins/bower_components/footable/js/footable.all.min.js')}}"></script>
<script src="{{asset('/dist/plugins/bower_components/bootstrap-select/bootstrap-select.min.js')}}" type="text/javascript"></script>

<script>
    
</script>

<!-- validasi jumlah yang disetujui -->
<script>

    // $('table').on("focus", ".nilaipagu", function(){
    //     $( '.nilaipagu' ).mask('000,000,000,000,000', {reverse: true});
    //     // $('.nilaipagu').val(+Number($('.nilaipagu').val().replace(/[^0-9.-]+/g,"")))
    // })
    // $('table').on("blur", ".nilaipagu", function(){
    //     $('.nilaipagu').val(+Number($('.nilaipagu').val().replace(/[^0-9.-]+/g,"")))
    // })

    // $('table').on('blur', 'nilaipagu', function() {
    //     const value = this.value.replace(/,/g, '');
    //     this.value = parseFloat(value).toLocaleString('en-US', {
    //         style: 'decimal',
    //         maximumFractionDigits: 2,
    //         minimumFractionDigits: 2
    //     });
    // });
    function changeHandler(val)
    {
        if (Number(val.value) > 100)
        {
        val.value = 100
        }
    }

    function compareJumlah() {
        var jumlahSetuju = $('#jumlahsetuju').val();
        var jumlah = $('#jumlah').val();
        if (!isNaN(jumlahSetuju) && jumlahSetuju > jumlah) {
            // alert("The first date is after the second date!");
            swal.fire('Peringatan!', `Jumlah yang disetujui tidak boleh melebihi dari jumlah yang diajukan (${jumlah})`, 'warning');
            $('#jumlahsetuju').val('');
        }
    }

    $(document).on("change", ".count-pagu", function() {
        var sum = 0;
        $(".count-pagu").each(function(){
            sum += +Number($(this).val().replace(/[^0-9.-]+/g,""));
        });
        console.log(sum)
        $(".total").val(sum);
        $(".total").text(rupiah(sum));
        $(".rupiahTerbilang").text(`(${terbilang(sum)})`);
    });
    $(document).on("click", ".delete-row-btn", function() {
        var sum = 0;
        $(".count-pagu").each(function(){
            sum += +Number($(this).val().replace(/[^0-9.-]+/g,""));
        });
        $(".total").val(sum);
        $(".total").text(rupiah(sum));
        $(".rupiahTerbilang").text(`(${terbilang(sum)})`);
    });
</script>
<script>

    $(document).ready(function () {

        $('.custom-select-detail').select2({
            dropdownParent: $('#modal-detailanggaran')
        });

        $('.custom-select-edit').select2({
            dropdownParent: $('#modal-detail-pagu-penganggaran')
        });
        $('.custom-select-edit-detail').select2({
            dropdownParent: $('#modal-detail-laporan .modal-content')
        });
        $('.custom-select-detail-jumlah').select2({
            dropdownParent: $('#modal-detail-form')
        })

        $('.custom-select').select2();

const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

$('form').on('change', '.file-input', function(e) {
  const $input = $(this);
  const imgPath = $input.val();
  const $imgPreview = $input.closest('form').find('.param_img_holder');
  const extension = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

var clickedButton = this;
var files = e.target.files,
filesLength = files.length;

  if (typeof(FileReader) == 'undefined') {
    $imgPreview.html('This browser does not support FileReader');
    return;
  }

  for (let i = 0; i < filesLength; i++) {
    
    var f = files[i];

    if (validExtensions.includes(extension)) {
        $imgPreview.empty();
        var reader = new FileReader();
        reader.onload = function(e) {
            var file = e.target
            // $('<iframe/>', {
            //     src: e.target.result,
            //     height: 60,
            //     width: 60,
            // }).appendTo($imgPreview);

            $("<div class=\"pip\">" +
            "<img width=\"100%\" height=\"600px\" class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\" />" +
            // "<br/><div class=\"remove\">Remove image</div>" +
            "</div>").appendTo($imgPreview);
            // $(".remove").click(function(){
            //     $(this).parent(".pip").remove();
            // });
        }
        // $imgPreview.show();
        // reader.readAsDataURL($input[0].files[0]);
        reader.readAsDataURL(f);
  } else {
    $imgPreview.empty();
    swal.fire('Peringatan!', 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.', 'warning');

        // remove file ketika validasi ekstensi file gagal
        if(this.value){
            try{
                this.value = ''; //for IE11, latest Chrome/Firefox/Opera...
            }catch(err){ }
            if(this.value){ //for IE5 ~ IE10
                var form = document.createElement('form'),
                    parentNode = this.parentNode, ref = this.nextSibling;
                form.appendChild(this);
                form.reset();
                parentNode.insertBefore(this,ref);
            }
        }
  }

  }
});

        // FILTER SEKOLAH - START
        $('#kotaid').select2().on('select2:select', function() {

            var urlSekolahKota = "{{ route('helper.getsekolahkota', ':id') }}";
            urlSekolahKota = urlSekolahKota.replace(':id', ($('#kotaid').val() == "" || $('#kotaid').val() == null ? "-1" : $('#kotaid').val()));

            $.ajax({
                url: urlSekolahKota,
                type: "GET",
                success: function(data) {
                    $('#sekolahid').empty();
                    $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                    $.each(data.data, function(key, value) {
                        $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.npsn + ' - ' + value.namasekolah));
                    });
                    $('#sekolahid').select2();
                    // $('#sekolahid').val(sekolahid);
                    $('#sekolahid').trigger('change');
                }
            })

            var url = "{{ route('helper.getkecamatan', ':id') }}";
            url = url.replace(':id', ($('#kotaid').val() == "" || $('#kotaid').val() == null ? "-1" : $('#kotaid').val()));

            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    $('#kecamatanid').empty();
                    $('#kecamatanid').append($("<option></option>").attr("value", "").text("-- Pilih Kecamatan --"));
                    $.each(data.data, function(key, value) {
                        $('#kecamatanid').append($("<option></option>").attr("value", value.kecamatanid).text(value.kodekec + ' - ' + value.namakec));
                    });
                    $('#kecamatanid').select2();
                    // $('#kecamatanid').val(kecamatanid);
                    $('#kecamatanid').trigger('change');
                }
            })

            if($('#kecamatanid').val() == '' && $('#jenjang').val() != '' && $('#jenis').val() != '') {
                var urlSekolahKotaJenjangJenis = "{{ route('helper.getSekolahKotaJenjangJenis', ['kotaid' => ':kotaid', 'jenjang' => ':jenjang', 'jenis' => ':jenis']) }}";
                urlSekolahKotaJenjangJenis = urlSekolahKotaJenjangJenis.replace(':kotaid', $('#kotaid').val() == "" );
                urlSekolahKotaJenjangJenis = urlSekolahKotaJenjangJenis.replace(':jenjang', $('#jenjang').val() == "" );
                urlSekolahKotaJenjangJenis = urlSekolahKotaJenjangJenis.replace(':jenis', $('#jenis').val() == "" );

                $.ajax({
                    url: urlSekolahKotaJenjangJenis,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.npsn + ' - ' + value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                    }
                })
            }
        });

        $('#kecamatanid').select2().on('select2:select', function() {
            var jenis = $('#jenis').val();
            var jenjang = $('#jenjang').val();
            url = "{{ route('helper.getsekolahjenis', ['jenis' => ':jenis', 'jenjang' => ':jenjang', 'kecamatanid' => ':kecamatanid']) }}";
            url = url.replace(':jenis', ($('#jenis').val() == "" || $('#jenis').val() == null ? "-1" : $('#jenis').val()));
            url = url.replace(':jenjang', ($('#jenjang').val() == "" || $('#jenjang').val() == null ? "-1" : $('#jenjang').val()));
            url = url.replace(':kecamatanid', ($('#kecamatanid').val() == "" || $('#kecamatanid').val() == null ? "-1" : $('#kecamatanid').val()))
            // var url = "{{ route('helper.getkecamatan', ':id') }}";
            // url = url.replace(':id', ($('#kecamatanid').val() == "" || $('#kecamatanid').val() == null ? "-1" : $('#kecamatanid').val()));

            var urlSekolah = "{{ route('helper.getsekolah', ':id') }}";
            urlSekolah = urlSekolah.replace(':id', $('#kecamatanid').val())

            $.ajax({
                url: urlSekolah,
                type: "GET",
                success: function(data) {
                    $('#sekolahid').empty();
                    $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                    $.each(data.data, function(key, value) {
                        $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                    });
                    $('#sekolahid').select2();
                    // $('#sekolahid').val(sekolahid);
                    $('#sekolahid').trigger('change');
                    $('#detail-sarpras-table').hide();
                    $('#detail-jumlah-sarpras-table').hide();

                }
            })

            if($('#kecamatanid').val() != '' && $('#jenjang').val() != '' && $('#jenis').val() != ''){
                var urlSekolahJenjangJenis = "{{ route('helper.getsekolahjenis', ['jenis' => ':jenis', 'jenjang' => ':jenjang', 'kecamatanid' => ':kecamatanid']) }}";
                urlSekolahJenjangJenis = urlSekolahJenjangJenis.replace(':jenis', $('#jenis').val());
                urlSekolahJenjangJenis = urlSekolahJenjangJenis.replace(':jenjang', $('#jenjang').val());
                urlSekolahJenjangJenis = urlSekolahJenjangJenis.replace(':kecamatanid', $('#kecamatanid').val());

                $.ajax({
                    url: urlSekolahJenjangJenis,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                        $('#detail-sarpras-table').hide();
                        $('#detail-jumlah-sarpras-table').hide();
                    }
                })
            }
        });

        $('#jenjang').select2().on('select2:select', function() {
            if($('#kotaid').val() != '') {
                var urlSekolahKotaJenjang = "{{ route('helper.getSekolahKotaJenjang1', ['kotaid' => ':kotaid', 'jenjang' => ':jenjang']) }}";
                urlSekolahKotaJenjang = urlSekolahKotaJenjang.replace(':kotaid', $('#kotaid').val());
                urlSekolahKotaJenjang = urlSekolahKotaJenjang.replace(':jenjang', $('#jenjang').val());

                $.ajax({
                    url: urlSekolahKotaJenjang,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                        $('#detail-sarpras-table').hide();
                        $('#detail-jumlah-sarpras-table').hide();

                    }
                })
            }
            if($('#kotaid').val() == '') {
                var urlSekolahJenjang = "{{ route('helper.getsekolahjenjang2', ['jenjang' => ':jenjang']) }}";
                urlSekolahJenjang = urlSekolahJenjang.replace(':jenjang', $('#jenjang').val());

                $.ajax({
                    url: urlSekolahJenjang,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                        $('#detail-sarpras-table').hide();
                        $('#detail-jumlah-sarpras-table').hide();
                    }
                })
            }
        });

        $('#jenis').select2().on('select2:select', function(){
            if($('#kotaid').val() == '') {
                var urlSekolahJenisKec = "{{ route('helper.getsekolahjenisjenjang', ['jenis' => ':jenis', 'jenjang' => ':jenjang']) }}";
                urlSekolahJenisKec = urlSekolahJenisKec.replace(':jenis', $('#jenis').val());
                urlSekolahJenisKec = urlSekolahJenisKec.replace(':jenjang', $('#jenjang').val());

                $.ajax({
                    url: urlSekolahJenisKec,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                        $('#detail-sarpras-table').hide();
                        $('#detail-jumlah-sarpras-table').hide();

                    }
                })
            }
            if($('#kotaid').val() == '' && $('#jenjang').val() == ''){
                var urlSekolahJenis = "{{ route('helper.getsekolahjenis2', ':jenis') }}";
                urlSekolahJenis = urlSekolahJenis.replace(':jenis', $('#jenis').val());

                $.ajax({
                    url: urlSekolahJenis,
                    type: "GET",
                    success: function(data) {
                        $('#sekolahid').empty();
                        $('#sekolahid').append($("<option></option>").attr("value", "").text("-- Pilih Sekolah --"));
                        $.each(data.data, function(key, value) {
                            $('#sekolahid').append($("<option></option>").attr("value", value.sekolahid).text(value.namasekolah));
                        });
                        $('#sekolahid').select2();
                        // $('#sekolahid').val(sekolahid);
                        $('#sekolahid').trigger('change');
                        $('#detail-sarpras-table').hide();
                        $('#detail-jumlah-sarpras-table').hide();

                    }
                })
            }
        });
        // FILTER SEKOLAH - END


        $('#kotaid').change(function () {
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });
        $('#kecamatanid').change(function () {
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });
        $('#jenjang').change(function () {
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });
        $('#jenis').change(function () {
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });
        $('#perusahaanid').change(function () {
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });
        $('#sekolahid').change( function() { 
            kebutuhansarprastable.draw();
            $('#detail-laporan-table').hide();
        });

        $('#search').keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#search').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                kebutuhansarprastable.draw();
                 $('#detail-laporan-table').hide();
            }
        });
    });

    // function setenabledtbutton(option) {
    //     detailPaguPenganggaranTable.buttons( '.view' ).disable();
    //     //detailPaguPenganggaranTable.buttons( '.print' ).disable();
    //     detailPaguPenganggaranTable.buttons( '.add' ).disable();
    //     detailPaguPenganggaranTable.buttons( '.edit' ).disable();
    //     detailPaguPenganggaranTable.buttons( '.delete' ).disable();

    //     if (option == "0") {
    //         detailPaguPenganggaranTable.buttons( '.view' ).enable();
    //         detailPaguPenganggaranTable.buttons( '.add' ).enable();
    //         detailPaguPenganggaranTable.buttons( '.edit' ).enable();
    //         detailPaguPenganggaranTable.buttons( '.delete' ).enable();
    //     }
    //     else if (option == "1") {
    //         detailPaguPenganggaranTable.buttons( '.view' ).enable();
    //         detailPaguPenganggaranTable.buttons( '.print' ).enable();
    //     }
    //     else if (option == "3" || option == "5" || option == "2" || option == "4" || option == "6") {
    //         detailPaguPenganggaranTable.buttons( '.view' ).enable();
    //     }
    // }

    var v_listDataDetail = [];
    var v_listDetailDeleted = [];

    var detailjumlahtable = $('#detailjumlah-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        pageLength: 50,
        dom: 'Bfrtip',
        select: true,
        ordering: false,
        language: {
            lengthMenu: "Menampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data",
            info: "Halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            search: "Pencarian :",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya",
            }
        },
        buttons: {
            buttons: [
            {
                text: 'Lihat Foto',
                className: 'view btn btn-primary btn-sm btn-datatable',
                action: function () {
                    if (detailjumlahtable.rows( { selected: true } ).count() <= 0) {
                        swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dilihat", "error");
                        return;
                    }
                    
                    $('#modal-detail-peralatan').modal('show');

                    var rowData = detailjumlahtable.rows( { selected: true } ).data()[0];
                    var detailjumlahperalatanid = rowData.detailjumlahperalatanid;
                    var jenisperalatanid = rowData.jenisperalatanid;

                    var listJenisPeralatan = @json($jenisperalatan);
                        // let listJenisPeralatan = JSON.parse('{!! json_encode(enum::listJenisSarpras()) !!}');
                        let namaJenisPeralatan;
                        listJenisPeralatan.forEach((value, index) => {
                            if(jenisperalatanid == value.jenisperalatanid) {
                                namaJenisPeralatan = value.nama;
                            }
                        });

                    loadFotoJenisPeralatan(detailjumlahperalatanid);
                    $("#modal-title-detail-peralatan").html('Lihat Foto ' + namaJenisPeralatan);
                    $('#th-detail-form').html(namaJenisPeralatan);


                }
            },
            {
                text: 'Tambah',
                className: 'add btn btn-info btn-sm btn-datatable',
                action: function () {
                    showmodaldetailjumlah('add');
                }
            },
            {
                text: 'Ubah',
                className: 'edit btn btn-warning btn-sm btn-datatable',
                action: function () {
                    if (detailjumlahtable.rows( { selected: true } ).count() <= 0) {
                        swal.fire("Data belum dipilih", "Silahkan pilih data yang akan diubah", "error");
                        return;
                    }
                    
                    showmodaldetailjumlah('edit');
                }
            },
            {
                text: 'Hapus',
                className: 'delete btn btn-danger btn-sm btn-datatable',
                action: function () {
                    if (detailjumlahtable.rows( { selected: true } ).count() <= 0) {
                        swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                        return;
                    }

                    var rowData = detailjumlahtable.rows( { selected: true } ).data()[0];
                    var detailjumlahperalatanid = rowData.detailjumlahperalatanid;

                    let url = "{{ route('progresfisik.deleteDetailJumlahPeralatan', ':id') }}"
                    url = url.replace(':id', detailjumlahperalatanid);

                    swal.fire({   
                        title: "Apakah anda yakin akan menghapus data ini?",   
                        text: "Data yang terhapus tidak dapat dikembalikan lagi!",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "Ya, lanjutkan!",   
                        closeOnConfirm: false 
                    }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                type: "POST",
                                cache:false,
                                url: url,
                                dataType: 'JSON',
                                data: {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(json){
                                    var success = json.success;
                                    var message = json.message;
                                    var data = json.data;
                                    console.log(data);
                                    
                                    if (success == 'true' || success == true) {
                                        swal.fire("Berhasil!", "Data anda telah dihapus.", "success"); 

                                        var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                                        var detailpenganggaranid = rowData.detailpenganggaranid;

                                        loadDetailJenisPeralatan(detailpenganggaranid);
                                    }
                                    else {
                                        swal.fire("Error!", data, "error"); 
                                    }
                                }
                            });   
                        }          
                    });
                }
            },
            ]
        },
        columns: [
                // {'orderData': 1, data: 'jenispagu', name: 'jenispagu',
                //     render: function(data, type, row) {
                //         if(row.jenispagu != null){
                //             if (row.jenispagu == "{{ enum::JENIS_PAGU_KONSULTAN_PERENCANAAN }}") {
                //                 return 'Konsultan Perencanaan'
                //             } else if (row.jenispagu == "{{ enum::JENIS_PAGU_KONSULTAN_PENGAWAS }}") {
                //                 return 'Konsultan Pengawasan'
                //             }
                //             else if (row.jenispagu == "{{ enum::JENIS_PAGU_BANGUNAN }}") {
                //                 return 'Bangunan'
                //             }
                //             else {
                //                 return 'Pengadaan'
                //             }
                //         }
                //     }
                // },
                {'orderData': 1, data: 'namaJenisPeralatan', name: 'namaJenisPeralatan',
                    render: (data, type, row) => {
                        if(row.namaJenisPeralatan != null) {
                            return row.namaJenisPeralatan;
                        }else{
                            return '---';
                        }
                    }
                },
                {'orderData': 2, data: 'jumlah', name: 'jumlah',
                    render: (data, type, row) => {
                        if(row.jumlah != null) {
                            return row.jumlah;
                        }else{
                            return '---';
                        }
                    }
                },
                {'orderData': 3, data: 'satuan', name: 'satuan',
                    render: function(data, type, row) {
                        if(row.satuan != null) {
                            return row.satuan
                        }else{
                            return '---'
                        }
                    }
                },
                {'orderData': 4, data: 'jenisperalatanid', name: 'jenisperalatanid', visible: false,
                    render: (data, type, row) => {
                        if(row.jenisperalatanid != null) {
                            return row.jenisperalatanid;
                        }else{
                            return '---';
                        }
                    }
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            drawCallback: function( settings ) {
                $("#detailjumlah-table").wrap( "<div class='table-responsive'></div>" );
            }
    });

        function resettablejumlah() {
            var v_listDataDetail = [];
            var v_listDetailDeleted = [];
            reloadTableDetailJumlah();
        }

        $("#btn_simpan_detail").click(function(){
            simpandatadetailjumlah();
        });

        function resetformdetailjumlah() {
            $("#detail-form")[0].reset();
            var v_max = 1;
            // if (v_listDataDetail.length > 0) {
            //     var v_maxobj = v_listDataDetail.reduce((prev, current) => (prev && prev.nourut > current.nourut) ? prev : current);
            //     v_max = parseInt(v_maxobj.nourut)+1;
            // }
            // $("#detail_detail_nourut").val(v_max);
            //alert(v_listDataDetail.length);
            //alert(v_listDataDetail.length + '->' + JSON.stringify(max));

            $('span[id^="err_detail_jumlah_"]', "#detail-form").each(function(index, el){
                $('#'+el.id).html("");
            });

            $('select[id^="detail_jumlah_"]', "#detail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("").trigger('change');
                }
            });
        }

        function bindformdetailjumlah() {
            $('textarea[id^="detail_jumlah_"]', "#detail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailjumlahtable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('input[id^="detail_jumlah_"]', "#detail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailjumlahtable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('select[id^="detail_jumlah_"]', "#detail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailjumlahtable.rows( { selected: true } ).data()[0][inputname]).trigger('change');
                }
            });
        }

        function setenableddetailjumlah(value) {
            if (value) {
                $("#btn_simpan").show();
            }
            else {
                $("#btn_simpan").hide();
            }
            
            $('textarea[id^="detail_"]', "#aktivitashariandetail-form").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('input[id^="detail_"]', "#aktivitashariandetail-form").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('select[id^="detail_"]', "#aktivitashariandetail-form").each(function(index, el){
                $("#"+el.id).prop("disabled", !value);
            });
        }

        var v_modedetail = "";
        function showmodaldetailjumlah(mode) {
            v_modedetail = mode;
            $("#detail_jumlah_mode").val(mode);
            resetformdetailjumlah();
            if (mode == "add") {
                $("#modal-title-detail-form").html('Tambah Data');
                $('#form-file-container').removeClass('d-none');
                $('#edit-peralatan-table-container').addClass('d-none');
                setenableddetailjumlah(true);
            }
            else if (mode == "edit") {
                $("#modal-title-detail-form").html('Ubah Data');
                $('#form-file-container').addClass('d-none');
                $('#edit-peralatan-table-container').removeClass('d-none');
                bindformdetailjumlah();
                setenableddetailjumlah(true);
                var rowData = detailjumlahtable.rows( { selected: true } ).data()[0];
                var detailjumlahperalatanid = rowData.detailjumlahperalatanid;
                var jenisperalatanid = rowData.jenisperalatanid;

                var listJenisPeralatan = @json($jenisperalatan);
                    // let listJenisPeralatan = JSON.parse('{!! json_encode(enum::listJenisSarpras()) !!}');
                    let namaJenisPeralatan;
                    listJenisPeralatan.forEach((value, index) => {
                        if(jenisperalatanid == value.jenisperalatanid) {
                            namaJenisPeralatan = value.nama;
                        }
                    });

                loadEditFotoJenisPeralatan(detailjumlahperalatanid);
                $('#th-detail-peralatan').html('Foto ' + namaJenisPeralatan);
            }
            else {
                $("#modal-title-detail-form").html('Lihat Data');
                bindformdetailjumlah();
                setenableddetailjumlah(false);
            }
            
            $("#modal-detail-form").modal('show');
        }

        function hidemodaldetailjumlah() {
            $("#modal-detail-form").modal('hide');
        }
        
        var v_detailTmpId = 0;
        function simpandatadetailjumlah() {
            if ($('#detail_jumlah_jenisperalatanid').val() == "") {
                swal("Jenis Peralatan Harus Diisi", "Silakan isi jenis peralatan terlebih dahulu", "warning");
                return;
            }
            if ($('#detail_jumlah_jumlah').val() == "") {
                swal("Jumlah harus diisi", "Silahkan isi jumlah terlebih dahulu", "warning");
                return;
            }
            if ($('#detail_jumlah_satuan').val() == "") {
                swal("satuan harus diisi", "Silahkan isi satuan terlebih dahulu", "warning");
                return;
            }

            var listJenisPeralatan = @json($jenisperalatan);
            // let listJenisPeralatan = JSON.parse('{!! json_encode(enum::listJenisSarpras()) !!}');
            let namaJenisPeralatan;
            listJenisPeralatan.forEach((value, index) => {
                if($('#detail_jumlah_jenisperalatanid').val() == value.jenisperalatanid) {
                    namaJenisPeralatan = value.nama;
                }
            });
            console.log(namaJenisPeralatan);

            if ($('#detail_jumlah_mode').val().toLowerCase() == 'add') {
                v_detailTmpId ++;
                var v_newData = {	
                    // "detailpagusarprasid": "tmp__"+v_detailTmpId, 
                    "detailjumlahid": v_detailTmpId, 
                    // "aktivitasharianid": $("#detail_aktivitasharianid").val(),
                    "jenisperalatanid": $('#detail_jumlah_jenisperalatanid').val(),
                    "namaJenisPeralatan": namaJenisPeralatan,
                    "jumlah": $('#detail_jumlah_jumlah').val(),
                    "satuan": $('#detail_jumlah_satuan').val(),

                    // "file": $('#detail_detail_file').val(),
                    // "status": $('#detail_detail_status').val(),
                    // "statusvw": $('#detail_detail_status option:selected').text(),
                };
                v_listDataDetail.push(v_newData);
                // console.log(v_listDataDetail);
            }
            else {
                $.each( v_listDataDetail, function( p_key, p_value ) {
                    if (p_value.detailjumlahid ==  $('#detail_jumlah_detailjumlahid').val()) {
                        p_value.jenisperalatanid = $('#detail_jumlah_jenisperalatanid').val();
                        p_value.namaJenisPeralatan = namaJenisPeralatan;
                        p_value.jumlah = $('#detail_jumlah_jumlah').val();
                        p_value.satuan = $('#detail_jumlah_satuan').val();
                        // p_value.nilaipagu = $('#detail_detail_nilaipagu').val();
                        // p_value.nokontrak = $('#detail_detail_nokontrak').val();
                        // p_value.nilaikontrak = $('#detail_detail_nilaikontrak').val();
                        // p_value.perusahaanid = $('#detail_detail_perusahaanid').val();
                        // p_value.tgldari = $('#detail_detail_tgldari').val();
                        // p_value.tglsampai = $('#detail_detail_tglsampai').val();
                        // p_value.file = $('#detail_detail_file').val();
                        // p_value.file = document.getElementById("detail_detail_file").files[0].name;
                        
                        return false;
                    }	
                });
            }

            $.each(v_listDataDetail, function( p_idx, p_obj ) {
                $.each(p_obj, function(obj_key, obj_val){
                    $("<input />").attr("type", "hidden")
                    .attr("class", `detail_jumlah`)
                    .attr("name", `detail_jumlah[${p_idx}][${obj_key}]`)
                    .attr("value", obj_val)
                    .appendTo("#detail-form");
                });
            });

            // $('#detail_jumlah_data').val(v_listDataDetail)

            hidemodaldetailjumlah();
            
            reloadTableDetailJumlah();
        }

        function reloadTableDetailJumlah() {
            $("#detail_jumlah_data").val(JSON.stringify(v_listDataDetail));

            detailjumlahtable.clear();
            detailjumlahtable.rows.add(v_listDataDetail);
            detailjumlahtable.draw();
        }

        function deleteDataDetail(p_id) {
            if (p_id != '' && p_id != null) {
                var v_newData = {"id": p_id, "model": "detail"};
                v_listDetailDeleted.push(v_newData);
            }
            v_listDataDetail.forEach(function(p_hasil, p_index) {
                if(p_hasil['detailjumlahid'] === p_id) {
                    v_listDataDetail.splice(p_index, 1);
                }    
            });

            // v_listDataDetail.forEach(function(p_hasil, p_index) {
            //     if(p_hasil['detailpagusarprasid'].toString() === p_id.toString()) {
            //         v_listDataDetail.splice(p_index, 1);
            //     }    
            // });
            
            $("#detail_jumlah_datadel").val(JSON.stringify(v_listDetailDeleted));
            reloadTableDetailJumlah();
        }

        function hapusRiwayatKerja(idx){
            swal({   
                title: "Apakah anda yakin akan menghapus Riwayat Pekerjaan ini ?",   
                text: "Data yang terhapus tidak dapat dikembalikan lagi!",   
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Ya, lanjutkan!",   
                closeOnConfirm: true 
            }, function(){
                v_listDataRiwayatKerja.forEach(function(p_hasil, p_index) {
                    if(p_hasil['indexriwayatkerja'].toString() === idx.toString()) {
                    v_listDataRiwayatKerja.splice(p_index, 1);
                    }    
                });
                
                loadDataRiwayatKerja();
            });
        }

        function handleSubmit(){
            $.each(v_listDataDetail, function( p_idx, p_obj ) {
                $.each(p_obj, function(obj_key, obj_val){
                    if(obj_key == 'file'){
                        $("<input />").attr("type", "file")
                        .attr("class", `detailjumlah d-none`)
                        .attr("name", `filedetailjumlah[${p_idx}][${obj_key}]`)
                        // .attr("value", obj_val)
                        .appendTo("#detail-form");
                    }else{
                        $("<input />").attr("type", "hidden")
                        .attr("class", `detail_jumlah`)
                        .attr("name", `detail_jumlah[${p_idx}][${obj_key}]`)
                        .attr("value", obj_val)
                        .appendTo("#detail-form");
                    }
                });
            });
        }

        var detailaporantable;
        var kebutuhansarprastable = $('#kebutuhan-sarpras-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            dom: 'Bfrtip',
            select: true,
            ordering: false,
            language: {
                lengthMenu: "Menampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data",
                info: "Halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ data)",
                search: "Pencarian :",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                }
            },
            ajax: {
                url: "{{ route('progresfisik.index') }}",
                dataSrc: function (response) {
                    response.recordsTotal = response.data.count;
                    response.recordsFiltered = response.data.count;
                    return response.data.data;
                },
                data: function (d) {
                    return $.extend({}, d, {
                        "perusahaanid": $("#perusahaanid").val().toLowerCase(),
                        "kotaid": $('#kotaid').val().toLowerCase(),
                        "jenjang": $('#jenjang').val().toLowerCase(),
                        "jenis": $('#jenis').val().toLowerCase(),
                        "sekolahid": $('#sekolahid').val().toLowerCase(),
                        "search": $("#search").val().toLowerCase()
                    });
                }
            },
            buttons: {
                buttons: [
                    {
                        attr: {id: 'btn-isidetailjenisperalatan'},
                        text: '<i class="fa fa-file-text-o" aria-hidden="true"></i> Lihat Data',
                        className: 'edit btn btn-info btn-datatable mb-2 d-none',
                        action: function () {
                            if (kebutuhansarprastable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data terlebih dahulu", "error");
                                return;
                            }

                            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                            var detailpenganggaranid = rowData.detailpenganggaranid;
                            console.log(detailpenganggaranid)

                            loadDetailJenisPeralatan(detailpenganggaranid);

                            $('#modal-detail-parent').modal('show');
                            enableSelesaiButton('false');

                        }
                    },
                    {
                        attr: {id: 'btn-selesai'},
                        text: '<i class="fa fa-check-square" aria-hidden="true"></i> Selesai',
                        className: 'edit btn btn-success btn-datatable mb-2',
                        action: function () {
                            if (kebutuhansarprastable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data terlebih dahulu", "error");
                                return;
                            }

                            let status = kebutuhansarprastable.rows( { selected: true } ).data()[0]['status'];
                            let statusDesc = kebutuhansarprastable.rows( { selected: true } ).data()[0]['status'] == 5 ? 'Proses Tender' : '';

                            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                            var detailpaguanggaranid = rowData.detailpaguanggaranid;
                            console.log(detailpaguanggaranid)

                            let url = "{{ route('progresfisik.selesai', ':id') }}"
                            url = url.replace(':id', detailpaguanggaranid);

                            swal.fire({   
                                title: "Konfirmasi",   
                                text: `Apakah anda yakin menyelesaikan data laporan ini?`,   
                                icon: "warning",   
                                showCancelButton: true,   
                                confirmButtonText: "Ya, lanjutkan!",   
                                closeOnConfirm: false 
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });
                                    $.ajax({
                                        type: "POST",
                                        cache:false,
                                        url: url,
                                        dataType: 'JSON',
                                        data: {
                                            "_token": $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(json){
                                            var success = json.success;
                                            var message = json.message;
                                            var data = json.data;
                                            console.log(data);
                                            
                                            if (success == 'true' || success == true) {
                                                swal.fire("Berhasil!", "Data pembangunan sudah masuk ke menu sarpras tersedia.", "success"); 
                                                // detailaporantable.draw();
                                                var rowData = kebutuhansarprastable.rows( {selected: true} ).data()[0]; // Get selected row data
                                                var sarpraskebutuhanid = rowData.sarpraskebutuhanid;
                                                kebutuhansarprastable.draw();
                                            }
                                            else if (success == 'false' || success == false) {
                                                swal.fire({
                                                    title: 'Peringatan!',
                                                    text: message + '. Apakah anda ingin mengisi data jenis peralatan?',
                                                    icon: "warning",   
                                                    showCancelButton: true,   
                                                    confirmButtonText: "Ya, lanjutkan!",
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                                                        var detailpenganggaranid = rowData.detailpenganggaranid;
                                                        console.log(detailpenganggaranid)

                                                        loadDetailJenisPeralatan(detailpenganggaranid)

                                                        $('#modal-detail-parent').modal('show');
                                                        enableSelesaiButton('true');
                                                    }
                                                }); 
                                            }
                                            else {
                                                swal.fire("Error!", message, "error"); 
                                            }
                                        }
                                    });  
                                }           
                            });
                        }
                    },
                ]
            },
            columns: [
                {'orderData': 0, data: 'jeniskebutuhan', name: 'jeniskebutuhan',
                    render: function(data, type, row) {
                        if(row.jeniskebutuhan != null) {
                            var listJenisKebutuhan = @json(enum::listJenisKebutuhan($id = ''));
                            // let listJenisKebutuhan = JSON.parse('{!! json_encode(enum::listJenisKebutuhan()) !!}');
                            let Desc;
                            listJenisKebutuhan.forEach((value, index) => {
                                if(row.jeniskebutuhan == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }else {
                            return '---'
                        }
                    }
                },
                {
                    'orderData': 1,
                    data: 'sekolahid',
                    render: function (data, type, row) {
                        if (row.sekolahid != null) {
                            return row.namasekolah;
                        }
                        else {
                            return '---'
                        }
                    },
                    name: 'sekolahid'
                },
                {
                    'orderData': 2,
                    data: 'jenispagu',
                    render: function (data, type, row) {
                        if (row.jenispagu != null) {
                            // var listMinggu = @json(enum::listMinggu($id));
                            let listJenisPagu = JSON.parse('{!! json_encode(enum::listJenisPagu($id)) !!}');
                            let jenisPaguDesc;
                            listJenisPagu.forEach((value, index) => {
                                if(row.jenispagu == index + 1) {
                                    jenisPaguDesc = value;
                                }
                            });
                            return jenisPaguDesc;
                        }
                    },
                    name: 'jenispagu'
                },
                {
                    'orderData': 3,
                    data: 'nama',
                    render: function(data, type, row){
                        if (row.nama != null) {
                            return (row.nama);
                        }else{
                            return '---'
                        }
                    },
                    name: 'nama'
                },
                {
                    'orderData': 4,
                    data: 'subkegnama',
                    render: function (data, type, row) {
                        return row.subkegnama;
                    },
                    name: 'subkegnama'
                },
                {'orderData': 5, data: 'subdetailkegiatan',
                    render: function ( data, type, row ) {
                        if (row.subdetailkegiatan != null) {
                            return row.subdetailkegiatan;
                        }else {
                            return '---'
                        }
                    }, 
                    name: 'subdetailkegiatan'
                },
                {
                    'orderData': 6,
                    data: 'nokontrak',
                    render: function (data, type, row) {
                        if (row.nokontrak != null) {
                            return (row.nokontrak);
                        }else {
                            return '---'
                        }
                    },
                    name: 'nokontrak',
                },
                {
                    'orderData': 7,
                    data: 'nilaikontrak',
                    render: function (data, type, row) {
                        if(row.nilaikontrak != null) { 
                            return rupiah(row.nilaikontrak);
                        }else {
                            return '---'
                        }
                    },
                    name: 'nilaikontrak',
                },
                {
                    'orderData': 8,
                    data: 'progres',
                    render: function (data, type, row) {
                        if(row.progres != null) { 
                            return `<div class="progress progress-lg">` +
                                        `<div class="progress-bar ${row.progres <= 75 ? "progress-bar-info" : "progress-bar-success"} progress-bar-striped active" role="progressbar" style="width: ${row.progres}%; role="progressbar""> ${row.progres}% </div>` +
                                    `</div>`
                            // return `${row.progres} %`;
                        }
                    },
                    name: 'progres',
                },
                {
                    'orderData': 9,
                    data: 'isselesai',
                    render: function (data, type, row) {
                        if(row.isselesai != null || row.selesai == true) { 
                            return '<span class="badge badge-pill badge-success">V</span>';
                        }else {
                            return '<span class="badge badge-pill badge-danger">X</span>';
                        }
                    },
                    sClass : "text-center", 
                    name: 'isselesai',
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        // store detail jumlah sarpras  
        $(document).on('submit', '#detail-parent-form', function(e){
            e.preventDefault();

            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
            var detailpaguanggaranid = rowData.detailpaguanggaranid;
            console.log(detailpaguanggaranid);

            // var url = '';

            let url = "{{ route('progresfisik.selesai', ':id') }}"
            url = url.replace(':id', detailpaguanggaranid);
            
            // var formData = new FormData($('#detail-foto-peralatan-form')[0]);

            swal.fire({   
                title: "Konfirmasi",   
                text: `Apakah anda yakin menyelesaikan data laporan ini?`,   
                icon: "warning",   
                showCancelButton: true,   
                confirmButtonText: "Ya, lanjutkan!",   
                closeOnConfirm: false 
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        cache:false,
                        url: url,
                        dataType: 'JSON',
                        data: {
                            "_token": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(json){
                            var success = json.success;
                            var message = json.message;
                            var data = json.data;
                            console.log(data);
                            
                            if (success == 'true' || success == true) {
                                swal.fire("Berhasil!", "Data pembangunan sudah masuk ke menu sarpras tersedia.", "success"); 
                                // detailaporantable.draw();
                                $('#modal-detail-parent').modal('hide');
                                var rowData = kebutuhansarprastable.rows( {selected: true} ).data()[0]; // Get selected row data
                                var sarpraskebutuhanid = rowData.sarpraskebutuhanid;
                                kebutuhansarprastable.draw();
                            }
                            else if (success == 'false' || success == false) {
                                swal.fire({
                                    title: 'Peringatan!',
                                    text: message + '. Apakah anda ingin mengisi data jenis peralatan?',
                                    icon: "warning",   
                                    showCancelButton: true,   
                                    confirmButtonText: "Ya, lanjutkan!",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                                        var detailpenganggaranid = rowData.detailpenganggaranid;
                                        console.log(detailpenganggaranid)

                                        loadDetailJenisPeralatan(detailpenganggaranid)

                                        $('#modal-detail-parent').modal('show');
                                        enableSelesaiButton('true');
                                    }
                                }); 
                            }
                            else {
                                swal.fire("Error!", message, "error"); 
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            var data = jqXHR.responseJSON;
                            console.log(data.errors);// this will be the error bag.
                            // printErrorMsg(data.errors);
                        }
                    })
                }
            })
        })

        const enableSelesaiButton = (condition) => {
            if (condition == 'true') {
                $('#btn-selesai-container').removeClass('d-none');
            } else if (condition == 'false') {
                $('#btn-selesai-container').addClass('d-none');
            }
        }

        function loadDetailLaporan(detailpaguanggaranid) {
            var url = "{{ route('progresfisik.loadDetailLaporan', ':id') }}";
            url = url.replace(':id', detailpaguanggaranid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    detailaporantable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        detailaporantable.row.add({
                            detaillaporanid: response.data.data[i].detaillaporanid,
                            bulan: response.data.data[i].bulan,
                            minggu: response.data.data[i].minggu,
                            daritgl: response.data.data[i].daritgl,
                            sampaitgl: response.data.data[i].sampaitgl,
                            target: response.data.data[i].target,
                            progres: response.data.data[i].progres,
                            keterangan: response.data.data[i].keterangan,
                        });
                    }

                    detailaporantable.draw();
                    $('#detail-laporan-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
        function loadDetailJenisPeralatan(detailpenganggaranid) {
            var url = "{{ route('progresfisik.loadDetailJenisPeralatan', ':id') }}";
            url = url.replace(':id', detailpenganggaranid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    detailjumlahtable.clear();

                    for (var i = 0; i < response.data.count; i++) {

                        var listJenisPeralatan = @json($jenisperalatan);
                        // let listJenisPeralatan = JSON.parse('{!! json_encode(enum::listJenisSarpras()) !!}');
                        let namaJenisPeralatan;
                        listJenisPeralatan.forEach((value, index) => {
                            if(response.data.data[i].jenisperalatanid == value.jenisperalatanid) {
                                namaJenisPeralatan = value.nama;
                            }
                        });

                        detailjumlahtable.row.add({
                            detailjumlahperalatanid: response.data.data[i].detailjumlahperalatanid,
                            detailpenganggaranid: response.data.data[i].detailpenganggaranid,
                            jenisperalatanid: response.data.data[i].jenisperalatanid,
                            namaJenisPeralatan: namaJenisPeralatan,
                            jumlah: response.data.data[i].jumlah,
                            satuan: response.data.data[i].satuan,
                        });
                    }

                    detailjumlahtable.draw();
                    $('#detailjumlah-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        const showJenisPeralatanBtn = (condition, jenissarpras) => {
            if (condition == 'select') {
                if (jenissarpras == "{{ enum::SARPRAS_PERALATAN }}") {
                    $('#btn-isidetailjenisperalatan').removeClass('d-none');
                    $('#btn-lihatdetailjenisperalatan').removeClass('d-none');
                }else {
                    $('#btn-isidetailjenisperalatan').addClass('d-none');
                    $('#btn-lihatdetailjenisperalatan').addClass('d-none');
                }
            }else if(condition == 'deselect') {
                $('#btn-isidetailjenisperalatan').addClass('d-none');
                $('#btn-lihatdetailjenisperalatan').addClass('d-none');
            }
        }

        // Listen for row selection event on kebutuhan-sarpras-table
        kebutuhansarprastable.on('select', function (e, dt, type, indexes) {
            var rowData = kebutuhansarprastable.rows(indexes).data()[0]; // Get selected row data
            var detailpaguanggaranid = rowData.detailpaguanggaranid;
            var jenissarpras = rowData.jenissarpras;
            var progres = rowData.progres;
            var selesai = rowData.isselesai

            console.log(jenissarpras == "{{ enum::SARPRAS_PERALATAN }}");
            
            showJenisPeralatanBtn('select', jenissarpras);
            setenableprogresbutton(progres, selesai);

            // Load history table with selected detailpaguanggaranid
            loadDetailLaporan(detailpaguanggaranid);
        });

        kebutuhansarprastable.on('deselect', function ( e, dt, type, indexes ) {
            var rowData = kebutuhansarprastable.rows(indexes).data()[0]; // Get selected row data
            var jenissarpras = rowData.jenissarpras;
            // hide history table
            $('#detail-laporan-table').hide();
            showJenisPeralatanBtn('deselect', jenissarpras);
        });


        // hide histiry table
        $('#detail-laporan-table').hide();

    // Foto detail jenis peralatan table
    var fotoDetailJenisPeralatan = $('#detail-peralatan-table').DataTable({
        responsive: true,
        processing: true,
        // serverSide: true,
        pageLength: 50,
        dom: 'Bfrtip',
        select: true,
        ordering: false,
        searching: false,
        language: {
            // lengthMenu: "Menampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data",
            info: "Halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            search: "Pencarian :",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya",
            }
        },
        // ... your detail-laporan-table initialization options ...
        columns: [
            {
                data: 'file',
                name: 'file',
                render: function(data, type, row) {
                    if(row.file != null) {
                        return row.file;
                    } 
                },
                visible: false
            },
            {
                data: 'file', 
                name: 'preview', 
                render: function (data, type, row){
                    if(row.file != null){
                        return "<div class=\"d-flex justify-content-center align-items-center\"><img src=\"/storage/uploaded/sarprastersedia/detailjumlahsarpras/"+row.file+"\" height=\"300\" /></div>";
                    }
                }
            },
        ],
        buttons: {
            buttons: [
                {
                    text: 'Download File',
                    className: 'edit btn btn-success mb-3 btn-datatable',
                    action: () => {
                    if (fotoDetailJenisPeralatan.rows( { selected: true } ).count() <= 0) {
                        swal.fire("Data belum dipilih", "Silahkan pilih data yang akan didownload", "error");
                        return;
                    }
                    let id = fotoDetailJenisPeralatan.rows( { selected: true } ).data()[0]['filedetailjumlahsarprasid'];
                    let namaFile = fotoDetailJenisPeralatan.rows( { selected: true } ).data()[0]['file'];
                    let url = "{{ route('sarprastersedia.downloadFileDetailJumlahSarpras', ':id') }}"
                    url = url.replace(':id', id);
                    console.log(url);
                    $.ajax({
                            type: "GET",
                            cache:false,
                            processData: false,
                            contentType: false,
                            // defining the response as a binary file
                            xhrFields: {
                                responseType: 'blob' 
                            },  
                            url: url,
                            success: (data) => {
                                let a = document.createElement('a');
                                let url = window.URL.createObjectURL(data);
                                a.href = url;
                                a.download = namaFile;
                                document.body.append(a);
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(url);
                            }
                    }
                    );
                    }
                },
                
            ]
        },
    });

    function showmodalfotodetailjumlah(mode) {
            // v_modedetail = mode;
            $("#detail_foto_mode").val(mode);
            // resetformdetailjumlah();
            if (mode == "add") {
                // $('#modal-detail-foto-peralatan').modal('show');
                $("#modal-title-detail-foto-peralatan").html('Tambah Foto');
                // setenableddetailjumlah(true);
            }
            else if (mode == "edit") {
                // $('#modal-detail-foto-peralatan').modal('show');
                $("#modal-title-detail-foto-peralatan").html('Ubah Foto');
                // bindformdetailjumlah();
                // setenableddetailjumlah(true);
                
            }
            else {
                $("#modal-title-detail-form").html('Lihat Data');
                bindformdetailjumlah();
                setenableddetailjumlah(false);
            }
            
            $("#modal-detail-foto-peralatan").modal('show');
        }

        function hidemodaldetailjumlah() {
            $("#modal-detail-form").modal('hide');
        }
        

    // Foto edit detail jenis peralatan table
    var fotoEditDetailJenisPeralatan = $('#detail-edit-peralatan-table').DataTable({
        responsive: true,
        processing: true,
        // serverSide: true,
        pageLength: 50,
        dom: 'Bfrtip',
        select: true,
        ordering: false,
        searching: false,
        language: {
            // lengthMenu: "Menampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data",
            info: "Halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            search: "Pencarian :",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya",
            }
        },
        // ... your detail-laporan-table initialization options ...
        columns: [
            {
                data: 'file',
                name: 'file',
                render: function(data, type, row) {
                    if(row.file != null) {
                        return row.file;
                    } 
                },
                visible: false
            },
            {
                data: 'file', 
                name: 'preview', 
                render: function (data, type, row){
                    if(row.file != null){
                        return "<div class=\"d-flex justify-content-center align-items-center\"><img src=\"/storage/uploaded/sarprastersedia/detailjumlahsarpras/"+row.file+"\" height=\"300\" /></div>";
                    }
                }
            },
        ],
        buttons: {
            buttons: [
                {
                    text: 'Download File',
                    className: 'edit btn btn-success btn-sm btn-datatable',
                    action: () => {
                    if (fotoDetailJenisPeralatan.rows( { selected: true } ).count() <= 0) {
                        swal.fire("Data belum dipilih", "Silahkan pilih data yang akan didownload", "error");
                        return;
                    }
                    let id = fotoDetailJenisPeralatan.rows( { selected: true } ).data()[0]['filedetailjumlahsarprasid'];
                    let namaFile = fotoDetailJenisPeralatan.rows( { selected: true } ).data()[0]['file'];
                    let url = "{{ route('sarprastersedia.downloadFileDetailJumlahSarpras', ':id') }}"
                    url = url.replace(':id', id);
                    console.log(url);
                    $.ajax({
                            type: "GET",
                            cache:false,
                            processData: false,
                            contentType: false,
                            // defining the response as a binary file
                            xhrFields: {
                                responseType: 'blob' 
                            },  
                            url: url,
                            success: (data) => {
                                let a = document.createElement('a');
                                let url = window.URL.createObjectURL(data);
                                a.href = url;
                                a.download = namaFile;
                                document.body.append(a);
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(url);
                            }
                    }
                    );
                    }
                },
                {
                    text: 'Tambah',
                    className: 'add btn btn-info btn-sm btn-datatable',
                    action: function () {
                        showmodalfotodetailjumlah("add");
                    }
                },
                {
                    text: 'Ubah',
                    className: 'edit btn btn-warning btn-sm btn-datatable',
                    action: function () {
                        if (fotoEditDetailJenisPeralatan.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan diubah", "error");
                            return;
                        }
                        
                        showmodalfotodetailjumlah("edit");
                    }
                },
                {
                    text: 'Hapus',
                    className: 'delete btn btn-danger btn-sm btn-datatable',
                    action: function () {
                        if (fotoEditDetailJenisPeralatan.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                            return;
                        }
                        
                        var id = fotoEditDetailJenisPeralatan.rows( { selected: true } ).data()[0]['filedetailjumlahperalatanid'];
                        var url = "{{ route('progresfisik.deleteDetailFotoPeralatan', ':id') }}"
                        url = url.replace(':id', id);
                        swal.fire({   
                            title: "Apakah anda yakin akan menghapus data ini?",   
                            text: "Data yang terhapus tidak dapat dikembalikan lagi!",   
                            type: "warning",   
                            showCancelButton: true,   
                            confirmButtonColor: "#DD6B55",   
                            confirmButtonText: "Ya, lanjutkan!",   
                            closeOnConfirm: false 
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    type: "POST",
                                    cache:false,
                                    url: url,
                                    dataType: 'JSON',
                                    data: {
                                        "_token": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(json){
                                        var success = json.success;
                                        var message = json.message;
                                        var data = json.data;
                                        console.log(data);
                                        
                                        if (success == 'true' || success == true) {
                                            swal.fire("Berhasil!", "Data anda telah dihapus.", "success"); 
                                            // jenisSarprasTersediaTable.draw();
                                            var rowData = detailjumlahtable.rows({ selected: true }).data()[0]; // Get selected row data
                                            var detailjumlahperalatanid = rowData.detailjumlahperalatanid;
                                            loadEditFotoJenisPeralatan(detailjumlahperalatanid);
                                        }
                                        else {
                                            swal.fire("Error!", data, "error"); 
                                        }
                                    }
                                });   
                            }          
                        });
                    }
                },
            ]
        },
    });

    function loadFotoJenisPeralatan(detailjumlahperalatanid) {
        var url = "{{ route('progresfisik.loadFotoJenisPeralatan', ':id') }}";
        url = url.replace(':id', detailjumlahperalatanid);

        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                fotoDetailJenisPeralatan.clear();

                for (var i = 0; i < response.data.count; i++) {

                    fotoDetailJenisPeralatan.row.add({
                        detailjumlahperalatanid: response.data.data[i].detailjumlahperalatanid,
                        file: response.data.data[i].file,
                        // jenisperalatanid: response.data.data[i].jenisperalatanid,
                    });
                }

                fotoDetailJenisPeralatan.draw();
                $('#detailjumlah-table').show();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function loadEditFotoJenisPeralatan(detailjumlahperalatanid) {
        var url = "{{ route('progresfisik.loadFotoJenisPeralatan', ':id') }}";
        url = url.replace(':id', detailjumlahperalatanid);

        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                fotoEditDetailJenisPeralatan.clear();

                for (var i = 0; i < response.data.count; i++) {

                    fotoEditDetailJenisPeralatan.row.add({
                        filedetailjumlahperalatanid: response.data.data[i].filedetailjumlahperalatanid,
                        detailjumlahperalatanid: response.data.data[i].detailjumlahperalatanid,
                        file: response.data.data[i].file,
                        // jenisperalatanid: response.data.data[i].jenisperalatanid,
                    });
                }

                fotoEditDetailJenisPeralatan.draw();
                $('#detail-edit-peralatan-table').show();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    // Initialize history table
    var detailaporantable = $('#detail-laporan-table').DataTable({
        responsive: true,
        processing: true,
        // serverSide: true,
        pageLength: 50,
        dom: 'Bfrtip',
        select: true,
        ordering: false,
        searching: false,
        language: {
            // lengthMenu: "Menampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data",
            info: "Halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            search: "Pencarian :",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya",
            }
        },
        // ... your detail-laporan-table initialization options ...
        columns: [
            {
                data: 'minggu',
                name: 'minggu',
                render: (data, type, row) => {
                    if(row.minggu != null) {
                        // var listMinggu = @json(enum::listMinggu($id));
                        let listMinggu = JSON.parse('{!! json_encode(enum::listMinggu($id)) !!}');
                        let mingguDesc;
                        listMinggu.forEach((value, index) => {
                            if(row.minggu == index + 1) {
                                mingguDesc = value;
                            }
                        });
                        return mingguDesc;
                    }
                }
            },
            {
                data: 'daritgl',
                name: 'daritgl',
                render: function(data, type, row) {
                    if(row.daritgl != null) {
                        return DateFormat(row.daritgl);
                    } 
                }
            },
            {
                data: 'sampaitgl',
                name: 'sampaitgl',
                render: function(data, type, row) {
                    if (row.daritgl != null) {
                        return DateFormat(row.sampaitgl);
                    }
                }
            },
            {
                data: 'progres',
                name: 'progres',
                render: function(data, type, row) {
                    if(row.progres != null) {
                        return `<div class="progress progress-lg">` +
                                        `<div class="progress-bar ${row.progres <= 75 ? "progress-bar-info" : "progress-bar-success"} progress-bar-striped active" role="progressbar" style="width: ${row.progres}%; role="progressbar""> ${row.progres}% </div>` +
                                    `</div>`;
                    }
                }
            },
            {
                data: 'keterangan',
                name: 'keterangan'
            },
        ],
        buttons: {
            buttons: [
                {
                    text: '<i class="fa fa-plus-circle aria-hidden="true"></i> Tambah',
                    className: 'edit btn btn-primary mb-3 btn-datatable',
                    action: function () {
                        if (kebutuhansarprastable.rows( {selected: true} ).count() <= 0) {
                            swal.fire("Data permintaan sarpras belum dipilih", "Silakan pilih data permintaan sarpras terlebih dahulu", "error");
                            return;
                        }
                        else{
                            var rowData = kebutuhansarprastable.rows( {selected: true} ).data()[0]; // Get selected row data
                            var detailpaguanggaranid = rowData.detailpaguanggaranid;

                            $('#detailpaguanggaranid').val(detailpaguanggaranid);

                            $('#modal-detail-laporan').modal('show');
                            showmodaldetail('add');
                        }
                    }
                }, 
                {
                    text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                    className: 'edit btn btn-warning mb-3 btn-datatable',
                    action: function () {
                        if (detailaporantable.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan diubah", "error");
                            return;
                        }
                        // var id = detailaporantable.rows( { selected: true } ).data()[0]['detailsaprasid'];
                        var rowData = detailaporantable.rows({ selected: true }).data()[0]; // Get selected row data
                        var detailpenganggaranid = rowData.detailpenganggaranid;

                        $('#modal-detail-laporan').modal('show');
                        showmodaldetail('edit');

                        // $('#subkegid-edit option[value="'+subkegid+'"]').prop('selected', true);

                        $('#modal-detail-laporan-penganggaran').modal('show');
                        $('#btnSubmitParent').show();
                        // $('#title-modal-detail-penganggaran').text('EDIT DETAIL PENGANGGARAN');
                        // showDetailPaguPenganggaran(detailpenganggaranid);
                        setenabledtbutton("0");
                        // console.log(detailpenganggaranid);
                    }
                },
            ]
        },
    });

    function resetformdetail() {
        $("#detailLaporan-form")[0].reset();
        // const $input = $('#detail_file')
        // const $imgPreview = $input.closest('div').find('.param_img_holder');
        // $imgPreview.empty();
        // removeImageValue('detail_file');
        // var v_max = 1;
        // if (v_listDataDetail.length > 0) {
        //     var v_maxobj = v_listDataDetail.reduce((prev, current) => (prev && prev.nourut > current.nourut) ? prev : current);
        //     v_max = parseInt(v_maxobj.nourut)+1;
        // }
        // $("#detail_detail_nourut").val(v_max);
        //alert(v_listDataDetail.length);
        //alert(v_listDataDetail.length + '->' + JSON.stringify(max));

        $('span[id^="err_detail_"]', "#detailLaporan-form").each(function(index, el){
            $('#'+el.id).html("");
        });

        $('select[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("").trigger('change');
            }
        });
        $('input[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("");
            }
        });
        $('textarea[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("");
            }
        });
    }

    function bindformdetail() {
        $('input[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            //alert(inputname);
            if (inputname != "mode") {
                $("#"+el.id).val(detailaporantable.rows( { selected: true } ).data()[0][inputname]);
            }
        });
        
        $('select[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            //alert(inputname);
            if (inputname != "mode") {
                $("#"+el.id).val(detailaporantable.rows( { selected: true } ).data()[0][inputname]).trigger('change');
            }
        });

        $('textarea[id^="detail_"]', "#detailLaporan-form").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            //alert(inputname);
            if (inputname != "mode") {
                $("#"+el.id).val(detailaporantable.rows( { selected: true } ).data()[0][inputname]);
            }
        });
    }

    function setenableddetail(value) {
        if (value) {
            $("#btnSubmit").show();
        }
        else {
            $("#btnSubmit").hide();
        }
        
        $('textarea[id^=""]', "#detailLaporan-form").each(function(index, el){
            $("#"+el.id).prop("readonly", !value);
        });
        $('input[id^=""]', "#detailLaporan-form").each(function(index, el){
            $("#"+el.id).prop("readonly", !value);
        });
        $('select[id^=""]', "#detailLaporan-form").each(function(index, el){
            $("#"+el.id).prop("disabled", !value);
        });
    }

    var v_modedetail = "";
    function showmodaldetail(mode) {
        v_modedetail = mode;
        $("#detail_mode").val(mode);
        resetformdetail();
        if (mode == "add") {
            $("#modal-title-detail-laporan").html('Tambah Data');
            setenableddetail(true);
            
            // console.log($("#detail_mode").val());
        }
        else if (mode == "edit") {
            $("#modal-title-detail-laporan").html('Ubah Data');
            bindformdetail();
            setenableddetail(true);
        }
        else {
            $("#modal-title-detail-laporan").html('Lihat Data');
            bindformdetail();
            setenableddetail(false);
        }
        
        $("#m_formshowdetail").modal('show');
    }

    function hidemodaldetail() {
        $("#m_formshowdetail").modal('hide');
    }

    function setenableprogresbutton(progres, selesai) {
        kebutuhansarprastable.buttons( '#btn-selesai' ).disable();
        if (progres >= 100 && selesai != true) {
            kebutuhansarprastable.buttons( '#btn-selesai' ).enable();
        }
        // else if (progres >= 100 || selesai == true) {
        //     kebutuhansarprastable.buttons( '#btn-selesai' ).disable();
        // }
        else {
            kebutuhansarprastable.buttons( '#btn-selesai' ).disable();
        }
    }

    // verifikasi kebutuhan sarpras 
    $(document).on('submit', '#detailLaporan-form', function(e){
            var url = '';
            
            e.preventDefault();
            
            var formData = new FormData($('#detailLaporan-form')[0]);
            
            if($("#detail_mode").val() == "add") {
                var url = "{{ route('progresfisik.storeDetailLaporan') }}"
                // url = url.replace(':id', id);   
            }else if($("#detail_mode").val() == "edit") {
                var url = "{{ route('progresfisik.updateDetailLaporan', ':id') }}"
                var id = detailaporantable.rows( { selected: true } ).data()[0]['detaillaporanid'];
                url = url.replace(':id', id); 
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: (json) => {
                    let success = json.success;
                    let message = json.message;
                    let data = json.data;
                    let errors = json.errors;

                    if (success == 'true' || success == true) {
                            swal.fire("Berhasil!", "Data progres fisik berhasil ditambah.", "success");
                            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                            var detailpaguanggaranid = rowData.detailpaguanggaranid;
                            loadDetailLaporan(detailpaguanggaranid);
                            kebutuhansarprastable.draw();
                            $('#detailLaporan-form').trigger("reset");
                            $('#modal-detail-laporan').modal('hide'); 
                    }
                    // else{
                    //     console.log(errors)
                    // }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                        var data = jqXHR.responseJSON;
                        console.log(data.errors);// this will be the error bag.
                        // printErrorMsg(data.errors);
                }
            })
        })

    // store detail jumlah sarpras  
    $(document).on('submit', '#detail-form', function(e){
        e.preventDefault();
        
        var url = ''

        if ($('#detail_jumlah_mode').val() == 'add') {

            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
            var detailpaguanggaranid = rowData.detailpaguanggaranid;

            var url = "{{ route('progresfisik.storeDetailJumlahPeralatan', ':id') }}";
            url = url.replace(':id', detailpaguanggaranid); 
        } else if($('#detail_jumlah_mode').val() == 'edit'){

            var rowDataPeralatan = detailjumlahtable.rows({ selected: true }).data()[0];
            var detailjumlahperalatanid = rowDataPeralatan.detailjumlahperalatanid;

            var url = "{{ route('progresfisik.updateDetailJumlahPeralatan', ':id') }}";
            url = url.replace(':id', detailjumlahperalatanid); 
        }
        
        var formData = new FormData($('#detail-form')[0]);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: (json) => {
                let success = json.success;
                let message = json.message;
                let data = json.data;
                let errors = json.errors;

                if (success == 'true' || success == true) {
                        swal.fire("Berhasil!", "Berhasil menambah detail jenis peralatan.", "success");
                        var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                        var detailpenganggaranid = rowData.detailpenganggaranid;
                        loadDetailJenisPeralatan(detailpenganggaranid);
                        // detailjumlahtable.draw();
                        $('#detail-form').trigger("reset");
                        $('#modal-detail-form').modal('hide'); 
                }
                // else{
                //     console.log(errors)
                // }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                    var data = jqXHR.responseJSON;
                    console.log(data.errors);// this will be the error bag.
                    // printErrorMsg(data.errors);
            }
        })
    })


    // store detail jumlah sarpras  
    $(document).on('submit', '#detail-foto-peralatan-form', function(e){
        e.preventDefault();

        var url = ''

        if ($('#detail_foto_mode').val() == 'add') {

            var rowDataJenisPeralatan = detailjumlahtable.rows({ selected: true }).data()[0]; // Get selected row data
            var detailjumlahperalatanid = rowDataJenisPeralatan.detailjumlahperalatanid;

            var url = "{{ route('progresfisik.storeDetailFotoPeralatan', ':id') }}";
            url = url.replace(':id', detailjumlahperalatanid); 
        } else if($('#detail_foto_mode').val() == 'edit'){

            var rowDataFotoPeralatan = fotoEditDetailJenisPeralatan.rows({ selected: true }).data()[0];
            var filedetailjumlahperalatanid = rowDataFotoPeralatan.filedetailjumlahperalatanid;

            var url = "{{ route('progresfisik.updateDetailFotoPeralatan', ':id') }}";
            url = url.replace(':id', filedetailjumlahperalatanid); 
        }
        
        var formData = new FormData($('#detail-foto-peralatan-form')[0]);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: (json) => {
                let success = json.success;
                let message = json.message;
                let data = json.data;
                let errors = json.errors;

                if (success == 'true' || success == true) {
                        swal.fire("Berhasil!", "Berhasil mengubah foto peralatan.", "success");
                        var rowData = detailjumlahtable.rows({ selected: true }).data()[0]; // Get selected row data
                        var detailjumlahperalatanid = rowData.detailjumlahperalatanid;
                        loadEditFotoJenisPeralatan(detailjumlahperalatanid);
                        // detailjumlahtable.draw();
                        $('#detail-foto-peralatan-form').trigger("reset");
                        $('#modal-detail-foto-peralatan').modal('hide'); 

                        const $imgPreview = $('#file').closest('div').find('.param_img_holder');
                        $imgPreview.empty();
                        // $('#file').val('');
                }
                // else{
                //     console.log(errors)
                // }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                    var data = jqXHR.responseJSON;
                    console.log(data.errors);// this will be the error bag.
                    // printErrorMsg(data.errors);
            }
        })
    })

</script>

@endsection
