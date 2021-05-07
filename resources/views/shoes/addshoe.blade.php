@extends('layouts.app')

@section('pageTitle', 'Add a shoe')

@section('content')

@if (isset($editShoe) && $editShoe)
<h1>Edit a shoe</h1>
@else
<h1>Add a shoe</h1>
@endif





<p class="error">* required</p>
<form method="post" enctype="multipart/form-data"
    @if (isset($editShoe) && $editShoe)
    action="{{ route('shoe.update', ['shoe'=> $id]) }}">
    @method('PUT')

    @else
    action="{{ route('shoe.store') }}">
    @endif


@csrf

<div class="row-table-form">

<label for="reference">Reference:</label> <input id="reference" type="text" name="reference" required
value="{{ old('reference', $reference) }}">
<span class="error">*
    @error('reference')
    {{ $message }}<br>
    @enderror
</span>
<br><br>
</div>

<div class="row-table-form">
<label for="barcode">Barcode: </label> <input id="barcode" type="text" maxlength="12" name="barcode" required value="{{ old('barcode', $barcode) }}">
<span class="error">*
    @error('barcode')
    {{ $message }}<br>
    @enderror</span>
<br><br>
</div>

<div class="row-table-form">
<label for="color">Color: </label> <input id="color" type="text" name="color" required value="{{ old('color', $color) }}">
<span class="error">*
    @error('color')
    {{ $message }}<br>
    @enderror</span>
<br><br>
</div>

<div class="row-table-form">
<label for="stock">Stock quantity: </label> <input id="stock" type="text" name="stock" required value="{{ old('stock', $stock) }}">
<span class="error">*
    @error('stock')
    {{ $message }}<br>
    @enderror</span>
<br><br>
</div>


<div class="row-table-form">

    @if (isset($editShoe) && $editShoe)

    <label for="feedstock">Feedstock (raw materials): </label>
    <div class="second-column">
        <select name="feedstock[]" multiple="multiple" id="feedstock" required>
            @foreach($feedstockFullList as $feedstockFull)
                <option
                    @foreach($feedstockList as $feedstock)
                        @if($feedstockFull->id == $feedstock->id)selected="selected"
                        @endif
                    @endforeach
                value="{{ $feedstockFull->id }}"> {{ $feedstockFull->name }}
                </option>
            @endforeach
        </select>
        <span class="error">*
            @error('feedstock')
            {{ $message }}<br>
            @enderror
        </span>
        <br><br>
    </div>

    </select>

    @else



    <label for="feedstock">Feedstock (raw materials): </label>
    <div class="second-column">
        <select name="feedstock[]" multiple="multiple" id="feedstock" required>
            <option selected disabled value="" style="display:none;">Select feedstock</option>
            @foreach($feedstockList as $feedstock)
                <option
                @if(  !empty( old('feedstock') )  )
                    @foreach( old('feedstock') as $oldFeedstock )
                        @if( $oldFeedstock == $feedstock->id ) selected="selected"
                        @endif
                    @endforeach
                @endif

                value="{{ $feedstock->id }}"> {{ $feedstock->name }} </option>
            @endforeach
        </select>
        <span class="error">*
            @error('feedstock')
            {{ $message }}<br>
            @enderror
        </span>
        <br><br>
    </div>

    @endif

    <a href="{{ route('feedstock.create') }}"> Create new feedstock?</a>
</div>



@if (isset($editShoe) && $editShoe)

    <input type="submit" name="submit" value="Edit shoe">
    </form>

    @if(Auth::user()->isAdmin())

            <form style="margin-top:20px;" action="{{ route('shoe.destroy', ['shoe' => $id]) }}" method="post">
                @method('DELETE')
                @csrf
                <input class="btn btn-danger" type="submit" value="DELETE SHOE" />
             </form>

    @endif

@else
    <input type="submit" name="submit" value="Add shoe">
    </form>
@endif





@endsection
