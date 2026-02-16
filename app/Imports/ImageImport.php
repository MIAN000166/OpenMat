<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ImageImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // You can add additional validation or processing if needed
        return $rows;
    }
}
