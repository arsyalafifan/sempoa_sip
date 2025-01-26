@extends('layouts.master')

@section('content')
<div class="card p-4">
    <div class="card-body">
        <h5 class="card-title text-uppercase">LIHAT DATA</h5><hr />
        <div class="form-group row mb-0 text-md-right">
            <div class="col-md-12">
                <a href="{{ route('prov.create') }}" class="btn btn-info btn-sm waves-effect waves-light m-r-5">{{ __('Tambah') }}</a>
                <a href="{{ route('prov.edit', $prov->provid) }}" class="btn btn-warning btn-sm waves-effect waves-light m-r-5">{{ __('Ubah') }}</a>
            </div>
        </div>

        <div class="form-group row">
            <label for="kodeprov" class="col-md-12 col-form-label text-md-left">{{ __('Kode Provinsi') }}</label>

            <div class="col-md-12">
                <input id="kodeprov" type="text" class="form-control" name="kodeprov" value="{{ $prov->kodeprov }}" readonly>
            </div>
        </div>

        <div class="form-group row">
            <label for="namaprov" class="col-md-12 col-form-label text-md-left">{{ __('Nama Provinsi') }}</label>

            <div class="col-md-12">
                <input id="namaprov" type="text" class="form-control" name="namaprov" value="{{ $prov->namaprov }}" readonly>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-12">
                <a href="{{ route('prov.index') }}" class="btn btn-info waves-effect waves-light m-r-10">
                    {{ __('Index Provinsi') }}
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
