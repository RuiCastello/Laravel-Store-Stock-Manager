@extends('layouts.app')

@section('pageTitle', 'Feedstock List')

@section('content')

<h1>Feedstock List</h1>





<div class="row-table-form" style="padding-top:50px;">
    <table style="width:90vw;">
    <thead>
      <tr>
        <th>Name</th>
        <th>Cost</th>
        <th>Stock</th>
        <th>Edit</th>
      </tr>
    </thead>

    <tbody>

    @foreach ($listFeedstocks as $feedstockObj)

    <tr>
    <td><a href="{{ route('feedstock.show', ['feedstock' => $feedstockObj->id]) }}">{{ $feedstockObj->name }}</a></td>
    <td>{{ $feedstockObj->cost }} €</td>
    <td>{{ $feedstockObj->stock }}</td>
    <td><a href="{{ route('feedstock.edit', ['feedstock' => $feedstockObj->id]) }}">Edit</a></td>
    </tr>

    @endforeach

    </tbody>
    </table>
</div>

</body>
</html>



@endsection
