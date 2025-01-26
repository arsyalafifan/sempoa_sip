<?php
use App\enumVar as enum;
use App\Helpers\Get_field;
?>
@extends('layouts.master')

@section('content')
<style>
    .dataTables_filter {
        /* display: none; */
    }

    /* div.dt-buttons {
        float: right;
    } */

    /* #pegawai-table {
        display: none;
    } */
    .btn-view-pengajuan:hover{
        background-color: rgb(24, 106, 154);
    }

    .modal {
        overflow-y:auto;
    }
    .bg-orange {
        background-color: #fd7e14;
    }
    .bg-secondary {
        background-color: #6c757d;
    }
</style>
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">DAFTAR PENGAJUAN GAJI BERKALA</h5>
        <hr />
        <form class="form-material">
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="unit" class="col-md-12 col-form-label text-md-left">{{ __('Unit') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="unit" class="col-md-12 custom-select form-control" name="unit" autofocus {{ $isSekolah ? 'disabled' : '' }}>
                                <option value="{{ $isSekolah ? enum::UNIT_SEKOLAH : '' }}">{{ $isSekolah ? enum::UNIT_DESC_SEKOLAH : '-- Pilih Unit --' }}</option>
                                @foreach (enum::listUnit() as $id)
                                <option value="{{ $id }}">{{ enum::listUnit('desc')[$loop->index] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>
            <div class="form-group row filter1">
                <div class="col-md-6 @if($isSekolah) d-none @endif">
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
                <div class="col-md-6 @if($isSekolah) d-none @endif">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="kecamatanid" class="col-md-12 col-form-label text-md-left">{{ __('Kecamatan') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="kecamatanid" class="col-md-12 custom-select form-control" name='kecamatanid' autofocus>
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatan as $item)
                                    <option value="{{$item->kecamatanid}}">{{  $item->kodekec . ' ' . $item->namakec }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="form-group row filter1">
                <div class="col-md-6 @if($isSekolah) d-none @endif">
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
                <div class="col-md-6 @if($isSekolah) d-none @endif">
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
            <div class="form-group row filter1">
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
                <div class="col-md-6">
                    <div class="col-md-3">
                        <label for="filter_jenispeg" class="control-label">Jenis Pegawai:</label>
                    </div>
                    <div class="col-md-9">
                        <select id="filter_jenispeg" class="col-md-12 custom-select form-control" name='filter_jenispeg'>
                            <option value="">-- Pilih Jenis Pegawai --</option>
                            @foreach (enum::listJenisPegawai() as $id)
                            <option value="{{ $id }}">{{ enum::listJenisPegawai('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
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
                <div class="col-md-6">
                    <div class="col-md-3">
                        <label for="status" class="control-label">Status:</label>
                    </div>
                    <div class="col-md-9">
                        <select id="filter_status" class="col-md-12 custom-select form-control" name='filter_status'>
                            <option value="">-- Pilih Status --</option>
                            @foreach (enum::listStatusPengajuanGajiBerkala() as $id)
                            <option value="{{ $id }}">{{ enum::listStatusPengajuanGajiBerkala('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
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
                {{-- <h3 class="card-title text-uppercase">SARPRAS</h3> --}}
                {{-- <hr> --}}
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="kebutuhan-sarpras-table">
                        <thead>
                            <tr>
                                <th>Sekolah</th>
                                <th>Status Kenaikan Gaji Berkala</th>
                                <th>Nama Pegawai</th>
                                <th>Pesan Dari Pegawai</th>
                                <th>Pesan Dari Dinas</th>
                                <th>Tgl Pengajuan</th>
                                <th>NIK</th>
                                <th>File</th>
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
<div class="modal" id="modal-realisasi" tabindex="-1" role="dialog" aria-labelledby="modal-title-realisasi" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="max-width: none; width:85%">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-realisasi"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="height: 75vh; max-height: 75vh; overflow-y: auto;">
                <form method="POST" id="formPengajuan" name="formPengajuan" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    {{-- <input type="hidden" id="sarpraskebutuhanid" name="sarpraskebutuhanid"> --}}
                    <input type="hidden" name="detail_mode" id="detail_mode"/>
                    <input type="hidden" name="detail_jenispeg" id="detail_jenispeg"/>
                    {{-- <div class="form-group">
                        <label for="nosp2d" class="control-label">No SP2D:</label>
                        <input id="detail_nosp2d" type="text" class="form-control @error('nosp2d') is-invalid @enderror" name="nosp2d" value="{{ (old('nosp2d')) }}" maxlength="100" required autocomplete="name">
                    </div> --}}
                    <div class="form-group">
                        <label for="tglverifikasi" class="control-label">Tgl Verifikasi:</label>
                        <input type="date" class="form-control @error('tglverifikasi') is-invalid @enderror" id="detail_tglverifikasi" name="tglverifikasi" value="{{ old('tglverifikasi') }}" required onchange="compareDates()">
                        @error('tglverifikasi')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status:</label>
                        <select id="detail_status" class="form-control" name='status'>
                            @if($isSekolah)
                            <option value="">-- Pilih Status --</option>
                            {{-- @foreach (enum::listStatusPengajuanGajiBerkala() as $id) --}}
                            <option value="1">Usul Baru</option>
                            {{-- @endforeach --}}
                            @else
                            <option value="">-- Pilih Status --</option>
                            @foreach (enum::listStatusPengajuanGajiBerkala() as $id)
                            <option value="{{ $id }}">{{ enum::listStatusPengajuanGajiBerkala('desc')[$loop->index] }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="control-label">Keterangan:</label>
                        <textarea id="detail_keterangan" name="keterangan" class="form-control" required></textarea>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered yajra-datatable table-striped" id="pegawai-table">
                            <thead>
                                <tr>
                                    <th>Pengajuan</th>
                                    <th>Sekolah</th>
                                    <th>Status Terakhir</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Upload File</th>
                                    <th>pegawaiid</th>
                                    <th>pegawaipengajuangajiid</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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

<div class="modal" id="modal-detail-pegawai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-pegawai">Lihat Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="formDetailPegawai" name="formDetailPegawai" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    <input type="hidden" id="pegawaiid" name="pegawaiid">
                    <input type="hidden" name="detail_mode" id="detail_mode"/>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="nama" class="col-md-12 col-form-label text-md-left">{{ __('Nama *') }}</label>
                                <input type="text" class="form-control" name="detail_nama" id="detail_nama" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip" class="col-md-12 col-form-label text-md-left">{{ __('NIP *') }}</label>
                                <input type="text" class="form-control" name="detail_nip" id="detail_nip" disabled/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgllahir" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Lahir *') }}</label>
                                <input id="detail_tgllahir" type="date" class="form-control" name="tgllahir" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="golpegawaiid" class="col-md-12 col-form-label text-md-left">{{ __('Golongan *') }}</label>
    
                                    <select id="detail_golpegawaiid" class="custom-select1 form-control @error('golpegawaiid') is-invalid @enderror" name='golpegawaiid' disabled>
                                        <option value="">---</option>
                                        @foreach (enum::listGolongan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listGolongan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenisjab" class="col-md-12 col-form-label text-md-left">{{ __('Jenis Jabatan *') }}</label>
    
                                    <select id="detail_jenisjab" class="custom-select1 form-control @error('jenisjab') is-invalid @enderror" name='jenisjab' disabled>
                                        <option value="">---</option>
                                        @foreach (enum::listJenisJabatan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listJenisJabatan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="golruangberkalaid" class="col-md-12 col-form-label text-md-left">{{ __('Gol Ruang Berkala *') }}</label>
    
                                    <select id="detail_golruangberkalaid" class="custom-select1 form-control @error('golruangberkalaid') is-invalid @enderror" name='golruangberkalaid' disabled>
                                        <option value="">---</option>
                                        @foreach (enum::listGolongan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listGolongan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jabatanid" class="col-md-12 col-form-label text-md-left">{{ __('Jabatan *') }}</label>
    
                                    <select id="detail_jabatanid" class="custom-select1 form-control @error('jabatanid') is-invalid @enderror" name='jabatanid' disabled>
                                        <option value="">---</option>
                                        @foreach ($jabatan as $item)
                                            <option value="{{ $item->jabatanid }}"> {{ $item->namajabatan }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eselon" class="col-md-12 col-form-label text-md-left">{{ __('Eselon *') }}</label>
    
                                    <select id="detail_eselon" class="custom-select1 form-control @error('eselon') is-invalid @enderror" name='eselon' disabled>
                                        <option value="">---</option>
                                        @foreach (enum::listEselon() as $id)
                                            <option value="{{ $id }}"> {{ enum::listEselon('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgajiberkalathn" class="col-md-12 col-form-label text-md-left">{{ __('MS Gaji Berkala Thn *') }}</label>
    
                                <input id="detail_msgajiberkalathn" type="number" class="form-control" name="msgajiberkalathn" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgajiberkalabln" class="col-md-12 col-form-label text-md-left">{{ __('MS Gaji Berkala Bln *') }}</label>

                                <input id="detail_msgajiberkalabln" type="number" class="form-control" name="msgajiberkalabln" disabled>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tmtberkala" class="control-label">TMT Berkala</label>
                                <input id="detail_tmtberkala" type="date" class="form-control" name="tmtberkala" disabled>
                            </div>
                        </div>
                    </div>
                    <h3>History</h3>
                    <hr>
                    <table class="table table-bordered yajra-datatable table-striped" id="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal tambah -->
<div class="modal" id="modal_upload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-upload">Upload File</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="formUpload" name="formUpload" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    {{-- <input type="hidden" name="detail_mode" id="detail_mode"/> --}}
                    <input type="hidden" name="pegawaipengajuangajiid" id="pegawaipengajuangajiid"/>
                    <div class="form-group">
                        <label for="file" class="control-label">File:</label>
                        <input id="detail_file" type="file" class="file-input fileinput fileinput-new input-group" data-provides="fileinput" name="file"/><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 5MB</span>
                        <div class="param_img_holder d-flex justify-content-center align-items-center">
                        </div>
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

<!-- foo table -->
<script src="{{asset('/dist/js/pages/footable-init.js')}}"></script>
<script src="{{asset('/dist/plugins/bower_components/footable/js/footable.all.min.js')}}"></script>
<script src="{{asset('/dist/plugins/bower_components/bootstrap-select/bootstrap-select.min.js')}}" type="text/javascript"></script>

<script>

    $(document).ready(function () {

        $('.custom-select-detail').select2({
            dropdownParent: $('#modal-realisasi .modal-content')
        });

        $('.custom-select').select2();

        // form pengajuan 
        $(document).on('submit', '#formPengajuan', function(e){
            e.preventDefault();

            var url = '';

            var formData = $(this).serializeArray();

            var checkedData = [];

            $('input[type=checkbox]:checked').each(function() {
                var rowData = $(this).closest('tr').find('td').map(function() {
                    return $(this).text();
                }).get();

                var pegawai = {
                    'nama': rowData[1],
                    'nip': rowData[2],
                    'pegawaiid': $(this).closest('td').find('input[name="check-pegawaiid"]').val()
                };

                checkedData.push(pegawai);
            });

            formData.push({name: 'checkedData', value: JSON.stringify(checkedData)});

            if($("#detail_mode").val() == "add") {
                var url = "{{ route('pengajuangajiberkala.storePengajuan') }}"
            }else if($("#detail_mode").val() == "edit") {
                var url = "{{ route('pengajuangajiberkala.updatePengajuan', ':id') }}"
                var rowData = kebutuhansarprastable.rows( { selected: true } ).data()[0];
                var id = rowData.pengajuangajiberkalaid;
                url = url.replace(':id', id); 
            }

            $.ajax({
                // url: "{{ route('pengajuangajiberkala.storePengajuan') }}",
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    // alert(response.message);
                    swal.fire("Berhasil!", `${response.message}`, "success");
                    // Lakukan sesuatu setelah berhasil disimpan
                    $('#modal-realisasi').modal('hide');
                    kebutuhansarprastable.draw();
                },
                error: function(xhr) {
                    // Tampilkan pesan kesalahan jika ada
                    swal.fire("Gagal!", `${xhr.responseJSON.message}`, "error");
                    // console.log(xhr.responseText);
                }
            });
        })

        // form upload file 
        $(document).on('submit', '#formUpload', function(e){
            var url = '';
            var id = '';
            
            e.preventDefault();
            
            let formData = new FormData($('#formUpload')[0]);

            // if($("#detail_mode").val() == "add") {
            //     var url = "{{ route('realisasi.storeRealisasi', ':id') }}"
            //     id = kebutuhansarprastable.rows( { selected: true } ).data()[0]['sarpraskebutuhanid'];
            //     url = url.replace(':id', id);   
            // }else if($("#detail_mode").val() == "edit") {
            //     var url = "{{ route('realisasi.updateRealisasi', ':id') }}"
            //     var rowData = realisasitable.rows( { selected: true } ).data()[0];
            //     id = rowData.realisasiid;
            //     url = url.replace(':id', id); 
            // }

            url = "{{ route('pengajuangajiberkala.uploadFile', ':id') }}";
            id = pegawaitable.rows( { selected: true } ).data()[0]['pegawaipengajuangajiid'];
            url = url.replace(':id', id); 

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
                            swal.fire("Berhasil!", "File berhasil diupload.", "success");
                            // kebutuhansarprastable.draw();
                            // formUploadtable.draw();
                            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                            var pengajuangajiberkalaid = rowData.pengajuangajiberkalaid;
                            loadPegawaiEdit(pengajuangajiberkalaid);

                            $('#formUpload').trigger("reset");
                            $('#modal_upload').modal('hide'); 
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                        var data = jqXHR.responseJSON;
                        console.log(data.errors);// this will be the error bag.
                        if (data.errors.file != undefined || data.errors.file != null) {
                            swal.fire("Error", `<p>${data.errors.file}</p>`, "error");
                        }else{
                            swal.fire('Error', 'Error', 'error')
                    }
                }
            })
        });

        $(document).on('click', '.downloadfile', function() {
            var url = "{{ route('pengajuangajiberkala.downloadFile', ':id') }}";
            var id = $(this).val();
            console.log(id);
            url = url.replace(':id', id);

            // Mengirimkan permintaan Ajax
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
                    console.log(data);
                    let a = document.createElement('a');
                    let url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = $(this).attr('id');
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                },
                error: function(xhr) {
                    // Tampilkan pesan kesalahan jika ada
                    swal.fire("Gagal!", `File tidak ada atau file belum diupload`, "error");
                }
            });
        });


        var pegawaitable;
        var kebutuhansarprastable = $('#kebutuhan-sarpras-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            dom: 'Bfrtip',
            select: true,
            ordering: false,
            searching: false,
            language: {
                lengthMenu: "Menampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data",
                info: "Halaman _PAGE_ dari _PAGES_ (Total: _TOTAL_ Data)",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ data)",
                search: "Pencarian :",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                }
            },
            ajax: {
                url: "{{ route('pengajuangajiberkala.index') }}",
                dataSrc: function (response) {
                    response.recordsTotal = response.data.count;
                    response.recordsFiltered = response.data.count;
                    return response.data.data;
                },
                data: function (d) {
                    return $.extend({}, d, {
                        "unit": $("#unit").val().toLowerCase(),
                        "kotaid": $("#kotaid").val().toLowerCase(),
                        "kecamatanid": $("#kecamatanid").val().toLowerCase(),
                        "jenjang": $("#jenjang").val().toLowerCase(),
                        "sekolahid": $("#sekolahid").val().toLowerCase(),
                        "jenis": $("#jenis").val().toLowerCase(),
                        "filter_status": $("#filter_status").val().toLowerCase(),
                        "filter_jenispeg": $("#filter_jenispeg").val().toLowerCase(),
                        "search": $("#search").val().toLowerCase()
                    });
                }
            },
            buttons: {
                buttons: [
                    {
                        text: '<i class="fa fa-plus" aria-hidden="true"></i> Verifikasi',
                        className: 'edit btn btn-primary mb-3 btn-datatable',
                        action: function() {

                            if (kebutuhansarprastable.rows( { selected: true } ).count() <= 0) {
                                // $('#modal-realisasi').modal('show');
                                // setenabledtbutton('0');
                                loadPegawai();
                                showmodaldetail('add');
                                // pegawaitable.draw();
                            }else {
                                var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
                                var pengajuangajiberkalaid = rowData.pengajuangajiberkalaid;

                                loadPegawaiEdit(pengajuangajiberkalaid);
                                showmodaldetail('edit');
                            }


                            // var file = rowData.file;
                            // console.log(pengajuangajiberkalaid);
                            // // var detailpagupenganggaranid = rowData.detailpagupenganggaranid;
                            // $('#modal-realisasi').modal('show');
                            // if(file != null) {
                            //     $('#detail_file').prop('required',false)
                            // }else{
                            //     $('#detail_file').prop('required', true)
                            // }
                        }
                    },
                    {
                        text: '<i class="fa fa-info-circle" aria-hidden="true"></i> Lihat',
                        className: 'edit btn btn-warning mb-3 btn-datatable',
                        action: function() {

                            if (kebutuhansarprastable.rows( { selected: true } ).count() <= 0) {
                                swal.fire("Data belum dipilih", "Silakan pilih data yang ingin diubah", "error");
                                return;
                            }
                            else{
                                var pegawaiid = kebutuhansarprastable.rows( { selected: true } ).data()[0]['pegawaiid'];
                                var pegawaipengajuangajiid = kebutuhansarprastable.rows( { selected: true } ).data()[0]['pegawaipengajuangajiid'];
                                loadDetailPegawai(pegawaiid);
                                loadHistoryTable(pegawaipengajuangajiid);
                            }
                        }
                    },
                    {
                    text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                    className: 'edit btn btn-danger mb-3 btn-datatable',
                    action: function () {
                        if (kebutuhansarprastable.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                            return;
                        }
                        var id = kebutuhansarprastable.rows( { selected: true } ).data()[0]['pengajuangajiberkalaid'];
                        var url = "{{ route('pengajuangajiberkala.destroy', ':id') }}"
                        url = url.replace(':id', id);
                        // var nama =  pegawaitable.rows( { selected: true } ).data()[0]['namasekolah'];
                        swal.fire({   
                            title: "Apakah anda yakin akan menghapus data ini?",   
                            text: "Data yang terhapus tidak dapat dikembalikan lagi!",   
                            icon: "warning",   
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
                                    type: "DELETE",
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
                                            kebutuhansarprastable.draw();
                                        }
                                        else {
                                            swal.fire("Error!", data, "error"); 
                                        }
                                    },
                                    error: function(xhr) {
                                        // Tampilkan pesan kesalahan jika ada
                                        swal.fire("Gagal!", `${xhr.responseJSON.message}`, "error");
                                        // console.log(xhr.responseText);
                                    }
                                });  
                            }           
                        });
                    }
                }
                ]
            },
            columns: [
                {
                    'orderData': 1,
                    data: 'sekolahid',
                    render: function (data, type, row) {
                        if (row.namasekolah && row.namasekolah != null) {
                            return row.namasekolah;
                        }
                        else {
                            return '-'
                        }
                    },
                    name: 'namasekolah'
                },
                {
                    'orderData': 2,
                    data: 'status',
                    render: function(data, type, row){
                        if (row.status != null) {
                            var listStatusPengajuanGajiBerkala = @json(enum::listStatusPengajuanGajiBerkala($id = ''));
                            // let listStatusPengajuanGajiBerkala = JSON.parse('{!! json_encode(enum::listStatusPengajuanGajiBerkala()) !!}');
                            let Desc;
                            listStatusPengajuanGajiBerkala.forEach((value, index) => {
                                if(row.status == index + 1) {
                                    Desc = value;
                                }
                            });
                            if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}"){
                                return '<span class="badge badge-pill bg-white text-dark">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_USUL_BARU }}</span>';
                            }else if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_BV }}"){
                                return '<span class="badge badge-pill bg-secondary">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_BV }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TMS }}"){
                                return '<span class="badge badge-pill bg-danger">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TMS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_MS }}"){
                                return '<span class="badge badge-pill bg-info">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_MS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TURUN_STATUS }}"){
                                return '<span class="badge badge-pill bg-orange">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TURUN_STATUS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_PROSES_BKD }}"){
                                return '<span class="badge badge-pill bg-warning">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_PROSES_BKD }}</span>';
                            }
                            else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"){
                                return '<span class="badge badge-pill bg-success">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_SELESAI }}</span>';
                            }
                        }
                        else {
                            return '-'
                        }
                    },
                    name: 'status'
                },
                {
                    'orderData': 3,
                    data: 'nama',
                    render: function(data, type, row){
                        return (row.nama);
                    },
                    name: 'nama'
                },
                {'orderData': 4, data: 'ketpegawai', name: 'ketpegawai',
                    render: function(data, type, row) {
                        if (row.ketpegawai != null || row.ketpegawai != '') {
                            return row.ketpegawai;
                        }
                        else {
                            return '-';
                        }
                    }
                },
                {'orderData': 5, data: 'keterangan', name: 'keterangan',
                    render: function(data, type, row) {
                        if (row.keterangan != null || row.keterangan != '') {
                            return row.keterangan;
                        }
                        else {
                            return '-';
                        }
                    }
                },
                {'orderData': 6, data: 'tglverifikasi', name: 'tglverifikasi',
                    render: function(data, type, row) {
                        return row.tglverifikasi
                    }
                },
                {
                    'orderData': 7,
                    data: 'nip',
                    render: function(data, type, row){
                        return (row.nip);
                    },
                    name: 'nip'
                },
                {
                    'orderData': 8,
                    data: 'file',
                    render: function(data, type, row){
                        if(row.status && row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}") {
                            return `
                                <button id="${row.file}" type="button" value = "${row.pegawaipengajuangajiid}" class="downloadfile btn btn-info waves-effect waves-light m-r-10">
                                    {{ __('Download File') }}
                                </button>
                            `;
                        }
                        else if (row.status && row.jenispeg == "{{ enum::JENIS_PEGAWAI_PPPK }}" && row.status != "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"){
                            return `
                                <button id="${row.file}" type="button" value = "${row.pegawaipengajuangajiid}" class="downloadfile btn btn-info waves-effect waves-light m-r-10">
                                    {{ __('Download File') }}
                                `;
                        }
                        else {
                            return '';
                        }
                    },
                    name: 'file'
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        function setenabletenderbutton(status = '') {
            kebutuhansarprastable.buttons( '#btn-batalprogrespembangunan' ).disable();
            kebutuhansarprastable.buttons( '#btn-progrespembangunan' ).disable();
            if (status == '{{ enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN }}') {
                kebutuhansarprastable.buttons( '#btn-batalprogrespembangunan' ).enable();
                kebutuhansarprastable.buttons( '#btn-progrespembangunan' ).disable();
            }
            else if (status == '{{ enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER }}') {
                kebutuhansarprastable.buttons( '#btn-batalprogrespembangunan' ).disable();
                kebutuhansarprastable.buttons( '#btn-progrespembangunan' ).enable();
            }
        }

        $('#unit').change(function () {
            if ($(this).val() === '{{ enum::UNIT_OPD }}') {
                $('.filter1').hide();
                $('#kotaid').val('');
                $('#kecamatanid').val('');
                $('#jenjang').val('');
                $('#sekolahid').val('');
            } else {
                $('.filter1').show();
            }

            pegawaitable.draw();
            pegawaitable.clear().draw();

            kebutuhansarprastable.draw();
            kebutuhansarprastable.clear().draw();
            
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
            // $('#pegawai-table').hide();
        });
        $('#kecamatanid').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
        });
        $('#jenjang').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
        });
        $('#jenis').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
        });
        $('#sekolahid').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
        });
        $('#filter_status').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
        });
        $('#filter_jenispeg').change(function () {
            kebutuhansarprastable.draw();
            // $('#pegawai-table').hide();
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
                //  $('#pegawai-table').hide();
            }
        });

        function loadPegawai() {
            var sekolahId = $('#sekolahid').val();
            var unit = $('#unit').val();
            var url = "{{ route('pengajuangajiberkala.loadPegawai') }}";
            // url = url.replace(':id', sarpraskebutuhanid);

            $.ajax({
                url: url,
                type: "GET",
                data: {
                    sekolahid: sekolahId, // Menambahkan data sekolahid ke dalam objek data yang akan dikirimkan ke backend
                    unit: unit,
                },
                success: function (response) {
                    // console.log(response.data.data);

                    pegawaitable.clear();

                    // for (var i = 0; i < response.data.count; i++) {
                    //     pegawaitable.row.add({
                    //         pegawaiid: response.data.data[i].pegawaiid,
                    //         nama: response.data.data[i].nama,
                    //         nip: response.data.data[i].nip,
                    //     });
                    // }

                    for (const record in response.data.data) {
                        pegawaitable.row.add({
                            pegawaiid: response.data.data[record].pegawaiid,
                            namasekolah: response.data.data[record].namasekolah,
                            nama: response.data.data[record].nama,
                            nip: response.data.data[record].nip,
                            status: response.data.data[record].status,
                            jenispeg: response.data.data[record].jenispeg,
                        });
                    }

                    pegawaitable.draw();
                    $('#pegawai-table').show();
                    $('#pegawai-table tbody input[type="checkbox"]').prop('checked', false);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        function loadPegawaiEdit(pengajuangajiberkalaid) {
            var url = "{{ route('pengajuangajiberkala.loadPegawaiEdit', ':id') }}";
            url = url.replace(':id', pengajuangajiberkalaid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    pegawaitable.clear();

                    for (const record in response.data.data) {
                        pegawaitable.row.add({
                            pegawaiid: response.data.data[record].pegawaiid,
                            namasekolah: response.data.data[record].namasekolah,
                            nama: response.data.data[record].nama,
                            nip: response.data.data[record].nip,
                            status: response.data.data[record].status,
                            pegawaipengajuangajiid: response.data.data[record].pegawaipengajuangajiid,
                            file: response.data.data[record].file,
                            jenispeg: response.data.data[record].jenispeg,
                        });
                    }

                    pegawaitable.draw();
                    $('#pegawai-table').show();
                    $('#pegawai-table tbody input[type="checkbox"]').prop('checked', true).prop('disabled', true);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        function loadHistoryTable(pegawaipengajuangaji) {
            var url = "{{ route('pengajuangajiberkala.loadHistoryPegawai', ':id') }}";
            url = url.replace(':id', pegawaipengajuangaji);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    historytable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        historytable.row.add({
                            tglverifikasi: response.data.data[i].tglverifikasi,
                            status: response.data.data[i].status,
                            keterangan: response.data.data[i].keterangan,
                            ketpegawai: response.data.data[i].ketpegawai,
                            pegawaipengajuangaji: response.data.data[i].pegawaipengajuangaji
                        });
                    }

                    historytable.draw();
                    $('#history-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }


        function loadDetailPegawai(pegawaiid) {
            var url = "{{ route('pengajuangajiberkala.loadDetailPegawai', ':id') }}";
            url = url.replace(':id', pegawaiid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $("#detail_nama").val(response.data.data[0].nama);
                    $("#detail_nip").val(response.data.data[0].nip);
                    $("#detail_tgllahir").val(response.data.data[0].tgllahir);
                    $("#detail_golpegawaiid").val(response.data.data[0].golpegawaiid).trigger('change');
                    $("#detail_jenisjab").val(response.data.data[0].jenisjab).trigger('change');
                    $("#detail_golruangberkalaid").val(response.data.data[0].golruangberkalaid).trigger('change');
                    $("#detail_jabatanid").val(response.data.data[0].jabatanid).trigger('change');
                    $("#detail_eselon").val(response.data.data[0].eselon).trigger('change');
                    $("#detail_msgajiberkalathn").val(response.data.data[0].msgajiberkalathn);
                    $("#detail_msgajiberkalabln").val(response.data.data[0].msgajiberkalabln);
                    $("#detail_tmtberkala").val(response.data.data[0].tmtberkala);
                    $("#modal-detail-pegawai").modal('show');
                    console.log(response);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }



        // // Listen for row selection event on kebutuhan-sarpras-table
        // kebutuhansarprastable.on('select', function (e, dt, type, indexes) {
        //     var rowData = kebutuhansarprastable.rows(indexes).data()[0]; // Get selected row data
        //     var sarpraskebutuhanid = rowData.sarpraskebutuhanid;
        //     var status = rowData.status;

        //     // Load history table with selected sarpraskebutuhanid
        //     setenabletenderbutton(status)
        //     loadPegawai();
        // });

        // kebutuhansarprastable.on('deselect', function ( e, dt, type, indexes ) {
        //     setenabletenderbutton();
        //     // hide history table
        //     $('#pegawai-table').hide();
        // });

    function resetformdetail() {
        $("#formPengajuan")[0].reset();
        const $input = $('#detail_file')
        const $imgPreview = $input.closest('div').find('.param_img_holder');
        $imgPreview.empty();
        // var v_max = 1;
        // if (v_listDataDetail.length > 0) {
        //     var v_maxobj = v_listDataDetail.reduce((prev, current) => (prev && prev.nourut > current.nourut) ? prev : current);
        //     v_max = parseInt(v_maxobj.nourut)+1;
        // }
        // $("#detail_detail_nourut").val(v_max);
        //alert(v_listDataDetail.length);
        //alert(v_listDataDetail.length + '->' + JSON.stringify(max));

        $('span[id^="err_detail_"]', "#formPengajuan").each(function(index, el){
            $('#'+el.id).html("");
        });

        $('select[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("").trigger('change');
            }
        });
        $('input[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("");
            }
        });
        $('textarea[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            if (inputname != "mode") {
                $("#"+el.id).val("");
            }
        });
    }

    function bindformdetail() {
        $('textarea[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            //alert(inputname);
            if (inputname != "mode") {
                $("#"+el.id).val(kebutuhansarprastable.rows( { selected: true } ).data()[0][inputname]);
            }
        });
        
        $('input[id^="detail_"]', "#formPengajuan").each(function(index, el){
            if(el.type != 'file') {
                var inputname = el.id.substring(7, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(kebutuhansarprastable.rows( { selected: true } ).data()[0][inputname]);
                }
            }

            if(el.type == 'file') {
                var inputname = el.id.substring(7, el.id.length);
                if(inputname != "mode") {
                    const $input = $('#detail_file');
                    const imgPath = kebutuhansarprastable.rows( {selected: true } ).data()[0][inputname];
                    const $imgPreview = $input.closest('div').find('.param_img_holder');
                    const extension = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                    if(extension.includes('pdf')) {
                        $('<iframe/>', {
                            src: `/storage/uploaded/sarpraskebutuhan/${imgPath}`,
                            height: 300,
                            width: 300,
                        }).appendTo($imgPreview); 
                    }else {
                        $('<img/>', {
                            src: `/storage/uploaded/sarpraskebutuhan/${imgPath}`,
                            // height: 300,
                            width: 150,
                        }).appendTo($imgPreview); 
                    }
                }
            }
        });
        
        $('select[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var inputname = el.id.substring(7, el.id.length);
            //alert(inputname);
            if (inputname != "mode") {
                $("#"+el.id).val(kebutuhansarprastable.rows( { selected: true } ).data()[0][inputname]).trigger('change');
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
        
        $('textarea[id^="detail_"]', "#formPengajuan").each(function(index, el){
            $("#"+el.id).prop("readonly", !value);
        });
        $('input[id^="detail_"]', "#formPengajuan").each(function(index, el){
            $("#"+el.id).prop("readonly", !value);
        });
        $('select[id^="detail_"]', "#formPengajuan").each(function(index, el){
            var isSekolah = {{ $isSekolah ? 'true' : 'false' }};
            // if (isSekolah == true) {
            //     $("#"+el.id).prop("disabled",  true);
            // }
            // else {
            //     $("#"+el.id).prop("disabled", !value);
            // }
        });
    }

    $('#detail_status').change(function () {
        if($("#detail_mode").val() == "edit") {
            var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
            var pengajuangajiberkalaid = rowData.pengajuangajiberkalaid;
            loadPegawaiEdit(pengajuangajiberkalaid);
            setenabledtbutton();
        }
    });

    var v_modedetail = "";
    function showmodaldetail(mode) {
        v_modedetail = mode;
        $("#detail_mode").val(mode);
        resetformdetail();
        // $('#detail_status').attr('disabled', 'disabled');
        if (mode == "add") {
            $("#modal-title-realisasi").html('Tambah Data');
            // bindformdetail();
            setenableddetail(true);
            // setenabledtbutton()
            pegawaitable.buttons( '.btn_modal_upload' ).disable();
            // console.log($("#detail_mode").val());
            // $('#detail_file').prop('required', true);
        }
        else if (mode == "edit") {
            $("#modal-title-realisasi").html('Ubah Data');
            bindformdetail();
            setenableddetail(true);
            setenabledtbutton()
            // $('#detail_file').prop('required', false);
            // $('#pegawai-table tbody input[type="checkbox"]').prop('checked', true);
        }
        else {
            $("#modal-title-realisasi").html('Lihat Data');
            bindformdetail();
            setenableddetail(false);
            setenabledtbutton()
        }
        
        $("#modal-realisasi").modal('show');
    }

    function hidemodaldetail() {
        $("#modal-realisasi").modal('hide');
    }

    function setenabledtbutton() {
        var rowData = kebutuhansarprastable.rows({ selected: true }).data()[0]; // Get selected row data
        var jenispeg = rowData.jenispeg;

        pegawaitable.buttons( '.btn_modal_upload' ).disable();

        if (jenispeg != "{{ enum::JENIS_PEGAWAI_PPPK }}" && $('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}") {
            pegawaitable.buttons( '.btn_modal_upload' ).enable();
        }
        else if ($('#detail_mode').val() == 'edit' && jenispeg == "{{ enum::JENIS_PEGAWAI_PPPK }}" &&  $('#detail_status').val() != "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"/*&& ($('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}" || $('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}")*/) {
            pegawaitable.buttons( '.btn_modal_upload' ).enable();
        }
    }

    // pegawaitable.buttons( '.btn_modal_upload' ).disable();
    // Listen for row selection event on kebutuhan-sarpras-table
    // pegawaitable.on('select', function (e, dt, type, indexes) {
    //     var rowData = pegawaitable.rows(indexes).data()[0]; // Get selected row data
    //     var jenispeg = rowData.jenispeg;

    //     // Load pegawaitable with selected sarpraskebutuhanid
    //     if ($('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}") {
    //         pegawaitable.buttons( '.btn_modal_upload' ).enable();
    //     }
    //     else if (jenispeg == "{{ enum::JENIS_PEGAWAI_PPPK }}" && ($('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}" || $('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}")) {
    //         pegawaitable.buttons( '.btn_modal_upload' ).enable();
    //     }
    // });

        // Initialize history table
        var pegawaitable = $('#pegawai-table').DataTable({
            responsive: true,
            processing: true,
            // serverSide: true,
            pageLength: 50,
            dom: 'Bfrtip',
            select: true,
            ordering: false,
            searching: true,
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
            // ... your pegawai-table initialization options ...
            columns: [
                {
                    data: 'pegawaiid',
                    name: 'pegawaiid',
                    render: function(data, type, row) {
                        if(row.pegawaiid != null) {
                            return `
                                <td width="15" class="text-center bg-success">
                                    <input name="pegawaiid" type="hidden" value="${row.pegawaiid}"/>
                                    <input name="check-pegawaiid" id="check-pegawaiid" class="checkmenu" type="checkbox" value="${row.pegawaiid}"/>
                                </td>
                            `;
                        }
                    }
                },
                {
                    data: 'namasekolah',
                    name: 'namasekolah',
                    render: function(data, type, row) {
                        if(row.namasekolah && row.namasekolah != null) {
                            return row.namasekolah;
                        }
                        else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        if(row.status != null) {
                            var listStatusPengajuanGajiBerkala = @json(enum::listStatusPengajuanGajiBerkala($id = ''));
                            // let listStatusPengajuanGajiBerkala = JSON.parse('{!! json_encode(enum::listStatusPengajuanGajiBerkala()) !!}');
                            let Desc;
                            listStatusPengajuanGajiBerkala.forEach((value, index) => {
                                if(row.status == index + 1) {
                                    Desc = value;
                                }
                            });
                            if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}"){
                                return '<span class="badge badge-pill bg-white text-dark">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_USUL_BARU }}</span>';
                            }else if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_BV }}"){
                                return '<span class="badge badge-pill bg-secondary">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_BV }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TMS }}"){
                                return '<span class="badge badge-pill bg-danger">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TMS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_MS }}"){
                                return '<span class="badge badge-pill bg-info">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_MS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TURUN_STATUS }}"){
                                return '<span class="badge badge-pill bg-orange">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TURUN_STATUS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_PROSES_BKD }}"){
                                return '<span class="badge badge-pill bg-warning">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_PROSES_BKD }}</span>';
                            }
                            else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"){
                                return '<span class="badge badge-pill bg-success">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_SELESAI }}</span>';
                            }
                        }else{
                            return '-'
                        }
                    }
                },
                {
                    data: 'nama',
                    name: 'nama',
                    render: function(data, type, row) {
                        if(row.nama != null) {
                            return row.nama;
                        }
                    }
                },
                {
                    data: 'nip',
                    name: 'nip',
                    render: function(data, type, row) {
                        if(row.nip != null) {
                            return row.nip;
                        }
                    }
                },
                {
                    data: 'file',
                    name: 'file',
                    render: function(data, type, row) {
                        // if(row.file != null) {
                            console.log(row.jenispeg);
                            if (row.jenispeg != "{{ enum::JENIS_PEGAWAI_PPPK }}" && $('#detail_status').val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"){
                                if (row.file != null) {
                                    // return "<div class=\"d-flex justify-content-center align-items-center\"><iframe src=\"{{ asset('storage/uploaded/pengajuangajiberkala/"+row.file+"') }}\" height=\"300\" /></div>";
                                    return `<div class="d-flex justify-content-center align-items-center"><iframe src="{{ asset('storage/uploaded/pengajuangajiberkala/${row.file}') }}" height="300" /></div>`;
                                } else {
                                    return `
                                        <div class="alert alert-dark" role="alert">
                                            anda belum meng-upload file
                                        </div>
                                    `
                                }
                            }
                            else if ($('#detail_mode').val() == 'edit' && row.jenispeg == "{{ enum::JENIS_PEGAWAI_PPPK }}" &&  $('#detail_status').val() != "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}" /*&& ($('#detail_status').val() == '{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}' || $('#detail_status').val() == '{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}')*/){
                                if (row.file != null) {
                                    // return "<div class=\"d-flex justify-content-center align-items-center\"><iframe src=\"{{ asset('storage/uploaded/pengajuangajiberkala/"+row.file+"') }}\" height=\"300\" /></div>";
                                    return `<div class="d-flex justify-content-center align-items-center"><iframe src="{{ asset('storage/uploaded/pengajuangajiberkala/${row.file}') }}" height="300" /></div>`;
                                } else {
                                    return `
                                        <div class="alert alert-dark" role="alert">
                                            anda belum meng-upload file
                                        </div>
                                    `
                                }
                            }
                            else{
                                return '';
                            }
                        // }
                    }
                },
                {
                    data: 'pegawaiid',
                    name: 'pegawaiid',
                    render: function(data, type, row) {
                        if(row.pegawaiid != null) {
                            return row.pegawaiid;
                        }
                    },
                    visible: false,
                },
                {
                    data: 'pegawaipengajuangajiid',
                    name: 'pegawaipengajuangajiid',
                    render: function(data, type, row) {
                        if(row.pegawaipengajuangaji && row.pegawaipengajuangajiid != null) {
                            return row.pegawaipengajuangajiid;
                        }else {
                            return ''
                        }
                    },
                    visible: false,
                },
            ],
            buttons: {
                buttons: [
                    {
                        text: '<i class="fa fa-plus" aria-hidden="true"></i> Upload File',
                        id: 'btn_modal_upload',
                        className: 'edit btn btn-info mb-3 btn-datatable btn_modal_upload',
                        action: function() {

                            if (pegawaitable.rows( { selected: true } ).count() <= 0) {
                                swal.fire("Data belum dipilih", "Silakan pilih data terlebih dahulu", "error");
                                return;
                            }
                            else{
                                var rowData = pegawaitable.rows({ selected: true }).data()[0]; // Get selected row data
                                var pegawaipengajuangajiid = rowData.pegawaipengajuangajiid;
                                // var detailjumlahsarprasid = rowData.detailjumlahsarprasid;
                                // // var detailpagusarprasid = rowData.detailpagusarprasid;
                                // console.log(detailjumlahsarprasid);
                                $('#modal_upload').modal('show');
                                $('#pegawaipengajuangajiid').val(pegawaipengajuangajiid);

                                // showFotoDetailJumlahSarpras(detailjumlahsarprasid)
                            }
                        }
                    },
                ]
            },
        });

        $('#modal-realisasi').on('hidden.bs.modal', function (e) {
            $('#pegawai-table').DataTable().search('').draw();
        });

        var historytable = $('#history-table').DataTable({
            responsive: true,
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
            // ... your history-table initialization options ...
            columns: [{
                    data: 'tglverifikasi',
                    name: 'tglverifikasi',
                    render: function(data, type, row) {
                        if(row.tglverifikasi != null) {
                            return (DateFormat(row.tglverifikasi));
                        }
                        else{
                            return '-'
                        }
                    }
                },
                {'orderData': 2, data: 'status', 
                    render: function(data, type, row){
                        if(row.status != null) {
                            var listStatusPengajuanGajiBerkala = @json(enum::listStatusPengajuanGajiBerkala($id = ''));
                            // let listStatusPengajuanGajiBerkala = JSON.parse('{!! json_encode(enum::listStatusPengajuanGajiBerkala()) !!}');
                            let Desc;
                            listStatusPengajuanGajiBerkala.forEach((value, index) => {
                                if(row.status == index + 1) {
                                    Desc = value;
                                }
                            });
                            if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_USUL_BARU }}"){
                                return '<span class="badge badge-pill bg-white text-dark">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_USUL_BARU }}</span>';
                            }else if(row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_BV }}"){
                                return '<span class="badge badge-pill bg-secondary">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_BV }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TMS }}"){
                                return '<span class="badge badge-pill bg-danger">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TMS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_MS }}"){
                                return '<span class="badge badge-pill bg-info">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_MS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_TURUN_STATUS }}"){
                                return '<span class="badge badge-pill bg-orange">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_TURUN_STATUS }}</span>';
                            }else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_PROSES_BKD }}"){
                                return '<span class="badge badge-pill bg-warning">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_PROSES_BKD }}</span>';
                            }
                            else if (row.status == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}"){
                                return '<span class="badge badge-pill bg-success">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_SELESAI }}</span>';
                            }
                        }else{
                            return '-'
                        }
                    },
                    name: 'status',
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                }
            ]
        });
    });
</script>

@endsection
