# Parser/Adaptor Architecture for Data Ingestion

**Date:** 2025-06-14
**Version:** 1.0
**Status:** Proposed

## 1. Introduction

This document outlines the architectural design for the modular, source-specific parsers/adaptors within the ScentSeeker Laravel application. These components are responsible for fetching raw data from various seller sources, parsing it, and transforming it into a standardized format suitable for insertion into the data staging area, as defined in the [`docs/data_ingestion_strategy.md`](docs/data_ingestion_strategy.md:1).

The primary goals of this architecture are:
*   **Modularity:** Each data source will have its own dedicated parser, allowing for independent development, testing, and maintenance.
*   **Consistency:** A common interface will ensure all parsers adhere to a defined contract for input, output, and core functionality.
*   **Extensibility:** The design should make it straightforward to add new parsers for additional data sources in the future.
*   **Integration:** Parsers will integrate with Laravel's configuration, logging, and error handling mechanisms.

## 2. Common Interface and Abstract Class

To ensure consistency and provide a clear contract for all parsers, we will define a PHP interface. An optional abstract class can be provided to share common functionalities.

### 2.1. `SourceParserInterface`

This interface will define the essential methods that every source-specific parser must implement.

```php
namespace App\Services\DataIngestion\Parsers\Contracts;

interface SourceParserInterface
{
    /**
     * Parses the raw input data from a source and transforms it into a structured array.
     *
     * The output array should contain data packets, each representing a perfume
     * and its associated prices, ready for insertion into staging tables.
     *
     * @param mixed $inputData Raw data from the source (e.g., API response string/array, file path).
     * @return array An array of structured data packets.
     * @throws \App\Services\DataIngestion\Parsers\Exceptions\ParserException if parsing fails.
     */
    public function parse(mixed $inputData): array;

    /**
     * Returns a unique identifier for the data source this parser handles.
     * This identifier will be used to tag data in the staging tables (e.g., "seller_x_api_v1", "seller_y_csv_daily").
     *
     * @return string The unique source identifier.
     */
    public function getSourceIdentifier(): string;

    /**
     * Retrieves any errors accumulated during the parsing process for the last operation.
     * Alternatively, parsers can throw specific exceptions for critical errors.
     *
     * @return array An array of error messages or structured error objects.
     */
    public function getErrors(): array;
}
```

### 2.2. `AbstractSourceParser` (Optional)

An abstract class can be created to provide common helper methods or properties that many parsers might use. This class would implement `SourceParserInterface`.

```php
namespace App\Services\DataIngestion\Parsers;

use App\Services\DataIngestion\Parsers\Contracts\SourceParserInterface;
use Illuminate\Support\Facades\Log;

abstract class AbstractSourceParser implements SourceParserInterface
{
    protected array $errors = [];
    protected string $sourceIdentifier;

    public function __construct(string $sourceIdentifier)
    {
        $this->sourceIdentifier = $sourceIdentifier;
    }

    public function getSourceIdentifier(): string
    {
        return $this->sourceIdentifier;
    }

    public function getErrors(): array
    {
        $tempErrors = $this->errors;
        $this->errors = []; // Clear errors after retrieval
        return $tempErrors;
    }

    protected function addError(string $message, array $context = []): void
    {
        $this->errors[] = $message;
        Log::warning("Parser error for source '{$this->sourceIdentifier}': {$message}", $context);
    }

    // Concrete parsers must implement the parse method
    // abstract public function parse(mixed $inputData): array;
}
```
Using an abstract class is beneficial for centralizing logging logic, error accumulation, or common data transformation utilities. Individual parsers would then extend this abstract class.

## 3. Structure of Individual Parsers

### 3.1. Naming Convention

Parsers will be named descriptively, indicating the source and type if applicable:
*   `SellerXApiParser.php`
*   `VendorYCsvParser.php`
*   `AffiliateZXmlFeedParser.php`

### 3.2. Directory Structure

Parser classes will reside within the Laravel application structure under:
`app/Services/DataIngestion/Parsers/`

Specific parser implementations would be in this directory, e.g.:
`app/Services/DataIngestion/Parsers/SellerXApiParser.php`

Custom exceptions related to parsing will be in:
`app/Services/DataIngestion/Parsers/Exceptions/`
*   `ParserException.php` (base exception)
*   `InvalidDataFormatException.php`
*   `SourceConnectionException.php`

## 4. Input to Parsers

The `parse(mixed $inputData)` method will accept various types of input depending on the data source:
*   **API-based sources:** Raw JSON/XML response string, or a pre-decoded PHP array/object.
*   **File-based sources (CSV, Excel, XML):** File path to the data file, or a file stream resource.
*   **Web Scraped Data:** HTML content string or a DOM crawler object.

The service responsible for invoking the parser (e.g., a Laravel Job) will handle fetching the raw data and passing it to the parser.

## 5. Output of Parsers

The `parse()` method must return an array of "data packets". Each packet represents a single perfume entity and its associated price information, structured to align with the `staging_perfumes` and `staging_prices` tables.

### 5.1. Output Structure Example

```php
[ // Array of data packets
    [
        // --- Common packet metadata ---
        'source_identifier' => 'seller_x_api_v1', // Automatically set or verified by the parser
        'raw_data_payload' => '{ "id": "prod123", "name": "Eau de Parfum XYZ", ... }', // For staging_perfumes.raw_data_payload

        // --- Perfume Data (for staging_perfumes) ---
        'perfume_data' => [
            'seller_provided_perfume_id' => 'prod123', // Crucial for linking
            'perfume_name_raw' => 'Eau de Parfum XYZ',
            'brand_name_raw' => 'Luxury Brand',
            'concentration_raw' => 'Eau de Parfum',
            'size_raw' => '100ml',
            'gender_raw' => 'Unisex',
            'description_raw' => 'A captivating scent...',
            'notes_raw' => '{"top": "Bergamot", "middle": "Rose", "base": "Sandalwood"}',
            'image_url_raw' => 'https://example.com/image.jpg',
            'seller_product_url_raw' => 'https://example.com/product/prod123',
            'category_raw' => 'Niche',
            'sku_raw' => 'SKU5678',
            // ... any other relevant fields from source, mapped to *_raw columns
        ],

        // --- Price Data (for staging_prices) ---
        'price_data' => [ // An array, as one perfume can have multiple price entries (e.g., different sizes from same product page)
            [
                'seller_provided_perfume_id' => 'prod123', // To link back to perfume_data if needed before staging IDs exist
                'price_raw' => '150.00',
                'currency_raw' => 'USD',
                'discount_price_raw' => null,
                'availability_raw' => 'In Stock',
                'seller_specific_price_id' => 'price_abc', // Optional, if source provides it
                // ... other price-specific *_raw fields
            ],
            // Potentially more price entries for the same perfume from this source
        ]
    ],
    // ... more data packets for other perfumes from the same source
]
```

### 5.2. Handling Related Entities

*   Each element in the array returned by `parse()` corresponds to one primary entity (a perfume).
*   Price information, being related to a perfume, is nested within the perfume's data packet.
*   The ingestion service consuming this output will be responsible for:
    1.  Inserting a record into `staging_perfumes` using `perfume_data` and `raw_data_payload`.
    2.  Retrieving the `id` of the newly inserted `staging_perfumes` record.
    3.  Iterating through `price_data` and inserting records into `staging_prices`.
    4.  The `staged_perfume_identifier` in `staging_prices` (as per [`docs/data_ingestion_strategy.md`](docs/data_ingestion_strategy.md:103)) can be populated using a combination of `source_identifier` and `seller_provided_perfume_id` from the `perfume_data` block, or by the ID of the corresponding `staging_perfumes` record once it's created.

## 6. Configuration Management

Source-specific configurations will be managed using Laravel's standard mechanisms:

*   **Environment Variables (`.env`):** For sensitive information like API keys, authentication tokens.
    *   Example: `SELLER_X_API_KEY=your_api_key`
*   **Configuration Files (in `config/` directory):**
    *   A dedicated config file, e.g., `config/data_sources.php` or `config/parsers.php`, can store non-sensitive configurations like API endpoint URLs, rate limits, or default settings for various sources.
    ```php
    // config/data_sources.php
    return [
        'seller_x_api_v1' => [
            'driver' => 'api', // or 'csv', 'xml'
            'parser' => App\Services\DataIngestion\Parsers\SellerXApiParser::class,
            'api_endpoint' => env('SELLER_X_API_ENDPOINT', 'https://api.sellerx.com/v1/products'),
            'api_key' => env('SELLER_X_API_KEY'),
            // Other specific settings like request parameters, pagination style
        ],
        'vendor_y_csv_daily' => [
            'driver' => 'csv',
            'parser' => App\Services\DataIngestion\Parsers\VendorYCsvParser::class,
            'file_path_pattern' => '/path/to/vendor_y/data_{date}.csv',
            'column_mapping' => [ // For complex mappings
                'productName' => 'perfume_name_raw',
                'brand' => 'brand_name_raw',
                'priceUSD' => 'price_raw',
                // ...
            ],
        ],
    ];
    ```
    Parsers can access these configurations via the `config()` helper or dependency injection of `Illuminate\Contracts\Config\Repository`.
*   **Database-driven Configuration:** For highly dynamic configurations or mappings that need to be updated by non-developers (e.g., via an admin panel), storing configurations in the database is an option. This would typically involve creating a dedicated table for source configurations. For the initial implementation, file-based configuration is recommended for simplicity.

## 7. Error Handling and Logging

Robust error handling and logging are crucial for a reliable ingestion pipeline.

*   **Parser-Level Errors:**
    *   Parsers should handle errors specific to their source (e.g., API connection issues, file not found, unexpected data format, missing required fields in the source data).
    *   Use custom exceptions (e.g., `InvalidDataFormatException`, `SourceConnectionException` extending a base `ParserException`) to signal critical failures that prevent parsing.
    *   For non-critical issues (e.g., a single record in a large file is malformed but others are okay), the parser can log the error using `Log::warning()` or `addError()` (if using the `AbstractSourceParser`) and attempt to continue with other records. The `error_details` field in the staging table can store this information.
*   **Logging:**
    *   All parsers should integrate with Laravel's logging system (`Illuminate\Support\Facades\Log`).
    *   Log significant events, such as the start and end of a parsing job, number of records processed, and any errors encountered.
    *   Contextual information (source identifier, specific item ID from source if available) should be included in log messages.
    *   Example: `Log::error('Failed to parse item from Seller X API', ['source' => $this->getSourceIdentifier(), 'itemId' => $item['id'], 'exception' => $e]);`
*   **Ingestion Service Responsibility:** The service calling the parser (e.g., a Laravel Job) will be responsible for catching exceptions thrown by the parser, logging them, and potentially marking the entire batch or specific records as failed in the staging area (updating `validation_status` and `error_details`).

## 8. Conceptual Example: CSV Parser

This example outlines how a hypothetical `ExampleCsvParser.php` might work.

```php
namespace App\Services\DataIngestion\Parsers;

use App\Services\DataIngestion\Parsers\Contracts\SourceParserInterface;
use App\Services\DataIngestion\Parsers\Exceptions\InvalidDataFormatException;
use App\Services\DataIngestion\Parsers\Exceptions\ParserException;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader; // Example CSV library

class ExampleCsvParser extends AbstractSourceParser // Or implements SourceParserInterface directly
{
    private array $columnMapping;

    public function __construct(string $sourceIdentifier, array $columnMapping = [])
    {
        parent::__construct($sourceIdentifier); // If extending AbstractSourceParser
        // $this->sourceIdentifier = $sourceIdentifier; // If implementing interface directly
        $this->columnMapping = $columnMapping; // e.g., ['ProductName' => 'perfume_name_raw', ...]
    }

    public function parse(mixed $inputData): array
    {
        if (!is_string($inputData) || !file_exists($inputData)) {
            throw new ParserException("Invalid input data: File path expected for CSV parser. Given: " . print_r($inputData, true));
        }

        $filePath = $inputData;
        $parsedDataPackets = [];

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0); // Assumes first row is header
            $records = $csv->getRecords();

            foreach ($records as $index => $record) {
                $rawPayload = json_encode($record); // Store the original CSV row as JSON
                $perfumeDetails = [];
                $priceDetails = [];

                // Basic validation: check for essential fields based on mapping
                if (empty($record[$this->getKeyForField('perfume_name_raw', 'DefaultPerfumeNameColumn')])) {
                    $this->addError("Skipping record at index {$index} due to missing perfume name.", ['record' => $record]);
                    continue;
                }

                // Map CSV columns to perfume_data fields
                $perfumeDetails['seller_provided_perfume_id'] = $record[$this->getKeyForField('seller_provided_perfume_id', 'ProductID')] ?? "csv_item_{$index}";
                $perfumeDetails['perfume_name_raw'] = $record[$this->getKeyForField('perfume_name_raw', 'ProductName')] ?? null;
                $perfumeDetails['brand_name_raw'] = $record[$this->getKeyForField('brand_name_raw', 'Brand')] ?? null;
                // ... map other perfume fields ...

                // Map CSV columns to price_data fields
                $currentPrice = [
                    'seller_provided_perfume_id' => $perfumeDetails['seller_provided_perfume_id'],
                    'price_raw' => $record[$this->getKeyForField('price_raw', 'Price')] ?? null,
                    'currency_raw' => $record[$this->getKeyForField('currency_raw', 'Currency')] ?? 'USD', // Default currency
                    'availability_raw' => $record[$this->getKeyForField('availability_raw', 'Availability')] ?? 'unknown',
                    // ... map other price fields ...
                ];
                $priceDetails[] = $currentPrice;


                $parsedDataPackets[] = [
                    'source_identifier' => $this->getSourceIdentifier(),
                    'raw_data_payload' => $rawPayload,
                    'perfume_data' => $perfumeDetails,
                    'price_data' => $priceDetails,
                ];
            }
        } catch (\Exception $e) {
            Log::error("CSV Parsing failed for source {$this->getSourceIdentifier()}: " . $e->getMessage(), ['file' => $filePath]);
            throw new ParserException("Error parsing CSV file {$filePath}: " . $e->getMessage(), 0, $e);
        }

        Log::info("Successfully parsed {$filePath} for source {$this->getSourceIdentifier()}. Found " . count($parsedDataPackets) . " items.");
        return $parsedDataPackets;
    }

    /**
     * Helper to get the actual CSV column name from the mapping or use a default.
     */
    private function getKeyForField(string $targetField, string $defaultCsvColumn): string
    {
        return array_search($targetField, $this->columnMapping) ?: $defaultCsvColumn;
    }
}
```

This conceptual example demonstrates:
*   Implementation of `SourceParserInterface` (or extension of `AbstractSourceParser`).
*   Handling of file path input.
*   Usage of a CSV parsing library.
*   Mapping CSV columns to the standardized output structure.
*   Basic error handling and logging.
*   Construction of the data packet array.

## 9. Data Flow with Parsers

The following diagram illustrates where the parsers fit into the overall data ingestion flow:

```mermaid
graph TD
    subgraph External Sources
        A1[Seller X API]
        A2[Seller Y CSV Feed]
        A3[Seller Z XML Feed]
    end

    subgraph Laravel Application - Ingestion Pipeline
        B1[API Fetch Job/Service] --> C1{SellerXApiParser}
        B2[CSV Download Job/Service] --> C2{VendorYCsvParser}
        B3[XML Fetch Job/Service] --> C3{AffiliateZXmlFeedParser}

        C1 -- Parsed Data Packet --> D[Staging Service]
        C2 -- Parsed Data Packet --> D
        C3 -- Parsed Data Packet --> D

        D -- Formatted Staging Records --> E[Staging Area Tables <br/> (staging_perfumes, staging_prices)]
    end

    E --> F[Validation Engine]
    F --> G[Cleaning & Transformation]
    G --> H[De-duplication & Matching]
    H --> I[Load to Production Tables]

    style C1 fill:#f9f,stroke:#333,stroke-width:2px
    style C2 fill:#f9f,stroke:#333,stroke-width:2px
    style C3 fill:#f9f,stroke:#333,stroke-width:2px
```

This diagram highlights that each source type will have a dedicated mechanism to fetch/retrieve data, which is then passed to its specific parser. The parser's output (standardized data packets) is then handed off to a common "Staging Service" responsible for populating the staging tables.

## 10. Next Steps

1.  Review and approve this architectural design.
2.  Proceed with the implementation of the `SourceParserInterface` and optionally the `AbstractSourceParser`.
3.  Begin development of individual parsers for prioritized data sources.
4.  Develop the "Staging Service" that consumes parser output and interacts with staging tables.