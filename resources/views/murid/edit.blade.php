<?php
use App\enumVar as enum;
?>
@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">UBAH DATA</h5>
        <hr />
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

        <form method="POST" action="{{ route('murid.update', $murid->muridid) }}"
            class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
            @csrf
            {{ method_field('PUT') }}
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="kodemurid">Kode Murid*</label>
                        <input type="text" class="form-control @error('kodemurid') is-invalid @enderror" id="kodemurid" name="kodemurid"
                            value="{{ old('kodemurid') ?? $murid->kodemurid }}" autofocus required>
                        @error('kodemurid')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="namamurid">Nama Murid*</label>
                        <input type="text" class="form-control @error('namamurid') is-invalid @enderror" id="namamurid"
                            name="namamurid" value="{{ old('namamurid') ?? $murid->namamurid }}" autofocus required>
                        @error('namamurid')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="jeniskelamin">Jenis Kelamin*</label>
                        <select id="jeniskelamin" class="col-md-12 custom-select form-control" name="jeniskelamin" autofocus
                            required>

                            <option value="">-- Pilih Jenis Kelamin --</option>
                            @foreach (enum::listJenisKelamin() as $id)
                            <option value="{{ $id }}" {{ $murid->jeniskelamin != null && $murid->jeniskelamin == $id ? "selected" : "" }}> {{ enum::listJenisKelamin('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="alamat">Alamat*</label>
                        <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                            name="alamat" value="{{ old('alamat') ?? $murid->alamat }}" autofocus required>
                        @error('alamat')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="agama">Agama*</label>
                        <select id="agama" class="col-md-12 custom-select form-control" name="agama" autofocus
                            required>

                            <option value="">-- Pilih Agama --</option>
                            @foreach (enum::listAgama() as $id)
                            <option value="{{ $id }}" {{ $murid->agama != null && $murid->agama == $id ? "selected" : "" }}> {{ enum::listAgama('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tempatlahir">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempatlahir') is-invalid @enderror"
                            id="tempatlahir" name="tempatlahir" value="{{ old('tempatlahir') ?? $murid->tempatlahir }}" autofocus>
                        @error('tempatlahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tgllahir" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Lahir *') }}</label>
                        <input type="date" class="form-control @error('tgllahir') is-invalid @enderror" id="tgllahir" name="tgllahir" value="{{ old('tgllahir') ?? $murid->tgllahir }}" required>
                        @error('tgllahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="notelp">Nomor Telp</label>
                        <input type="text" class="form-control @error('notelp') is-invalid @enderror"
                            id="notelp" name="notelp" value="{{ old('notelp') ?? $murid->notelp }}" autofocus>
                        @error('notelp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="namasekolah">Nama Sekolah</label>
                        <input type="text" class="form-control @error('namasekolah') is-invalid @enderror"
                            id="namasekolah" name="namasekolah" value="{{ old('namasekolah') ?? $murid->namasekolah }}" autofocus>
                        @error('namasekolah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="namaortu">Nama Ortu</label>
                        <input type="text" class="form-control @error('namaortu') is-invalid @enderror" id="namaortu"
                            name="namaortu" value="{{ old('namaortu') ?? $murid->namaortu }}" autofocus>
                        @error('namaortu')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tglmasuk" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Masuk *') }}</label>
                        <div class="col-md-12">
                            <input type="date" class="form-control @error('tglmasuk') is-invalid @enderror" id="tglmasuk" name="tglmasuk" value="{{ old('tglmasuk') ?? $murid->tglmasuk }}" required>
                            @error('tglmasuk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="emailortu">Email Orang Tua</label>
                        <input type="text" class="form-control @error('emailortu') is-invalid @enderror" id="emailortu"
                            name="emailortu" value="{{ old('emailortu') ?? $murid->emailortu }}" autofocus>
                        @error('emailortu')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="level">Level*</label>
                        <select id="level" class="col-md-12 custom-select form-control" name="level" autofocus
                            required>

                            <option value="">-- Pilih Level --</option>
                            @foreach (enum::listLevelMurid() as $id)
                            <option value="{{ $id }}" {{ $murid->level != null && $murid->level == $id ? "selected" : "" }}> {{ enum::listLevelMurid('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="status">Status*</label>
                        <select id="status" class="col-md-12 custom-select form-control" name="status" autofocus
                            required>

                            <option value="">-- Pilih Status --</option>
                            @foreach (enum::listStatusMurid() as $id)
                            <option value="{{ $id }}" {{ $murid->status != null && $murid->status == $id ? "selected" : ""}}> {{ enum::listStatusMurid('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group row mb-0">
                    <button type="submit" class="btn btn-info waves-effect waves-light m-r-10">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('murid.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                        {{ __('Index murid') }}
                    </a>
                    {{-- <a href="{{ route('home') }}" class="btn btn-dark waves-effect waves-light m-r-10">
                    {{ __('Home') }}
                    </a> --}}
                </div>
            </div>
    </div>
    </form>
</div>
</div>
</div>

<script>
</script>
@endsection
