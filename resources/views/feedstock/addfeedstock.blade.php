@extends('layouts.app')

@section('pageTitle', 'Add a feedstock')

@section('content')

@if (isset($editFeedstock) && $editFeedstock)
<h1>Edit a feedstock</h1>
@else
<h1>Add a feedstock</h1>
@endif




<p class="error">* required</p>
<form method="post" enctype="multipart/form-data"
    @if (isset($editFeedstock) && $editFeedstock)
    action="{{ route('feedstock.update', ['feedstock'=> $id]) }}">
    @method('PUT')

    @else
    action="{{ route('feedstock.store') }}">
    @endif


@csrf

<div class="row-table-form">

<label for="name">Name:</label> <input id="name" type="text" name="name" required value="{{ old('name', $name) }}">
<span class="error">*
    @error('name')
    {{ $message }}<br>
    @enderror
</span>
<br><br>
</div>

<div class="row-table-form">
<label for="cost">Cost: </label> <input id="cost" type="number" step="any" min="0" name="cost" required value="{{ old('cost', $cost) }}">
<span class="error">*
    @error('cost')
    {{ $message }}<br>
    @enderror</span>
<br><br>
</div>

<div class="row-table-form">
<label for="stock">Stock quantity: </label> <input id="stock" type="number" min="0" name="stock" required value="{{ old('stock', $stock) }}">
<span class="error">*
    @error('stock')
    {{ $message }}<br>
    @enderror</span>
<br><br>
</div>




@if (isset($editFeedstock) && $editFeedstock)
    <input type="submit" name="submit" value="Edit feedstock">
    </form>

    @if(Auth::user()->isAdmin())

        <form style="margin-top:20px;" action="{{ route('feedstock.destroy', ['feedstock' => $id]) }}" method="post">
            @method('DELETE')
            @csrf
            <input class="btn btn-danger" type="submit" value="DELETE" />
        </form>
    @endif

@else
    <input type="submit" name="submit" value="Add feedstock">
    </form>

@endif




@endsection
