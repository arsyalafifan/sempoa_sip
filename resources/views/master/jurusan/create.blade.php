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

            <form method="POST" action="{{ route('jurusan.store') }}" class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label for="nojurusan" class="col-md-12 col-form-label text-md-left">{{ __('Nomor Jurusan *') }}</label>

                    <div class="col-md-12">
                        <input id="nojurusan" type="text" class="form-control @error('nojurusan') is-invalid @enderror" name="nojurusan" value="{{ old('nojurusan') }}" maxlength="100" required autocomplete="nojurusan" autofocus>

                        @error('nojurusan')
                            <span class="invalid-feedback" role="alert">
                                <p class="text-danger">{{ $message }}</p>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="namajurusan" class="col-md-12 col-form-label text-md-left">{{ __('Nama Jurusan *') }}</label>

                    <div class="col-md-12">
                        <input id="namajurusan" type="text" class="form-control @error('namajurusan') is-invalid @enderror" name="namajurusan" value="{{ old('namajurusan') }}" required autocomplete="name" max="50">

                        @error('namajurusan')
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
                        <a href="{{ route('jurusan.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                            {{ __('Index Jurusan') }}
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

        var url = "{{ route('jurusan.nextno') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#nojurusan').val(data);
            }
        });

        // $('#nojurusan').on('focus', function() {
        //     if ($('#nojurusan').val() == "") {
        //         $('#nojurusan').val('');
        //     }
        //     else {
        //         var url = "{{ route('jurusan.nextno') }}"
        //         // url = url.replace(':id', $('#nojurusan').val());
        //         $.ajax({
        //             url:url,
        //             type:'GET',
        //             success:function(data) {
        //                 $('#nojurusan').val(data);
        //             }
        //         });
        //     }
        // }).trigger('focus');
    });
</script>
@endsection
