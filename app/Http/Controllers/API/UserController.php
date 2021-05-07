<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {

	public function __construct() {
        $this->middleware('auth:api', ['except' => ['store']]);
        $this->middleware('checkadminapi', ['except' => ['store']]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return response()->json( User::all(), 201 );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {

		$validated = \Validator::make(
			$request->all(),
			array(
				'name'     => 'required',
				'email'    => 'required|email|unique:users',
                'password' => 'required',
                'role'     => 'required',
            ),
            [
                'role.required' => 'Por favor selecione um role. O role pode ser user ou admin.',
            ]
		);

		if ( $validated->fails() ) {
			return response()->json(
				array(
					'status'  => 'error',
					'errors'  => $validated->errors(),
					'message' => 'Validations erros',
					'data'    => false,
				)
			);
		}

		$user = new User();

		$user->name     = $request->name;
		$user->email    = $request->email;
		$user->role     = $request->role;
		$user->password = Hash::make( $request->password );

		$user->save();

		return response()->json(
			array(
				'status'  => 'success',
				'message' => 'User inserted -- ' . $user->id,
				'data'    => $user,
			)
		);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  \App\User $user
	 * @return \Illuminate\Http\Response
	 */
	public function show( User $user ) {
		return response()->json(
			array(
				'status'  => 'success',
				'message' => '',
				'data'    => array(
					'id'   => $user->id,
					'name' => $user->name,
				),
			)
		);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\User                $user
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, User $user ) {

		$validations = array();
		foreach ( $request->all() as $key => $value ) {
			switch ( $key ) {
				case 'name':
					$validations[ $key ] = 'required';
					break;
				case 'email':
					$validations[ $key ] = 'required|email|unique:users';
					break;
				case 'password':
					$validations[ $key ] = 'required';
					break;
				case 'role':
					$validations[ $key ] = 'required';
					break;
			}
		}

		$validated = \Validator::make(
			$request->all(),
			$validations
		);

		if ( $validated->fails() ) {
			return response()->json(
				array(
					'status'  => 'error',
					'errors'  => $validated->errors(),
					'message' => 'Validations erros',
					'data'    => false,
				)
			);
		}

		foreach ( $request->all() as $key => $value ) {
			if ( 'password' === $key ) {
				$user->$key = Hash::make( $value );
			} else {
				$user->$key = $value;
			}
		}
		$user->save();

		return response()->json(
			array(
				'status'  => 'success',
				'message' => 'User updated -- ' . $user->id,
				'data'    => $user,
			)
		);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\User $user
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( User $user ) {
		$user->delete();

		return response()->json( null, 204 );
	}
}
