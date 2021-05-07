<?php

namespace App\Http\Controllers\api;

use App\Feedstock;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedstockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('auth:api');
        $this->middleware('checkadminapi', ['only' => ['destroy']]);

    }

    public function index()
    {
        $list = Feedstock::paginate(5);

        //Esta é a forma de podermos usar o makeVisible no resultado do paginate, pois o makeVisible só funciona para colecoes ou models, como o paginate() devolve algo diferente, um LengthAwarePaginator, temos de pegar apenas na collection fazer o makevisible e depois voltar a integrar a collection no objecto original
        $list->setCollection( $list->getCollection()->makeVisible( 'id' ) );

        $data  = [ 'data' => $list ];
        return response()->json( $data, 201 );
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
            'name' => 'required',
            'cost' => 'required',
            'stock' => 'required|numeric|min:0',
        ];

        $customErrorMessages = [
            'name.required'=> 'A feedstock name is required',
            'cost.required'=> 'A cost value is required',
            'stock.required'=> 'A value for stock quantity is required (can be zero)',
            'stock.min'=> 'A value for stock quantity cannot be negative',
        ];

        $validated = \Validator::make(
            $request->all(),
            $validationMatchArray,
            $customErrorMessages
        );

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

        // firstOrCreate() dá imenso jeito, mas tem um senão, como primeiro faz uma query para ver se já existe um record, essa query pode dar erro quando se usa um mass assignment, pois o utilizador pode acrescentar campos que não existem na DB. Enquanto que no create() isso já não dá erro, porque o mass assignment é limitado pela variável $fillable no model, que limita as colunas que serão atualizadas e portanto previne tentativas maliciosas de alterar uma query por parte dos users.
        // $newFeedstock = Feedstock::firstOrCreate( $request->all() );

        $newFeedstock = Feedstock::create( $request->all() );

        return response()->json(
			array(
				'status'  => 'Success',
				'message' => 'New feedstock has been added -- ' . $newFeedstock->id,
				'data'    => $newFeedstock,
			)
		);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function show(Feedstock $feedstock)
    {
        //
        $data = [
            'status'  => 'success',
            'message' => '',
            'data' => $feedstock
            ];

        return response()->json($data);
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
        $validationMatchArray= [
            'name' => 'required',
            'cost' => 'required',
            'stock' => 'required|numeric|min:0',
        ];

        $customErrorMessages = [
            'name.required'=> 'A feedstock name is required',
            'cost.required'=> 'A cost value is required',
            'stock.required'=> 'A value for stock quantity is required (can be zero)',
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

    $feedstock->update( $request->all() );

    return response()->json($feedstock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feedstock  $feedstock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedstock $feedstock)
    {
        $feedstock->delete();

        // Como estamos a dizer que o erro é 204 (content not found), metemos o conteúdo como null, pois não é suposto haver conteúdo.
        return response()->json( null, 204 );
    }
}
