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

        <form method="POST" action="{{ route('pembayaran.update', $pembayaran->pembayaranid) }}"
            class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
            @csrf
            {{ method_field('PUT') }}
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="kodepembayaran">Kode Pembayaran*</label>
                        <input type="text" class="form-control @error('kodepembayaran') is-invalid @enderror" id="kodepembayaran" name="kodepembayaran"
                            value="{{ old('kodepembayaran') ?? $pembayaran->kodepembayaran }}" autofocus required>
                        @error('kodepembayaran')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tgllahir" class="col-md-12 col-form-label text-md-left">{{ __('Murid *') }}</label>
                        <div class="col-md-12">
                            <select id="muridid" class="custom-select form-control @error('muridid') is-invalid @enderror" name='muridid' required autofocus>
                                <option value="">-- Pilih Murid --</option>
                                @foreach ($murid as $item)
                                <option value="{{$item->muridid}}" @if ($pembayaran->muridid == $item->muridid) selected @endif>{{ $item->kodemurid . ' || ' . $item->namamurid }}</option>
                                @endforeach
                            </select>
    
                            @error('muridid')
                                <span class="invalid-feedback" role="alert">
                                    <p class="text-danger">{{ $message }}</p>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="category">Kategori Pembayaran*</label>
                        <select id="category" class="col-md-12 custom-select form-cosntrol" name="category" autofocus
                            required>

                            <option value="">-- Pilih Kategori Pembayaran --</option>
                            @foreach (enum::listPembayaran() as $id)
                            <option value="{{ $id }}" {{ $pembayaran->category != null && $pembayaran->category == $id ? "selected" : "" }}> {{ enum::listPembayaran('desc')[$loop->index] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="tglbayar" class="col-md-12 col-form-label text-md-left">{{ __('Tanggal Pembayaran *') }}</label>
                        <div class="col-md-12">
                            <input type="date" class="form-control @error('tglbayar') is-invalid @enderror" id="tglbayar" name="tglbayar" value="{{ old('tglbayar') ?? $pembayaran->tglbayar }}" required>
                            @error('tglbayar')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="status">Status*</label>
                        <select id="status" class="col-md-12 custom-select form-control" name="status" autofocus
                            required>

                            <option value="0" {{ $pembayaran->status != null && $pembayaran->status == $id ? "selected" : "" }}>Pending</option>
                            <option value="1" {{ $pembayaran->status != null && $pembayaran->status == $id ? "selected" : "" }}>Terverivikasi</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group row mb-0">
                    <button type="submit" class="btn btn-info waves-effect waves-light m-r-10">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                        {{ __('Kembali') }}
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
