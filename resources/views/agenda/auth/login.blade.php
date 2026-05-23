@extends('agenda.layouts.app')

@section('title', 'Login - Agenda Muncks')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-16">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-blue-900 mb-6">Agenda Muncks</h1>
        <h2 class="text-lg text-center text-gray-600 mb-8">POLIFERRO</h2>

        <form method="POST" action="/agenda/login">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2.5 border"
                    required autofocus>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2.5 border"
                    required>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-600">Lembrar acesso</span>
                </label>
            </div>

            <button type="submit"
                class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                Entrar
            </button>
        </form>
    </div>
</div>
@endsection
