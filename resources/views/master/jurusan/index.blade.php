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
        <h5 class="card-title text-uppercase">DAFTAR JURUSAN</h5><hr />
        <form class="form-material">
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="search" class="col-md-12 col-form-label text-md-left">{{ __('Filter') }}</label>
                        </div>
                        <div class="col-md-9">
                            <input id="search" type="text" class="col-md-12 form-control" name="search" value="{{ old('search') }}" maxlength="100" autocomplete="search">
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
                    <table class="table table-bordered yajra-datatable table-striped" id="jurusan-table">
                        <thead>
                            <tr>
                                <th>No Jurusan</th>
                                <th>Nama Jurusan</th>
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
    
        var jurusantable = $('#jurusan-table').DataTable({
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
                url: "{{ route('jurusan.index') }}",
                dataSrc: function(response){
                    response.recordsTotal = response.data.count;
                    response.recordsFiltered = response.data.count;
                    return response.data.data;
                },
                data: function ( d ) {
                    return $.extend( {}, d, {
                        "search": $("#search").val().toLowerCase(),
                    } );
                }
            },
            buttons: {
                buttons: [{
                    text: '<i class="fa fa-info-circle" aria-hidden="true"></i> Lihat',
                    className: 'view btn btn-primary btn-sm btn-datatable',
                    action: function () {
                        if (jurusantable.rows({
                                selected: true
                            }).count() <= 0) {
                            swal.fire("Data belum dipilih",
                                "Silahkan pilih data yang akan dilihat", "error");
                            return;
                        }
                        var id = jurusantable.rows({
                            selected: true
                        }).data()[0]['jurusanid'];
                        var url = "{{ route('jurusan.show', ':id') }}"
                        url = url.replace(':id', id);
                        window.location = url;
                    }
                }, {
                    text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                    className: 'edit btn btn-warning btn-sm btn-datatable',
                    action: function () {
                        if (jurusantable.rows({
                                selected: true
                            }).count() <= 0) {
                            swal.fire("Data belum dipilih",
                                "Silahkan pilih data yang akan diubah", "error");
                            return;
                        }
                        var id = jurusantable.rows({
                            selected: true
                        }).data()[0]['jurusanid'];
                        var url = "{{ route('jurusan.edit', ':id') }}"
                        url = url.replace(':id', id);
                        window.location = url;
                    }
                }, {
                    text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                    className: 'edit btn btn-danger btn-sm btn-datatable',
                    action: function () {
                        if (jurusantable.rows({
                                selected: true
                            }).count() <= 0) {
                            swal.fire("Data belum dipilih",
                                "Silahkan pilih data yang akan dihapus", "error");
                            return;
                        }
                        var id = jurusantable.rows({
                            selected: true
                        }).data()[0]['jurusanid'];
                        var url = "{{ route('jurusan.destroy', ':id') }}"
                        url = url.replace(':id', id);
                        var kode = jurusantable.rows({
                            selected: true
                        }).data()[0]['nojurusan'];
                        var nama = jurusantable.rows({
                            selected: true
                        }).data()[0]['namajurusan'];
                        swal.fire({
                            title: "Apakah anda yakin akan menghapus kelas " + nama +
                                "?",
                            icon: 'warning',
                            text: "Data yang terhapus tidak dapat dikembalikan lagi!",
                            // type: "warning",   
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Ya, lanjutkan!",
                            // closeOnConfirm: false 
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
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
                                            success == true) {
                                            swal.fire("Berhasil!",
                                                "Data anda telah dihapus.",
                                                "success");
                                                jurusantable.draw();
                                        } else {
                                            swal.fire("Error!", data,
                                                "error");
                                        }
                                    }
                                });
                            }
                        })
                    }
                }]
            },
            columns: [
                {'orderData': 0, data: 'nojurusan',
                    render: function ( data, type, row ) {
                        return row.nojurusan;
                    }, 
                    name: 'nojurusan'},
                {'orderData': 1, data: 'namajurusan',
                    render: function ( data, type, row ) {
                        return row.namajurusan;
                    }, 
                    name: 'namajurusan'},
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        $('#search').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        
        $('#search').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                jurusantable.draw();
            }
        });

    });
</script>

@endsection