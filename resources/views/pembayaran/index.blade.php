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
        <h5 class="card-title text-uppercase">DAFTAR PEMBAYARAN</h5>
        <hr />
        <form class="form-filter form-material">
            <div class="form-group row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="pembayaran" class="col-md-12 col-form-label text-md-left">{{ __('Pembayaran') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="pembayaran" class="col-md-12 custom-select form-control" name="pembayaran">
                                <option value="">'-- Pilih Pembayaran --</option>
                                @foreach (enum::listPembayaran() as $id)
                                <option value="{{ $id }}">{{ enum::listPembayaran('desc')[$loop->index] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="status" class="col-md-12 col-form-label text-md-left">{{ __('Status') }}</label>
                        </div>
                        <div class="col-md-9">
                            <select id="status" class="col-md-12 custom-select form-control" name="status">
                                <option value="">-- Pilih Status --</option>
                                @foreach (enum::listStatusPembayaran() as $id)
                                <option value="{{ $id }}">{{ enum::listStatusPembayaran('desc')[$loop->index] }}</option>
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

                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="pembayaran-table">
                        <thead>
                            <tr>
                                <th>Kode Pembayaran</th>
                                <th>Nama Murid</th>
                                <th>Category</th>
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
        <h2 class="card-title text-uppercase" id="bukti-pembayaran-title"></h2><hr />
        <div class="form-group row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable table-striped" id="bukti-pembayaran-table">
                        <thead>
                            <tr>
                                <th>Nama File</th>
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
<div class="modal" id="modal-bukti-pembayaran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-bukti-pembayaran"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="formUploadBuktiPembayaran" name="formUploadBuktiPembayaran" class="form-horizontal form-material needs-validation" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="detailsarprasid" value={{ $detailsarpras->detailsarprasid }} id="detailsarprasid"/> --}}
                    <input type="hidden" id="pembayaranid" name="pembayaranid">
                    <input type="hidden" name="detail_mode" id="detail_mode"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgajiberkalabln" class="col-md-12 col-form-label text-md-left">{{ __('Upload Bukti Pembayaran *') }}</label>

                                <input type="file" class="form-control file-input" name="file" required /><span style="font-size: 12px" class="help-block">Format: PDF, JPG, JPEG, PNG | Max: 2MB</span>

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
            dropdownParent: $('#modal-bukti-pembayaran .modal-content')
        });
        var pembayarantable = $('#pembayaran-table').DataTable({
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
                url: "{{ route('pembayaran.index') }}",
                dataSrc: function (response) {
                    response.recordsTotal = response.data.count;
                    response.recordsFiltered = response.data.count;
                    return response.data.data;
                },
                data: function (d) {
                    return $.extend({}, d, {
                        "pembayaran": $("#pembayaran").val().toLowerCase(),
                        "status": $("#status").val().toLowerCase(),
                        "search": $("#search").val().toLowerCase(),
                    });
                }
            },
            buttons: {
                buttons: [
                    @if(!$isSekolah) // Jika isSekolah == false, tambahkan tombol
                    {

                        text: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Ubah',
                        className: 'edit btn btn-warning btn-sm btn-datatable',
                        action: function () {
                            if (pembayarantable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan diubah", "error");
                                return;
                            }
                            var id = pembayarantable.rows({
                                selected: true
                            }).data()[0]['pembayaranid'];
                            var url = "{{ route('pembayaran.edit', ':id') }}"
                            url = url.replace(':id', id);
                            window.location = url;
                        }
                    },
                    @endif 
                    @if(!$isSekolah)
                    {
                        text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                        className: 'edit btn btn-danger btn-sm btn-datatable',
                        action: function () {
                            if (pembayarantable.rows({
                                    selected: true
                                }).count() <= 0) {
                                swal.fire("Data belum dipilih",
                                    "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = pembayarantable.rows({
                                selected: true
                            }).data()[0]['guruid'];
                            var url = "{{ route('guru.destroy', ':id') }}"
                            url = url.replace(':id', id);
                            var nama = pembayarantable.rows({
                                selected: true
                            }).data()[0]['nama'];
                            swal.fire({
                                title: "Apakah anda yakin akan menghapus guru " +
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
                                                pembayarantable.draw();
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
                    @endif
                ]
            },
            columns: [
                {
                    'orderData': 1,
                    data: 'kodepembayaran',
                    render: function (data, type, row) {
                        if(row.kodepembayaran != '' || row.kodepembayaran != null){
                            return (row.kodepembayaran);
                        }else{
                            return '---'
                        }
                    },
                    name: 'kodepembayaran'
                },
                {
                    'orderData': 2,
                    data: 'namamurid',
                    render: function (data, type, row) {
                        if(row.namamurid != '' || row.namamurid != null){
                            return (row.namamurid);
                        }else{
                            return '---'
                        }
                    },
                    name: 'namamurid'
                },
                {
                    'orderData': 3,
                    data: 'category',
                    render: function (data, type, row) {
                        if (row.category != null) {

                            var listPembayaran = @json(enum::listPembayaran($id = ''));
                            // let listPembayaran = JSON.parse('{!! json_encode(enum::listPembayaran()) !!}');
                            let Desc;
                            listPembayaran.forEach((value, index) => {
                                if(row.category == index + 1) {
                                    Desc = value;
                                }
                            });
                            return Desc;
                        }
                        else{
                            return '---'
                        }
                    },
                    name: 'category'
                },
                {
                    'orderData': 4,
                    data: 'status',
                    render: function (data, type, row) {
                        if (row.status != null) {

                            // var listStatusPembayaran = @json(enum::listStatusPembayaran($id = ''));
                            // // let listStatusPembayaran = JSON.parse('{!! json_encode(enum::listStatusPembayaran()) !!}');
                            // let Desc;
                            // listStatusPembayaran.forEach((value, index) => {
                            //     if(row.status == index + 1) {
                            //         Desc = value;
                            //     }
                            // });
                            // return Desc;

                            if (row.status == true) {
                                return 'Verified'
                            }else {
                                return 'Pending'
                            }
                        }
                        else{
                            return '---'
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

        var buktipembayarantable;

        var buktipembayarantable = $('#bukti-pembayaran-table').DataTable({
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
                @if(!$isSekolah) // Jika isSekolah == false, tambahkan tombol
                    {
                        text: '<i class="fa fa-plus-circle aria-hidden="true"></i> Tambah',
                        id: 'btn-tambah-detail-peg',
                        className: 'edit btn btn-primary mb-3 btn-datatable',
                        action: function () {
                            if (pembayarantable.rows( {selected: true} ).count() <= 0) {
                                swal.fire("Pegawai Belum Dipilih", "Silakan pilih pegawai terlebih dahulu", "error");
                                return;
                            }
                            else{
                                var rowData = pembayarantable.rows( {selected: true} ).data()[0]; // Get selected row data
                                var pembayaranid = rowData.pembayaranid;
                                console.log(pembayaranid);
                                // var url = "{{  route('sarprastersedia.createDetailSarpras', ['sarprastersediaid' => ':id']) }}";
                                // url = url.replace(':id', sarprastersediaid);
                                // window.location.href = url;

                                // $('#modal-bukti-pembayaran').modal('show');
                                $('#pembayaranid').val(pembayaranid);
                                showmodaldetail('add');
                            }
                        }
                    },
                @endif
                @if(!$isSekolah)
                    {
                        text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                        id: 'btn-hapus-detail-peg',
                        className: 'edit btn btn-danger mb-3 btn-datatable',
                        action: function () {
                            if (buktipembayarantable.rows( { selected: true } ).count() <= 0) {
                                swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = buktipembayarantable.rows( { selected: true } ).data()[0]['buktipembayaranid'];
                            var url = "{{ route('pembayaran.deletebuktipembayaran', ':id') }}"
                            url = url.replace(':id', id);
                            // var nama =  buktipembayarantable.rows( { selected: true } ).data()[0]['namasekolah'];
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
                                                var rowData = pembayarantable.rows( {selected: true} ).data()[0]; // Get selected row data
                                                var pembayaranid = rowData.pembayaranid;
                                                loadBuktiPembayaran(pembayaranid);
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
                    @endif
                ]
                },

                columns: [
                    {'orderData': 1, data: 'buktipembayaranid', name: 'buktipembayaranid', 
                        render: function(data, type, row){
                            return row.buktipembayaranid;
                        }
                    },
                    {'orderData': 2, data: 'buktipembayaran', name: 'buktipembayaran',
                        render: function (data, type, row){
                            if(row.buktipembayaran != null){
                                return "<div class=\"d-flex justify-content-center align-items-center\"><iframe src=\"/storage/uploaded/buktipembayaran/"+row.buktipembayaran+"\" height=\"300\" /></div>";
                            }else{
                                return '---'
                            }
                        }
                    },
                ],
                initComplete: function (settings, json) {
                    $(".btn-datatable").removeClass("dt-button");
                },
                //order: [[1, 'asc']]
            });

        // hide detail jumlah sarpras table table
        $('#bukti-pembayaran-table').hide();

        function loadBuktiPembayaran(pembayaranid) {
            var url = "{{ route('pembayaran.showbuktipembayaran', ':id') }}";
            url = url.replace(':id', pembayaranid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    buktipembayarantable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        buktipembayarantable.row.add({
                            buktipembayaranid: response.data.data[i].buktipembayaranid,
                            pembayaranid: response.data.data[i].pembayaranid,
                            buktipembayaran: response.data.data[i].buktipembayaran,
                        });
                    }

                    buktipembayarantable.draw();
                    $('#bukti-pembayaran-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        // Listen for row selection event on legalisir-table
        pembayarantable.on('select', function (e, dt, type, indexes) {
            var rowData = pembayarantable.rows(indexes).data()[0]; // Get selected row data
            var pembayaranid = rowData.pembayaranid;

            // $('#bukti-pembayaran-title').html(`detail pegawai A/n ${nama} || NIP: ${nip}`)

            // Load history table with selected pegawaiid
            loadBuktiPembayaran(pembayaranid);
        });

        pembayarantable.on('deselect', function ( e, dt, type, indexes ) {
            // $('#bukti-pembayaran-title').html(`detail pegawai`)
            // hide histiry table
            $('#bukti-pembayaran-table').hide();
        });

        function resetformdetail() {
            // $("#formBuktiPembayaran")[0].reset();
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

            $('span[id^="err_detail_"]', "#formBuktiPembayaran").each(function(index, el){
                $('#'+el.id).html("");
            });

            $('select[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("").trigger('change');
                }
            });
            $('input[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("");
                }
            });
            $('textarea[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                if (inputname != "mode") {
                    $("#"+el.id).val("");
                }
            });
        }

        function bindformdetail() {
            $('textarea[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(buktipembayarantable.rows( { selected: true } ).data()[0][inputname]);
                }
            });
            
            $('input[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                if(el.type != 'file') {
                    var inputname = el.id.substring(7, el.id.length);
                    //alert(inputname);
                    if (inputname != "mode") {
                        $("#"+el.id).val(buktipembayarantable.rows( { selected: true } ).data()[0][inputname]);
                    }
                }

                if(el.type == 'file') {
                    var inputname = el.id.substring(7, el.id.length);
                    if(inputname != "mode") {
                        const $input = $('#detail_file');
                        const imgPath = buktipembayarantable.rows( {selected: true } ).data()[0][inputname];
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
            
            $('select[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                var inputname = el.id.substring(7, el.id.length);
                //alert(inputname);
                if (inputname != "mode") {
                    $("#"+el.id).val(buktipembayarantable.rows( { selected: true } ).data()[0][inputname]).trigger('change');
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
            
            $('textarea[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('input[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                $("#"+el.id).prop("readonly", !value);
            });
            $('select[id^="detail_"]', "#formBuktiPembayaran").each(function(index, el){
                $("#"+el.id).prop("disabled", !value);
            });
        }

        var v_modedetail = "";
        function showmodaldetail(mode) {
            v_modedetail = mode;
            $("#detail_mode").val(mode);
            resetformdetail();
            if (mode == "add") {
                $("#modal-title-bukti-pembayaran").html('Tambah Data');
                // bindformdetail();
                setenableddetail(true);
                // console.log($("#detail_mode").val());
                // $('#detail_file').prop('required', true);
            }
            else if (mode == "edit") {
                $("#modal-title-bukti-pembayaran").html('Ubah Data');
                bindformdetail();
                setenableddetail(true);
                // $('#detail_file').prop('required', false);
            }
            else {
                $("#modal-title-bukti-pembayaran").html('Lihat Data');
                bindformdetail();
                setenableddetail(false);
            }
            
            $("#modal-bukti-pembayaran").modal('show');
        }

        function hidemodaldetail() {
            $("#modal-bukti-pembayaran").modal('hide');
        }

        function setenabledtbutton(option) {
            buktipembayarantable.buttons( '.view' ).disable();
            //buktipembayarantable.buttons( '.print' ).disable();
            buktipembayarantable.buttons( '.add' ).disable();
            buktipembayarantable.buttons( '.edit' ).disable();
            buktipembayarantable.buttons( '.delete' ).disable();

            if (option == "0") {
                buktipembayarantable.buttons( '.view' ).enable();
                buktipembayarantable.buttons( '.add' ).enable();
                buktipembayarantable.buttons( '.edit' ).enable();
                buktipembayarantable.buttons( '.delete' ).enable();
            }
            else if (option == "1") {
                buktipembayarantable.buttons( '.view' ).enable();
                buktipembayarantable.buttons( '.print' ).enable();
            }
            else if (option == "3" || option == "5" || option == "2" || option == "4" || option == "6") {
                buktipembayarantable.buttons( '.view' ).enable();
            }
        }

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
    $(document).on('submit', '#formUploadBuktiPembayaran', function(e){
        e.preventDefault();

        var url = ''

        // if ($('#detail_foto_mode').val() == 'add') {

        //     var rowDataJenisPeralatan = detailjumlahtable.rows({ selected: true }).data()[0]; // Get selected row data
        //     var detailjumlahperalatanid = rowDataJenisPeralatan.detailjumlahperalatanid;

        //     var url = "{{ route('progresfisik.storeDetailFotoPeralatan', ':id') }}";
        //     url = url.replace(':id', detailjumlahperalatanid); 
        // } else if($('#detail_foto_mode').val() == 'edit'){

        //     var rowDataFotoPeralatan = fotoEditDetailJenisPeralatan.rows({ selected: true }).data()[0];
        //     var filedetailjumlahperalatanid = rowDataFotoPeralatan.filedetailjumlahperalatanid;

        //     var url = "{{ route('progresfisik.updateDetailFotoPeralatan', ':id') }}";
        //     url = url.replace(':id', filedetailjumlahperalatanid); 
        // }

        var url = "{{ route('pembayaran.storebuktipembayaran') }}";
        
        var formData = new FormData($('#formUploadBuktiPembayaran')[0]);

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
                        var rowData = pembayarantable.rows({ selected: true }).data()[0]; // Get selected row data
                        var pembayaranid = rowData.pembayaranid;
                        loadBuktiPembayaran(pembayaranid);
                        // detailjumlahtable.draw();
                        $('#formUploadBuktiPembayaran').trigger("reset");
                        $('#modal-bukti-pembayaran').modal('hide'); 

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

            pembayarantable.draw();
            pembayarantable.clear().draw();
            
        });
        //kota change
        $('#kotaid').change(function () {
            pembayarantable.draw();
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
            pembayarantable.draw();
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
            pembayarantable.draw();
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
        $('#pembayaran').change(function () {
            pembayarantable.draw();
        });
        $('#status').change(function () {
            pembayarantable.draw();
        });

        $('#search').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        
        $('#search').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                pembayarantable.draw();
            }
        });

    //     $('#btnTambah').click(function (event) {
    //     event.preventDefault(); // Prevent the default link behavior

    //     var sekolahId = $('#sekolahid').val();
    //     var unit = $('#unit').val();

    //     if (sekolahId === '' && unit === '') {
    //         swal.fire("Silakan pilih unit terlebih dahulu", "", "error");
    //     } else if (sekolahId === '' && unit === "{{ enum::UNIT_SEKOLAH }}") {
    //         swal.fire("Silakan pilih sekolah terlebih dahulu", "", "error");
    //     } else if (unit === "{{ enum::UNIT_SEKOLAH }}" && sekolahId !== '') {
    //         var url = "{{ route('pegawai.createWithSekolah', ['sekolahId' => ':id']) }}";
    //         url = url.replace(':id', sekolahId);
    //         window.location.href = url;
    //     } else if (unit === "{{ enum::UNIT_OPD }}" && sekolahId === '') {
    //         var url = "{{ route('pegawai.create') }}";
    //         window.location.href = url;
    //     }else {
    //         swal.fire("Silakan pilih unit terlebih dahulu", "", "error");
    //     }
    // });

    });

</script>

@endsection
