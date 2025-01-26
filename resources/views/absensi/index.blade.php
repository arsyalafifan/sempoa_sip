@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Absensi Murid</h1>

    <form method="POST" action="{{ route('absensi.store') }}" class="{{$isSekolah == true ? "hidden" : ""}}">
        @csrf
        <div class="form-group">
            <label for="murid_id">Nama Murid</label>
            <select name="muridid" id="muridid" class="form-control" required>
                <option value="">Pilih Murid</option>
                @foreach($murids as $murid)
                    <option value="{{ $murid->muridid }}">{{ $murid->namamurid }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Hadir">Hadir</option>
                <option value="Tidak Hadir">Tidak Hadir</option>
                <option value="Izin">Izin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
    </form>

    <hr>

    <h2>Riwayat Absensi</h2>
    <!-- Tabel absensi -->
    <table class="table table-bordered">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered yajra-datatable table-striped" id="absensi-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('.custom-select').select2();
        loadAbsensi()

        var absensitable = $('#absensi-table').DataTable({
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
                        text: '<i class="fa fa-trash" aria-hidden="true"></i> Hapus',
                        id: 'btn-hapus-detail-peg',
                        className: 'edit btn btn-danger mb-3 btn-datatable',
                        action: function () {
                            if (absensitable.rows( { selected: true } ).count() <= 0) {
                                swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = absensitable.rows( { selected: true } ).data()[0]['absensiid'];
                            var url = "{{ route('absensi.deleteabsensi', ':id') }}"
                            url = url.replace(':id', id);
                            // var nama =  absensi.rows( { selected: true } ).data()[0]['namasekolah'];
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
                                                var rowData = absensitable.rows( {selected: true} ).data()[0]; // Get selected row data
                                                var absensiid = rowData.absensiid;
                                                loadAbsensi(absensiid);
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
                    {'orderData': 1, data: 'namamurid', name: 'namamurid', 
                        render: function(data, type, row){
                            return row.namamurid;
                        }
                    },
                    {'orderData': 2, data: 'tanggal', name: 'tanggal', 
                        render: function(data, type, row){
                            return row.tanggal;
                        }
                    },
                    {'orderData': 3, data: 'status', name: 'status', 
                        render: function(data, type, row){
                            return row.status;
                        }
                    },
                ],
                initComplete: function (settings, json) {
                    $(".btn-datatable").removeClass("dt-button");
                },
                //order: [[1, 'asc']]
            });

        function loadAbsensi() {
            var url = "{{ route('absensi.showabsensi') }}";
            // url = url.replace(':id', absensiid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    absensitable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        absensitable.row.add({
                            absensiid: response.data.data[i].absensiid,
                            namamurid: response.data.data[i].namamurid,
                            tanggal: response.data.data[i].tanggal,
                            status: response.data.data[i].status,
                        });
                    }

                    absensitable.draw();
                    $('#absensi-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    })
</script>
@endsection
