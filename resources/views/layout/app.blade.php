<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('asset/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/cropper.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        @yield('content')
    </div>
    <script src="{{ asset('asset/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('asset/axios.min.js')}}"></script>
    <script src="{{ asset('asset//cropper.min.js') }}"></script>
    <script src="{{ asset('asset/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
