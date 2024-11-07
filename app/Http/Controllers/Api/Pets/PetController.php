<?php

namespace App\Http\Controllers\Api\Pets;

use Exception;
use App\Models\Pets\Pet;
use Illuminate\Http\Request;
use App\Http\Traits\AppTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\Archives\ArchivesRepository;
use Illuminate\Database\QueryException;
use App\Repositories\Eloquent\Pet\PetRepository;
use Illuminate\Validation\ValidationException;

class PetController extends Controller
{
    use AppTrait;

    public $repository, $table, $archives_repository;
    public function __construct(PetRepository $repository, ArchivesRepository $archives_repository)
    {
        $this->repository = $repository;
        $this->archives_repository = $archives_repository;
        $this->table = new Pet();
    }

    public function getContent()
    {
        return $this->table
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'breed',
                'age',
                'size',
                'pedigree',
                'bio',
                'description'
            ]);
    }

    public function index()
    {
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        try {
            $content = $this->getContent();

            return response()->json($content, 200, $header, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {

            Log::error("Erro ao buscar pets: {$e->getMessage()}");

            return response()->json([
                "type" => "about:blank",
                "title" => "Falha ao buscar pets",
                "status" => "404",
                "detail" => "Nenhum pet encontrado nessa listagem"
            ], 404, $header, JSON_UNESCAPED_UNICODE);
        }
    }

    public function store(Request $request)
    {
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        try {
            $pet = $this->repository->upInsert($request);

            if ($pet) {
                if ($request->hasFile('images')) {
                    $this->archives_repository->upInsert($request->file('images'), $pet->id);                    
                }

                return response()->json([
                    "type" => "about:blank",
                    "title" => "Pet cadastrado com sucesso",
                    "status" => "200",
                    "detail" => "Seu pet foi cadastrado com sucesso"
                ], 200);
            }
        } catch (ValidationException $e) {

            return response()->json([
                "type" => "about:blank",
                "title" => "Erro de validação nos dados fornecidos",
                "status" => "422",
                "detail" => "Os dados não foram preenchidos corretamente",
                "errors" => $e->errors()
            ], 422, $header, JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {

            Log::error("Erro no banco de dados ao cadastrar pet: {$e->getMessage()}");
            return response()->json([
                "type" => "about:blank",
                "title" => "Erro ao salvar os dados no banco de dados",
                "status" => "500",
                "detail" => "Os dados preenchidos não foram salvos no banco de dados",
            ], 500, $header, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {

            Log::error("Erro ao cadastrar pet: {$e->getMessage()}");
            return response()->json([
                "type" => "about:blank",
                "title" => "Erro inesperado",
                "status" => "500",
                "detail" => "Ocorreu um erro inesperado ao salvar os dados",
            ], 500, $header, JSON_UNESCAPED_UNICODE);
        }
    }

    public function show($id)
    {
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        try {
            $pet = $this->table
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->get([
                    'id',
                    'name',
                    'breed',
                    'age',
                    'size',
                    'pedigree',
                    'bio',
                    'description'
                ]);          

            if (count($pet) > 0) {
                return response()->json($pet, 200, $header, JSON_UNESCAPED_UNICODE);
            }

            throw new Exception('Something went wrong.');

        } catch (Exception $e) {
            Log::error("Erro ao encontrar pet: {$e->getMessage()}");
            return response()->json([
                "type" => "about:blank",
                "title" => "Pet não encontrado",
                "status" => "404",
                "detail" => "O pet não foi encontrado"
            ], 404, $header, JSON_UNESCAPED_UNICODE);
        }
    }

    public function update(Request $request, $id)
    {
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        try {
            $pet = $this->repository->upInsert($request, $id);
            if ($pet) {
                if ($request->hasFile('images')) {
                    $this->archives_repository->upInsert($request->file('images'), $pet->id);                    
                }

                return response()->json([
                    "type" => "about:blank",
                    "title" => "Pet atualizado com sucesso",
                    "status" => "200",
                    "detail" => "Seu Pet foi atualizado com sucesso"
                ], 200, $header, JSON_UNESCAPED_UNICODE);
            }
        } catch (ValidationException $e) {

            return response()->json([
                "type" => "about:blank",
                "title" => "Erro de validação nos dados fornecidos",
                "status" => "422",
                "detail" => "Os dados não foram preenchidos corretamente",
                "errors" => $e->errors()
            ], 422, $header, JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {

            Log::error("Erro no banco de dados ao cadastrar pet: {$e->getMessage()}");
            return response()->json([
                "type" => "about:blank",
                "title" => "Erro ao salvar os dados no banco de dados",
                "status" => "500",
                "detail" => "Os dados preenchidos não foram salvos no banco de dados",
            ], 500, $header, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {

            Log::error("Erro ao cadastrar pet: {$e->getMessage()}");
            return response()->json([
                "type" => "about:blank",
                "title" => "Erro inesperado",
                "status" => "500",
                "detail" => "Ocorreu um erro inesperado ao salvar os dados",
            ], 500, $header, JSON_UNESCAPED_UNICODE);
        }
    }

    public function destroy($id)
    {
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        try {
            if (Pet::findOrFail($id)->delete()) {
                return response()->json([
                    "type" => "about:blank",
                    "title" => "Pet excluído com sucesso",
                    "status" => "200",
                    "detail" => "Seu Pet foi excluído com sucesso"
                ], 200, $header, JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $exception) {
            return response()->json([
                "type" => "about:blank",
                "title" => "Erro ao excluir o pet",
                "status" => "404",
                "detail" => "Ocorreu um erro ao tentar excluir o pet"
            ], 404, $header, JSON_UNESCAPED_UNICODE);
        }
    }
}
