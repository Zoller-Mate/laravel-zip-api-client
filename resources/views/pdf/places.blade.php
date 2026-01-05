@extends('pdf')

@section('content')
    <table>
        <thead>
            <tr>
                <th colspan="4">{{ __('Városok') }}</th>
            </tr>
            <tr>
                <th>{{ __('Város') }}</th>
                <th>{{ __('Megye') }}</th>
                <th>{{ __('Irányítószám') }}</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $entity)
                @if($loop->iteration % 2 == 0)
                    <tr class="even">
                @else
                    <tr class="odd">
                @endif
                    <td>{{$entity->name}}</td>
                    <td>{{$entity->county->name ?? 'N/A'}}</td>
                    <td>
                        @if(isset($entity->postal_codes) && is_array($entity->postal_codes) && count($entity->postal_codes) > 0)
                            {{ $entity->postal_codes[0]->postal_code ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{$entity->id}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
