<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Digital Library</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      input:-webkit-autofill,
      input:-webkit-autofill:hover,
      input:-webkit-autofill:focus,
      textarea:-webkit-autofill,
      textarea:-webkit-autofill:hover,
      textarea:-webkit-autofill:focus,
      select:-webkit-autofill,
      select:-webkit-autofill:hover,
      select:-webkit-autofill:focus {
        background-color: #111827 !important;
  -webkit-text-fill-color: #E5E7EB !important;
  transition: background-color 5000s ease-in-out 0s;
    }
    }
  </style>
</head>

<body class="min-h-screen bg-cover bg-center flex items-center justify-center"
      style="background-image: url('{{ asset('images/mandala.png') }}');">

  <div class="bg-[#1F2937]/60 backdrop-blur-md border border-white/10 w-full max-w-md p-8 rounded-2xl shadow-2xl">

    <!-- <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold text-[#E5E7EB]">Mandala Perpus</h1>
    </div> -->

    <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold text-[#E5E7EB]">KOST ATHIA</h1>
      <p class="text-gray-400 text-sm">Login untuk melanjutkan</p>
    </div>

    <!-- ERROR MESSAGE -->
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-500/20 text-red-400 rounded-lg text-sm">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-500/20 text-red-400 rounded-lg text-sm">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="/login" method="POST" class="space-y-4">
      @csrf

      <!-- Email -->
      <div>
        <label class="text-sm text-gray-400">Email</label>
        <input
          type="email"
          name="email"
          required
          placeholder="Masukkan email"
          value="{{ old('email') }}"
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
        @error('email')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
      </div>

      <!-- Password -->
      <div>
        <label class="text-sm text-gray-400">Password</label>
        <input
          type="password"
          name="password"
          required
          placeholder="Masukkan password"
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
        @error('password')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
      </div>

      <!-- Options -->
      <div class="flex justify-between items-center text-sm">
        <label class="flex items-center gap-2 text-gray-400">
          <input type="checkbox" class="accent-[#1E3A8A]">
          Remember me
        </label>
        <a href="#" class="text-[#49769f] hover:underline">Lupa password?</a>
      </div>

      <!-- Button -->
      <button
        type="submit"
        class="w-full py-3 bg-[#1E3A8A] hover:bg-blue-800 transition rounded-xl text-white font-semibold shadow-lg">
        Login
      </button>

    </form>

    <p class="text-center text-gray-400 text-sm mt-6">
      Belum punya akun?
      <a href="/register" class="text-[#49769f] hover:underline">daftar</a>
    </p>

  </div>

</body>
</html>
