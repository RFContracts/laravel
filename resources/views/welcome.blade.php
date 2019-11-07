<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <body>
<ul>
    @foreach($tasks as $task)
        <a href="tasks/{{$task->id}}"><li>{{$task->name}}</li></a>

        @endforeach
</ul>
    </body>
</html>
