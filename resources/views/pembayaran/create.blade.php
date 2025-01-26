<?php
use App\enumVar as enum;
?>
@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h5 class="card-title text-uppercase">TAMBAH DATA</h5>
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

        <form method="POST" action="{{ route('pembayaran.store') }}"
            class="form-horizontal form-material m-t-40 needs-validation" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label for="kodepembayaran">Kode Pembayaran*</label>
                        <input type="text" class="form-control @error('kodepembayaran') is-invalid @enderror" id="kodepembayaran" name="kodepembayaran"
                            value="{{ old('kodepembayaran') }}" autofocus required>
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
                                <option value="{{$item->muridid}}">{{ $item->kodemurid . ' || ' . $item->namamurid }}</option>
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
                        <select id="category" class="col-md-12 custom-select form-control" name="category" autofocus
                            required>

                            <option value="">-- Pilih Kategori Pembayaran --</option>
                            @foreach (enum::listPembayaran() as $id)
                            <option value="{{ $id }}"> {{ enum::listPembayaran('desc')[$loop->index] }}</option>
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
                            <input type="date" class="form-control @error('tglbayar') is-invalid @enderror" id="tglbayar" name="tglbayar" value="{{ old('tglbayar') }}" required>
                            @error('tglbayar')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-2">
                <div class="form-group row mb-0">
                    <button type="submit" class="btn btn-info waves-effect waves-light m-r-10">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-primary waves-effect waves-light m-r-10">
                        {{ __('Index pembayaran') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<script>
$(document).ready(function() {
        $('.custom-select').select2();

        var url = "{{ route('pembayaran.nextno') }}";
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#kodepembayaran').val(data);
            }
        });
    });
</script>
@endsection
