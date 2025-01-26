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
        <h5 class="card-title text-uppercase">DAFTAR IJAZAH</h5>
        <hr />
        <form class="form-filter">
            <div class="form-group row">
                <div class="col-md-6 @if($isSekolah) d-none @endif">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="provinsiid"
                                class="col-md-12 col-form-label text-md-left">{{ __('Provinsi') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="provinsiid" class="col-md-12 custom-select form-control" name='provinsiid'
                                autofocus>
                                <option value="">-- Pilih Provinsi --</option>
                                <option value="{{enum::PROVINSI_KEPRI}}">{{  enum::PROVINSI_DESC_KEPRI }}</option>
                                <option value="{{enum::PROVINSI_LAINNYA}}">{{  enum::PROVINSI_DESC_LAINNYA }}</option>
                            </select>
                        </div>
                    </div>
                </div>
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
            </div>
            <div class="form-group row">
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
                <div class="col-md-6 @if($isSekolah) d-none @endif">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="jenjang"
                                class="col-md-12 col-form-label text-md-left">{{ __('Jenjang') }}</label>
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
                
            </div>
            <div class="form-group row">
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
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="sekolahid"
                                class="col-md-12 col-form-label text-md-left">{{ __('Sekolah') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="sekolahid" class="col-md-12 custom-select form-control" name='sekolahid'
                                autofocus {{ $isSekolah ? 'disabled' : '' }}>
                                <option value="{{ $isSekolah ? $userSekolah->sekolahid : '' }}">{{ $isSekolah ? $userSekolah->namasekolah : '-- Pilih Sekolah --' }}</option>
                                @foreach ($sekolah as $item)
                                <option value="{{$item->sekolahid}}">{{  $item->namasekolah }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="search" class="col-md-12 col-form-label text-md-left">{{ __('Filter') }}</label>
                        </div>
                        <div class="col-md-10">
                            <input id="search" type="text" class="col-md-12 form-control" name="search"
                                value="{{ old('search') }}" maxlength="100" autocomplete="search">
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
                    <table class="table table-bordered yajra-datatable table-striped" id="ijazah-table">
                        <thead>
                            <tr>
                                <th>Sekolah Asal</th>
                                <th>No Ijazah</th>
                                <th>Tanggal Kelulusan</th>
                                <th>Nama</th>
                                <th>NIS</th>
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

<script>
    $(document).ready(function () {
        $('.custom-select').select2();

        var ijazahtable = $('#ijazah-table').DataTable({
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
                url: "{{ route('ijazah.index') }}",
                dataSrc: function (response) {
                    response.recordsTotal = response.data.count;
                    response.recordsFiltered = response.data.count;
                    return response.data.data;
                },
                data: function (d) {
                    return $.extend({}, d, {
                        "kotaid": $("#kotaid").val().toLowerCase(),
                        "kecamatanid": $("#kecamatanid").val().toLowerCase(),
                        "jenjang": $("#jenjang").val().toLowerCase(),
                        "jenis": $("#jenis").val().toLowerCase(),
                        "sekolahid": $("#sekolahid").val().toLowerCase(),
                        "search": $("#search").val().toLowerCase(),
                        "provinsiid": $("#provinsiid").val().toLowerCase(),

                    });
                }
            },
            buttons: {
                buttons: [{
                        text: '<i class="fa fa-info-circle" aria-hidden="true"></i> Lihat',
                        className: 'view btn btn-primary btn-sm btn-datatable',
                        action: function () {
                            if (ijazahtable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan dilihat", "error");
                                return;
                            }
                            var id = ijazahtable.rows({
                                selected: true
                            }).data()[0]['ijazahid'];
                            var url = "{{ route('ijazah.show', ':id') }}"
                            url = url.replace(':id', id);
                            window.location = url;
                        }
                    },
                    {
                        text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                        className: 'edit btn btn-warning btn-sm btn-datatable',
                        action: function () {
                            if (ijazahtable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan diubah", "error");
                                return;
                            }
                            var id = ijazahtable.rows({
                                selected: true
                            }).data()[0]['ijazahid'];
                            var url = "{{ route('ijazah.edit', ':id') }}"
                            url = url.replace(':id', id);
                            window.location = url;
                        }
                    }, {
                        text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                        className: 'edit btn btn-danger btn-sm btn-datatable',
                        action: function () {
                            if (ijazahtable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = ijazahtable.rows({
                                selected: true
                            }).data()[0]['ijazahid'];
                            var url = "{{ route('ijazah.destroy', ':id') }}"
                            url = url.replace(':id', id);
                            var nama = ijazahtable.rows({
                                selected: true
                            }).data()[0]['namasiswa'];
                            swal.fire({
                                title: "Apakah anda yakin akan menghapus Ijazah Siswa " +
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
                                                ijazahtable.draw();
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
            columns: [{
                    'orderData': 1,
                    data: 'sekolahid',
                    render: function (data, type, row) {
                        if (data === null) {
                            return row.namasekolah;
                        } else {
                            return row.ns;
                        }
                    },
                    name: 'namasekolah'
                },
                {
                    'orderData': 2,
                    data: 'noijazah',
                    name: 'noijazah'
                },
                {
                    'orderData': 3,
                    data: 'tgl_lulus',
                    render: function (data, type, row) {
                        var tglLulus = row.tgl_lulus;
                        var dateObject = new Date(tglLulus);

                        var day = String(dateObject.getDate()).padStart(2, '0');
                        var month = String(dateObject.getMonth() + 1).padStart(2, '0');
                        var year = dateObject.getFullYear();

                        var formattedDate = day + '-' + month + '-' + year;

                        return formattedDate;
                    },
                    name: 'tgl_lulus'
                },
                {
                    'orderData': 4,
                    data: 'namasiswa',
                    name: 'namasiswa'
                },
                {
                    'orderData': 5,
                    data: 'nis',
                    name: 'nis'
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });
        // function catch sekolah 
        function fetchSchools() {
            ijazahtable.draw();
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
        }
        // end function 

        // filter sekolah
        $('#provinsiid').change(function () {
            if ($(this).val() === '{{ enum::PROVINSI_KEPRI }}' || $(this).val() === ''){
                $("#jenjang").prop("disabled", false);
                $("#kotaid").prop("disabled", false);
                $("#kecamatanid").prop("disabled", false);
                $("#jenis").prop("disabled", false);
                $("#sekolahid").prop("disabled", false);

                $.ajax({
                    url: "{{ route('helper.getkota', ':id') }}".replace(':id', 1),
                }).done(function (result) {
                    let dataWr = result.data;
                    
                    $("#kotaid").html("");
                    var d = new Option("-- Semua Kota --", "");
                    $("#kotaid").append(d);
            
                    if (dataWr) {
                        dataWr.forEach((element) => {
                            var text = element.kodekota + ' - ' + element.namakota; 
                            var o = new Option(text, element.kotaid);
                            $("#kotaid").append(o);
                        });
                    }
                });
                fetchSchools();
            }else{
                $('#kotaid').val('');
                $('#kecamatanid').val('');
                $('#jenjang').val('');
                $('#sekolahid').val('');
                $('#jenis').val('');
                $("#kotaid").html("");
                var dd1 = new Option("-- Pilih Kota --", "");
                $("#kotaid").append(dd1);
                $("#kecamatanid").html("");
                var dd2 = new Option("-- Pilih Kecamatan --", "");
                $("#kecamatanid").append(dd2);
                $("#jenjang").prop("disabled", true);
                $("#kotaid").prop("disabled", true);
                $("#kecamatanid").prop("disabled", true);
                $("#jenis").prop("disabled", true);
                $("#sekolahid").prop("disabled", true);
                $("#sekolahid").html("");
                var dd5 = new Option("-- Pilih Sekolah --", "");
                $("#sekolahid").append(dd5);
            }
            ijazahtable.draw();
        });
        $('#kotaid').change(function () {
            ijazahtable.draw();
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
            ijazahtable.draw();
            fetchSchools();
        });
        $('#jenjang').change(function () {
            ijazahtable.draw();
            fetchSchools();
        });
        $('#jenis').change(function () {
            ijazahtable.draw();
            fetchSchools();
        });
        
        $('#sekolahid').change(function () {
            ijazahtable.draw();
        });

        $('#search').keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#search').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                ijazahtable.draw();
            }
        });
        $('#btnTambah').click(function (event) {

            event.preventDefault(); // Prevent the default link behavior

            
            var sekolahId = $('#sekolahid').val(); 
            // cek user sekolah atau bukan
            var uSekolah = '{{ $isSekolah }}';
            // jika login user sekolah
            if(uSekolah == '1'){
                // provinsinya = kepri
                var provinsiId = '1';
            }else{
                var provinsiId = $('#provinsiid').val();
            }
            //cek provinsinya
            if(provinsiId === ''){
                swal.fire("Silakan pilih provinsi terlebih dahulu", "", "error");
            }else{
                // jika provinsi kepri maka wajib pilih sekolah
                if (sekolahId === '' && provinsiId === '1') {
                swal.fire("Silakan pilih sekolah terlebih dahulu", "", "error");
                } else {
                    if(provinsiId === '1'){
                        var url = "{{ route('ijazah.createWithSekolah', ['provId' => ':provid', 'sekolahId' => ':id']) }}";

                        url = url.replace(':id', sekolahId).replace(':provid', provinsiId);

                        window.location.href = url;
                    }else{
                        var url = "{{ route('ijazah.createWithSekolah', ['provId' => ':provid', 'sekolahId' => ':id']) }}";
                        
                        url = url.replace(':id', null).replace(':provid', provinsiId);

                        window.location.href = url;
                    }
                }
            }
        });

    });

</script>

@endsection
