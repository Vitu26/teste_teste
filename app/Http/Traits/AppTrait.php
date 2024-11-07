<?php

namespace App\Http\Traits;

trait AppTrait
{
    public function traitRequest($data)
    {
        if (is_array($data)) :
            foreach ($data as $key => $value) :
                if (!is_array($value)) :
                    switch ((string) $key):
                        case 'birth':
                            $data[$key] = (string) $this->sanitizeString($this->toUSDataFormat($value));
                            break;
                        case 'currency':
                        case 'value':
                            $data[$key] = $this->toUSCurrencyFormat($value);
                            break;
                        case 'url':
                        case 'link':
                            $data[$key] = (string) $this->sanitizeURL($value);
                            break;
                        case 'integer':
                        case 'age':
                        case 'id':
                            $data[$key] = (int) $this->sanitizeInteger($value);
                            break;
                        case 'email':
                        case 'e-mail':
                            $data[$key] = (string) $this->sanitizeEmail($value);
                            break;
                        default:
                            $data[$key] = $value;
                    endswitch;
                else :
                    $data[$key] = $this->traitRequest($value);
                endif;
            endforeach;
            return $data;
        endif;
        return null;
    }


    public function sanitizeEmail($email)
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }


    public function sanitizeString($string)
    {
        return filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    }


    public function sanitizeInteger($integer)
    {
        return filter_var($integer, FILTER_SANITIZE_NUMBER_INT);
    }

    public function sanitizeURL($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public function toUSCurrencyFormat(string $valor = null)
    {
        return $valor ? (float) str_replace(",", ".", preg_replace('/[a-zA-Z\$\.]/i', '', $valor)) : "";
    }

    public function filter_request(array $request, array $fields): array
    {
        $filter = array();
        foreach ($request as $campo => $valor) {
            if (in_array($campo, $fields)) {
                if (!$valor)
                    $filter[$campo] = null;
                else
                    $filter[$campo] = $valor;
            }
        }
        return $filter;
    }

    public function toBRDataFormat($valor, string $name = null)
    {
        $valor = $this->extracted_use_model($valor, $name);
        if (!$valor)
            return null;

        if (!$this->is_date($valor))
            return $this->tError("Formato de data inválido! Erro: $valor");
        $array = explode("-", substr($valor, 0, 10));
        return count($array) == 3 ? "$array[2]/$array[1]/$array[0]" : $valor;
    }

    public function is_date($value): bool
    {
        $try_br = (bool)preg_match('/(?<dia>\d{2})(\/)(?<mes>\d{2})(\/)(?<ano>\d{4})/', $value);
        if (!$try_br)
            return (bool)preg_match('/(?<year>\d{4})(\-)(?<month>\d{2})(\-)(?<day>\d{2})/', $value);
        return true;
    }

    public function extracted_use_model($valor = null, string $name = null)
    {
        if (!$valor && $name && $this->is_model())
            return $this->$name;
        return $valor;
    }

    function removerPrefixos($arrayOriginal, $prefixos)
    {
        $arrayModificado = [];

        foreach ($arrayOriginal as $chave => $valor) {
            $novaChave = $chave;
            foreach ($prefixos as $prefixo) {
                $novaChave = str_replace($prefixo, '', $novaChave);
            }
            $arrayModificado[$novaChave] = $valor;
        }

        return $arrayModificado;
    }


}
