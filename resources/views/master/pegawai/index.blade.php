<?php
use App\enumVar as enum;
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

</style>
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">DAFTAR PEGAWAI</h5>
        <hr />
        <form class="form-filter form-material">
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
                            <label for="kotaid"
                                class="col-md-12 col-form-label text-md-left">{{ __('Kota/Kabupaten') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="kotaid" class="col-md-12 custom-select form-control" name='kotaid' autofocus>
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                @foreach ($kota as $item)
                                <option value="{{$item->kotaid}}">{{  $item->kodekota . ' ' . $item->namakota }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 @if($isSekolah) d-none @endif">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="kecamatanid"
                                class="col-md-12 col-form-label text-md-left">{{ __('Kecamatan') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="kecamatanid" class="col-md-12 custom-select form-control" name='kecamatanid'
                                autofocus>
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatan as $item)
                                <option value="{{$item->kecamatanid}}">{{  $item->kodekec . ' ' . $item->namakec }}
                                </option>
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
                            <label for="jenjang"
                                class="col-md-12 col-form-label text-md-left">{{ __('Jenjang') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="jenjang" class="col-md-12 custom-select form-control" name="jenjang" autofocus>
                                <option value="">-- Pilih Jenjang --</option>
                                @foreach (enum::listJenjang() as $jenjang)
                                <option value="{{ $jenjang }}">{{ enum::listJenjang('desc')[$loop->index] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="sekolahid"
                                class="col-md-12 col-form-label text-md-left">{{ __('Sekolah') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="sekolahid" class="col-md-12 custom-select form-control" name='sekolahid' {{ $isSekolah ? 'disabled' : '' }}>
                                <option value="{{ $isSekolah ? $userSekolah->sekolahid : '' }}">{{ $isSekolah ? $userSekolah->namasekolah : '-- Pilih Sekolah --' }}</option>
                                @foreach ($sekolah as $item)
                                <option value="{{ $item->sekolahid }}">{{  $item->namasekolah }}</option>
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

                {{-- @if (session()->has('success'))
                <p class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </p>
                @endif --}}

                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="pegawai1-table">
                        <thead>
                            <tr>
                                <th>Sekolah</th>
                                <th>Status Gaji Berkala</th>
                                <th>Nama Pegawai</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Status</th>
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
<div class="card mt-3">
    <div class="card-body p-4">
        <h2 class="card-title text-uppercase" id="detail-pegawai-title"></h2><hr />
        <div class="form-group row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="detail-pegawai-table">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Gol Ruang</th>
                                <th>Jenis Jabatan</th>
                                <th>Eselon</th>
                                <th>Nama Jabatan</th>
                                <th>Gol Ruang Berkala</th>
                                <th>MS Berkala Thn</th>
                                <th>MS Berkala Bln</th>
                                <th>TMT Berkala</th>
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
<div class="modal" id="modal-detail-pegawai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-pegawai"></h4>
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
                                <label for="tahun" class="col-md-12 col-form-label text-md-left">{{ __('Tahun *') }}</label>
    
                                    <select id="detail_tahun" class="custom-select1 form-control @error('tahun') is-invalid @enderror" name='tahun' required>
                                        <option value="">-- Tahun --</option>
                                        @foreach (enum::listTahun() as $id)
                                            <option value="{{ $id }}"> {{ enum::listTahun('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('tahun')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="golpegawaiid" class="col-md-12 col-form-label text-md-left">{{ __('Golongan *') }}</label>
    
                                    <select id="detail_golpegawaiid" class="custom-select1 form-control @error('golpegawaiid') is-invalid @enderror" name='golpegawaiid' required>
                                        <option value="">-- Pilih Golongan --</option>
                                        @foreach (enum::listGolongan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listGolongan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('golpegawaiid')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenisjab" class="col-md-12 col-form-label text-md-left">{{ __('Jenis Jabatan *') }}</label>
    
                                    <select id="detail_jenisjab" class="custom-select1 form-control @error('jenisjab') is-invalid @enderror" name='jenisjab' required>
                                        <option value="">-- Pilih Jenis Jabatan --</option>
                                        @foreach (enum::listJenisJabatan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listJenisJabatan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('jenisjab')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="golruangberkalaid" class="col-md-12 col-form-label text-md-left">{{ __('Gol Ruang Berkala *') }}</label>
    
                                    <select id="detail_golruangberkalaid" class="custom-select1 form-control @error('golruangberkalaid') is-invalid @enderror" name='golruangberkalaid' required>
                                        <option value="">-- Pilih Gol Ruang Berkala --</option>
                                        @foreach (enum::listGolongan() as $id)
                                            <option value="{{ $id }}"> {{ enum::listGolongan('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('golruangberkalaid')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jabatanid" class="col-md-12 col-form-label text-md-left">{{ __('Jabatan *') }}</label>
    
                                    <select id="detail_jabatanid" class="custom-select1 form-control @error('jabatanid') is-invalid @enderror" name='jabatanid' required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $item)
                                            <option value="{{ $item->jabatanid }}"> {{ $item->namajabatan }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('jabatanid')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eselon" class="col-md-12 col-form-label text-md-left">{{ __('Eselon *') }}</label>
    
                                    <select id="detail_eselon" class="custom-select1 form-control @error('eselon') is-invalid @enderror" name='eselon' required>
                                        <option value="">-- Pilih Eselon --</option>
                                        @foreach (enum::listEselon() as $id)
                                            <option value="{{ $id }}"> {{ enum::listEselon('desc')[$loop->index] }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('eselon')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgajiberkalathn" class="col-md-12 col-form-label text-md-left">{{ __('MS Gaji Berkala Thn *') }}</label>
    
                                <input id="detail_msgajiberkalathn" type="number" class="form-control @error('msgajiberkalathn') is-invalid @enderror" name="msgajiberkalathn" value="{{ (old('msgajiberkalathn')) }}" autocomplete="name">
    
                                    @error('msgajiberkalathn')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgajiberkalabln" class="col-md-12 col-form-label text-md-left">{{ __('MS Gaji Berkala Bln *') }}</label>

                                <input id="detail_msgajiberkalabln" type="number" class="form-control @error('msgajiberkalabln') is-invalid @enderror" name="msgajiberkalabln" value="{{ (old('msgajiberkalabln')) }}" autocomplete="name">
    
                                    @error('msgajiberkalabln')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tmtberkala" class="control-label">TMT Berkala</label>
                                <input id="detail_tmtberkala" type="date" class="form-control @error('tmtberkala') is-invalid @enderror" name="tmtberkala" value="{{ (old('tmtberkala')) }}" maxlength="100" autocomplete="name">
    
                                @error('tmtberkala')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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

<script>
    $(document).ready(function () {
        $('.custom-select').select2();
        $('.custom-select1').select2({
            dropdownParent: $('#modal-detail-pegawai .modal-content')
        });
        var pegawaitable = $('#pegawai1-table').DataTable({
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
                url: "{{ route('pegawai.index') }}",
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
                        "search": $("#search").val().toLowerCase(),
                    });
                }
            },
            buttons: {
                buttons: [{

                        text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                        className: 'edit btn btn-warning btn-sm btn-datatable',
                        action: function () {
                            if (pegawaitable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan diubah", "error");
                                return;
                            }
                            var id = pegawaitable.rows({
                                selected: true
                            }).data()[0]['pegawaiid'];
                            var url = "{{ route('pegawai.edit', ':id') }}"
                            url = url.replace(':id', id);
                            window.location = url;
                        }
                    }, {
                        text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                        className: 'edit btn btn-danger btn-sm btn-datatable',
                        action: function () {
                            if (pegawaitable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = pegawaitable.rows({
                                selected: true
                            }).data()[0]['pegawaiid'];
                            var url = "{{ route('pegawai.destroy', ':id') }}"
                            url = url.replace(':id', id);
                            var nama = pegawaitable.rows({
                                selected: true
                            }).data()[0]['nama'];
                            swal.fire({
                                title: "Apakah anda yakin akan menghapus Pegawai " +
                                    nama + "?",
                                text: "Data yang terhapus tidak dapat dikembalikan lagi!",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Ya, lanjutkan!",
                                closeOnConfirm: false
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]')
                                                .attr(
                                                    'content')
                                        }
                                    });
                                    $.ajax({
                                        type: "DELETE",
                                        cache: false,
                                        url: url,
                                        dataType: 'JSON',
                                        data: {
                                            "_token": $(
                                                    'meta[name="csrf-token"]')
                                                .attr('content')
                                        },
                                        success: function (json) {
                                            var success = json.success;
                                            var message = json.message;
                                            var data = json.data;
                                            console.log(data);

                                            if (success == 'true' ||
                                                success ==
                                                true) {
                                                swal.fire("Berhasil!",
                                                    "Data anda telah dihapus.",
                                                    "success");
                                                pegawaitable.draw();
                                            } else {
                                                swal.fire("Error!", data,
                                                    "error");
                                            }
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
                    data: 'namasekolah',
                    render: function (data, type, row) {
                        if(row.namasekolah != '' || row.namasekolah != null){
                            return (row.namasekolah);
                        }else{
                            return '---'
                        }
                    },
                    name: 'namasekolah'
                },
                {
                    'orderData': 2,
                    data: 'namasekolah',
                    render: function (data, type, row) {
                        return '---';
                    },
                    name: 'namasekolah'
                },
                {
                    'orderData': 3,
                    data: 'nama',
                    render: function (data, type, row) {
                        return (row.nama);
                    },
                    name: 'nama'
                },
                {
                    'orderData': 4,
                    data: 'nip',
                    name: 'nip'
                },
                {
                    'orderData': 5,
                    data: 'jabatan',
                    render: function (data, type, row) {
                        if (row.jabatan != null) {
                            // if (row.unit == {{ enum::UNIT_OPD }}) {
                            //     // if(row.jabatan == {{ enum::JABATAN_OPD_KEPALADINAS}}){
                            //     //     return '<p>{{ enum::JABATAN_OPD_DESC_KEPALADINAS }}</p>';
                            //     // }else if(row.jabatan == {{ enum::JABATAN_OPD_STAFDINAS}}){
                            //     //     return '<p>{{ enum::JABATAN_OPD_DESC_STAFDINAS }}</p>';
                            //     // }

                            //     var listJabatanOPD = @json(enum::listJabatanOPD($id = ''));
                            //     // let listJabatanOPD = JSON.parse('{!! json_encode(enum::listJabatanOPD()) !!}');
                            //     let Desc;
                            //     listJabatanOPD.forEach((value, index) => {
                            //         if(row.jabatan == index + 1) {
                            //             Desc = value;
                            //         }
                            //     });
                            //     return Desc;
                            // } else {
                                // if(row.jabatan == {{ enum::JABATAN_SEKOLAH_KEPALASEKOLAH}}){
                                //     return '<p>{{ enum::JABATAN_SEKOLAH_DESC_KEPALASEKOLAH }}</p>';
                                // }else if(row.jabatan == {{ enum::JABATAN_SEKOLAH_BENDAHARABOS}}){
                                //     return '<p>{{ enum::JABATAN_SEKOLAH_DESC_BENDAHARABOS }}</p>';
                                // }else if(row.jabatan == {{ enum::JABATAN_SEKOLAH_BENDAHARASPP}}){
                                //     return '<p>{{ enum::JABATAN_SEKOLAH_DESC_BENDAHARASPP }}</p>';
                                // }else if(row.jabatan == {{ enum::JABATAN_SEKOLAH_PENGURUSBARANG}}){
                                //     return '<p>{{ enum::JABATAN_SEKOLAH_DESC_PENGURUSBARANG }}</p>';
                                // }

                                var listJabatanSekolah = @json(enum::listJabatanSekolah($id = ''));
                                // let listJabatanSekolah = JSON.parse('{!! json_encode(enum::listJabatanSekolah()) !!}');
                                let Desc;
                                listJabatanSekolah.forEach((value, index) => {
                                    if(row.jabatan == index + 1) {
                                        Desc = value;
                                    }
                                });
                                return Desc;
                            // }   
                        }
                        else{
                            return '---'
                        }
                    },
                    name: 'jabatan'
                },
                {
                    'orderData': 6,
                    data: 'status',
                    render: function (data, type, row) {
                        if (row.status == 1) {
                            return '<p class="text-success">Aktif</p>';
                        } else {
                            return '<p class="text-danger">Tidak Aktif</p>';
                        }
                    },
                    name: 'status'
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        var detailpegawaitable;

        var detailpegawaitable = $('#detail-pegawai-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            pageLength: 50,
            dom: 'Bfrtip',
            select: true,
            ordering: false,
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

            buttons: {
                buttons: [
                {
                    text: '<i class="fa fa-plus-circle aria-hidden="true"></i> Tambah',
                    id: 'btn-tambah-detail-peg',
                    className: 'edit btn btn-primary mb-3 btn-datatable',
                    action: function () {
                        if (pegawaitable.rows( {selected: true} ).count() <= 0) {
                            swal.fire("Pegawai Belum Dipilih", "Silakan pilih pegawai terlebih dahulu", "error");
                            return;
                        }
                        else{
                            var rowData = pegawaitable.rows( {selected: true} ).data()[0]; // Get selected row data
                            var pegawaiid = rowData.pegawaiid;
                            // var url = "{{  route('sarprastersedia.createDetailSarpras', ['sarprastersediaid' => ':id']) }}";
                            // url = url.replace(':id', sarprastersediaid);
                            // window.location.href = url;

                            // $('#modal-detail-pegawai').modal('show');
                            $('#pegawaiid').val(pegawaiid);
                            showmodaldetail('add');
                        }
                    }
                },
                {
                    text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                    id: 'btn-ubah-detail-peg',
                    className: 'edit btn btn-warning mb-3 btn-datatable',
                    action: function () {
                        if (detailpegawaitable.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan diubah", "error");
                            return;
                        }
                        // var id = detailpegawaitable.rows( { selected: true } ).data()[0]['detailsaprasid'];
                        var rowData = detailpegawaitable.rows({ selected: true }).data()[0]; // Get selected row data
                        var id = rowData.detailsarprasid;
                        showmodaldetail('edit');
                    }
                }, {
                    text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                    id: 'btn-hapus-detail-peg',
                    className: 'edit btn btn-danger mb-3 btn-datatable',
                    action: function () {
                        if (detailpegawaitable.rows( { selected: true } ).count() <= 0) {
                            swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                            return;
                        }
                        var id = detailpegawaitable.rows( { selected: true } ).data()[0]['detailpegawaiid'];
                        var url = "{{ route('pegawai.destroydetailpegawai', ':id') }}"
                        url = url.replace(':id', id);
                        // var nama =  detailpegawaitable.rows( { selected: true } ).data()[0]['namasekolah'];
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
                                            // detailpegawaitable.draw();
                                            var rowData = pegawaitable.rows( {selected: true} ).data()[0]; // Get selected row data
                                            var pegawaiid = rowData.pegawaiid;
                                            loadDetailPegawai(pegawaiid);
                                        }
                                        else {
                                            swal.fire("Error!", data, "error"); 
                                        }
                                    }
                                });  
                            }           
                        });
                    }
                }
            ]
            },

            columns: [
                {'orderData': 1, data: 'tahun', name: 'tahun', 
                    render: function(data, type, row){
                        return row.tahun;
                    }
                },
                {'orderData': 2, data: 'golpegawaiid', name: 'golpegawaiid',
                    render: function(data, type, row){
                        if(row.golpegawaiid != null) {
                            var listGolongan = @json(enum::listGolongan($id = ''));
                            // let listGolongan = JSON.parse('{!! json_encode(enum::listGolongan()) !!}');
                            let Desc;
                            listGolongan.forEach((value, index) => {
                                if(row.golpegawaiid == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }else {
                            return '---'
                        }
                    }
                },
                {'orderData': 3, data: 'jenisjab', name: 'jenisjab',
                    render: function(data, type, row){
                        if(row.jenisjab != null) {
                            var listJenisJabatan = @json(enum::listJenisJabatan($id = ''));
                            // let listJenisJabatan = JSON.parse('{!! json_encode(enum::listJenisJabatan()) !!}');
                            let Desc;
                            listJenisJabatan.forEach((value, index) => {
                                if(row.jenisjab == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }else {
                            return '---'
                        }
                    }
                },
                {'orderData': 4, data: 'eselon', name: 'eselon',
                    render: function(data, type, row){
                        if(row.eselon != null) {
                            var listEselon = @json(enum::listEselon($id = ''));
                            // let listEselon = JSON.parse('{!! json_encode(enum::listEselon()) !!}');
                            let Desc;
                            listEselon.forEach((value, index) => {
                                if(row.eselon == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }else {
                            return '---'
                        }
                    }
                },
                {'orderData': 5, data: 'jabatanid', name: 'jabatanid',
                    render: function(data, type, row){
                        if(row.jabatanid != null) {
                            return row.namajabatan
                        }else {
                            return '---'
                        }
                    }
                },
                {'orderData': 6, data: 'golruangberkalaid', name: 'golruangberkalaid',
                    render: function(data, type, row){
                        if(row.golruangberkalaid != null) {
                            var listGolongan = @json(enum::listGolongan($id = ''));
                            // let listGolongan = JSON.parse('{!! json_encode(enum::listGolongan()) !!}');
                            let Desc;
                            listGolongan.forEach((value, index) => {
                                if(row.golruangberkalaid == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }else {
                            return '---'
                        }
                    }
                },
                {'orderData': 7, data: 'msgajiberkalathn', name: 'msgajiberkalathn'},
                {'orderData': 8, data: 'msgajiberkalabln', name: 'msgajiberkalabln'},
                {'orderData': 9, data: 'tmtberkala', name: 'tmtberkala'},
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        // hide detail jumlah sarpras table table
        $('#detail-pegawai-table').hide();

        function loadDetailPegawai(pegawaiid) {
            var url = "{{ route('pegawai.showdetailpegawai', ':id') }}";
            url = url.replace(':id', pegawaiid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    detailpegawaitable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        detailpegawaitable.row.add({
                            detailpegawaiid: response.data.data[i].detailpegawaiid,
                            tahun: response.data.data[i].tahun,
                            golruangberkalaid: response.data.data[i].golruangberkalaid,
                            jenisjab: response.data.data[i].jenisjab,
                            eselon: response.data.data[i].eselon,
                            jabatanid: response.data.data[i].jabatanid,
                            golpegawaiid: response.data.data[i].golpegawaiid,
                            msgajiberkalathn: response.data.data[i].msgajiberkalathn,
                            msgajiberkalabln: response.data.data[i].msgajiberkalabln,
                            tmtberkala: response.data.data[i].tmtberkala,
                            namajabatan: response.data.data[i].namajabatan,
                        });
                    }

                    detailpegawaitable.draw();
                    $('#detail-pegawai-table').show();
                    $('#detail-jumlah-pegawai-table').hide();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        // Listen for row selection event on legalisir-table
        pegawaitable.on('select', function (e, dt, type, indexes) {
            var rowData = pegawaitable.rows(indexes).data()[0]; // Get selected row data
            var pegawaiid = rowData.pegawaiid;
            var nama = rowData.nama;
            var nip = rowData.nip;

            $('#detail-pegawai-title').html(`detail pegawai A/n ${nama} || NIP: ${nip}`)

            // Load history table with selected pegawaiid
            loadDetailPegawai(pegawaiid);
        });

        pegawaitable.on('deselect', function ( e, dt, type, indexes ) {
            $('#detail-pegawai-title').html(`detail pegawai`)
            // hide histiry table
            $('#detail-pegawai-table').hide();
        });

        function resetformdetail() {
            $("#formDetailPegawai")[0].reset();
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

            $('span[id^="err_detail_"]', "#formDetailPegawai").each(function(index, el){
                $('#'+el.id).html("");
            });

            $('select[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("").trigger('change');
                }
            });
            $('input[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("");
                }
            });
            $('textarea[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("");
                }
            });
        }

        function bindformdetail() {
            $('textarea[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailpegawaitable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('input[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                if(el.type != 'file') {
                    var inputname = el.id.substring(7, el.id.length);
                    //alert(inputname);
                    if (inputname != "mode") {
                        $("#"+el.id).val(detailpegawaitable.rows( { selected: true } ).data()[0][inputname]);
                    }
                }

                if(el.type == 'file') {
                    var inputname = el.id.substring(7, el.id.length);
                    if(inputname != "mode") {
                        const $input = $('#detail_file');
                        const imgPath = detailpegawaitable.rows( {selected: true } ).data()[0][inputname];
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
            
            $('select[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(detailpegawaitable.rows( { selected: true } ).data()[0][inputname]).trigger('change');
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
            
            $('textarea[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('input[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('select[id^="detail_"]', "#formDetailPegawai").each(function(index, el){
                $("#"+el.id).prop("disabled", !value);
            });
        }

        var v_modedetail = "";
        function showmodaldetail(mode) {
            v_modedetail = mode;
            $("#detail_mode").val(mode);
            resetformdetail();
            if (mode == "add") {
                $("#modal-title-detail-pegawai").html('Tambah Data');
                // bindformdetail();
                setenableddetail(true);
                // console.log($("#detail_mode").val());
                // $('#detail_file').prop('required', true);
            }
            else if (mode == "edit") {
                $("#modal-title-detail-pegawai").html('Ubah Data');
                bindformdetail();
                setenableddetail(true);
                // $('#detail_file').prop('required', false);
            }
            else {
                $("#modal-title-detail-pegawai").html('Lihat Data');
                bindformdetail();
                setenableddetail(false);
            }
            
            $("#modal-detail-pegawai").modal('show');
        }

        function hidemodaldetail() {
            $("#modal-detail-pegawai").modal('hide');
        }

        function setenabledtbutton(option) {
            detailpegawaitable.buttons( '.view' ).disable();
            //detailpegawaitable.buttons( '.print' ).disable();
            detailpegawaitable.buttons( '.add' ).disable();
            detailpegawaitable.buttons( '.edit' ).disable();
            detailpegawaitable.buttons( '.delete' ).disable();

            if (option == "0") {
                detailpegawaitable.buttons( '.view' ).enable();
                detailpegawaitable.buttons( '.add' ).enable();
                detailpegawaitable.buttons( '.edit' ).enable();
                detailpegawaitable.buttons( '.delete' ).enable();
            }
            else if (option == "1") {
                detailpegawaitable.buttons( '.view' ).enable();
                detailpegawaitable.buttons( '.print' ).enable();
            }
            else if (option == "3" || option == "5" || option == "2" || option == "4" || option == "6") {
                detailpegawaitable.buttons( '.view' ).enable();
            }
        }

        // verifikasi kebutuhan sarpras 
        $(document).on('submit', '#formDetailPegawai', function(e){
            e.preventDefault();
            var url = '';
            var type = '';
            var id = '';

            
            if($("#detail_mode").val() == 'add') {
                url = "{{ route('pegawai.storedetailpegawai') }}";
                type = 'POST'
                // url = url.replace(':id', id);   
            }
            else if($("#detail_mode").val() == "edit") {
                url = "{{ route('pegawai.updatedetailpegawai', ':id') }}";
                id = pegawaitable.rows( { selected: true } ).data()[0]['pegawaiid'];
                url = url.replace(':id', id); 
                type = 'POST'
            }
            var formData = new FormData($('#formDetailPegawai')[0]);
            
            $.ajax({
                type: type,
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                // data: data,
                contentType: false,
                processData: false,
                success: (json) => {
                    let success = json.success;
                    let message = json.message;
                    let data = json.data;
                    // let errors = json.errors;

                    if (success == 'true' || success == true) {
                            swal.fire("Berhasil!", "Data detail pegawai berhasil ditambah.", "success");
                            // kebutuhansarprastable.draw();
                            // formDetailPegawaitable.draw();
                            // var rowData = detailanggarantable.rows({ selected: true }).data()[0]; // Get selected row data
                            // var detailpenganggaranid = rowData.detailpenganggaranid;
                            // showDetailPaguPenganggaran(detailpenganggaranid);
                            // detailpegawaitable.draw();
                            id = pegawaitable.rows( { selected: true } ).data()[0]['pegawaiid'];
                            loadDetailPegawai(id)

                            $('#formDetailPegawai').trigger("reset");
                            $('#modal-detail-pegawai').modal('hide'); 
                    }
                },
                // error: function(jqXHR, textStatus, errorThrown) {
                //         var data = jqXHR.responseJSON;
                //         console.log(data.errors);// this will be the error bag.
                //         // printErrorMsg(data.errors);
                //     }
            })
        })
       
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
            
        });
        //kota change
        $('#kotaid').change(function () {
            pegawaitable.draw();
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
        });
        //kecamatan change
        $('#kecamatanid').change(function () {
            pegawaitable.draw();
            let kecamatanid = this.value;
            let jenjangValue = $('#jenjang').val();
            if (this.value) {
                if(jenjangValue != ''){
                    var link = "{{ route('helper.getsekolahjenjang', [':jenjang', ':kecamatanid']) }}".replace(':jenjang', jenjangValue).replace(':kecamatanid', kecamatanid);
                }else{
                    var link = "{{ route('helper.getsekolah', ':kecamatanid') }}".replace(':kecamatanid', kecamatanid);
                }
                
                $.ajax({
                    url: link,
                }).done(function (result) {
                    let dataWr = result.data;
                    
                    $("#sekolahid").html("");
                    var d = new Option("-- Semua Sekolah --", "");
                    $("#sekolahid").append(d);
            
                    if (dataWr) {
                        dataWr.forEach((element) => {
                            var text = element.namasekolah; 
                            var o = new Option(text, element.sekolahid);
                            $("#sekolahid").append(o);
                        });
                    }
                });
            }else{

                $("#sekolahid").html("");
                
                var dd = new Option("-- Pilih Sekolah --", "");
                $("#sekolahid").append(dd);
            }
        });
        //jenjang change
        $('#jenjang').change(function () {
            pegawaitable.draw();
            let kecamatanid = $('#kecamatanid').val();
            let jenjangValue = this.value;
            if (this.value) {
                if(kecamatanid != ''){
                    var link = "{{ route('helper.getsekolahjenjang', [':jenjang', ':kecamatanid']) }}".replace(':jenjang', jenjangValue).replace(':kecamatanid', kecamatanid);
                }else{
                    var link = "{{ route('helper.getsekolahjenjang2', ':jenjang') }}".replace(':jenjang', jenjangValue);
                }
                
                $.ajax({
                    url: link,
                }).done(function (result) {
                    let dataWr = result.data;
                    
                    $("#sekolahid").html("");
                    var d = new Option("-- Semua Sekolah --", "");
                    $("#sekolahid").append(d);
            
                    if (dataWr) {
                        dataWr.forEach((element) => {
                            var text = element.namasekolah; 
                            var o = new Option(text, element.sekolahid);
                            $("#sekolahid").append(o);
                        });
                    }
                });
            }else{

                $("#sekolahid").html("");
                
                var dd = new Option("-- Pilih Sekolah --", "");
                $("#sekolahid").append(dd);
            }
            
        });
        $('#sekolahid').change(function () {
            pegawaitable.draw();
        });

        $('#search').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        
        $('#search').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                pegawaitable.draw();
            }
        });

        $('#btnTambah').click(function (event) {
        event.preventDefault(); // Prevent the default link behavior

        var sekolahId = $('#sekolahid').val();
        var unit = $('#unit').val();

        if (sekolahId === '' && unit === '') {
            swal.fire("Silakan pilih unit terlebih dahulu", "", "error");
        } else if (sekolahId === '' && unit === "{{ enum::UNIT_SEKOLAH }}") {
            swal.fire("Silakan pilih sekolah terlebih dahulu", "", "error");
        } else if (unit === "{{ enum::UNIT_SEKOLAH }}" && sekolahId !== '') {
            var url = "{{ route('pegawai.createWithSekolah', ['sekolahId' => ':id']) }}";
            url = url.replace(':id', sekolahId);
            window.location.href = url;
        } else if (unit === "{{ enum::UNIT_OPD }}" && sekolahId === '') {
            var url = "{{ route('pegawai.create') }}";
            window.location.href = url;
        }else {
            swal.fire("Silakan pilih unit terlebih dahulu", "", "error");
        }
    });

    });

</script>

@endsection
