<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

trait FileTrait
{
    /**
     * Cria um arquivo com as informaçẽos que você deseja.
     *
     * @param $path
     * @param $context
     * @return bool
     */
    public function createFile($path, $context): bool
    {
        if ($repository = fopen($path, "a")) {
            fwrite($repository, trim($context));
            fclose($repository);
            return true;
        }
        return false;
    }

    /**
     * Verifica a existência e se o arquivo é valido.
     *
     * @param Request $request
     * @param string $fileName
     * @return bool
     */
    public function validFile(Request $request, string $fileName): bool
    {
        return ($request->hasFile($fileName) && $request->file($fileName)->isValid());
    }

    /**
     * Caso arquivo não exista registre no sistema.
     * Retorna o nome do arquivo.
     *
     * @param $file
     * @param $dir
     * @param $fileOld
     * @return string
     */
    public function saveUploads(Request $request, $dir, $inputName = 'image'): string
    {
        $savedFiles = [];

        if ($request->hasFile($inputName)) {
            $files = $request->file($inputName);

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $savedFiles[] = $this->saveUpload($file, $dir);
                }
            }
        }

        // Junta os nomes dos arquivos salvos em uma única string, separados por vírgula
        return implode(',', $savedFiles);
    }

    public function saveUpload($file, $dir, $fileOld = null): string
    {
        $nameFile = $this->getFileName($file);

        if (!is_null($fileOld)) {
            $this->deleteFile(explode("/", $fileOld)[1], $dir);
        }

        $file->storeAs($dir, $nameFile);
        $this->compress(storage_path("app/public/$dir/$nameFile"));

        return $nameFile;
    }

    public function deleteFile($files, string $dir)
    {
        if (!is_array($files)) {
            return Storage::delete("{$dir}/{$files}");
        }

        foreach ($files as $file) {
            Storage::delete("{$dir}/{$file}");
        }
    }

    public function getFileName($file): string
    {
        return uniqid() . '.' . strtolower($file->getClientOriginalExtension());
    }

    private function fileExistsInPath($nameFile, $dir): bool
    {
        return ($nameFile && file_exist("storage/{$dir}/{$nameFile}"));
    }
    public function compress(string $src_path, string $destinatio_path = null, $wWith = null, $wHeight = 500, $extras = null)
    {
        $imgInfo = getimagesize("$src_path");

        if (!in_array($imgInfo[2], [1, 2, 3]))
            return;

        if (!$destinatio_path)
            $destinatio_path = $src_path;

        if (!$extras) {
            list("dirname" => $dirname, "filename" => $filename, "extension" => $extension) = pathinfo($destinatio_path);
            $min = "$dirname/{$filename}_min.$extension";
            $this->compress($src_path, $min, null, 100, true);
            $med = "$dirname/{$filename}_med.$extension";
            $this->compress($src_path, $med, null, 280, true);
            $larg = "$dirname/{$filename}_larg.$extension";
            $this->compress($src_path, $larg, 1024, null, true);
            // normal
            $this->compress($src_path, null, null, 500, true);
            return;
        } else {
            if ($wWith && $wHeight) {
                Image::make($src_path)->resize($wWith, $wHeight, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinatio_path, 90);
            } elseif ($wWith) {
                Image::make($src_path)->resize($wWith, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinatio_path, 90);
            } else {
                Image::make($src_path)->resize(null, $wHeight, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinatio_path, 90);
            }
        }

        $bytes = filesize($src_path);

        if ($bytes < (30 * 1024))
            return; // não mexe com menor que 30KB

        // resmush compress
        $this->extractedCompress($destinatio_path);
    }

    public function extractedCompress(string $destinatio_path)
    {
        $imgInfo = getimagesize("$destinatio_path");

        switch ($imgInfo[2]) {
            case 1:
                $im = imagecreatefromgif($destinatio_path);
                break;
            case 2:
                $im = imagecreatefromjpeg($destinatio_path);
                break;
            case 3:
                $im = imagecreatefrompng($destinatio_path);
                break;
            default:
                return false;
        }

        $nWidth = round($imgInfo[0]);
        $nHeight = round($imgInfo[1]);
        $newImg = imagecreatetruecolor($nWidth, $nHeight);
        if (($imgInfo[2] == 1)) {
            $white = imagecolorallocate($newImg, 255, 255, 255);
            $black = imagecolorallocate($newImg, 0, 0, 0);
            $transparent = imagecolortransparent($newImg, $white);
            $transparent = imagecolortransparent($newImg, $black);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }
        if (($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
        switch ($imgInfo[2]) {
            case 1:
                imagegif($newImg, $destinatio_path);
                break;
            case 2:
                imagejpeg($newImg, $destinatio_path, 90);
                break;
            case 3:
                imagepng($newImg, $destinatio_path, 9, PNG_ALL_FILTERS);
                break;
            default:
                trigger_error('Redimensionamento da imagem deu águia!', E_USER_WARNING);
                break;
        }
    }
    public function makeSlug(string $sluggable)
    {
        return $this->sanitizeString(
            strtolower(
                $this->rejectHyphenOnInit(
                    $this->rejectDoubleDot(
                        $this->rejectDoubleHyphen(
                            $this->rejectSpaceOrDashOrNotNumberAndNotString($sluggable, '')
                        ),
                        ''
                    )
                )
            )
        );
    }

    public function rejectHyphenOnInit(string $slug)
    {
        return preg_replace('/^(-)/', '', $slug);
    }

    public function rejectDoubleHyphen(string $slug)
    {
        return preg_replace('/(--)+/', '', $slug);
    }

    public function rejectDoubleDot(string $slug, string $replace = '.')
    {
        return preg_replace('/(\.\.)+/', $replace, $slug);
    }

    public function rejectSpaceOrDashOrNotNumberAndNotString(string $slug, string $replace = '-')
    {

        return preg_replace('/\s+|_+|[^a-zA-Z0-9.]+/', $replace, $slug);
    }

    public function getFile($path)
    {
        return File::get($path);
    }
}
