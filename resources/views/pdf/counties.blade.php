@extends('pdf')

@section('content')
    <table>
        <thead>
            <tr>
                <th colspan="2">{{ __('Megyék') }}</th>
            </tr>
            <tr>
                <th>#</th>
                <th>{{ __('Megnevezés') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $entity)
                @if($loop->iteration % 2 == 0)
                    <tr class="even">
                @else
                    <tr class="odd">
                @endif
                    <td>{{$entity->id}}</td>
                    <td>{{$entity->name}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
