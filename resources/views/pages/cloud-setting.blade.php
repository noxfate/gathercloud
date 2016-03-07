<!-- Change to layout.layout-of-setting bar -->
@extends('layout.layout-of-index')
@section('content')
    <h1>Cloud Connection Setting</h1>
    E-mail: {{ $user->email }} <br>

    <table border="1">
        <tr>
            <td>Connection Name</td>
            <td>Connection Email</td>
            <td>Provider</td>
            <td>Last Updated</td>
        </tr>
        @foreach( $conn as $c )
        <tr>
            <td>{{ $c->connection_name }}</td>
            <td>{{ $c->connection_email }}</td>
            <td>{{ $c->provider  }}</td>
            <td>{{ $c->updated_at  }}</td>
        </tr>
        @endforeach
    </table>

@endsection