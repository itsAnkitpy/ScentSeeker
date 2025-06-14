<?php

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
     * @return array An array of error messages or error objects.
     */
    public function getErrors(): array;
}