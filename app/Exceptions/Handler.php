<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

//Adicionado para poder fazer a exceção genérica de quando não se encontra um determinao model na DB, isto está implementado no render() mais abaixo
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        //Inserido manualmente para tratar uma excepção especifica
        if ( $exception instanceof ModelNotFoundException && $request->is('api/*') ) {
			return response()->json(
				array(
					'status'  => 'error',
					'error'   => 'Entry for ' . str_replace(
						'App\\',
						'',
						$exception->getModel()
					) . ' not found',
					'message' => $exception->getMessage(),
				),
				404
			);
		}


        if ( $exception instanceof  MethodNotAllowedHttpException && $request->is('api/*') ) {
			return response()->json(
				array(
					'status'  => 'error',
					'error'   => 'Este método de requisição não é suportado por este URL, por favor use um URL adequado.',
					'message' => $exception->getMessage(),
				),
				404
			);
		}


        if ( $exception instanceof QueryException && $request->is('api/*') ) {
			return response()->json(
				array(
					'status'  => 'error',
					'error'   => 'Erro, por favor verifique todos os elementos do seu pedido, pelo menos um deles é inválido.',
					'message' => $exception->getMessage(),
				),
				404
			);
        }

        if ( $exception instanceof AuthenticationException && $request->is('api/*') ) {
			return response()->json(
				array(
					'status'  => 'error',
					'error'   => 'Erro, Problema com a autenticação, por favor faça login e tente novamente.',
					'message' => $exception->getMessage(),
				),
				404
			);
		}

        return parent::render($request, $exception);
    }
}
