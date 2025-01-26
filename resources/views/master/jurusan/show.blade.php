@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">LIHAT DATA</h5><hr />
        <div class="form-group row mb-0 text-md-right">
            <div class="col-md-12">
                <a href="{{ route('jurusan.create') }}" class="btn btn-info btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Tambah') }}</a>
                <a href="{{ route('jurusan.edit', $jurusan->jurusanid) }}" class="btn btn-warning btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Ubah') }}</a>
            </div>
        </div>

        <div class="form-group row">
            <label for="nojurusan" class="col-md-12 col-form-label text-md-left">{{ __('Nomor Jurusan') }}</label>

            <div class="col-md-12">
                <input id="nojurusan" type="text" class="form-control" name="nojurusan" value="{{ $jurusan->nojurusan }}" readonly>
            </div>
        </div>

        <div class="form-group row">
            <label for="namajurusan" class="col-md-12 col-form-label text-md-left">{{ __('Nama Jurusan') }}</label>

            <div class="col-md-12">
                <input id="namajurusan" type="text" class="form-control" name="namajurusan" value="{{ $jurusan->namajurusan }}" readonly>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-12">
                <a href="{{ route('jurusan.index') }}" class="btn btn-info waves-effect waves-light m-r-10">
                    {{ __('Index Jurusan') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    });
</script>
@endsection
