<?php

use App\Http\Traits\AppTrait;
use App\Http\Traits\FileTrait;

/**
 * Mascarando qualquer elemento string
 * @param string $val valor a ser mascarado
 * @param string $mask mascara a ser composta
 * @return string
 */
function mask(string $val, string $mask)
{

    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {

        if ($mask[$i] == '#') {

            if (isset($val[$k]))
                $maskared .= $val[$k++];
        } else {

            if (isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

/**
 * Retirando caracteres especiais de um cpf
 * @param string $valor CPF mascarado
 * @return array|string|string[]
 */
function limpaCPF_CNPJ(string $valor)
{
    $valor = trim($valor);
    $valor = str_replace(".", "", $valor);
    $valor = str_replace(",", "", $valor);
    $valor = str_replace("-", "", $valor);
    $valor = str_replace("/", "", $valor);
    return $valor;
}

/**
 * Retorno de CPF ocultado
 * @param string $cpf
 * @return array|string|string[]
 */
function escondeCPF(string $cpf)
{
    $cpf = substr_replace($cpf, 'XXX', 0, 3);
    $cpf = substr_replace($cpf, 'XX', -2, 2);
    return $cpf;
}

/**
 * Retorno de CPF ocultado
 * @param string $cpf
 * @return array|string|string[]
 */
function escondeCNPJ(string $cpf)
{
    $cpf = substr_replace($cpf, 'XXX', 0, 2);
    $cpf = substr_replace($cpf, 'XX', -2, 2);
    return $cpf;
}


if (!function_exists('translateMonth')) {

    /**
     * Retorna o nome do mês traduzido
     * @param int $date número do mês
     * @return string|void
     */
    function translateMonth(int $date)
    {
        switch ($date) {
            case '1':
                return 'Janeiro';
                break;
            case '2':
                return 'Fevereiro';
                break;
            case '3':
                return 'Março';
                break;
            case '4':
                return 'Abril';
                break;
            case '5':
                return 'Maio';
                break;
            case '6':
                return 'Junho';
                break;
            case '7':
                return 'Julho';
                break;
            case '8':
                return 'Agosto';
                break;
            case '9':
                return 'Setembro';
                break;
            case '10':
                return 'Outubro';
                break;
            case '11':
                return 'Novembro';
                break;
            case '12':
                return 'Dezembro';
                break;
            default:
                '';
        }
    }
}

if (!function_exists('translateMonthFromDate')) {

    /**
     * Retorna a data com o mês escrito
     * @param string $date data
     * @return string|void
     */
    function translateMonthFromDate(string $date)
    {
        $date = date('d-m-Y', strtotime($date));
        $explode = explode('-', $date);

        switch ($explode[1]) {
            case '1':
                return $explode[0].' Janeiro '.$explode[2];
            case '2':
                return $explode[0].' Fevereiro '.$explode[2];
            case '3':
                return $explode[0].' Março '.$explode[2];
            case '4':
                return $explode[0].' Abril '.$explode[2];
            case '5':
                return $explode[0].' Maio '.$explode[2];
            case '6':
                return $explode[0].' Junho '.$explode[2];
            case '7':
                return $explode[0].' Julho '.$explode[2];
            case '8':
                return $explode[0].' Agosto '.$explode[2];
            case '9':
                return $explode[0].' Setembro '.$explode[2];
            case '10':
                return $explode[0].' Outubro '.$explode[2];
            case '11':
                return $explode[0].' Novembro '.$explode[2];
            case '12':
                return $explode[0].' Dezembro '.$explode[2];
            default:
                return '';
        }
    }
}

if (!function_exists('translateDays')) {

    function translateDays($day)
    {
        $semana = array(
            'Sun' => 'Domingo',
            'Mon' => 'Segunda-Feira',
            'Tue' => 'Terça-Feira',
            'Wed' => 'Quarta-Feira',
            'Thu' => 'Quinta-Feira',
            'Fri' => 'Sexta-Feira',
            'Sat' => 'Sábado'
        );

        return $semana[$day];
    }
}

/*
 * @param $type = verifica o limite de quantidade de itens a ser
 * exibido para a categoria passada e ordena os itens
 */
if (!function_exists('getTypeContent')) {

    /**
     * Retorna o tipo de conteúdo, podendo ser: Ultimas Lidas(lastreads), Destaques(highlights) ou Novidades(newsHome)
     * @param mixed $collection Dados a serem buscados
     * @param string $type Tipo do Conteúdo
     * @param $limit Limitador de quantidade
     * @return \Illuminate\Support\Collection
     */
    function getTypeContent($collection, string $type, $limit = null)
    {
        if ($type == 'lastReads') {
            return collect($collection)->sortByDesc('clicks')->take($limit);
        }

        if ($type == 'highlights') {
            $colectionHighlights = $collection->reject(function ($value) {
                return $value['highlights'] != 1 && $value->archive;
            });
            return $colectionHighlights->take($limit);
        }

        if ($type == 'newsHome') {
            if ($collection) {
                return collect($collection->items())->take($limit);
            } else {
                return collect($collection);
            }
        }

        return $collection->take($limit);
    }
}

if (!function_exists('abreviaString')) {
    /**
     * Retorna uma Abreviação do termo passado
     * @param string $texto
     * @return void
     */
    function abreviaString(string $texto)
    {
        $split_name = explode(" ", $texto);

        if (count($split_name) > 2) {

            for ($i = 1; (count($split_name) - 1) > $i; $i++) {

                if (strlen($split_name[$i]) > 3) {

                    $split_name[$i] = substr($split_name[$i], 0, 1) . ".";
                }
            }
        }

        echo implode(" ", $split_name);
    }
}

/**
 * Verifica se o arquivo existe no caminho passado
 * @param string $path Caminho do arquivo
 * @return false|string
 */
function file_exist(string $path)
{
    if (file_exists($path)) {
        return $path;
    } else {
        return false;
    }
}

/**
 * Verifica se o arquivo existe no caminho passado
 * @param string $path Caminho do arquivo
 * @return false|string
 */
function file_exist_portal(string $path)
{
    if (file_exists($path)) {
        return $path;
    } else {
        return 'storage/img/exemplos/sem-imagem-min.png';
    }
}

/**
 * Verifica se o arquivo existe no caminho passado
 * @param string $path Caminho do arquivo
 * @return false|string
 */
function file_exist_destaque(string $path)
{
    if (file_exists($path)) {
        return $path;
    } else {
        return 'storage/img/exemplos/sem-imagem.png';
    }
}


if (!function_exists('fileContentExist')) {

    /**
     * Verifica se uma imagem Existe e caso não retorna uma imagem padrão
     * @param string $archivePath Caminho da imagem
     * @return string
     */
    function fileContentExist(string $archivePath)
    {
        if (isset($archivePath) && file_exists($archivePath)) {
            return url($archivePath);
        } else {
            return '/storage/img/exemplos/sem-imagem.png';
        }
    }
}

//Retorna a imagem min da noticia
if (!function_exists('fileContentExistMin')) {

    /**
     * Verifica se uma imagem(minificada) Existe e caso não retorna uma imagem padrão
     * @param string $archivePath Caminho da imagem
     * @return string
     */
    function fileContentExistMin(string $archivePath)
    {
        if (file_exists($archivePath)) {

            $archivePath = explode('.', $archivePath);
            $archivePath = $archivePath[0] . '_min.' . $archivePath[1];


            if (file_exists($archivePath)) {
                return url($archivePath);
            } else {
                return '/storage/img/exemplos/sem-imagem-min.png';
            }
        } else {
            return '/storage/img/exemplos/sem-imagem-min.png';
        }
    }
}

if (!function_exists('fileBannerExist')) {

    /**
     * Verifica se existe o banner no caminho especificado, se não retorna uma imagem padrão
     * @param string $pathBanner Caminho do banner
     * @return string
     */
    function fileBannerExist(string $pathBanner)
    {
        if (file_exists($pathBanner)) {
            return $pathBanner;
        } else {
            return '/storage/img/exemplos/banner-sem-imagem.png';
        }
    }
}

/**
 * Verifica se existe o banner no caminho especificado, se não retorna uma imagem padrão
 * @param string $pathBanner Caminho do banner
 * @return string
 */
function fileSuperBannerExist(string $pathBanner)
{
    if (file_exists($pathBanner)) {
        return $pathBanner;
    } else {
        return 'storage\img\exemplos\super-banner-sem-imagem.png';
    }
}


if (!function_exists('getResultSearch')) {

    /**
     * Retorna o resultado de uma busca os dados
     * @param \Illuminate\Support\Collection $collection
     * @return \Illuminate\Support\Collection
     */
    function getResultSearch($collection)
    {
        $value['Notícias'] = array();
        $value['Legislações'] = array();
        $value['Publicações'] = array();
        $value['A Prefeitura'] = array();
        $value['A Cidade'] = array();
        $value['Secretarias'] = array();
        $value['Perguntas Frequentes'] = array();
        $value['Glossário'] = array();
        $value['Órgãos'] = array();

        $totalNoticias = 0;
        $totalLegislacao = 0;
        $totalPublicacoes = 0;
        $totalPrefeitura = 0;
        $totalCidade = 0;
        $totalSecretarias = 0;
        $totalOrgaos = 0;
        $totalGlossario = 0;
        $totalPerguntas = 0;


        foreach ($collection as $item) {
            if ($item->category_contents_id == 1 || $item->categoryContents->category_contents_id == 1) {
                $item['categoryMainName'] = 'noticias';
                $totalNoticias = $totalNoticias + 1;
                $item['CountTotal'] = $totalNoticias;
                array_push($value['Notícias'], $item);
            }

            if ($item->category_contents_id == 2 || $item->categoryContents->category_contents_id == 2) {
                $item['categoryMainName'] = 'legislacao';
                $totalLegislacao = $totalLegislacao + 1;
                $item['CountTotal'] = $totalLegislacao;
                array_push($value['Legislações'], $item);
            }

            if ($item->category_contents_id == 3 || $item->categoryContents->category_contents_id == 3) {
                $item['categoryMainName'] = 'publicacoes';
                $totalPublicacoes = $totalPublicacoes + 1;
                $item['CountTotal'] = $totalPublicacoes;
                array_push($value['Publicações'], $item);
            }

            if ($item->category_contents_id == 6 || $item->categoryContents->category_contents_id == 6) {
                $item['categoryMainName'] = 'a-prefeitura';
                $totalPrefeitura = $totalPrefeitura + 1;
                $item['CountTotal'] = $totalPrefeitura;
                array_push($value['A Prefeitura'], $item);
            }

            if ($item->category_contents_id == 7 || $item->categoryContents->category_contents_id == 7) {
                $item['categoryMainName'] = 'secretarias';
                $totalSecretarias = $totalSecretarias + 1;
                $item['CountTotal'] = $totalSecretarias;
                array_push($value['Secretarias'], $item);
            }

            if ($item->category_contents_id == 11 || $item->categoryContents->category_contents_id == 11) {
                $item['categoryMainName'] = 'glossario';
                $totalGlossario = $totalGlossario + 1;
                $item['CountTotal'] = $totalGlossario;
                array_push($value['Glossário'], $item);
            }

            if ($item->category_contents_id == 4 || $item->categoryContents->category_contents_id == 4) {
                $item['categoryMainName'] = $item->categoryContents->slug;
                $totalCidade = $totalCidade + 1;
                $item['CountTotal'] = $totalCidade;
                array_push($value['A Cidade'], $item);
            }

            if ($item->category_contents_id == 12 || $item->categoryContents->category_contents_id == 12) {
                $item['categoryMainName'] = 'perguntas';
                $totalPerguntas = $totalPerguntas + 1;
                $item['CountTotal'] = $totalPerguntas;
                array_push($value['Perguntas Frequentes'], $item);
            }

            if ($item->category_contents_id == 32 || $item->categoryContents->category_contents_id == 32) {
                $item['categoryMainName'] = 'orgaos';
                $totalOrgaos = $totalOrgaos + 1;
                $item['CountTotal'] = $totalOrgaos;
                array_push($value['Órgãos'], $item);
            }
        }

        return $results = collect($value)->sortBy(function ($value, $key) {
            return $key;
        });
    }
}
if (!function_exists('getFilterCategories')) {
    /**
     * Buscando informações por categoria
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    function getFilterCategories($collection)
    {
        $arrayName['nameCategory'] = array();
        foreach ($collection as $item) {

            if ($item->categoryContents->category_contents_id == 1) {
                if (!in_array('Notícias', $arrayName['nameCategory'])) {
                    array_push($arrayName['nameCategory'], 'Notícias');
                }
            } else {
                if (!in_array($item->categoryContents->name, $arrayName['nameCategory'])) {
                    array_push($arrayName['nameCategory'], $item->categoryContents->name);
                }
            }
        }

        return $arrayName['nameCategory'];
    }
}

if (!function_exists('getDateIsValid')) {
    function getDateIsValid($value)
    {
        if (isset($value)) {

            $valueExplode = explode('-', $value);
            $day = $valueExplode[0];
            $month = $valueExplode[1];
            $year = $valueExplode[2];
            $firstValueYear = substr($year, 0, 1); //Se o ano for 0026 eh invalido
            $isValid = checkdate($month, $day, $year);

            if ($isValid && $firstValueYear > 0) {
                return true;
            }
            return false;
        }
    }
}


if (!function_exists('embedVideo')) {
    function embedVideo($link)
    {
        if (strpos($link, 'youtube') !== false or strpos($link, 'youtu.be') !== false) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match);
            $youtube_id = $match[1];

            return "https://youtube.com/embed/$youtube_id";
        }

        if (strpos($link, 'vimeo') !== false) {
            preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $link, $match);
            $vimeo_id = $match[5];

            return "https://player.vimeo.com/video/$vimeo_id";
        }

        return $link;
    }
}

if (!function_exists('convertDateToDB')) {
    function convertDateToDB($date)
    {
        $o_datahora = $date;
        $n_datahora = str_replace('/', '-', $o_datahora);
        $dta_formatada = date('Y-m-d H:i:s', strtotime($n_datahora));

        return $dta_formatada;
    }
}


if (!function_exists('maskCpfCns')) {
    function maskCpfCns($item)
    {
        $string = isset($item->cpf) ? $item->cpf : $item->number_sus;
        $string = substr($string, 5, 5);
        $string = str_replace('.', '', $string);
        return "XXX{$string}XXX";
    }
}

if (!function_exists('onlyNumbers')) {
    function onlyNumbers($number)
    {
        return (int) preg_replace("/[^0-9]/", "", $number);
    }
}

if (!function_exists('formatPercent')) {
    function formatPercent($percent)
    {
        switch ($percent) {
            case strlen((int) $percent) == 2:
                return ($percent > 0) ? number_format($percent, 1, '.', '') : 0;
                break;
            case strlen((int) $percent) == 1 && (float) $percent < 0.01:
                return ($percent > 0) ? number_format($percent, 3, '.', '') : 0;
                break;
            case strlen((int) $percent) == 1:
                return ($percent > 0) ? number_format($percent, 2, '.', '') : 0;
                break;
            default:
                return ($percent > 0) ? number_format($percent, 0, '.', '') : 0;
                break;
        }
    }
}

function slug_fix($name)
{
    $name = clean_slug($name);

    $name = str_replace(' ', '-', $name);
    $name = preg_replace('/-+/', '-', $name);
    return $name;
}

function clean_slug($string)
{
    $nova_string = preg_replace(
        array(
            "/(á|à|ã|â|ä)/",
            "/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/",
            "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/",
            "/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/",
            "/(Ú|Ù|Û|Ü)/",
            "/(ñ)/",
            "/(Ñ)/",
            "/(ç)/",
            "/(Ç)/",
            "/[^A-Za-z0-9. ]/"
        ),
        explode(" ", "a A e E i I o O u U n N c C"),
        $string
    );

    return $nova_string;
}

function addHash($name)
{
    $final = explode('.', $name);
    $finalCompare = '.' . $final[count($final) - 1];
    $final = '-' . date("dmYHis") . $finalCompare;
    return str_replace($finalCompare, $final, $name);
}

function cleanText($text)
{
    $text = strip_tags($text);

    return $text;
}


function calcPorcentagem($parcial, $total)
{
    return ($parcial * 100) / $total;
}

function formatarDataPorExtenso($data)
{
    $formatter = new IntlDateFormatter(
        'pt_BR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'America/Sao_Paulo',
        IntlDateFormatter::GREGORIAN
    );

    $data_arr = explode(" ", $formatter->format(date_create($data)));
    $data_retornada = ucfirst($data_arr[0]) . " " . $data_arr[1] . " " . $data_arr[2] . " " . ucfirst($data_arr[3]) . " " . $data_arr[4] . " " . $data_arr[5];
    return $data_retornada;
}

function tobrdata($valor)
{
    $array = explode("-", substr($valor, 0, 10));
    return count($array) == 3 ? "$array[2]/$array[1]/$array[0]" : $valor;
}

function objBanner($template)
{
    $data = null;

    if ($template == 'modelo5') {
        $data = [
            (object) collect([
                "id" => 1,
                "name" => 'Esquerda'
            ])->all(),
            (object) collect([
                "id" => 2,
                "name" => 'Direita'
            ])->all()
        ];
    } else {
        $data = [
            (object) collect([
                "id" => 1,
                "name" => 'Em cima'
            ])->all(),
            (object) collect([
                "id" => 2,
                "name" => 'Embaixo'
            ])->all()
        ];
    }

    return collect($data);
}

function extractedFromImageTrait($path)
{
    list("dirname" => $dirname, "filename" => $filename, "extension" => $extension) = pathinfo($path);
    $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (stripos($dirname, $actual_link) !== false) {
        $dirname = str_replace($actual_link, "", $dirname);
    }
    if (stripos($dirname, "/") !== 0) {
        $dirname = "/$dirname";
    }
    if (stripos($path, "storage") === 0) {
        $path = "/$path";
    }
    return [$dirname, $filename, $extension, $path];
}
function getMinFile($path = null)
{
    $path = $path ?? "/storage/img/exemplos/sem-imagem-min.png";
    list($dirname, $filename, $extension, $path) = extractedFromImageTrait($path);
    $wPath = "$dirname/{$filename}_min.$extension";
    if (file_exists(public_path($wPath))) {
        return $wPath;
    }
    return $path;
}
function getMedFile($path = null)
{
    $path = $path ?? "/storage/img/exemplos/sem-imagem.png";
    list($dirname, $filename, $extension, $path) = extractedFromImageTrait($path);
    $wPath = "$dirname/{$filename}_med.$extension";
    if (file_exists(public_path($wPath))) {
        return $wPath;
    }
    return $path;
}

function getLargFile($path = null)
{
    $path = $path ?? "/storage/img/exemplos/sem-imagem.png";
    list($dirname, $filename, $extension, $path) = extractedFromImageTrait($path);
    $wPath = "$dirname/{$filename}_larg.$extension";
    if (file_exists(public_path($wPath))) {
        return $wPath;
    }
    return $path;
}

function getTypeSize($type = null)
{
    if($type && isset($type->size)) return $type->size;

    return "600px";

}

function sliceCollection($collection, $limit)
{
    if (!empty($collection)) {
        return $collection->slice($limit);
    } else {
        return $collection;
    }
}