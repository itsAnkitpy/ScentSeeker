<?php

namespace App\Services\DataIngestion\Parsers;

use App\Services\DataIngestion\Parsers\Contracts\SourceParserInterface;
use Illuminate\Support\Facades\Log; // Included as per the architecture document, though not directly used in this abstract class snippet.

abstract class AbstractSourceParser implements SourceParserInterface
{
    protected array $errors = [];
    protected string $sourceIdentifier;

    public function __construct(string $sourceIdentifier)
    {
        $this->sourceIdentifier = $sourceIdentifier;
    }

    /**
     * Parses the raw input data from a source and transforms it into a structured array.
     *
     * Concrete implementations must define this method.
     *
     * @param mixed $inputData Raw data from the source.
     * @return array An array of structured data packets.
     * @throws \App\Services\DataIngestion\Parsers\Exceptions\ParserException if parsing fails.
     */
    abstract public function parse(mixed $inputData): array;

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

    /**
     * Helper method to add an error.
     *
     * @param string $message
     * @return void
     */
    protected function addError(string $message): void
    {
        $this->errors[] = $message;
        // Optionally log the error as well
        // Log::error("Parser Error ({$this->sourceIdentifier}): {$message}");
    }
}