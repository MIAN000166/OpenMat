<?php

namespace App\Exports;

use App\Models\Admin\Mat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ExportMats implements FromCollection, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Fetch the data you want to export from the database
        $matsData = \App\Models\Admin\Mat::select(
            'name',
            'region',
            'state',
            'location',
            'phone_number',
            'website',
            'physical_address',
            'open_mat_time',
            'open_mat_day',
            'link_to_waiver',
            'other_info',
            'image',
            'event_date',
        )->get();

        return $matsData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Specify the column headings
        return [
            'Name',
            'Region',
            'State',
            'Location',
            'Phone Number',
            'Website',
            'Physical Address',
            'Open Mat Time',
            'Open Mat Day',
            'Link to Waiver',
            'Other Info',
            'Image',
            'Event Date',
        ];

    }
}
