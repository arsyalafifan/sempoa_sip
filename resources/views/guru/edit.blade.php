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

        <form method="POST" action="{{ route('guru.update', $guru->guruid) }}"
            class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
            @csrf
            {{ method_field('PUT') }}
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="kodeguru">Kode Guru*</label>
                        <input type="text" class="form-control @error('kodeguru') is-invalid @enderror" id="kodeguru" name="kodeguru"
                            value="{{ old('kodeguru') ?? $guru->kodeguru }}" autofocus required>
                        @error('kodeguru')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tgllahir" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Lahir *') }}</label>
                        <div class="col-md-12">
                            <input type="date" class="form-control @error('tgllahir') is-invalid @enderror" id="tgllahir" name="tgllahir" value="{{ old('tgllahir') ?? $guru->tgllahir }}" required>
                            @error('tgllahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="namaguru">Nama Guru*</label>
                        <input type="text" class="form-control @error('namaguru') is-invalid @enderror" id="namaguru"
                            name="namaguru" value="{{ old('namaguru') ?? $guru->namaguru }}" autofocus required>
                        @error('namaguru')
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
                        <label for="namapanggilan">Nama Panggilan*</label>
                        <input type="text" class="form-control @error('namapanggilan') is-invalid @enderror" id="namapanggilan"
                            name="namapanggilan" value="{{ old('namapanggilan') ?? $guru->namapanggilan }}" autofocus required>
                        @error('namapanggilan')
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
                            <option value="{{ $id }}" {{ $guru->jeniskelamin != null && $guru->jeniskelamin == $id ? 'selected' : '' }}> {{ enum::listJenisKelamin('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="alamat">Alamat*</label>
                        <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                            name="alamat" value="{{ old('alamat') ?? $guru->alamat }}" autofocus required>
                        @error('alamat')
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
                        <label for="tempatlahir">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempatlahir') is-invalid @enderror"
                            id="tempatlahir" name="tempatlahir" value="{{ old('tempatlahir') ?? $guru->tempatlahir }}" autofocus>
                        @error('tempatlahir')
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
                            <option value="{{ $id }}" {{ $guru->agama != null && $guru->agama == $id ? 'selected' : '' }}> {{ enum::listAgama('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tgllahir" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Lahir *') }}</label>
                        <div class="col-md-12">
                            <input type="date" class="form-control @error('tgllahir') is-invalid @enderror" id="tgllahir" name="tgllahir" value="{{ old('tgllahir') ?? $guru->tgllahir }}" required>
                            @error('tgllahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="notelp">HP</label>
                        <input type="text" class="form-control @error('notelp') is-invalid @enderror" id="notelp"
                            name="notelp" value="{{ old('notelp') ?? $guru->notelp }}" autofocus>
                        @error('notelp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="pendidikan">Pendidikan Terakhir</label>
                        <input type="text" class="form-control @error('pendidikan') is-invalid @enderror" id="pendidikan"
                            name="pendidikan" value="{{ old('pendidikan') ?? $guru->pendidikan }}" autofocus>
                        @error('pendidikan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="email">Email</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') ?? $guru->email }}" autofocus>
                        @error('email')
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
                            @foreach (enum::listLevelGuru() as $id)
                            <option value="{{ $id }}" {{ $guru->level != null && $guru->level == $id ? 'selected' : '' }}> {{ enum::listLevelGuru('desc')[$loop->index] }}</option>
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
                            @foreach (enum::listStatusGuru() as $id)
                            <option value="{{ $id }}" {{ $guru->status != null && $guru->status == $id ? 'selected' : '' }}> {{ enum::listStatusGuru('desc')[$loop->index] }}</option>
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
                    <a href="{{ route('guru.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                        {{ __('Index guru') }}
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
