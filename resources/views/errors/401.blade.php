@extends('errors.layout')

@section('title', '401 — Autentikasi Diperlukan')

@section('code', '401')
@section('headline', 'Autentikasi Diperlukan')

@section('description')
{{ $exception->getMessage() ?: 'Sesi Anda telah kedaluwarsa atau Anda belum masuk. Silakan login terlebih dahulu untuk melanjutkan.' }}
@endsection

