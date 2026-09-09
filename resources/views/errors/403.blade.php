@extends('errors.layout')

@section('title', '403 — Akses Dibatasi')

@section('code', '403')
@section('headline', 'Akses Dibatasi')

@section('description')
{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman ini. Hubungi administrator jika Anda memerlukan izin.' }}
@endsection

