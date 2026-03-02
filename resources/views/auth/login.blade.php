@extends('layouts.guest')

{{-- タイトル(タブ名)を変えたくなければなくてもいい --}}
@section('title', 'Sign in')

{{-- テスト用 --}}
@php
    $error = false;
@endphp
{{--         --}}

@section('content')
  <main class="login-main">
    <div class="login-card">
      <h2 class="title">Sign in</h2>
      <div class="login-form">
        <form action="" method="">
          <div class="email-field">
              <label>Email</label>
              <input class="textbox" type="email" id="email" name="email" required>
          </div>
          <label>Password</label>
          <div class="password-field">
            <input
              class="textbox @if($error) is-invalid @endif"
              type="password" id="password" name="password" required
            >
            <button type="button" class="toggle-password" id="toggle-password">
                <img src="/images/icons/eye-slash.svg" alt="" id="toggle-password-icon">
            </button>
          </div>
          <input class="button" type="submit" value="Sign in">
        </form>
      </div>

      @if ($error)
        <p class="error-message" id="auth-error">The email address or password is incorrect.</p>
      @endif

      <a class="forgot-password" href="">Forgot your password?</a>
      <hr>
      <div class="sign-up">
        <span>Don’t have an account?</span>
        <a href="">Sign up</a>
      </div>    
        
  </main>
@endsection