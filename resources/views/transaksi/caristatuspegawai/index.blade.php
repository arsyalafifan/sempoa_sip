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
        height: 10em;
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
        font-size: 14px;
        padding: 10px;
    }

    .cari {
        width: 100%;
        border-radius: 0.9em;
        background-color: #299AA7;
        border-color: white;
    }
    .cari:hover{
        background-color: #A2C8A7;
    }
    .swal2-title, .swal2-content,.swal2-confirm ,.data h3,
    section, .cari:hover, .cari, .form-control, .legalisir, #legalisir
    {
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
    }
    .history{
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

    .alert a {
        color: #299AA7;
    }

    .alert a:hover {
        color: red;
    }

    @media (min-width:770px) and (max-width: 992px){
        
        .legalisir {
            margin-top: -6em;
        }
        .alert-danger {
            margin-top: -12em;
        }

        .no-margin {
        margin-top: 0 !important; 
        }
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
        section, .btn, .card, .form-control {
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
        section, .btn, .card, .form-control {
            font-size: 10px;
        }
        .alert-danger {
            width: 80%;
        }
    }

</style>
<style>

    /* Style input fields */
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
        padding: 10px;
    }

    h4 {
        color: #686868 !important;
    }

    label {
        color: #686868;
    }

    /* Style select dropdown */
    .custom-select1 {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 10px;
    }

    /* Style table */
    table {
    width: 100%;
    border-collapse: collapse;
    }

    table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
    }

    table th {
    background-color: #f2f2f2;
    }

    /* Style buttons */
    .btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    }

    .btn-default {
    background-color: #f2f2f2;
    color: black;
    }

    /* Style modal footer */
    .modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 15px;
    border-top: 1px solid #ddd;
    }

    .downloadfile {
        /* background-color: #198754 !important;
        color: white !important;
        border: none !important; */
        width: 150px;
    }

    .bg-orange {
        background-color: #fd7e14;
    }


</style>
{{-- <link href="{{ asset('/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet"> --}}
<section class="page-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body legalisir">
                        <form id="formCekStatus">
                            <div class="form-group text-center">
                                <label for="">Masukkan NIP</label>
                                <input id="nip" type="text" name="nip" class="form-control mt-3 nip" required>
                            </div>
                            <div class="form-group mt-2 mb-2">
                                <button type="submit" class="btn cari text-white">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h4 class="text-center">Status Pengajuan Gaji Berkala</h4>
                    <div class="card-body legalisir">
                        <div style="width: 85%; margin: auto;">
                            <canvas style="display: inline-block;" id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal" id="modal-detail-pegawai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-3">
            <div class="modal-header d-flex">
                <h4 class="modal-title" id="modal-title-detail-pegawai">Status Pegawai</h4>
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
                    <input type="hidden" id="current_status" name="current_status">
                    <input type="hidden" id="current_pegawaipengajuangajiid" name="current_pegawaipengajuangajiid">
                    <input type="hidden" id="file" name="file">
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
                                <label for="tmtberkala" class="col-md-12 col-form-label text-md-left">TMT Berkala</label>
                                <input id="detail_tmtberkala" type="date" class="form-control" name="tmtberkala" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label for="keterangan" class="col-md-12 col-form-label text-md-left">Catatan:</label>
                        <textarea class="form-control" name="keterangan" id="detail_keterangan" cols="30" rows="10" required></textarea>
                    </div>
                    <div class="form-group row mt-2 mb-2">
                        <button type="submit" class="btn cari text-white">
                            Simpan
                        </button>
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
                </form>
            </div>
            <button type="button" class="downloadfile btn btn-link waves-effect waves-light m-r-10">
                <i class="fa fa-download" aria-hidden="true"></i> {{ __('Download File') }}
            </button>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var ctx = document.getElementById('statusPieChart').getContext('2d');
    var chartData = @json($chartData['data']);
    var chartLabels = @json($chartData['labels']);
    // Menggabungkan data dan label dalam satu array
    var chartLabelsFormatted = chartLabels.map((label, index) => `${label} (${chartData[index]} data)`);

    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartLabelsFormatted,
            datasets: [{
                data: @json($chartData['data']),
                backgroundColor: [
                    '#fff',
                    '#6c757d',
                    '#fb9778',
                    '#03a7f3',
                    '#fd7d14',
                    '#fec007',
                    '#00c292'

                ],
                borderColor: [
                    '#bab8b8',
                    '#3b4045',
                    '#b0634c',
                    '#027cb5',
                    '#bd5d0f',
                    '#ad8407',
                    '#017054'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 14 // Ubah ukuran font sesuai kebutuhan Anda
                        }
                    }
                }
            }
        }
    });
</script>
<script>

    $(document).on('submit', '#formCekStatus', function(e){
        e.preventDefault();

        var url = '';

        // var formData = new FormData($('#formCekStatus')[0]);

        url = "{{ route('caristatuspegawai.loadCekStatusDetailPegawai', ':nip') }}";
        var nip = $('#nip').val();
        url = url.replace(':nip', nip);

        $.ajax({
            // url: "{{ route('pengajuangajiberkala.storePengajuan') }}",
            url: url,
            type: 'GET',
            // data: formData,
            success: function(response) {
                // alert(response.message);
                // swal.fire("Berhasil!", `${response.message}`, "success");
                // // Lakukan sesuatu setelah berhasil disimpan
                // $('#modal-realisasi').modal('hide');
                // kebutuhansarprastable.draw();

                // console.log(response.data.count)
                if (response.data.count > 0) {
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
                    $("#pegawaiid").val(response.data.data[0].pegawaiid);
                    $("#detail_keterangan").val('');
                    loadHistoryTable(response.data.data[0].pegawaiid);
                    $("#modal-detail-pegawai").modal('show');
                } else {
                    swal.fire("Error!", `Maaf Nip yang anda masukkan salah, silakan masukkan NIP dengan benar`, "error");
                }
            },
            error: function(xhr) {
                // Tampilkan pesan kesalahan jika ada
                swal.fire("Gagal!", `${xhr.responseJSON.message}`, "error");
                // console.log(xhr.responseText);
            }
        });
    })

    $(document).on('submit', '#formDetailPegawai', function(e){
        e.preventDefault();
        var url = '';
        var type = '';
        var id = '';

        var formData = new FormData($('#formDetailPegawai')[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            type: 'POST',
            url: "{{ route('caristatuspegawai.storeHistory') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            contentType: false,
            processData: false,
            success: (json) => {
                let success = json.success;
                let message = json.message;
                let data = json.data;
                // let errors = json.errors;

                if (success == 'true' || success == true) {
                        swal.fire("Berhasil!", "Berhasil menyimpan keterangan.", "success");

                        id = $("#pegawaiid").val();
                        loadHistoryTable(id);
                        $("#detail_keterangan").val('');

                        // $('#formDetailPegawai').trigger("reset");
                        // $('#modal-detail-pegawai').modal('hide'); 
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
                // Handle error
            }
            // error: function(jqXHR, textStatus, errorThrown) {
            //         var data = jqXHR.responseJSON;
            //         console.log(data.errors);// this will be the error bag.
            //         // printErrorMsg(data.errors);
            //     }
        })
    })

    $('.downloadfile').hide();

    function loadHistoryTable(pegawaiid) {
        var url = "{{ route('caristatuspegawai.loadCekHistoryPegawai', ':pegawaiid') }}";
        url = url.replace(':pegawaiid', pegawaiid);

        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {

                historytable.clear();

                for (const record in response.data.data) {
                    historytable.row.add({
                        tglverifikasi: response.data.data[record].tglverifikasi,
                        status: response.data.data[record].status,
                        keterangan: response.data.data[record].keterangan,
                    });
                    $("#current_status").val(response.data.data[record].status);
                    $("#current_pegawaipengajuangajiid").val(response.data.data[record].pegawaipengajuangajiid);
                    $("#file").val(response.data.data[record].file);

                    if ($("#current_status").val() == "{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_SELESAI }}") {
                        $('.downloadfile').show();
                    }
                    else {
                        $('.downloadfile').hide();
                    }
                }


                historytable.draw();
                $('#history-table').show();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $(document).on('click', '.downloadfile', function() {
        var url = "{{ route('pengajuangajiberkala.downloadFilePengajuan', ':id') }}";
        var id = $("#current_pegawaipengajuangajiid").val();
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
                a.download = $("#file").val();
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

    var historytable = $('#history-table').DataTable({
        responsive: true,
        searching: false,
        dom: 'Bfrtip',
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
        buttons: 
        {
            buttons: 
            [
                // {
                //     text: '<i class="fa fa-download" aria-hidden="true"></i> Download',
                //     // id: 'btn-download-file',
                //     className: 'btn btn-success mb-3 btn-download-file',
                //     action: function () {
                //         // var pegawaipengajuangajiid = $("#current_pegawaipengajuangajiid").val();
                //         // console.log(pegawaipengajuangajiid);

                //         // $(document).on('click', '.downloadfile', function() {
                //             var url = "{{ route('pengajuangajiberkala.downloadFilePengajuan', ':id') }}";
                //             var id = $("#current_pegawaipengajuangajiid").val();
                //             console.log(id);
                //             url = url.replace(':id', id);

                //             // Mengirimkan permintaan Ajax
                //             $.ajax({
                //                 type: "GET",
                //                 cache:false,
                //                 processData: false,
                //                 contentType: false,
                //                 // defining the response as a binary file
                //                 xhrFields: {
                //                     responseType: 'blob' 
                //                 },  
                //                 url: url,
                //                 success: (data) => {
                //                     console.log(data);
                //                     let a = document.createElement('a');
                //                     let url = window.URL.createObjectURL(data);
                //                     a.href = url;
                //                     a.download = $("#file").val();
                //                     document.body.append(a);
                //                     a.click();
                //                     a.remove();
                //                     window.URL.revokeObjectURL(url);
                //                 }
                //             });
                //         // });

                //     }
                // }
            ]
        },

        // buttons: [
        //     {
        //         extend: 'excelHtml5',
        //         text: '<i class="fa fa-download" aria-hidden="true"></i> Download',
        //         className: 'btn-success mb-3 btn-datatable'
        //     }
        // ],
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
                            return '<span class="badge badge-pill bg-white text-black">{{ enum::STATUS_PENGAJUAN_GAJI_BERKALA_DESC_USUL_BARU }}</span>';
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
                        // return Desc;
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
</script>
<!-- tether -->
{{-- <script src="{{asset('/bootstrap/dist/js/tether.min.js')}}"></script> --}}
{{-- <script src="{{asset('/bootstrap/dist/js/bootstrap.min.js')}}"></script> --}}

@endsection

