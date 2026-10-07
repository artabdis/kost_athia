<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Digital Library</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      input:-webkit-autofill,
      input:-webkit-autofill:hover,
      input:-webkit-autofill:focus,
      input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #111827 inset !important;
        -webkit-text-fill-color: #E5E7EB !important;
        caret-color: #E5E7EB;
        border: 1px solid #374151;
        transition: background-color 9999s ease-in-out 0s;
      }

      input::selection {
        background: #1E3A8A;
        color: #E5E7EB;
      }

      input:focus {
        background-color: #111827 !important;
        color: #E5E7EB !important;
      }
    }
  </style>
</head>

<body class="bg-[#111827] flex items-center justify-center min-h-screen">

  <!-- Card -->
  <div class="bg-[#1F2937] w-full max-w-md p-8 rounded-2xl shadow-2xl">

    <!-- Logo -->
    <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold text-[#E5E7EB]"> Digital Library</h1>
    </div>

    <!-- Title -->
    <div class="text-center mb-6">
      <h2 class="text-xl font-semibold text-[#E5E7EB]">Create Account</h2>
      <p class="text-gray-400 text-sm">Daftar untuk mulai membaca</p>
    </div>

    <!-- ERROR MESSAGE -->
    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-500/20 text-red-400 rounded-lg text-sm">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-500/20 text-green-400 rounded-lg text-sm">
        {{ session('success') }}
      </div>
    @endif

    <!-- Form -->
    <form method="POST" action="/register" class="space-y-4">
      <!-- CSRF (Laravel) -->
      @csrf

      <!-- Name -->
      <div>
        <label class="text-sm text-gray-400">Nama</label>
        <input type="text" name="name" placeholder="Masukkan nama" required value="{{ old('name') }}"
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
        @error('name')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
      </div>

      <!-- Email -->
      <div>
        <label class="text-sm text-gray-400">Email</label>
        <input type="email" name="email" placeholder="Masukkan email" required value="{{ old('email') }}"
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
        @error('email')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
      </div>

      <!-- Password -->
      <div>
        <label class="text-sm text-gray-400">Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
        @error('password')<span class="text-red-400 text-xs">{{ $message }}</span>@enderror
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="text-sm text-gray-400">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" placeholder="Ulangi password" required
          class="w-full mt-1 px-4 py-3 bg-[#111827] border border-gray-700 rounded-xl text-[#E5E7EB] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
      </div>

      <!-- Button -->
      <button
        class="w-full py-3 bg-[#1E3A8A] hover:bg-blue-800 transition rounded-xl text-white font-semibold shadow-lg">
        Register
      </button>
    </form>

    <!-- Footer -->
    <p class="text-center text-gray-400 text-sm mt-6">
      Sudah punya akun?
      <a href="/login" class="text-[#1E3A8A] hover:underline">Login</a>
    </p>

  </div>

</body>
</html>
