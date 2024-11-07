<?php

namespace App\Repositories;

use App\Http\Traits\AppTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class Repository
{
    use AppTrait;

    protected $model;

    /**
     * Retorna a model utilizada para a Repository
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Estrutura de array para utilizar a modal tanto na inserção ou atualização.
     *
     * @param $request
     * @return array
     */
    protected abstract function structureUpInsert($request): array;

    /**
     * Prepara os dados da request para inserção ou atualização no banco
     * @param $request
     * @return array
     * @throws \Exception
     */
    protected function dataTrait($request): array
    {
        if ($request instanceof Request)
            return $this->traitRequest($request->request->all());
        if (is_array($request))
            return $this->traitRequest($request);
    }

    /**
     * Insere ou atualiza na Model.
     *
     * @param $request
     * @param int|null $id
     * @return mixed
     */
    public function upInsert($request, int $id = null)
    {
        try {
            $data = DB::transaction(function () use ($request, $id) {
                return $this->structureUpInsert($this->dataTrait($request));
            });
            return $this->getModel()->updateOrCreate(['id' => $id], $data);
        } catch (\Throwable $th) {
            throw new Exception('Something went wrong.');
        }
        return false;
    }

    /**
     * Deletar um dado passando o nome da coluna ou indo direto pela primary key "id".
     *
     * @param int|string $id
     * @param string|null $column
     * @return bool
     */
    public function destroy($id, string $column = 'id'): bool
    {
        try {
            $element = DB::transaction(function () use ($id, $column) {
                return $this->getModel()->where($column, $id);
            });
            if ($element->delete())
                return true;
        } catch (\Exception $e) {
        }
        return false;
    }

    /**
     * Deletar vários dados passando o nome da coluna ou indo direto pela primary key "id".
     *
     * @param $array
     * @param string|null $column
     * @return JsonResponse
     */
    public function destroyAll($array, string $column = 'id'): JsonResponse
    {
        try {
            $delete = DB::transaction(function () use ($array, $column) {
                return $this->getModel()->whereIn($column, $array)->delete();
            });
            if ($delete)
                return response()->json(true);
        } catch (\Exception $e) {
        }
        return response()->json(false);
    }

    /**
     * Apaga elemento junto com o seu relacionamento.
     *
     * @param int $id
     * @param string $relation
     * @param string|null $column
     * @return false
     */
    public function destroyWithRelation(int $id, string $relation, string $column = null): bool
    {
        if ($column)
            $element = $this->getModel()->where($column, $id)->first();
        else
            $element = $this->getModel()->find($id);
        $relation_id = $element->{$relation . "_id"};
        if ($this->destroy($id, $column))
            return $element->$relation->delete($relation_id);
        else
            return false;
    }
}
