@extends('layout.layout')
@section('welcome_page_title')
Get Started here
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/signup.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <section class="home">
  <div class="main_content_wrapper" style="width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
    <div class="form_container" style="position:relative;overflow:hidden;">
      <!-- Login Form -->
      <div class="form login_form">

    <form action="{{ route('get_started.store') }}" method="post" style="width:100%;">
        @csrf
      <h3 style="text-align:center;margin-bottom:18px;">Input a valid E-mail</h3>
      <div class="input_box">
      <input type="email" name="email" placeholder="Enter your email" required />
            <i class="uil uil-envelope-alt email"></i>
          </div>
          <button class="button" id="proceedEmailBtn">Proceed</button>

          <div class="login_signup">A verification link will be sent to your E-mail, click the link and continue the sign up process</div>
        </form>
      </div>
    </div>
  </div>
    </section>

<script>
    // Display success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true,
        });
    @endif

    // Display error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK'
        });
    @endif

    // Display validation errors
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection
