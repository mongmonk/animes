@extends('layouts.app')

@section('title', 'Offline')

@section('content')
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mb-6 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-3.536 5 5 0 011.414-3.536m1.236-.905L4.636 5.636m12.728 12.728L5.636 5.636M5.636 5.636a9 9 0 0112.728 0" />
        </svg>
        <h1 class="text-3xl font-bold mb-4">Kamu Sedang Offline</h1>
        <p class="text-white/60 mb-8 max-w-md">Sepertinya koneksi internetmu terputus. Halaman ini akan tersedia kembali setelah kamu terhubung ke internet.</p>
        <a href="/" class="bg-accent-green text-dark-primary font-bold px-8 py-3 rounded-xl transition hover:opacity-90">Coba Lagi</a>
    </div>
@endsection