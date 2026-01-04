<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GenerateImportTemplate extends Command
{
    protected $signature = 'template:generate';
    protected $description = 'Generate the Excel import template file';

    public function handle(): int
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Perfume Import Template');

        // Define headers with metadata
        $headers = [
            'A' => ['name' => 'Perfume Name', 'required' => true, 'example' => 'Bleu de Chanel'],
            'B' => ['name' => 'Brand', 'required' => true, 'example' => 'Chanel'],
            'C' => ['name' => 'Size (ml)', 'required' => true, 'example' => '100'],
            'D' => ['name' => 'Price', 'required' => true, 'example' => '12500'],
            'E' => ['name' => 'Currency', 'required' => true, 'example' => 'INR'],
            'F' => ['name' => 'Stock Status', 'required' => false, 'example' => 'In Stock'],
            'G' => ['name' => 'Product URL', 'required' => false, 'example' => 'https://example.com/product'],
            'H' => ['name' => 'Item Type', 'required' => false, 'example' => 'Full Bottle'],
            'I' => ['name' => 'Concentration', 'required' => false, 'example' => 'EDP'],
            'J' => ['name' => 'Gender Affinity', 'required' => false, 'example' => 'Male'],
            'K' => ['name' => 'Description', 'required' => false, 'example' => 'A fresh citrus fragrance'],
            'L' => ['name' => 'Notes', 'required' => false, 'example' => 'Citrus, Woody, Musk'],
            'M' => ['name' => 'Image URL', 'required' => false, 'example' => 'https://example.com/image.jpg'],
            'N' => ['name' => 'Launch Year', 'required' => false, 'example' => '2010'],
            'O' => ['name' => 'Offer Details', 'required' => false, 'example' => '10% off on orders above 5000'],
        ];

        // Style for required headers (red background)
        $requiredStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF6B6B'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ];

        // Style for optional headers (blue background)
        $optionalStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4DABF7'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ];

        // Set headers
        foreach ($headers as $col => $config) {
            $cell = $col . '1';
            $headerText = $config['name'] . ($config['required'] ? ' *' : '');
            $sheet->setCellValue($cell, $headerText);
            $sheet->getStyle($cell)->applyFromArray($config['required'] ? $requiredStyle : $optionalStyle);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add example data row
        foreach ($headers as $col => $config) {
            $sheet->setCellValue($col . '2', $config['example']);
        }

        // Add a second example row
        $exampleRow2 = [
            'A' => 'Sauvage',
            'B' => 'Dior',
            'C' => '50',
            'D' => '8900',
            'E' => 'INR',
            'F' => 'In Stock',
            'G' => 'https://example.com/sauvage',
            'H' => 'Full Bottle',
            'I' => 'EDT',
            'J' => 'Male',
            'K' => 'Bold and magnetic fragrance',
            'L' => 'Bergamot, Pepper, Ambroxan',
            'M' => '',
            'N' => '2015',
            'O' => '',
        ];

        foreach ($exampleRow2 as $col => $value) {
            $sheet->setCellValue($col . '3', $value);
        }

        // Add instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');

        $instructions = [
            ['ScentSeeker Import Template Instructions'],
            [''],
            ['REQUIRED FIELDS (marked with * and red background):'],
            ['- Perfume Name: The name of the perfume'],
            ['- Brand: The brand/house name'],
            ['- Size (ml): Bottle size in milliliters (number only)'],
            ['- Price: Price in the specified currency (number only)'],
            ['- Currency: Currency code (e.g., INR, USD, EUR)'],
            [''],
            ['OPTIONAL FIELDS (blue background):'],
            ['- Stock Status: "In Stock" or "Out of Stock" (defaults to "In Stock")'],
            ['- Product URL: Direct link to the product page'],
            ['- Item Type: "Full Bottle", "Decant", "Sample", etc. (defaults to "Full Bottle")'],
            ['- Concentration: EDP, EDT, Parfum, Cologne, etc.'],
            ['- Gender Affinity: Male, Female, or Unisex'],
            ['- Description: Brief product description'],
            ['- Notes: Comma-separated fragrance notes'],
            ['- Image URL: Direct link to product image'],
            ['- Launch Year: Year the fragrance was launched'],
            ['- Offer Details: Any special offers or discounts'],
            [''],
            ['TIPS:'],
            ['- Delete these example rows before importing your data'],
            ['- Each row represents one product listing'],
            ['- For the same perfume with multiple sizes, add separate rows'],
            ['- Make sure header names match exactly (case-insensitive)'],
        ];

        foreach ($instructions as $index => $row) {
            $instructionsSheet->setCellValue('A' . ($index + 1), $row[0]);
        }

        $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionsSheet->getStyle('A3')->getFont()->setBold(true);
        $instructionsSheet->getStyle('A10')->getFont()->setBold(true);
        $instructionsSheet->getStyle('A21')->getFont()->setBold(true);
        $instructionsSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Save the file
        $filePath = storage_path('app/public/templates/perfume_import_template.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $this->info("Template generated at: {$filePath}");

        return Command::SUCCESS;
    }
}
