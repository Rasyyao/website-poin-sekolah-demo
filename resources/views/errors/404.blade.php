@extends('errors.layout')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('code', '404')
@section('headline', 'Halaman Tidak Ditemukan')

@section('description')
{{ $exception->getMessage() ?: 'Halaman atau data yang Anda cari tidak dapat ditemukan. Tautan mungkin salah ketik atau telah dipindahkan.' }}
@endsection

