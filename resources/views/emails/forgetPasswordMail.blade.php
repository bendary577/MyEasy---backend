<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <h2>Reset Password Email</h2>
    <p>{{ $content }}</p>
    <a href="{{route('request.admin.activation', ['email' => $email] )}}" class="btn btn-primary">Activate Account</a>
  </body>
</html>