<?php

namespace App\Repositories\Eloquent\Archives;

use App\Models\Archives;
use App\Http\Traits\FileTrait;
use App\Repositories\Repository;

class ArchivesRepository extends Repository
{

    use FileTrait;

    public function __construct()
    {
        $this->model = new Archives();
    }

    protected function structureUpInsert($file): array
    {
        $extension = $file->getClientOriginalExtension();
        $name = $this->getFileName($file);
        $title = $name;
        $type = $file->getMimeType();
        $path = "storage/uploads/{$name}.{$extension}";

        return [
            'name' => $name,
            'title' => $title,
            'type' => $type,
            'path' => $path,
        ];
    }


    public function upInsert($request, int $id = null)
    {
        try {
            foreach ($request->images as $image) {
                $data = $this->structureUpInsert($image);

                if ($id != null) {
                    Archives::where('id', $id)->update($data);
                } else {
                    Archives::create($data);
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
