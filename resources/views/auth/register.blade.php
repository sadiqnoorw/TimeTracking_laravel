<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" id="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="text-decoration-underline text-sm">
                {{ __('Already registered?') }}
            </a>
            <button type="submit" class="btn btn-primary ms-2">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>
