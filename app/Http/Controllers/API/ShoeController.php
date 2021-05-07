<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Shoe;
use Illuminate\Http\Request;

class ShoeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api');
        $this->middleware('checkadminapi', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $listShoes = Shoe::all();
        $listShoes = Shoe::paginate(5);

        $listShoes->setCollection( $listShoes->getCollection()->makeVisible( 'id' ) );

        $data  = [ 'data' => $listShoes ];

        //todos estas formas de fazer return parecem ter o mesmo resultado, devolvem a coleção de objectos já em json. Magia laravel?
        // return response($listShoes);
        // return $listShoes;
        // return response()->json($listShoes) ;

        return response()->json($data, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationMatchArray= [
            'reference' => 'required',
            'barcode' => 'required|digits:12',
            'color' => 'required',
            'stock' => 'required|numeric|min:0',
            'feedstock' => 'required',
        ];

        $customErrorMessages = [
            'reference.required'=> 'A shoe reference is required',
            'barcode.required'=> 'A barcode is required',
            'color.required'=> 'A color is required',
            'stock.required'=> 'A value for stock quantity is required (can be zero)',
            'feedstock.required'=> 'A base raw material (feedstock) must be defined for creating a new shoe reference. Visit the Feedstock API URL /api/feedstocks to check available options and/or create new ones.',
            'stock.min'=> 'A value for stock quantity cannot be negative',
        ];

        $validated = \Validator::make(
            $request->all(),
            $validationMatchArray,
            $customErrorMessages
        );

        if (!is_array($request->feedstock)) $request->feedstock = [$request->feedstock];
         $newFeedstocks = \App\Feedstock::find($request->feedstock)->sortBy("stock");


        // Se o utilizador não tiver escolhido pelo menos um material existente na base de dados, então dar erro.
        if($newFeedstocks->count() < 1) {
            $erroMsg = "Erro, tem de escolher pelo menos um material válido. Caso não exista nenhum material, então crie um primeiro.";
        }

        // Verificar se há stock suficiente de todas as matérias primas escolhidas
        foreach ($newFeedstocks as $eachFeedstock){
            if ($eachFeedstock->stock < $request->stock){
                $limiteMax = $eachFeedstock->stock;
                $erroMsg = "Erro, o stock de {$eachFeedstock->name} é {$limiteMax} e o seu pedido foi de $request->stock.";
            }
        }


        if ( $validated->fails() ) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validated->errors(),
                    'message' => 'Validation Errors',
                    'data' => false,
                ]
            );
        };


        if (!empty($erroMsg)) {
            return response()->json(
                array(
                    'status'  => 'Failed',
                    'message' => $erroMsg,
                    'data'    => null,
                )
            );
        }

        $newShoe = Shoe::create( $request->all() );

         //Este sync() faz a sincronização automática dos registos de many to many na pivot table (tabela de relação muitos para muitos). Sync() Aceita um array de IDs ou uma Collection de laravel.
         $newShoe->feedstocks()->sync($newFeedstocks);

         foreach ($newFeedstocks as $eachFeedstock){
             $eachFeedstock->stock -= $newShoe->stock;
             $eachFeedstock->save();
         }

        return response()->json(
			array(
				'status'  => 'Success',
				'message' => 'New shoe inserted -- ' . $newShoe->id,
				'data'    => $newShoe,
			)
		);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function show(Shoe $shoe)
    {
        if ( !is_array($shoe->getFeedstockIds()) ) {
            $feedstockArray = [$shoe->getFeedstockIds()];
        }else{
            $feedstockArray = $shoe->getFeedstockIds();
        }
        // dd($feedstockArray);
        $feedstockList = \App\Feedstock::find($feedstockArray)->sortBy("stock")->pluck('name');

        // $feedstockList = $shoe->feedstocks()->orderBy('name')->get();
        $shoe-> feedstock = $feedstockList;

        $data = [
                'status'  => 'success',
                'message' => '',
                'data' => $shoe
                ];

        return response()->json($data);
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
        //
            $validationMatchArray= [
                'reference' => 'required',
                'barcode' => 'required|digits:12',
                'color' => 'required',
                'stock' => 'required|numeric|min:0',
                'feedstock' => 'required',
            ];

            $customErrorMessages = [
                'reference.required'=> 'A shoe reference is required',
                'barcode.required'=> 'A barcode is required',
                'color.required'=> 'A color is required',
                'stock.required'=> 'A value for stock quantity is required (can be zero)',
                'feedstock.required'=> 'A base raw material (feedstock) must be defined for creating a new shoe reference.',
                'stock.min'=> 'A value for stock quantity cannot be negative',
            ];

		$finalValidationArray = [];

        // Validação campo a campo ( Se campo existir, então aplicar regras de validação desse campo )
		foreach ( $request->all() as $key => $value ) {
            foreach ($validationMatchArray as $keyMatch => $valueMatch) {
                if ($key == $keyMatch) $finalValidationArray[$key] = $valueMatch;
            }
		}

		// validação parte 2
		    $validated = \Validator::make(
                $request->all(),
                $finalValidationArray,
                $customErrorMessages
		    );

    if ( !empty($request->feedstock) )
        {
            if ( !is_array($request->feedstock) ) $request->feedstock = [$request->feedstock];
            $feedstockArray = $request->feedstock;
        }
        else{

            if ( !is_array($shoe->getFeedstockIds()) ) {
                $feedstockArray = [$shoe->getFeedstockIds()];
            }else{
                $feedstockArray = $shoe->getFeedstockIds();
            }
        }
            $newFeedstocks = \App\Feedstock::find($feedstockArray)->sortBy("stock");


           // Se o utilizador não tiver escolhido pelo menos um material existente na base de dados, então dar erro.
           if($newFeedstocks->count() < 1) {
               $erroMsg = "Erro, tem de escolher pelo menos um material válido. Caso não exista nenhum material, então crie um primeiro.";
           }



           // Verificar se há stock suficiente de todas as matérias primas escolhidas

           if( !empty($request->stock) ) $aumentoStock = $request->stock - $shoe->stock;

           foreach ($newFeedstocks as $eachFeedstock){

                if (isset($aumentoStock) && $aumentoStock > 0){

                    if ($eachFeedstock->stock < $aumentoStock){

                        $limiteMax = $shoe->stock + $eachFeedstock->stock;
                        $erroMsg = "Só pode aumentar o stock dos sapatos até {$limiteMax}.";
                    }
                }
           }




		if ( $validated->fails() ) {
			return response()->json(
				array(
					'status'  => 'error',
					'errors'  => $validated->errors(),
					'message' => 'Validation errors',
					'data'    => false,
				)
			);
        }

        if (!empty($erroMsg)) {
            return response()->json(
                array(
                    'status'  => 'Failed',
                    'message' => $erroMsg,
                    'data'    => null,
                )
            );
        }

         $shoe->update( $request->all() );


            //Este sync() faz a sincronização automática dos registos de many to many na pivot table (tabela de relação muitos para muitos). Sync() Aceita um array de IDs ou uma Collection de laravel.
            $shoe->feedstocks()->sync($newFeedstocks);


            if (isset($aumentoStock) && $aumentoStock > 0){
                foreach ($newFeedstocks as $eachFeedstock){
                    $eachFeedstock->stock -= $aumentoStock;
                    $eachFeedstock->save();
                }
            }


        return response()->json($shoe);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shoe  $shoe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shoe $shoe)
    {
        //
        $shoe->delete();
        $data = [
            'msg' => "O item foi removido com sucesso.",
            'data' => $shoe,
        ];

        //à partida aqui a variável $data nem será enviada, pois como estamos a dizer que o erro é 204 (content not found), ele não envia sequer content, mas para testar vamos ver o que acontece.
        return response()->json($data, 204 );
    }
}
