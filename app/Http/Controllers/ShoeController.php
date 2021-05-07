<?php

namespace App\Http\Controllers;

use App\Shoe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class ShoeController extends Controller
{


    public function __construct(){

        $this->middleware('auth');
        $this->middleware('checkadmin', ['except' => ['index', 'show']]);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $listShoes = Shoe::all();

        foreach($listShoes as $shoe){
            try{
                $shoe->barcodeImg = \DNS1D::getBarcodeHTML($shoe->barcode, 'EAN13');
                }
                catch(\Exception $e){
                    $shoe->barcodeImg = "";
                }
            }

        return view('shoes.listshoes', ['listShoes'=> $listShoes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reference = "";
        $barcode = "";
        $color = "";
        $stock = "";
        $feedstockList = \App\Feedstock::all();

         //Se a lista de materiais não tiver pelo menos um material, então dar erro.
         if($feedstockList->count() < 1) {
            return Redirect()->back()->with(['errorMessage' => "Erro, tem de escolher pelo menos um material. Caso não exista nenhum material, então <a href=\"".route('feedstock.create')."\"> crie um primeiro.</a>"]);
        }

        $dataArray = [
            'reference' => $reference,
            'barcode' =>  $barcode,
            'color' => $color,
            'stock' => $stock,
            'feedstockList' => $feedstockList,
        ];

        return view('shoes.addshoe', $dataArray);
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
                'reference' => 'required',
                'barcode' => 'required|digits:12',
                'color' => 'required',
                'stock' => 'required|numeric|min:0',
                'feedstock' => 'required',
            ],
            [
                'reference.required'=> 'A shoe reference is required',
                'barcode.required'=> 'A barcode is required',
                'color.required'=> 'A color is required',
                'stock.required'=> 'A value for stock quantity is required (can be zero)',
                'feedstock.required'=> 'A base raw material (feedstock) must be defined for creating a new shoe reference.',
                'stock.min'=> 'A value for stock quantity cannot be negative',
            ]
        );


        // guarda os valores do input na sessão
        $request->flash();

        $newFeedstocks = \App\Feedstock::find($request->feedstock)->sortBy("stock");

        // Se o utilizador não tiver escolhido pelo menos um material existente na base de dados, então dar erro.
        if($newFeedstocks->count() < 1) {
            return Redirect()->back()->with(['errorMessage' => "Erro, tem de escolher pelo menos um material. Caso não exista nenhum material, então crie um primeiro."]);
        }

        // Verificar se há stock suficiente de todas as matérias primas escolhidas
        foreach ($newFeedstocks as $eachFeedstock){
            if ($eachFeedstock->stock < $request->stock){
                $limiteMax = $eachFeedstock->stock;
                return Redirect()->back()->with(['errorMessage' => "Erro, o stock de {$eachFeedstock->name} é  {$limiteMax} e o seu pedido foi de $request->stock."]);
            }
        }


        $newShoe = new Shoe();
        $newShoe->reference = $request->reference;
        $newShoe->barcode = $request->barcode;
        $newShoe->color = $request->color;
        $newShoe->stock = $request->stock;

        $newShoe->save();


        //Este sync() faz a sincronização automática dos registos de many to many na pivot table (tabela de relação muitos para muitos). Sync() Aceita um array de IDs ou uma Collection de laravel.
        $newShoe->feedstocks()->sync($newFeedstocks);

        foreach ($newFeedstocks as $eachFeedstock){
            $eachFeedstock->stock -= $newShoe->stock;
            $eachFeedstock->save();
        }

        //Este apenas grava um novo registo na pivot table de many to many.
        // $newFeedstock = \App\Feedstock::find($request->feedstock);
        // $newShoe->feedstocks()->save($newFeedstock);


        //em alternativa ao flash() feito mais acima podemos fazer simplesmente withInput() diretamente no redirect, que faz exactamente a mesma coisa, faz flash do input para a sessão. É preciso é ter atenção que fizemos mais redirects mais acima, ou adicionamos withInput() a todos, ou mais vale fazer simplesmente o flash() uma vez acima de todos os redirects
        return redirect()->route('shoe.index')->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function show(Shoe $shoe)
    {
        $errorMessage = null;
        $barcodeImg ="";

        $feedstocks = $shoe->feedstocks()->orderBy('name')->get();

        $shoeCost = 0;
        foreach($feedstocks as $feedstock){
            $shoeCost += $feedstock->cost;
        }

        try{
        $barcodeImg = \DNS1D::getBarcodeHTML($shoe->barcode, 'EAN13');
        }
        catch(\Exception $e){
            $barcodeImg = $errorMessage = "!O código EAN13 não é válido, por favor altere o número!";
        }

        $feedstockList = $shoe->feedstocks()->orderBy('name')->get();
        $listShoes = [$shoe];
        return view('shoes.displayshoe', [
            'listShoes'=> $listShoes,
            'feedstockList' => $feedstockList,
            'shoeCost' => $shoeCost,
            'barcodeImg' => $barcodeImg,
            'errorMessage' => $errorMessage,
            ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function edit(Shoe $shoe)
    {
        //

        $reference = $shoe->reference;
        $barcode = $shoe->barcode;
        $color = $shoe->color;
        $stock = $shoe->stock;
        $id = $shoe->id;
        $feedstockFullList = \App\Feedstock::all();
        $feedstockList = $shoe->feedstocks()->orderBy('name')->get();


        $dataArray = [
            'reference' => $reference,
            'barcode' =>  $barcode,
            'color' => $color,
            'stock' => $stock,
            'id' => $id,
            'editShoe' => true,
            'feedstockList' => $feedstockList,
            'feedstockFullList' => $feedstockFullList,
        ];

        return view('shoes.addshoe', $dataArray);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shoe $shoe)
    {

        $validatedData = $request->validate(
            [
                'reference' => 'required',
                'barcode' => 'required|digits:12',
                'color' => 'required',
                'stock' => 'required|numeric|min:0',
            ],
            [
                'reference.required'=> 'A shoe reference is required',
                'barcode.required'=> 'A barcode is required',
                'color.required'=> 'A color is required',
                'stock.required'=> 'A value for stock quantity is required (can be zero)',
                'stock.min'=> 'A value for stock quantity cannot be negative',
            ]
        );



        $newFeedstocks = \App\Feedstock::find($request->feedstock)->sortBy("stock");


         //Se o utilizador não tiver escolhido pelo menos um material existente na base de dados, então dar erro.
         if($newFeedstocks->count() < 1) {
            return Redirect()->back()->with(['errorMessage' => "Erro, tem de escolher pelo menos um material. Caso não exista nenhum material, então crie um primeiro."]);
        }

        // Verificar se há stock suficiente de todas as mátérias primas escolhidas
        $aumentoStock = $request->stock - $shoe->stock;
        foreach ($newFeedstocks as $eachFeedstock){

            if ($aumentoStock > 0){

                if ($eachFeedstock->stock < $aumentoStock){

                    $limiteMax = $shoe->stock + $eachFeedstock->stock;
                    return Redirect()->back()->with(['errorMessage' => "Só pode aumentar o stock dos sapatos até {$limiteMax}."]);
                }
            }
        }


        $shoe->reference = $request->reference;
        $shoe->barcode = $request->barcode;
        $shoe->color = $request->color;
        $shoe->stock = $request->stock;

        $shoe->save();


        $shoe->feedstocks()->sync($newFeedstocks);

        // Se o pedido for de um aumento de stock de sapatos, então:
        // Subtrair a quantidade aumentada no stock de sapatos, do stock dos materiais respectivos.
        if ($aumentoStock > 0){
            foreach ($newFeedstocks as $eachFeedstock){
                $eachFeedstock->stock -= $aumentoStock;
                $eachFeedstock->save();
            }
        }

        return redirect()->route('shoe.index');
        // return redirect()->route('shoe.edit', $shoe->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shoe $shoe)
    {
        $shoe->delete();
        return redirect()->route('shoe.index');
    }
}
