<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="UPTM University">
    <title>UPTM Academic AI Assistant Tools - Login</title>
    <link rel="shortcut icon" href="{{ asset('upload/uptm.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <style>
        /* Custom styles for the password toggle icon */
        .ni {
            font-family: "Nioicon" !important;
            speak: none;
            font-style: normal;
            font-weight: 400;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .ni-eye:before {
            content: "";
        }

        .ni-eye-off:before {
            content: "";
        }
    </style>
</head>

<body class="bg-[#f0f4f8] min-h-screen flex items-center justify-center relative p-4">
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute -top-1/4 -left-1/4 w-3/4 h-3/4 bg-[#d0e6f5] rounded-full filter blur-3xl opacity-50"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-3/4 h-3/4 bg-[#e0f2fe] rounded-full filter blur-3xl opacity-50"></div>
    </div>

    <div class="relative z-10 w-full max-w-xs sm:max-w-sm md:max-w-md mx-auto my-auto">
        <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 text-center border border-gray-100">
            <div class="mb-4">
                <img class="w-40 h-auto mx-auto" src="{{ asset('upload/uptm.png') }}" alt="UPTM University Logo">
            </div>

            <div class="mb-5">
                <h1 class="text-xl md:text-2xl font-extrabold text-[#1e40af] mb-1">UPTM Academic AI Assistant Tools</h1>
                <p class="text-[#64748b] text-xs md:text-sm font-medium max-w-xs mx-auto mt-2">
                    Log in for academic AI assistance
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 text-left mb-1">Email Address</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all" 
                            type="email" 
                            id="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required />
                    </div>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 text-left mb-1">Password</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10" 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Enter your password"
                            required />
                        <a href="#" class="absolute right-0 top-1/2 -translate-y-1/2 pr-3 text-[#94a3b8] hover:text-[#1e40af] transition-colors"
                            title="Toggle show/hide password" onclick="togglePasswordVisibility(event)">
                            <i class="fas fa-eye-slash" id="eye-icon"></i>
                        </a>
                    </div>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-between items-center text-xs">
                    <div class="flex items-center">
                    </div>
                    <a class="font-medium text-[#1e40af] hover:underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                </div>

                <div>
                    <button class="w-full bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg shadow-md hover:bg-[#1d4ed8] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8] focus:ring-offset-2 transition-colors" type="submit">
                        Sign In
                    </button>
                </div>
            </form>

            <div class="mt-5 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-3">Don't have an account yet?</p>
                <a href="{{ route('register') }}" class="inline-block border border-[#1e40af] text-[#1e40af] py-1.5 px-4 rounded-lg text-xs font-semibold hover:bg-[#1e40af] hover:text-white transition-colors">
                    Create Account
                </a>
            </div>
            
            <div class="mt-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} UPTM University. All rights reserved.
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility(event) {
            event.preventDefault();
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }

        // Toastr notifications
        @if(Session::has('message'))
        var type = "{{ Session::get('alert-type','info') }}";
        switch(type) {
            case 'info': toastr.info("{{ Session::get('message') }}"); break;
            case 'success': toastr.success("{{ Session::get('message') }}"); break;
            case 'warning': toastr.warning("{{ Session::get('message') }}"); break;
            case 'error': toastr.error("{{ Session::get('message') }}"); break;
        }
        @endif

        @if ($errors->any())
            toastr.error("Invalid email or password.");
        @endif
    </script>

</body>

</html>