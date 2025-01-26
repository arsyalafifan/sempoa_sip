@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">LIHAT DATA</h5><hr />
        <div class="form-group row mb-0 text-md-right">
            <div class="col-md-12">
                <a href="{{ route('kelas.create') }}" class="btn btn-info btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Tambah') }}</a>
                <a href="{{ route('kelas.edit', $kelas->kelasid) }}" class="btn btn-warning btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Ubah') }}</a>
            </div>
        </div>

        <div class="form-group row">
            <label for="nokelas" class="col-md-12 col-form-label text-md-left">{{ __('Nomor Kelas') }}</label>

            <div class="col-md-12">
                <input id="nokelas" type="text" class="form-control" name="nokelas" value="{{ $kelas->nokelas }}" readonly>
            </div>
        </div>

        <div class="form-group row">
            <label for="namakelas" class="col-md-12 col-form-label text-md-left">{{ __('Nama Kelas') }}</label>

            <div class="col-md-12">
                <input id="namakelas" type="text" class="form-control" name="namakelas" value="{{ $kelas->namakelas }}" readonly>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-12">
                <a href="{{ route('kelas.index') }}" class="btn btn-info waves-effect waves-light m-r-10">
                    {{ __('Index kelas') }}
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
