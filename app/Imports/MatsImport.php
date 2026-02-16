<?php

namespace App\Imports;

use App\Models\Admin\Mat;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MatsImport implements ToCollection, WithStartRow
{
    private $userId;
    private $validationErrors = [];

    public function startRow(): int
    {
        return 1; // Start processing from the second row
    }

    /**
     * @param array $row
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        // Define the expected column names
        $expectedColumns = [
            'Name',
            'Region',
            'State',
            'Location',
            'Phone Number',
            'Website',
            'Physical Address',
            'E-Mail',
            'Open Mat Time',
            'Open Mat Start',
            'Open Mat Day',
            'Time',
            'Link to Waiver',
            'Other Info',
//            'Image',
//            'Event Date',
        ];

        $headingRow = $rows->shift();

        $actualColumns = $headingRow->toArray();

        $lastNonNullIndex = null;
        foreach (array_reverse($actualColumns) as $index => $value) {
            if ($value !== null) {
                $lastNonNullIndex = count($actualColumns) - $index - 1;
                break;
            }
        }
        $actualColumns = array_slice($actualColumns, 0, $lastNonNullIndex + 1);
        $missingColumns = array_diff($expectedColumns, $actualColumns);


        if (!empty($missingColumns)) {
            throw ValidationException::withMessages([
                'import' => ['Missing required columns: ' . implode(', ', $missingColumns)]
            ]);
        }

        $user = request()->user();
        $expectedcount=count($expectedColumns);
        $actualCount=count($actualColumns);


if($expectedcount!=$actualCount){
    throw ValidationException::withMessages(['import' =>'Excel sheet having more columns than expected']);
}


        foreach ($rows->slice(1) as $row) {

            $allNull = true;
            foreach ($row as $value) {
                if (!is_null($value)) {
                    $allNull = false;
                    break;
                }
            }


            if ($allNull) {
                continue;
            }
            $trimmedRow = array_slice($row->toArray(), 0, count($expectedColumns));

            $rowData = array_combine($expectedColumns, $trimmedRow);

            $validator = Validator::make($rowData, [
                'Name' => 'nullable',
                'Region' => 'nullable',
                'State' => 'nullable',
                'Location' => 'nullable',
                'Phone Number' => 'nullable',
                'Website' => 'nullable',
                'Physical Address' => 'nullable',
                'E-Mail'=>"nullable|email",
                'Open Mat Time' => 'nullable',
                'Open Mat Start' => 'nullable',
                'Open Mat Day' => 'nullable',
                'Link to Waiver' => 'nullable',
                'Time'=>"nullable",
                'Other Info' => 'nullable',
//                'Image' => 'nullable|url',
//                "Event Date"=>"required|date_format:Y-m-d",
            ]);

            if ($validator->fails()) {
                // Collect validation errors for the row
                $this->validationErrors[] = [
                    'row' => $rowData,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $imagePath = null;


            if (isset($rowData['Image']) && $rowData['Image']) {
                // Download the image and save it locally
                $imagePath = $this->downloadImage($rowData['Image']);
            }

            if ($rowData['Physical Address']!=null){
                $apiKey = 'AIzaSyBiIvCLftcHwIONNtyTUsGHkS1vZuCPeuw';
                $client = new Client();
                $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                    'query' => [
                        'address' => urlencode($rowData['Physical Address']),
                        'key' => $apiKey,
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                if ($body['status'] === 'OK') {
                    $location = $body['results'][0]['geometry']['location'];
                    $latitude = $location['lat'];
                    $longitude = $location['lng'];

                }else{
                    $latitude = null;
                    $longitude = null;
                }
            }else{
                $latitude = null;
                $longitude = null;
            }

            Mat::create([
                'name' => $rowData['Name'],
                'region' => $rowData['Region'],
                'state' => $rowData['State'],
                'location' => $rowData['Location'],
                'phone_number' => $rowData['Phone Number'],
                'website' => $rowData['Website'],
                'physical_address' => $rowData['Physical Address'],
                'email'=>$rowData['E-Mail'],
                'open_mat_time' => $rowData['Open Mat Time'],
                'open_mat_start' => $rowData['Open Mat Start'],
                'open_mat_day' => $rowData['Open Mat Day'],
                'time' => $rowData['Time'],
                'link_to_waiver' => $rowData['Link to Waiver'],
                'other_info' => $rowData['Other Info'],
                'longitude'=>$longitude,
                'latitude'=>$latitude,
                'user_id' => $user->id,
//                'image' => $imagePath,
//                'image'=> asset('storage/' . $imagePath),
//                'event_date'=>$rowData['Event Date'],
            ]);
        }

        if (!empty($this->validationErrors)) {
            throw ValidationException::withMessages(['import' => $this->validationErrors]);
        }
    }

    private function downloadImage($imageUrl)
    {

        $imageName = uniqid() . '.jpg';
        $imagePath = 'images/' . $imageName;


        $imageContents = file_get_contents($imageUrl);
        Storage::disk('public')->put($imagePath, $imageContents);

        return $imagePath;
    }
}
