<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="UPTM University">
    <title>UPTM Academic AI Assistant Tools - Reset Password</title>
    <link rel="shortcut icon" href="{{ asset('upload/uptm.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    
    <style>
        /* Modal animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-overlay {
            animation: fadeIn 0.3s ease-out;
        }
        
        .modal-content {
            animation: slideUp 0.3s ease-out;
        }

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

    <div class="relative z-10 w-full max-w-xs sm:max-w-sm md:max-w-md mx-auto my-auto">
        <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 text-center border border-gray-100">
            <div class="mb-4">
                <img class="w-40 h-auto mx-auto" src="{{ asset('upload/uptm.png') }}" alt="UPTM University Logo">
            </div>

            <div class="mb-5">
                <h1 class="text-xl md:text-2xl font-extrabold text-[#1e40af] mb-1">Reset Password</h1>
                <p class="text-[#64748b] text-xs md:text-sm font-medium max-w-xs mx-auto mt-2">
                    Enter your email and new password below.
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 text-left mb-1">Email Address</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all
                               @error('email') border-red-500 @enderror" 
                            type="email" 
                            id="email" 
                            name="email"
                            value="{{ old('email', $request->email) }}"
                            placeholder="Enter your email address"
                            required
                            autofocus
                            autocomplete="username" />
                    </div>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1 text-left">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                        <button type="button" 
                            onclick="openRequirementsModal()" 
                            class="text-xs text-[#1e40af] hover:text-[#1d4ed8] font-semibold flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            Requirements
                        </button>
                    </div>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10
                               @error('password') border-red-500 @enderror" 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Enter a new password"
                            oninput="validatePassword()"
                            required 
                            autocomplete="new-password" />
                        <a href="#" class="absolute right-0 top-1/2 -translate-y-1/2 pr-3 text-[#94a3b8] hover:text-[#1e40af] transition-colors"
                            title="Toggle show/hide password" onclick="togglePasswordVisibility(event, 'password', 'eye-icon-pass')">
                            <i class="fas fa-eye-slash" id="eye-icon-pass"></i>
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
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 text-left mb-1">Confirm New Password</label>
                    <div class="relative">
                        <input class="block w-full py-2 px-3 rounded-lg border border-[#e2e8f0] focus:outline-none focus:ring-2 focus:ring-[#60a5fa] focus:border-[#60a5fa] transition-all pr-10
                               @error('password_confirmation') border-red-500 @enderror" 
                            type="password" 
                            id="password_confirmation"
                            name="password_confirmation" 
                            placeholder="Confirm the new password"
                            oninput="validatePasswordMatch()"
                            required 
                            autocomplete="new-password" />
                        <a href="#" class="absolute right-0 top-1/2 -translate-y-1/2 pr-3 text-[#94a3b8] hover:text-[#1e40af] transition-colors"
                            title="Toggle show/hide password" onclick="togglePasswordVisibility(event, 'password_confirmation', 'eye-icon-confirm')">
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

                <div class="flex items-center justify-center pt-2">
                    <button class="w-full bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg shadow-md hover:bg-[#1d4ed8] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8] focus:ring-offset-2 transition-colors" type="submit">
                        <i class="fas fa-sync-alt mr-2"></i> Reset Password
                    </button>
                </div>
            </form>
            
            <div class="mt-5 pt-4 border-t border-gray-200">
                <a href="{{ route('login') }}" class="inline-block border border-gray-300 text-gray-700 py-1.5 px-4 rounded-lg text-xs font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Login
                </a>
            </div>

            <div class="mt-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} UPTM University. All rights reserved.
            </div>
        </div>
    </div>

    <!-- Password Requirements Modal -->
    <div id="requirementsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Overlay -->
        <div class="modal-overlay fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeRequirementsModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-100">
                <!-- Close Button -->
                <button onclick="closeRequirementsModal()" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <!-- Modal Header -->
                <div class="mb-5">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-lock text-2xl text-[#1e40af]"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 text-center">Password Requirements</h2>
                    <p class="text-sm text-gray-500 text-center mt-1">Your password must meet the following criteria</p>
                </div>

                <!-- Requirements List -->
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

                <!-- Example -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-gray-600 mb-1"><strong>Example of a strong password:</strong></p>
                    <code class="text-sm font-mono text-[#1e40af]">MyP@ssw0rd2024!</code>
                </div>

                <!-- Close Button -->
                <button onclick="closeRequirementsModal()" 
                        class="w-full mt-5 bg-[#1e40af] text-white py-2.5 font-semibold rounded-lg hover:bg-[#1d4ed8] transition-colors">
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Password visibility toggle function
        function togglePasswordVisibility(event, inputId, iconId) {
            event.preventDefault();
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
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
        function openRequirementsModal() {
            document.getElementById('requirementsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeRequirementsModal() {
            document.getElementById('requirementsModal').classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRequirementsModal();
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
        
        @if (session('status'))
            toastr.success("{{ session('status') }}");
        @endif
    </script>
</body>

</html>