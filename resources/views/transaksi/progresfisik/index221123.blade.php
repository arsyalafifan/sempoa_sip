<?php
use App\enumVar as enum;
use App\Helpers\Get_field;
?>
@extends('layouts.master')

@section('content')
<style>
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
            <div class="col-md-12">
                {{-- <h3 class="card-title text-uppercase">PENGANGGARAN</h3> --}}
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="kebutuhan-sarpras-table">
                        <thead>
                            <tr>
                                <th>Jenis Kebutuhan</th>
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
    <div class="modal-dialog" role="document" style="max-width: 720px;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-jumlah"></h4>
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
                    <input type="hidden" name="detail_mode" id="detail_mode"/>

                    <div class="row" style="overflow-y: auto; overflow-x:auto;">
                        {{-- <h4 class="card-title text-uppercase text-bold m-t-40">Upload Foto Kebutuhan Sarpras</h4><hr /> --}}
    
                        <table id="demo-foo-addrow-sarprastersedia" class="table table-responsive table-bordered table-hover toggle-circle" data-page-size="7">
                            <thead style="background-color: #d8d8d868;">
                                <tr>
                                    <th class="text-center" data-sort-initial="true" data-toggle="true">Jenis Peralatan</th>
                                    <th class="text-center" data-sort-initial="true" data-toggle="true">Jumlah Unit</th>
                                    <th class="text-center" data-sort-initial="true" data-toggle="true">Satuan</th>
                                    <th class="text-center" data-sort-ignore="true" data-toggle="true">Upload File</th>
                                    <th class="text-center d-none" data-sort-ignore="true" data-toggle="true">Preview</th>
                                    <th class="text-center" data-sort-ignore="true" data-toggle="true">Hapus</th>
                                </tr>
                            </thead>
                            <div class="padding-bottom-15">
                                <div class="row">
                                    <div class="col-sm-12 text-right m-b-5">
                                        <button type="button" id="demo-btn-addrow-sarprastersedia" class="btn btn-primary"><i class="fldemo glyphicon glyphicon-plus"></i> Tambah
                                        </button>
                                    </div>
                                    
                                </div>
                            </div>
                            <tbody id="tbody-sarprastersedia" style="font-weight: 300;">
                                <tr>
                                    <td class="border-0 text-center">
                                        <div class="form-group" style="width: 200px">
                                            <select id="detail_jumlah_jenisperalatanid" class="custom-select-detail-jumlah form-control" name='jenisperalatanid[]'>
                                                <option value="">-- Jenis Peralatan --</option>
                                                @foreach ($jenisperalatan as $item)
                                                <option value="{{ $item->jenisperalatanid }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <div class="form-group">
                                            <input id="detail_jumlah_jumlah" type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah[]" value="{{ (old('jumlah')) }}" autocomplete="name">
                                        </div>
                                    </td>
                                    <td class="border-0 text-center" style="width: 200px">
                                        <div class="form-group">
                                            <input id="detail_jumlah_satuan" type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan[]" value="{{ (old('satuan')) }}" autocomplete="name">
                                        </div>
                                    </td>
                                    <td class="border-0" style="width: 200px">
                                        <input type="file" class="form-control file-input" name="file[]" multiple required /><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 2MB</span>
                                    </td>
                                    <td class="border-0 d-none" style="width: 200px">
                                        <div class="param_img_holder d-flex justify-content-center align-items-center d-none">
                                        </div>
                                    </td>
                                    <td class="border-0">
                                        <button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="close" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button>
                                    </td>
        
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <div class="text-right">
                                            <ul class="pagination">
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button id="button-close-selesai" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button value="btnSubmitSelesai" type="submit" id="btnSubmitSelesai" class="btn btn-success btnSubmitSelesai"><i class="fa fa-check" aria-hidden="true"></i> Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal form detail jumlah -->
<div class="modal" id="modal-detail-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document" style="max-width: 720px;">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info btn-save" id="btn_simpan_detail">OK</button>
                {{-- <button type="button" id="btn_simpan_detail" class="btn btn-primary"><i class="icon wb-plus"></i>Simpan --}}
                </button>
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

        const validExtensions = ['pdf', 'jpeg', 'jpg', 'png', 'gif', 'webp'];

$('table').on('change', '.file-input', function() {
  const $input = $(this);
  const imgPath = $input.val();
  const $imgPreview = $input.closest('tr').find('.param_img_holder');
  const extension = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

  if (typeof(FileReader) == 'undefined') {
    $imgPreview.html('This browser does not support FileReader');
    return;
  }

  if (validExtensions.includes(extension)) {
    $imgPreview.empty();
    var reader = new FileReader();
    reader.onload = function(e) {
      $('<iframe/>', {
        src: e.target.result,
        height: 300,
        width: 300,
      }).appendTo($imgPreview);
    }
    $imgPreview.show();
    reader.readAsDataURL($input[0].files[0]);
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
            if($('#kecamatanid').val() != '' && $('#kotaid').val() != '') {
                var urlSekolah = "{{ route('helper.getsekolahjenjang', ['kecamatanid' => ':kecamatanid', 'jenjang' => ':jenjang']) }}";
                urlSekolah = urlSekolah.replace(':kecamatanid', $('#kecamatanid').val());
                urlSekolah = urlSekolah.replace(':jenjang', $('#jenjang').val());

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
            }
            if($('#kotaid').val() != '' && $('#kecamatanid').val() == '') {
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
            if($('#kotaid').val() == '' && $('#kecamatanid').val() == '') {
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
            if($('#kecamatanid').val() != '' && $('#jenjang').val() != ''){
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
            if($('#kecamatanid').val() != '' && $('#jenjang').val() == '') {
                var urlSekolahJenisKec = "{{ route('helper.getsekolahjeniskecamatan', ['jenis' => ':jenis', 'kecamatanid' => ':kecamatanid']) }}";
                urlSekolahJenisKec = urlSekolahJenisKec.replace(':jenis', $('#jenis').val());
                urlSekolahJenisKec = urlSekolahJenisKec.replace(':kecamatanid', $('#kecamatanid').val());

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
            if($('#kecamatanid').val() == '' && $('#kotaid').val() == '') {
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
            if($('#kecamatanid').val() == '' && $('#kotaid').val() == '' && $('#jenjang').val() == ''){
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
        data: v_listDataDetail,
        buttons: {
            buttons: [
            {
                text: 'Lihat',
                className: 'view btn btn-primary btn-sm btn-datatable',
                action: function () {
                    if (detailjumlahtable.rows( { selected: true } ).count() <= 0) {
                        swal("Data belum dipilih", "Silahkan pilih data yang akan dilihat", "error");
                        return;
                    }
                    
                    showmodaldetailjumlah('view');
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
                        swal("Data belum dipilih", "Silahkan pilih data yang akan diubah", "error");
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
                        swal("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                        return;
                    }
                    var id = detailjumlahtable.rows( { selected: true } ).data()[0]['detailpagusarprasid'];
                    deleteDataDetail(id);
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
            if (v_listDataDetail.length > 0) {
                var v_maxobj = v_listDataDetail.reduce((prev, current) => (prev && prev.nourut > current.nourut) ? prev : current);
                v_max = parseInt(v_maxobj.nourut)+1;
            }
            $("#detail_detail_nourut").val(v_max);
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
            $('textarea[id^="detail_detail_"]', "#aktivitashariandetail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailjumlahtable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('input[id^="detail_detail_"]', "#aktivitashariandetail-form").each(function(index, el){
                var inputname = el.id.substring(14, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailjumlahtable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('select[id^="detail_detail_"]', "#aktivitashariandetail-form").each(function(index, el){
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
                setenableddetailjumlah(true);
            }
            else if (mode == "edit") {
                $("#modal-title-detail-form").html('Ubah Data');
                bindformdetailjumlah();
                setenableddetailjumlah(true);
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
                        "search": $("#search").val().toLowerCase()
                    });
                }
            },
            buttons: {
                buttons: [
                    //sementara dinonaktifkan untuk btn setuju
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

                            if (rowData.jenissarpras == "{{ enum::SARPRAS_PERALATAN }}") {
                                $('#modal-detail-parent').modal('show');
                                $("#demo-foo-addrow-sarprastersedia > tbody").empty();
                            }
                            else {
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
                                                else {
                                                    swal.fire("Error!", message, "error"); 
                                                }
                                            }
                                        });  
                                    }           
                                });
                            }

                            // if(status == 5){
                            //     swal.fire("Tidak dapat melakukan proses tender",
                            //         `Data yang anda pilih sudah berstatus ${status == 5 ? statusDesc : ''}`, "error");
                            //     return;
                            // }else {
                            
                            // }

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
                    'orderData': 2,
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
                    'orderData': 3,
                    data: 'subkegnama',
                    render: function (data, type, row) {
                        return row.subkegnama;
                    },
                    name: 'subkegnama'
                },
                {'orderData': 4, data: 'subdetailkegiatan',
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
                // {'orderData': 7, data: 'status', 
                //     render: function(data, type, row){
                //         if(row.status != null){
                //             if(row.status == 1){
                //                 return '<span class="badge bg-secondary">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DRAFT }}</span>';
                //             }else if(row.status == 2){
                //                 return '<span class="badge bg-info">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_PENGAJUAN }}</span>';
                //             }else if (row.status == 3){
                //                 return '<span class="badge bg-success">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DISETUJUI }}</span>';
                //             }else if (row.status == 5){
                //                 return '<span class="badge bg-primary">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_PROSES_TENDER }}</span>';
                //             }else{
                //                 return '<span class="badge bg-danger">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DITOLAK }}</span>';
                //             }
                //         }else{
                //             return '-'
                //         }
                //     },
                //     name: 'status',
                // },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

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

        // Listen for row selection event on kebutuhan-sarpras-table
        kebutuhansarprastable.on('select', function (e, dt, type, indexes) {
            var rowData = kebutuhansarprastable.rows(indexes).data()[0]; // Get selected row data
            var detailpaguanggaranid = rowData.detailpaguanggaranid;
            var progres = rowData.progres;
            var selesai = rowData.isselesai

            console.log(progres);

            setenableprogresbutton(progres, selesai);

            // Load history table with selected detailpaguanggaranid
            loadDetailLaporan(detailpaguanggaranid);
        });

        kebutuhansarprastable.on('deselect', function ( e, dt, type, indexes ) {
            // hide history table
            $('#detail-laporan-table').hide();
        });


        // hide histiry table
        $('#detail-laporan-table').hide();

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
                        // var bar = $(".progress-bar");

                        // if (row.progres > 90)
                        //     $(".progress-bar").addClass("progress-bar-success");
                        // else if (row.progres > 50)
                        //     $(".progress-bar").addClass("progress-bar-warning");
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
            // {'orderData': 2, data: 'status', 
            //     render: function(data, type, row){
            //         if(row.status != null){
            //             if(row.status == 1){
            //                 return '<span class="badge bg-secondary">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DRAFT }}</span>';
            //             }else if(row.status == 2){
            //                 return '<span class="badge bg-info">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_PENGAJUAN }}</span>';
            //             }else if (row.status == 3){
            //                 return '<span class="badge bg-success">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DISETUJUI }}</span>';
            //             }else{
            //                 return '<span class="badge bg-danger">{{ enum::STATUS_KEBUTUHAN_SARPRAS_DESC_DITOLAK }}</span>';
            //             }
            //         }else{
            //             return '-'
            //         }
            //     },
            //     name: 'status',
            // },
            // {
            //     data: 'keterangan',
            //     name: 'keterangan'
            // }
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

        // verifikasi kebutuhan sarpras 
    $(document).on('submit', '#detail-parent-form', function(e){
            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
            var detailpaguanggaranid = rowData.detailpaguanggaranid;

            var url = "{{ route('progresfisik.selesai', ':id') }}";
            url = url.replace(':id', detailpaguanggaranid); 
            
            e.preventDefault();
            
            var formData = new FormData($('#detail-parent-form')[0]);
            
            // if($("#detail_mode").val() == "add") {
            //     var url = "{{ route('progresfisik.storeDetailLaporan') }}"
            //     // url = url.replace(':id', id);   
            // }else if($("#detail_mode").val() == "edit") {
            //     var url = "{{ route('progresfisik.updateDetailLaporan', ':id') }}"
            //     var id = detailaporantable.rows( { selected: true } ).data()[0]['detaillaporanid'];
            //     url = url.replace(':id', id); 
            // }

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
                            swal.fire("Berhasil!", "Berhasil mengubah status progres fisik.", "success");
                            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                            var detailpaguanggaranid = rowData.detailpaguanggaranid;
                            // loadDetailLaporan(detailpaguanggaranid);
                            kebutuhansarprastable.draw();
                            // $('#detailLaporan-form').trigger("reset");
                            // $('#modal-detail-laporan').modal('hide'); 
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

<!-- Sarpras Tersedia -->
<script>
    $(window).on('load', function() {

    // Search input
    $('#demo-input-search2').on('input', function (e) {
        e.preventDefault();
        addrow.trigger('footable_filter', {filter: $(this).val()});
    });

    // Add & Remove Row
    var addrow = $('#demo-foo-addrow-sarprastersedia');
    addrow.footable().on('click', '.delete-row-btn', function() {

        //get the footable object
        var footable = addrow.data('footable');

        //get the row we are wanting to delete
        // var row = $(this).parents('tr:first');
        var row = $(this).parents('tr:first');

        //delete the row
        footable.removeRow(row);
    });
    // Add Row Button
    $('#demo-btn-addrow-sarprastersedia').click(function() {

            //get the footable object
            var footable = addrow.data('footable');
            
            //build up the row we are wanting to add
            // var newRow = '<tr><td class="border-0"><select id="jenispagu" class="custom-select-detail form-control jenispagu @error("jenispagu") is-invalid @enderror" name="jenispagu[]" required><option value="">-- Pilih Jenis Pagu --</option>@foreach (enum::listJenisPagu() as $id)<option {{ old("jenispagu") != "" || old("jenispagu") != null ? "selected" : "" }} value="{{ old("jenispagu") ?? $id }}">{{ enum::listJenisPagu("desc")[$loop->index] }}</option>@endforeach</select></td><td class="border-0" style="width: 200px"><div class="input-group"><span class="p-2">Rp </span><input id="nilaipagu" required type="text" class="form-control nilaipagu count-pagu @error("nilaipagu") is-invalid @enderror" name="nilaipagu[]" value="{{ (old("nilaipagu")) }}"></div></td><td class="border-0"><input id="nokontrak" disabled type="text" class="form-control @error("nokontrak") is-invalid @enderror" name="nokontrak[]" value="{{ (old("nokontrak")) }}" maxlength="100"></td><td class="border-0"><input id="nilaikontrak" disabled type="number" class="form-control @error("nilaikontrak") is-invalid @enderror" name="nilaikontrak[]" value="{{ (old("nilaikontrak")) }}" maxlength="100"></td><td class="border-0"><select id="perusahaanid" class="custom-select-detail form-control perusahaanid @error("perusahaanid") is-invalid @enderror" name="perusahaanid[]" disabled><option value="">-- Pilih Perusahaan --</option>@foreach ($perusahaan as $item)<option {{ old("perusahaanid") != '' || old("perusahaanid") != null ? "selected" : '' }} value="{{ old("perusahaanid") ?? $item->perusahaanid }}">{{ $item->nama }}</option>@endforeach</select></td><td class="border-0"><input disabled type="date" class="form-control @error("tgldari") is-invalid @enderror" id="tgldari" name="tgldari[]" value="{{ old("tgldari") }}" required onchange="compareDates()"></td><td class="border-0"><input type="date" class="form-control @error("tglsampai") is-invalid @enderror" id="tglsampai" name="tglsampai[]" value="{{ old("tglsampai") }}" disabled onchange="compareDates()"></td><td class="border-0"><input type="file" class="form-control file-input" name="file[]" disabled/><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 2MB</span></td><td class="border-0"><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="close" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button></td></tr>';

            var newRow =    '<tr class="added">' +
                                '<td class="border-0">' +
                                    '<div class="form-group">' +
                                        '<select id="detail_jumlah_jenisperalatanid" class="custom-select-detail-jumlah form-control" name="jenisperalatanid[]">' +
                                            '<option value="">-- Jenis Peralatan --</option>' +
                                            '@foreach ($jenisperalatan as $item)' +
                                            '<option value="{{ $item->jenisperalatanid }}">{{ $item->nama }}</option>' +
                                            '@endforeach' +
                                        '</select>' +
                                    '</div>' +
                                '</td>' +
                                '<td class="border-0" style="width: 200px">' +
                                    '<div class="form-group">' +
                                        '<input id="detail_jumlah_jumlah" type="number" class="form-control @error("jumlah") is-invalid @enderror" name="jumlah[]" value="{{ (old("jumlah")) }}" autocomplete="name">' +
                                    '</div>' +
                                '</td>' +
                                '<td class="border-0">' +
                                    '<div class="form-group">' +
                                        '<input id="detail_jumlah_satuan" type="text" class="form-control @error("satuan") is-invalid @enderror" name="satuan[]" value="{{ (old("satuan")) }}" autocomplete="name">' +
                                    '</div>' +
                                '</td>' +
                                '<td class="border-0">' +
                                    '<input type="file" class="form-control file-input" name="file[]" required /><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 2MB</span>' +
                                '</td>' +
                                '<td class="border-0 d-none">' +
                                    '<div class="param_img_holder d-flex justify-content-center align-items-center d-none">' +
                                    '</div>' +
                                '</td>' +
                                '<td class="border-0">' +
                                    '<button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="close" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button>' +
                                '</td>' +
                            '</tr>';

            // let imgParams = <td class="border-0"><div class="param_img_holder d-flex justify-content-center align-items-center d-none"></div></td>
            
            // var newRow = '<tr><td><input type="file" onchange="readURL(this);" name="filesarpraskebutuhan[]" id="filesarpraskebutuhan" class="dropify"/></td><td><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button></td></tr>';


            //add it
            footable.appendRow(newRow);
    });
});
</script>

@endsection
