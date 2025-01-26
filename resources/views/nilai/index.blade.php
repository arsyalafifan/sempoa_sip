@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Input Nilai Siswa</h1>

    <form method="POST" action="{{ route('nilai.store') }}" class="{{$isSekolah == true ? "hidden" : ""}}">
        @csrf
        <div class="form-group">
            <label for="muridid">Nama Murid</label>
            <select name="muridid" id="muridid" class="form-control" required>
                <option value="">Pilih Murid</option>
                @foreach($murids as $murid)
                    <option value="{{ $murid->muridid }}">{{ $murid->namamurid }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="mapel">Mata Pelajaran</label>
            <input type="text" name="mapel" id="mapel" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nilai">Nilai</label>
            <input type="number" name="nilai" id="nilai" class="form-control" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
    </form>

    <hr>

    <h2>Riwayat Nilai</h2>
    <table class="table table-bordered">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered yajra-datatable table-striped" id="nilai-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            <th>Mata Pelajaran</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
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
        loadnilai()

        var nilaitable = $('#nilai-table').DataTable({
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
                            if (nilaitable.rows({ selected: true }).count() <= 0) {
                                swal.fire("Data belum dipilih", "Silahkan pilih data yang akan dihapus", "error");
                                return;
                            }
                            var id = nilaitable.rows({ selected: true }).data()[0]['nilaiid'];
                            var url = "{{ route('nilai.deletenilai', ':id') }}";
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
                                        cache: false,
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
                                                var rowData = nilaitable.rows({ selected: true }).data()[0]; // Get selected row data
                                                var nilaiid = rowData.nilaiid;
                                                loadnilai(nilaiid);
                                            } else {
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
                {'orderData': 2, data: 'mapel', name: 'mapel', 
                    render: function(data, type, row){
                        return row.mapel;
                    }
                },
                {'orderData': 3, data: 'tanggal', name: 'tanggal', 
                    render: function(data, type, row){
                        return row.tanggal;
                    }
                },
                {'orderData': 4, data: 'keterangan', name: 'keterangan', 
                    render: function(data, type, row){
                        return row.keterangan;
                    }
                },
            ],
            initComplete: function (settings, json) {
                $(".btn-datatable").removeClass("dt-button");
            },
            //order: [[1, 'asc']]
        });

        function loadnilai() {
            var url = "{{ route('nilai.shownilai') }}";
            // url = url.replace(':id', nilaiid);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {

                    nilaitable.clear();

                    for (var i = 0; i < response.data.count; i++) {
                        nilaitable.row.add({
                            nilaiid: response.data.data[i].nilaiid,
                            namamurid: response.data.data[i].namamurid,
                            mapel: response.data.data[i].mapel,
                            tanggal: response.data.data[i].tanggal,
                            keterangan: response.data.data[i].keterangan
                        });
                    }

                    nilaitable.draw();
                    $('#nilai-table').show();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    })
</script>
@endsection
