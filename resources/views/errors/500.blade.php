@extends('errors.layout')

@section('title', '500 — Kesalahan Server')

@section('code', '500')
@section('headline', 'Terjadi Kesalahan Server')

@section('description')
{{ $exception->getMessage() ?: 'Terjadi kendala teknis pada server. Silakan coba muat ulang halaman atau kembali beberapa saat lagi.' }}
@endsection

