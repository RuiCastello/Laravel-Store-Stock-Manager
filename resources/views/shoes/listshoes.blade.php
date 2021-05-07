@extends('layouts.app')

@section('pageTitle', 'Shoe List')

@section('content')

<h1>Shoe List</h1>






<div class="row-table-form" style="padding-top:50px;">
    <table style="width:90vw;">
    <thead>
      <tr>
        <th>Reference</th>
        <th>Barcode</th>
        <th>Color</th>
        <th>Cost</th>
        <th>Stock</th>

        @if (Auth::user()->isAdmin())
        <th>Edit</th>
        @endif
    </tr>
    </thead>

    <tbody>

    @foreach ($listShoes as $shoeObj)

    <tr>
    <td><a href="{{ route('shoe.show', ['shoe' => $shoeObj->id]) }}">{{ $shoeObj->reference }}</a></td>
    <td>{!!$shoeObj->barcodeImg!!} {{ $shoeObj->barcode }}</td>
    <td>{{ $shoeObj->color }}</td>
    <td>{{ $shoeObj->cost() }} €</td>
    <td>{{ $shoeObj->stock }}</td>
    @if (Auth::user()->isAdmin())
    <td><a href="{{ route('shoe.edit', ['shoe' => $shoeObj->id]) }}">Edit</a></td>
    @endif
    </tr>

    @endforeach

    </tbody>
    </table>
</div>

</body>
</html>



@endsection
