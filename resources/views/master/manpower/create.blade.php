<?php
use App\enumVar as enum;
?>
@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">TAMBAH DATA</h5><hr />
            @if (count($errors) > 0)
            @foreach ($errors->all() as $error)
                <p class="alert alert-danger alert-dismissible fade show" role="alert">{{ $error }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </p>
            @endforeach
            @endif

            @if (session()->has('message'))
                <p class="alert alert-success alert-dismissible fade show" role="alert">{{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </p>
            @endif

            <form method="POST" action="{{ route('kelas.store') }}" class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label for="nokelas" class="col-md-12 col-form-label text-md-left">{{ __('Nomor Kelas *') }}</label>

                    <div class="col-md-12">
                        <input id="nokelas" type="text" class="form-control @error('nokelas') is-invalid @enderror" name="nokelas" value="{{ old('nokelas') }}" maxlength="100" required autocomplete="nokelas" autofocus>

                        @error('nokelas')
                            <span class="invalid-feedback" role="alert">
                                <p class="text-danger">{{ $message }}</p>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="namakelas" class="col-md-12 col-form-label text-md-left">{{ __('Nama Kelas *') }}</label>

                    <div class="col-md-12">
                        <input id="namakelas" type="text" class="form-control @error('namakelas') is-invalid @enderror" name="namakelas" value="{{ old('namakelas') }}" required autocomplete="name">

                        @error('namakelas')
                            <span class="invalid-feedback" role="alert">
                                <p class="text-danger">{{ $message }}</p>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-info waves-effect waves-light m-r-10">
                            {{ __('Simpan') }}
                        </button>
                        <a href="{{ route('kegiatan.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                            {{ __('Index Kelas') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.custom-select').select2();

        var url = "{{ route('kelas.nextno') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#nokelas').val(data);
            }
        });

        // $('#nokelas').on('focus', function() {
        //     if ($('#nokelas').val() == "") {
        //         $('#nokelas').val('');
        //     }
        //     else {
        //         var url = "{{ route('kelas.nextno') }}"
        //         // url = url.replace(':id', $('#nokelas').val());
        //         $.ajax({
        //             url:url,
        //             type:'GET',
        //             success:function(data) {
        //                 $('#nokelas').val(data);
        //             }
        //         });
        //     }
        // }).trigger('focus');
    });
</script>
@endsection
