<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsPreviewImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
        // Excel::toArray will capture the array directly.
    }
}
