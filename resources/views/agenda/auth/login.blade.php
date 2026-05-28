@extends('agenda.layouts.app')

@section('title', 'Login - Agenda Muncks')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-16">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-blue-900 mb-6">Agenda Muncks</h1>
        <h2 class="text-lg text-center text-gray-600 mb-8 font-bold">POLIFERRO</h2>

        <form method="POST" action="/agenda/login">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2.5 border"
                    required autofocus>
            </div>

            <div class="mb-6 relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password" id="password-field"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2.5 border pr-16"
                    required>
                <button type="button" id="toggle-senha"
                    class="absolute right-2 top-[38px] text-sm text-blue-900 hover:text-blue-700 font-semibold touch-target px-2 py-1">
                    Mostrar
                </button>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-600">Manter conectado</span>
                </label>
            </div>

            <button type="submit"
                class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                Entrar
            </button>

            <div style="text-align: center; margin-top: 15px;">
                <p>© 2026 Poliferro</p>
                <p><a href="#">Sobre</a> | <a href="#">Contato</a></p>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('toggle-senha');
    var passwordField = document.getElementById('password-field');

    if (toggleBtn && passwordField) {
        toggleBtn.addEventListener('click', function () {
            var isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            toggleBtn.textContent = isPassword ? 'Ocultar' : 'Mostrar';
        });
    }
});
</script>
@endpush
@endsection
