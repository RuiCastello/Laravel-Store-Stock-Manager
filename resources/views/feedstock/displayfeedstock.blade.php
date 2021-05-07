@extends('layouts.app')

@section('pageTitle', 'Feedstock details')

@section('content')

<h1>Feedstock details</h1>






<div class="row-table-form" style="padding-top:50px;">
    <table style="width:90vw;">

    <thead>
        <tr>
            <td class="column1"></td>
            <td class="column2"></td>
        </tr>
    </thead>

    <tbody>

    @foreach ($listFeedstocks as $feedstockObj)

    <tr>
        <td>Name</td>
        <td>{{ $feedstockObj->name }}</td>
    </tr>

    <tr>
        <td>Cost</td>
        <td>{{ $feedstockObj->cost }} €</td>
    </tr>

    <tr>
        <td>Stock</td>
        <td>{{ $feedstockObj->stock }}</td>
    </tr>

    <tr>
        <td>Edit details</td>
        <td><a href="{{ route('feedstock.edit', ['feedstock' => $feedstockObj->id]) }}">EDIT</a></td>
    </tr>
    @if(Auth::user()->isAdmin())
    <tr>
        <td>Delete feedstock record</td>
        <td>
            <form action="{{ route('feedstock.destroy', ['feedstock' => $feedstockObj->id]) }}" method="post">
                @method('DELETE')
                @csrf
                <input class="btn btn-danger" type="submit" value="DELETE" />
             </form>
        </td>
    </tr>
    @endif

    @endforeach

    </tbody>
    </table>
</div>

</body>
</html>



@endsection
