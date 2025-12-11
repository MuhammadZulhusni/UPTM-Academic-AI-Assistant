<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="UPTM University">
    <title>UPTM Academic AI Assistant Tools - Forgot Password</title>
    <link rel="shortcut icon" href="{{ asset('upload/uptm.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
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
                <h1 class="text-xl md:text-2xl font-extrabold text-[#1e40af] mb-1">Forgot Password</h1>
            </div>
            
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-700 bg-green-100 p-3 rounded-lg text-left">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 text-left text-gray-600 border-l-4 border-[#60a5fa] pl-3 py-1 bg-gray-50 rounded-r-md text-sm">
                Forgot your password? No problem. Provide your email below and we'll send you a password reset link.
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 text-left mb-1">Email Address</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all
                               @error('email') border-red-500 @enderror" 
                            type="email" 
                            id="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="e.g., your.name@student.uptm.edu.my"
                            required
                            autofocus />
                    </div>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-center pt-2">
                    <button class="w-full bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg shadow-md hover:bg-[#1d4ed8] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8] focus:ring-offset-2 transition-colors" type="submit">
                        <i class="fas fa-paper-plane mr-2"></i> Send Reset Link
                    </button>
                </div>
            </form>
            
            <div class="mt-5 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">Remember your password?</p>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 py-1.5 px-4 rounded-lg text-xs font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Login
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

        // A general error message for form validation errors
        // This will catch any validation errors not specifically handled above, like rate limits.
        @if ($errors->any())
            toastr.error("Please correct the errors in the form.");
        @endif
    </script>

</body>

</html>