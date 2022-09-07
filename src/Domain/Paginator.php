<?php

namespace App\Domain;

class Paginator
{

    public function getDataPaginated($pageNumber, $recordsPerPage, $data): array
    {
        $finalPageNumber = 1;
        $length = 10;

        if ($pageNumber) {
            if (ctype_xdigit($pageNumber)) {
                if (intval($pageNumber) > 0) {
                    $finalPageNumber = (int)$pageNumber;
                }
            }
        }

        if ($recordsPerPage) {
            if (ctype_xdigit($recordsPerPage)) {
                if (intval($recordsPerPage) < $length) {
                    $length = (int)$recordsPerPage;
                }
            }
        }

        $initialIndex = ($finalPageNumber * $length) - $length;

        $hasNextPage = count($data) > ($initialIndex + $length);

        return [
            'data' => array_slice($data, $initialIndex, $length),
            'hasNextPage' => $hasNextPage
        ];
    }
}
