 @extends('layouts.app')

 @section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container"  style="margin-top: 5rem">
      <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" id="login-tab" data-bs-toggle="tab" href="#tab-item-login"
            role="tab" aria-controls="tab-item-login" aria-selected="true">Login</a>
        </li>
      </ul>
      <div class="tab-content pt-2" id="login_register_tab_content">
        <div class="tab-pane fade show active" id="tab-item-login" role="tabpanel" aria-labelledby="login-tab">
          
          {{-- Form Error Message --}}
           @if ($errors->any())
           <div class="alert alert-danger">
           <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
          </ul>
         </div>
          @endif
          
          {{-- Login Form --}}
          <div class="login-form">
            <form method="POST" action="{{ route('login') }}" name="login-form" id="login-form" class="needs-validation" novalidate="">
                @csrf

                <div class="form-floating mb-3">
                <input class="form-control form-control_gray @error('email') is invalid @enderror" name="email" value="{{ old('email') }}" required="" autocomplete="email"
                  autofocus="">
                <label for="email">Email address *</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>                   
                @enderror
              </div>

              <div class="pb-3"></div>

              <div class="form-floating mb-3">
                <input id="password" type="password"                   
                 class="form-control form-control_gray @error('password') is invalid @enderror" name="password" required=""
                  autocomplete="current-password">
                <label for="customerPasswodInput">Password *</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>                   
                @enderror
              </div>

              <button class="btn btn-primary w-100 text-uppercase" onclick="event.preventDefault(); document.getElementById('login-form').submit();" type="submit">
                Log In
                </button>

              <div class="customer-option mt-4 text-center">
                <span class="text-secondary">No account yet?</span>
                <a href="{{ route('register') }}" class="btn-text js-show-register">Create Account</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  
 {{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginform">
        @csrf

        <!-- Email Address -->
       <div class="form-floating mb-3">
            <input class="form-control form-control_gray @error('email') is invalid @enderror" name="email" value="{{ old('email') }}" required="" autocomplete="email"
                autofocus="">
            <label for="email">Email address *</label>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>                   
            @enderror
         </div>

        <!-- Password -->
        <div class="form-floating mb-3">
                <input id="password" type="password"                   
                 class="form-control form-control_gray @error('password') is invalid @enderror" name="password" required=""
                  autocomplete="current-password">
                <label for="customerPasswodInput">Password *</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>                   
                @enderror
              </div>
        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <button class="btn btn-primary w-100 text-uppercase" onclick="event.preventDefault(); document.getElementById('loginform').submit();" type="submit">
            Log In
        </button>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout> --}}
@endsection
