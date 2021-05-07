<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Throwable;

class CreateDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the main database for this project to work. \nPlease use this command before running any migrations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        //Temos de anular o nome da base de dados para poder executar um DB:statement() sem dar erro, depois devolvemos o valor correto após criação da DB.
        config(["database.connections.mysql.database" => null]);
        $newDatabase = "exelaravel";

        $queryCheckDB = " SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$newDatabase' ";

        try{
            $db = DB::select($queryCheckDB, [$newDatabase]);
            if (empty($db)) {
                $mensagem = "Base de dados '$newDatabase' criada com sucesso!";
            } else {
                $mensagem = "Base de dados '$newDatabase' já existe.";
            }
        }
        catch (\Exception $e) {
            $mensagem = "Base de dados '$newDatabase' já existe.";
            // throw $e;
        }






        $query = "CREATE DATABASE IF NOT EXISTS $newDatabase";

        if ( DB::statement($query) ) {

            $this->info($mensagem);
            config(["database.connections.mysql.database" => $newDatabase]);
            return 1;

        }
        else {
            $mensagem = "Algo correu mal, por favor tente novamente.";
            $this->info($mensagem);
            return 0;
        }
    }
}
