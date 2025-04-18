<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
          <meta http-equiv="X-UA-Compatible" content="ie=edge">
          <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>

        @vite(['resources/js/app.js', 'resources/css/app.css'])
    </head>
	<body>
		<div id="app">
		</div>
	</body>
</html>
<script>
    window.Laravel = {
        csrfToken: "{{ csrf_token() }}"
    };
</script>
