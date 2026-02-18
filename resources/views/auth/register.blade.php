<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="UPTM University">
    <meta name="csrf-token" content="{{ csrf_token() }}">  
    <title>UPTM Academic AI Assistant Tools - Sign Up</title>
    <link rel="shortcut icon" href="{{ asset('upload/uptm.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <style>
        .requirement-met {
            color: #10b981;
        }
        .requirement-unmet {
            color: #ef4444;
        }
    </style>
</head>

<body class="bg-[#f0f4f8] min-h-screen flex items-center justify-center relative p-4">
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute -top-1/4 -left-1/4 w-3/4 h-3/4 bg-[#d0e6f5] rounded-full filter blur-3xl opacity-50"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-3/4 h-3/4 bg-[#e0f2fe] rounded-full filter blur-3xl opacity-50"></div>
    </div>

    <div class="relative z-10 w-full max-w-xs sm:max-w-md md:max-w-lg mx-auto my-auto">
        <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 text-center border border-gray-100">
            
            <div class="mb-4">
                <img class="w-40 h-auto mx-auto" src="{{ asset('upload/uptm.png') }}" alt="UPTM University Logo">
            </div>

            <div class="mb-5">
                <h1 class="text-xl md:text-2xl font-extrabold text-[#1e40af] mb-1">UPTM Academic AI Assistant Tools</h1>
                <p class="text-[#64748b] text-xs md:text-sm font-medium max-w-xs mx-auto mt-2">
                    Create an account for academic AI assistance
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 text-left mb-2">Name</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all" 
                            type="text" 
                            id="name" 
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name"
                            required autocomplete="name" />
                    </div>
                    @error('name')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 text-left mb-2">Email Address</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10" 
                            type="email" 
                            id="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required autocomplete="username" />
                        <!-- Status icon inside input -->
                        <span class="absolute right-3 top-1/2 -translate-y-1/2" id="emailStatusIcon">
                            <i class="fas fa-envelope text-gray-400" id="emailIcon"></i>
                        </span>
                    </div>

                    <!-- Live feedback banner -->
                    <div id="emailFeedback" class="mt-2 hidden rounded-lg px-3 py-2 text-xs flex items-center gap-2"></div>

                    @error('email')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                        <button type="button" 
                            onclick="openPasswordModal()" 
                            class="text-xs text-[#1e40af] hover:text-[#1d4ed8] font-semibold flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            Requirements
                        </button>
                    </div>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10" 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Enter your password"
                            oninput="validatePassword()"
                            required autocomplete="new-password" />
                        <a href="#" class="absolute right-0 top-1/2 -translate-y-1/2 pr-3 text-[#94a3b8] hover:text-[#1e40af] transition-colors"
                            title="Toggle show/hide password" onclick="togglePasswordVisibility(event, 'password')">
                            <i class="fas fa-eye-slash" id="eye-icon-password"></i>
                        </a>
                    </div>
                    
                    <!-- Real-time validation preview -->
                    <div id="validation-preview" class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200 hidden">
                        <p class="text-xs font-semibold text-gray-700 mb-2">Password Requirements:</p>
                        <ul class="space-y-1 text-xs">
                            <li id="req-length" class="flex items-center gap-2 requirement-unmet">
                                <i class="fas fa-times-circle"></i>
                                <span>Must be 8-64 characters long</span>
                            </li>
                            <li id="req-uppercase" class="flex items-center gap-2 requirement-unmet">
                                <i class="fas fa-times-circle"></i>
                                <span>At least one uppercase letter (A-Z)</span>
                            </li>
                            <li id="req-lowercase" class="flex items-center gap-2 requirement-unmet">
                                <i class="fas fa-times-circle"></i>
                                <span>At least one lowercase letter (a-z)</span>
                            </li>
                            <li id="req-number" class="flex items-center gap-2 requirement-unmet">
                                <i class="fas fa-times-circle"></i>
                                <span>At least one number (0-9)</span>
                            </li>
                            <li id="req-special" class="flex items-center gap-2 requirement-unmet">
                                <i class="fas fa-times-circle"></i>
                                <span>At least one special character (@$!%*#?&)</span>
                            </li>
                        </ul>
                    </div>

                    @error('password')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 text-left mb-2">Confirm Password</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10" 
                            type="password" 
                            id="password_confirmation"
                            name="password_confirmation" 
                            placeholder="Confirm your password"
                            oninput="validatePasswordMatch()"
                            required autocomplete="new-password" />
                        <a href="#" class="absolute right-0 top-1/2 -translate-y-1/2 pr-3 text-[#94a3b8] hover:text-[#1e40af] transition-colors"
                            title="Toggle show/hide password" onclick="togglePasswordVisibility(event, 'password_confirmation')">
                            <i class="fas fa-eye-slash" id="eye-icon-confirm"></i>
                        </a>
                    </div>
                    
                    <!-- Password match indicator -->
                    <div id="match-indicator" class="mt-2 hidden">
                        <p class="text-xs flex items-center gap-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="text-red-500">Password confirmation must match</span>
                        </p>
                    </div>
                    <div id="match-success" class="mt-2 hidden">
                        <p class="text-xs flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-green-500">Password confirmation must match</span>
                        </p>
                    </div>

                    @error('password_confirmation')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 text-left mb-2">
                        Register As
                    </label>
                    <select id="role" name="role" 
                        class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0]
                        focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all"
                        required>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                    </select>
                    @error('role')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <button class="w-full bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg shadow-md hover:bg-[#1d4ed8] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8] focus:ring-offset-2 transition-colors mt-2" type="submit">
                        Sign Up
                    </button>
                </div>

            </form>

            <div class="mt-4 pt-3 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">Already have an account?</p>
                <a href="{{ route('login') }}" class="inline-block border border-[#1e40af] text-[#1e40af] py-1.5 px-4 rounded-lg text-xs font-semibold hover:bg-[#1e40af] hover:text-white transition-colors">
                    Sign In
                </a>
            </div>

            <div class="mt-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} UPTM University. All rights reserved.
            </div>

        </div>
    </div>

    <!-- Password Requirements Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 relative animate-fadeIn">
            <button onclick="closePasswordModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <div class="mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-lock text-2xl text-[#1e40af]"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 text-center">Password Requirements</h2>
                <p class="text-sm text-gray-500 text-center mt-1">Your password must meet the following criteria:</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <ul class="list-disc list-inside ml-2 space-y-0.5 text-xs">
                    <li>Must be 8-64 characters long</li>
                    <li>At least one uppercase letter (A-Z)</li>
                    <li>At least one lowercase letter (a-z)</li>
                    <li>At least one number (0-9)</li>
                    <li>At least one special character (@$!%*#?&)</li>
                    <li>Password confirmation must match</li>
                </ul>
            </div>

            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-800 flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <span>For your security, please create a strong password that includes a mix of characters.</span>
                </p>
            </div>

            <button onclick="closePasswordModal()" class="w-full mt-4 bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg hover:bg-[#1d4ed8] transition-colors">
                Got it!
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility(event, inputId) {
            event.preventDefault();
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId === 'password' ? 'eye-icon-password' : 'eye-icon-confirm');
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

        // Modal functions
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePasswordModal();
            }
        });

        // Password validation
        function validatePassword() {
            const password = document.getElementById('password').value;
            const preview = document.getElementById('validation-preview');
            
            // Show preview when user starts typing
            if (password.length > 0) {
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
                return;
            }

            // Validation checks
            const checks = {
                length: password.length >= 8 && password.length <= 64,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[@$!%*#?&]/.test(password)
            };

            // Update UI for each requirement
            updateRequirement('req-length', checks.length);
            updateRequirement('req-uppercase', checks.uppercase);
            updateRequirement('req-lowercase', checks.lowercase);
            updateRequirement('req-number', checks.number);
            updateRequirement('req-special', checks.special);

            // Validate password match if confirmation field has value
            validatePasswordMatch();
        }

        function updateRequirement(elementId, isMet) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('i');
            
            if (isMet) {
                element.classList.remove('requirement-unmet');
                element.classList.add('requirement-met');
                icon.classList.remove('fa-times-circle');
                icon.classList.add('fa-check-circle');
            } else {
                element.classList.remove('requirement-met');
                element.classList.add('requirement-unmet');
                icon.classList.remove('fa-check-circle');
                icon.classList.add('fa-times-circle');
            }
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const matchIndicator = document.getElementById('match-indicator');
            const matchSuccess = document.getElementById('match-success');

            if (confirmation.length === 0) {
                matchIndicator.classList.add('hidden');
                matchSuccess.classList.add('hidden');
                return;
            }

            if (password === confirmation) {
                matchIndicator.classList.add('hidden');
                matchSuccess.classList.remove('hidden');
            } else {
                matchIndicator.classList.remove('hidden');
                matchSuccess.classList.add('hidden');
            }
        }

        // ─── Email Real-Time Validation ────────────────────────────────
        const emailInput    = document.getElementById('email');
        const emailFeedback = document.getElementById('emailFeedback');
        const emailIcon     = document.getElementById('emailIcon');

        function isValidEmailFormat(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
        }

        function setEmailState(state, message) {
            // Reset classes
            emailInput.classList.remove(
                'border-green-400', 'ring-2', 'ring-green-200',
                'border-red-400',   'ring-red-200',
                'border-[#e2e8f0]'
            );
            emailIcon.className = 'fas';
            emailFeedback.className = 'mt-2 rounded-lg px-3 py-2 text-xs flex items-center gap-2';

            if (state === 'empty') {
                emailInput.classList.add('border-[#e2e8f0]');
                emailIcon.classList.add('fa-envelope', 'text-gray-400');
                emailFeedback.classList.add('hidden');
                return;
            }

            if (state === 'checking') {
                emailInput.classList.add('border-[#e2e8f0]');
                emailIcon.classList.add('fa-spinner', 'fa-spin', 'text-gray-400');
                emailFeedback.classList.remove('hidden');
                emailFeedback.classList.add('bg-gray-100', 'text-gray-600');
                emailFeedback.innerHTML = '<i class="fas fa-hourglass-half"></i><span>' + message + '</span>';
                return;
            }

            if (state === 'invalid_format') {
                emailInput.classList.add('border-red-400', 'ring-2', 'ring-red-200');
                emailIcon.classList.add('fa-times-circle', 'text-red-500');
                emailFeedback.classList.remove('hidden');
                emailFeedback.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
                emailFeedback.innerHTML = '<i class="fas fa-times-circle"></i><span>' + message + '</span>';
                return;
            }

            if (state === 'taken') {
                emailInput.classList.add('border-red-400', 'ring-2', 'ring-red-200');
                emailIcon.classList.add('fa-times-circle', 'text-red-500');
                emailFeedback.classList.remove('hidden');
                emailFeedback.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-200');
                emailFeedback.innerHTML = '<i class="fas fa-times-circle"></i><span>' + message + '</span>';
                return;
            }

            if (state === 'available') {
                emailInput.classList.add('border-green-400', 'ring-2', 'ring-green-200');
                emailIcon.classList.add('fa-check-circle', 'text-green-500');
                emailFeedback.classList.remove('hidden');
                emailFeedback.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                emailFeedback.innerHTML = '<i class="fas fa-check-circle"></i><span>' + message + '</span>';
            }
        }

        let emailDebounceTimer = null;

        emailInput.addEventListener('input', function () {
            clearTimeout(emailDebounceTimer);
            const value = this.value.trim();

            if (value.length === 0) {
                setEmailState('empty');
                return;
            }

            if (!isValidEmailFormat(value)) {
                setEmailState('invalid_format', 'Please enter a valid email (e.g. user@example.com)');
                return;
            }

            setEmailState('checking', 'Checking availability...');

            emailDebounceTimer = setTimeout(function () {
                fetch('{{ route('auth.check.email') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: value })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'available') {
                        setEmailState('available', 'Email is available, you\'re good to go!');
                    } else if (data.status === 'taken') {
                        setEmailState('taken', 'This email is already registered. Try signing in instead.');
                    } else {
                        setEmailState('invalid_format', data.message || 'Invalid email format');
                    }
                })
                .catch(() => {
                    setEmailState('invalid_format', 'Could not verify email. Please try again.');
                });
            }, 600);
        });

        // Block form submission if email not confirmed available
        document.querySelector('form').addEventListener('submit', function (e) {
            const value = emailInput.value.trim();
            if (!isValidEmailFormat(value) || !emailInput.classList.contains('border-green-400')) {
                e.preventDefault();
                setEmailState('invalid_format', 'Please use a valid, available email before signing up.');
                emailInput.focus();

                if (typeof toastr !== 'undefined') {
                    toastr.error('Please fix the email field before submitting.', 'Invalid Email');
                }
            }
        });

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
            toastr.error("Please check the form fields and try again.");
        @endif
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }
    </style>
</body>

</html>