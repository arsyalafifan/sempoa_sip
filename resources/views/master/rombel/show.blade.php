@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">LIHAT DATA</h5><hr />
        <div class="form-group row mb-0 text-md-right">
            <div class="col-md-12">
                <a href="{{ route('rombel.create') }}" class="btn btn-info btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Tambah') }}</a>
                <a href="{{ route('rombel.edit', $rombel->rombelid) }}" class="btn btn-warning btn-sm waves-effect waves-light m-r-5"><i class="fa fa-plus-circle"></i> {{ __('Ubah') }}</a>
            </div>
        </div>

        <div class="form-group row">
            <label for="norombel" class="col-md-12 col-form-label text-md-left">{{ __('Nomor Rombel') }}</label>

            <div class="col-md-12">
                <input id="norombel" type="text" class="form-control" name="norombel" value="{{ $rombel->norombel }}" readonly>
            </div>
        </div>

        <div class="form-group row">
            <label for="namarombel" class="col-md-12 col-form-label text-md-left">{{ __('Nama Rombel') }}</label>

            <div class="col-md-12">
                <input id="namarombel" type="text" class="form-control" name="namarombel" value="{{ $rombel->namarombel }}" readonly>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-12">
                <a href="{{ route('rombel.index') }}" class="btn btn-info waves-effect waves-light m-r-10">
                    {{ __('Index Rombel') }}
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
