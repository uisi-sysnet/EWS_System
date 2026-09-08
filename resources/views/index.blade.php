@include('layouts.header')

<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 sm:p-6 text-gray-900 dark:text-blue-200">

  <div class="w-full max-w-4xl bg-white/90 dark:bg-gray-900/85 backdrop-blur-md rounded-2xl shadow-2xl dark:shadow-none dark:card-glow border border-gray-300 dark:border-blue-800/50 overflow-hidden">

    <div class="flex flex-col md:flex-row">

      <div class="md:w-1/2 bg-blue-700 dark:bg-[#003595] p-8 md:p-12 flex flex-col items-center justify-center text-center text-white border-b border-blue-600 dark:border-blue-800 md:border-b-0 md:border-r">
        <div class="flex flex-col md:flex-row items-center mb-8">
          <div class="w-26 h-26 md:w-36 md:h-36 overflow-hidden">
            <img src="{{ asset('images/Munti-logo.png') }}" alt="DRRM Logo" class="w-full h-full object-contain p-3">
          </div>
          <div class="w-24 h-24 md:w-32 md:h-32 overflow-hidden">
            <img src="{{ asset('images/DRRM-logo.png') }}" alt="Muntinlupa City" class="w-full h-full object-contain p-2">
          </div>
        </div>

        <p class="text-sm font-medium mb-6 uppercase">Muntinlupa City Department of Disaster Resilience and Management</p>
        <p class="text-xl font-semibold tracking-wide">EMERGENCY WARNING SYSTEM</p>
      </div>

      <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <h2 class="text-xl font-bold mb-8 text-gray-900 dark:text-white text-center md:text-left">Log In</h2>

        @if ($errors->any())
        <div class="mb-2 p-4 bg-red-100 dark:bg-red-900/40 text-red-700 text-sm dark:text-red-300 rounded-lg">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6 text-sm">
          @csrf

          <div>
            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">USERNAME</label>
            <input type="text" name="username" value="{{ old('username') }}" required autofocus
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-blue-800/50 rounded-lg text-gray-900 dark:text-blue-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:border-blue-700 transition backdrop-blur-sm"
              placeholder="Enter your username">
          </div>

          <div>
            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">PASSWORD</label>
            <input type="password" name="password" required
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-blue-800/50 rounded-lg text-gray-900 dark:text-blue-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:border-blue-700 transition backdrop-blur-sm"
              placeholder="Enter your password">
          </div>

          <!-- <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
          </div> -->

          <button type="submit"
            class="group relative w-full overflow-hidden rounded-2xl py-2 text-lg font-extrabold tracking-wide uppercase
             transition-all duration-150 ease-in-out cursor-pointer
             shadow-[0_6px_0_0_#092273] hover:shadow-[0_3px_0_0_#092273] hover:translate-y-[3px]
             active:shadow-[0_1px_0_0_#092273] active:translate-y-[5px]
             bg-blue-700 hover:bg-blue-600 dark:bg-[#003595] dark:hover:bg-[#0040c0]
             border border-blue-600/40 text-white focus:outline-none focus:ring-2 focus:ring-blue-700/40 focus:ring-offset-2 mt-8">
            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
            <span class="relative z-10">LOGIN</span>
            <span class="absolute inset-0 rounded-2xl border border-blue-400/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
          </button>
        </form>

        <!-- <div class="mt-6 text-center text-xs">
          <a href="#" class="text-blue-700 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 font-medium transition-colors">
            Forgot Password?
          </a>
        </div> -->
      </div>

    </div>
  </div>

  @include('layouts.footer')