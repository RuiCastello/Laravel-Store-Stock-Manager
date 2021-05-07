@extends('layouts.app')

@section('pageTitle', 'Shoe details')

@section('content')

<h1>Shoe details</h1>






<div class="row-table-form" style="padding-top:50px;">
    <table style="width:90vw;">

    <thead>
        <tr>
            <td class="column1"></td>
            <td class="column2"></td>
        </tr>
    </thead>

    <tbody>

    @foreach ($listShoes as $shoeObj)

    <tr>
        <td>Reference</td>
        <td>{{ $shoeObj->reference }}</td>
    </tr>

    <tr>
        <td>Barcode</td>
        <td>{!!$barcodeImg!!} {{ $shoeObj->barcode }}</td>
    </tr>

    <tr>
        <td>Color</td>
        <td>{{ $shoeObj->color }}</td>
    </tr>

    <tr>
        <td>Shoe production cost</td>
        <td>{{ $shoeCost }} €</td>
    </tr>

    <tr>
        <td>Total shoe production cost(+20% labor)</td>
        <td>{{ $shoeCost + $shoeCost*0.2 }} €</td>
    </tr>

    <tr>
        <td>Stock</td>
        <td>{{ $shoeObj->stock }}</td>
    </tr>
    <tr>
        <td>Feedstock</td>
        <td>
            @foreach ($feedstockList as $feedstock)
            {{ $feedstock->name }} <br>
            @endforeach
        </td>
    </tr>

    @if (Auth::user()->isAdmin())
    <tr>
        <td>Edit details</td>
        <td><a href="{{ route('shoe.edit', ['shoe' => $shoeObj->id]) }}">EDIT</a></td>
    </tr>
    <tr>
        <td>Delete shoe record</td>
        <td>
            <form action="{{ route('shoe.destroy', ['shoe' => $shoeObj->id]) }}" method="post">
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
