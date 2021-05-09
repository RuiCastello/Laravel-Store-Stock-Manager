
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('pageTitle')</title>


  <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
  <style>
  .error {color: #FF0000;}
  </style>

  </head>
  <body>

  @yield('content')

  </body>
  </html>
