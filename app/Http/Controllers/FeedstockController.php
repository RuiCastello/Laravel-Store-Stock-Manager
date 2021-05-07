<?php

namespace App\Http\Controllers;

use App\Feedstock;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FeedstockController extends Controller
{


       public function __construct(){

        $this->middleware('auth');

        $this->middleware('checkadmin', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        $listFeedstocks = Feedstock::all();
        return view('feedstock.listfeedstocks', ['listFeedstocks'=> $listFeedstocks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $name = "";
        $cost = "";
        $stock = "";

        $dataArray = [
            'name' => $name,
            'cost' =>  $cost,
            'stock' => $stock,
        ];

        return view('feedstock.addfeedstock', $dataArray);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'name' => 'required',
                'cost' => 'required',
                'stock' => 'required|numeric|min:0',
            ],
            [
                'name.required'=> 'A feedstock name is required',
                'cost.required'=> 'A cost value is required',
                'stock.required'=> 'A value for stock quantity is required (can be zero)',
                'stock.min'=> 'A value for stock quantity cannot be negative',
            ]
        );

        $newFeedstock = new Feedstock();
        $newFeedstock->name = $request->name;
        $newFeedstock->cost = $request->cost;
        $newFeedstock->stock = $request->stock;

        // FAZER RELATIONSHIP
        // $newFeedstock->department = $request->department;
        // $newFeedstock->first_name = $request->role;

        $newFeedstock->save();

        return redirect()->route('feedstock.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function show(Feedstock $feedstock)
    {

        $listFeedstocks = [$feedstock];
        return view('feedstock.displayfeedstock', [
            'listFeedstocks'=> $listFeedstocks,
            ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedstock $feedstock)
    {

        $dataArray = [
            'name' => $feedstock->name,
            'cost' => $feedstock->cost,
            'stock' => $feedstock->stock,
            'id' => $feedstock->id,
            'editFeedstock' => true,
        ];

        return view('feedstock.addfeedstock', $dataArray);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedstock $feedstock)
    {
        $validatedData = $request->validate(
            [
                'name' => 'required',
                'cost' => 'required',
                'stock' => 'required|numeric|min:0',
            ],
            [
                'name.required'=> 'A feedstock name is required',
                'cost.required'=> 'A cost value is required',
                'stock.required'=> 'A value for stock quantity is required (can be zero)',
                'stock.min'=> 'A value for stock quantity cannot be negative',
            ]
        );

        $feedstock->name = $request->name;
        $feedstock->cost = $request->cost;
        $feedstock->stock = $request->stock;


        $feedstock->save();

        // return redirect()->route('feedstock.edit', $feedstock->id);
        return redirect()->route('feedstock.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedstock $feedstock)
    {


        try {
            $feedstock->delete();
        }
        catch(QueryException $ex){
            // dd($ex->getMessage());

            return Redirect()->back()->with(['errorMessage' => 'A referência a este material não pode ser removida (está a ser utilizada). <br> Em alternativa, poderá reduzir o stock a zero.']);

        }

        return redirect()->route('feedstock.index');
    }
}
