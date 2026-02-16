<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImageImport;

class ImageImportController extends Controller
{
    public function importImages()
    {
        // Download a sample Excel file with image URLs
        $sampleFile = public_path('images/sample.xlsx');

        // You can create this file manually with image URLs in a column
        // Or use the provided sample file with example URLs
        // In a real scenario, this file would be uploaded by users

        // Create an import instance
        $import = new ImageImport();

        // Import data from the Excel file
        Excel::import($import, $sampleFile);

        // Access the imported data, including image URLs
        $data = $import->getData();

        // Process each row and download/store images
        foreach ($data as $row) {
            $imageUrl = $row['image_url']; // Replace with the actual column name containing image URLs

            // Use Laravel's file handling to download and store the image
            $imageContent = file_get_contents($imageUrl);

            // Store the image in the public folder with a unique name
            $imageName = uniqid('image_') . '.jpg';
            file_put_contents(public_path("images/{$imageName}"), $imageContent);
        }

        return "Images imported and stored successfully.";
    }
}
