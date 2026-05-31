@extends('layouts.app')

@section('title', 'Register • MyanGames')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-8">
    <div class="bg-white dark:bg-card w-full max-w-md p-8 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-2xl fade-in">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Join MyanGames 🎮</h2>
            <p class="text-slate-500 dark:text-gray-400">Buat akun untuk berbagi pengalaman gamingmu</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required maxlength="50"
                       placeholder="Contoh: ProGamer123"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('username')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('email')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('password')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required minlength="6"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition">
            </div>

            <!-- Error Message General -->
            @if($errors->any() && !$errors->has('username') && !$errors->has('email') && !$errors->has('password'))
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Submit -->
            <button type="submit" 
                class="w-full bg-gradient-to-r from-primary to-secondary 
                       hover:from-indigo-600 hover:to-purple-600 
                       hover:scale-[1.02] hover:shadow-glow-lg 
                       active:scale-[0.98] 
                       text-white font-bold py-3 rounded-lg 
                       transition-all duration-200 ease-out 
                       shadow-lg shadow-primary/25 mt-2">
                Register & Start Playing 🚀
            </button>
        </form>

        <p class="mt-6 text-center text-slate-500 dark:text-gray-400 text-sm">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-primary hover:text-accent font-medium transition">Login disini</a>
        </p>
    </div>
</div>
@endsection