<?php

namespace App\Services\DataIngestion\Parsers;

use App\Services\DataIngestion\Parsers\Contracts\SourceParserInterface;
use App\Services\DataIngestion\Parsers\Exceptions\ParserException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class ExcelParserService implements SourceParserInterface
{
    protected array $errors = [];

    /**
     * Parses the Excel input data from a file path and transforms it into a structured array.
     *
     * The output array should contain data packets, each representing a perfume
     * and its associated prices, ready for insertion into staging tables.
     *
     * @param mixed $inputData File path to the Excel sheet.
     * @return array An array of structured data packets.
     * @throws ParserException if parsing fails or the file is invalid.
     */
    public function parse(mixed $inputData): array
    {
        $this->errors = []; // Reset errors for the current parse operation
        $structuredData = [];

        if (!is_string($inputData) || !file_exists($inputData) || !is_readable($inputData)) {
            throw new ParserException("Invalid or unreadable file path provided: " . $inputData);
        }

        // Define expected headers (case-insensitive keys for mapping)
        $expectedHeaders = [
            'perfume name' => ['required' => true, 'key' => 'perfume_name'],
            'brand' => ['required' => true, 'key' => 'brand'],
            'size (ml)' => ['required' => true, 'key' => 'size_ml'],
            'price' => ['required' => true, 'key' => 'price'],
            'currency' => ['required' => true, 'key' => 'currency'],
            'stock status' => ['required' => false, 'key' => 'stock_status', 'default' => 'In Stock'],
            'product url' => ['required' => false, 'key' => 'product_url'],
            'item type' => ['required' => false, 'key' => 'item_type', 'default' => 'Full Bottle'],
            'concentration' => ['required' => false, 'key' => 'concentration'],
            'gender affinity' => ['required' => false, 'key' => 'gender_affinity'],
            'description' => ['required' => false, 'key' => 'description'],
            'notes' => ['required' => false, 'key' => 'notes'],
            'image url' => ['required' => false, 'key' => 'image_url'],
            'launch year' => ['required' => false, 'key' => 'launch_year'],
            'offer details' => ['required' => false, 'key' => 'offer_details'],
        ];
        $headerMap = []; // To store the mapping of actual header index to our expected key

        try {
            $spreadsheet = IOFactory::load($inputData);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestDataRow();

            if ($highestRow < 2) { // Must have at least a header row and one data row
                $this->errors[] = "The Excel sheet is empty or contains only a header row.";
                return [];
            }
            
            // Process Header Row
            $headerRow = $sheet->rangeToArray('A1:' . $sheet->getHighestDataColumn() . '1', null, true, false, true)[1];
            $actualHeaders = array_map(fn($h) => trim(strtolower($h ?? '')), $headerRow);

            foreach ($expectedHeaders as $expectedKey => $config) {
                $columnIndex = array_search($expectedKey, $actualHeaders);
                if ($columnIndex !== false) {
                    $headerMap[$config['key']] = $columnIndex; // Map our key to the actual column letter/index
                } elseif ($config['required']) {
                    $this->errors[] = "Required header column '{$expectedKey}' not found.";
                }
            }

            if (!empty($this->errors)) {
                 // If required headers are missing, we cannot proceed reliably.
                throw new ParserException("Missing required header columns. Check errors for details.");
            }
            
            // Process Data Rows
            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                $rowDataArray = $sheet->rangeToArray('A' . $rowIndex . ':' . $sheet->getHighestDataColumn() . $rowIndex, null, true, false, true)[$rowIndex];
                $item = [];
                $perfumeDetails = [];
                $priceDetails = [];

                $hasRequiredData = true;

                foreach ($expectedHeaders as $expectedInternalKey => $config) {
                    $keyToUse = $config['key'];
                    $columnIndex = $headerMap[$keyToUse] ?? null;
                    $value = null;

                    if ($columnIndex !== null && isset($rowDataArray[$columnIndex])) {
                        $value = trim($rowDataArray[$columnIndex]);
                    }

                    if (empty($value) && $config['required']) {
                        $this->errors[] = "Row {$rowIndex}: Required value for '{$expectedInternalKey}' is missing.";
                        $hasRequiredData = false; // Mark this row as problematic for required fields
                        continue; // Skip this column for this row if required and empty
                    } elseif (empty($value) && isset($config['default'])) {
                        $value = $config['default'];
                    } elseif (empty($value)) {
                        $value = null; // Ensure empty optional fields are null
                    }
                    
                    // Assign to perfume or price details based on the key
                    if (in_array($keyToUse, ['perfume_name', 'brand', 'description', 'notes', 'image_url', 'concentration', 'gender_affinity', 'launch_year'])) {
                        $perfumeDetails[$keyToUse] = $value;
                    } elseif (in_array($keyToUse, ['size_ml', 'price', 'currency', 'stock_status', 'product_url', 'item_type', 'offer_details'])) {
                        // Basic type casting for known numeric fields
                        if ($keyToUse === 'size_ml' && $value !== null) $value = (int)$value;
                        if ($keyToUse === 'price' && $value !== null) $value = (float)$value;
                        if ($keyToUse === 'launch_year' && $value !== null) $value = (int)$value;
                        $priceDetails[$keyToUse] = $value;
                    }
                }

                if (!$hasRequiredData) {
                    // If a row is missing required data, log it as an error for this row and skip adding it.
                    // The specific missing field errors are already in $this->errors.
                    $this->errors[] = "Row {$rowIndex}: Skipped due to missing required information.";
                    continue;
                }
                
                if (!empty($perfumeDetails) && !empty($priceDetails)) {
                    // Combine perfume details with its price(s)
                    // For now, assuming one price entry per row in the Excel.
                    // If a perfume can have multiple prices in the same sheet, this structure needs adjustment.
                    $structuredData[] = array_merge(
                        $perfumeDetails,
                        ['prices' => [$priceDetails]] // Embed price details in a 'prices' array
                    );
                } elseif (!empty($perfumeDetails) || !empty($priceDetails)) {
                    // If we have some data but not enough to form a complete perfume/price entry
                    $this->errors[] = "Row {$rowIndex}: Incomplete data. Perfume name, brand, size, price, and currency are essential.";
                }
            }

            if (empty($structuredData) && $highestRow > 1 && empty($this->errors)) {
                $this->errors[] = "No data could be structured from the sheet. Please check the file content and format against the expected standard.";
            }

        } catch (ReaderException $e) {
            throw new ParserException("Failed to read the Excel file: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            // Catch any other unexpected errors during parsing
            throw new ParserException("An unexpected error occurred during Excel parsing: " . $e->getMessage(), 0, $e);
        }

        return $structuredData;
    }

    /**
     * Returns a unique identifier for the data source this parser handles.
     * This identifier will be used to tag data in the staging tables.
     *
     * @return string The unique source identifier.
     */
    public function getSourceIdentifier(): string
    {
        // This identifier can be made more dynamic if needed, e.g., based on filename patterns
        // or if the Excel sheet itself contains a seller identifier.
        // For now, it's generic for all Excel imports via this parser.
        return 'excel_import_standard_v1';
    }

    /**
     * Retrieves any non-critical errors accumulated during the parsing process.
     *
     * @return array An array of error messages or error objects.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}