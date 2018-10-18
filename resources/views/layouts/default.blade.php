<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Echo') - Echo</title>
    <link rel="stylesheet" href="{{ URL::asset('/css/app.css') }}">
  </head>
  <body>
    @include('layouts._header')

    <div class="container">
      <div class="col-md-offset-1 col-md-10">
        @include('shared._messages')
        @yield('content')
        @include('layouts._footer')
      </div>
    </div>


  </body>

  <script src="{{ URL::asset('/js/app.js') }}"></script>>
</html>