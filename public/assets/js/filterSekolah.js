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
})
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
    sarpraskebutuhantable.draw();
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
    sarpraskebutuhantable.draw();
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
})

// FILTER SEKOLAH - END