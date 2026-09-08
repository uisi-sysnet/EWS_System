@include('layouts.header')

<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 sm:p-6 text-gray-900 dark:text-blue-200">

    <div class="w-full max-w-md bg-white/90 dark:bg-gray-900/85 backdrop-blur-md rounded-2xl shadow-2xl dark:shadow-none dark:card-glow border border-gray-300 dark:border-blue-800/50 overflow-hidden p-8">
        <h2 class="text-xl font-bold mb-6 text-center text-gray-900 dark:text-white">Change Your Password</h2>

        @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">CURRENT PASSWORD</label>
                <input type="password" name="current_password" required autofocus
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-blue-800/50 rounded-lg text-gray-900 dark:text-blue-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:border-blue-700 transition"
                    placeholder="Enter your current password">
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">NEW PASSWORD</label>
                <input type="password" name="new_password" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-blue-800/50 rounded-lg text-gray-900 dark:text-blue-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:border-blue-700 transition"
                    placeholder="Enter new password">
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">CONFIRM NEW PASSWORD</label>
                <input type="password" name="new_password_confirmation" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-blue-800/50 rounded-lg text-gray-900 dark:text-blue-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:border-blue-700 transition"
                    placeholder="Confirm new password">
            </div>

            <button type="submit"
                class="group relative w-full overflow-hidden rounded-2xl py-2 text-lg font-extrabold tracking-wide uppercase transition-all duration-150 ease-in-out cursor-pointer shadow-[0_6px_0_0_#092273] hover:shadow-[0_3px_0_0_#092273] hover:translate-y-[3px] active:shadow-[0_1px_0_0_#092273] active:translate-y-[5px] bg-blue-700 hover:bg-blue-600 dark:bg-[#003595] dark:hover:bg-[#0040c0] border border-blue-600/40 text-white focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:ring-offset-2 mt-4">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
                <span class="relative z-10">CHANGE PASSWORD</span>
            </button>
        </form>
        <div class="mt-6 text-gray-500 dark:text-gray-400">
            <p class="font-medium mb-1">Password must contain:</p>
            <ul class="list-disc pl-5 space-y-1">
                <li class="{{ preg_match('/[A-Z]/', old('new_password', '')) ? 'text-green-500' : '' }}">At least one uppercase letter</li>
                <li class="{{ preg_match('/[a-z]/', old('new_password', '')) ? 'text-green-500' : '' }}">At least one lowercase letter</li>
                <li class="{{ preg_match('/[0-9]/', old('new_password', '')) ? 'text-green-500' : '' }}">At least one number</li>
                <li class="{{ preg_match('/[@$!%*#?&]/', old('new_password', '')) ? 'text-green-500' : '' }}">At least one special character (@$!%*#?&)</li>
                <li class="{{ strlen(old('new_password', '')) >= 8 ? 'text-green-500' : '' }}">Minimum 8 characters</li>
            </ul>
        </div>
        <div class="mt-6 text-center text-xs">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="text-blue-700 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 font-medium transition-colors">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

    </div>

    @include('layouts.footer')