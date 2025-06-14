# Data Ingestion Strategy: Staging Area Design

**Phase:** 2 - Enhancing Comparison & User Features
**Task:** Advanced Seller Integration & Data Ingestion
**Focus:** Overall Strategy & Data Staging Area Design

## 1. Purpose of the Data Staging Area

A dedicated data staging area is a critical component of a robust data ingestion pipeline, especially when integrating data from multiple, heterogeneous external sources like perfume sellers. Its primary purposes are:

*   **Isolation:** It acts as an intermediary buffer, preventing raw, unverified, and potentially "dirty" data from directly impacting the integrity and performance of the live production database.
*   **Data Validation:** Provides a controlled environment to systematically check incoming data against predefined rules, ensuring data types, formats, completeness, and consistency before it's considered for production use.
*   **Data Cleaning:** Facilitates processes to handle inconsistencies, errors, missing values, and standardize varying data representations from different sellers.
*   **Data Transformation:** Allows for the conversion of seller-specific data structures and terminologies into the standardized schema required by the production tables (e.g., `perfumes`, `prices`).
*   **De-duplication & Matching:** Enables sophisticated logic to identify and match incoming perfume data with existing entries in the production database, preventing redundant records and ensuring accurate price associations.
*   **Traceability & Auditing:** By storing raw data and tracking processing steps, it allows for better data lineage, easier debugging of ingestion issues, and the ability to re-process data if necessary.
*   **Performance Optimization:** Offloads resource-intensive validation, cleaning, and transformation tasks from the main application servers and production database, ensuring the user-facing application remains responsive.
*   **Facilitating Manual Review:** Provides a workspace where data that fails automated checks or requires manual verification can be reviewed and corrected without affecting live data.

## 2. Staging Area Design Options

Two primary options were considered for implementing the staging area:

### Option A: Separate Staging Tables

*   **Description:** This approach involves creating a distinct set of tables in the database specifically for staging data, mirroring the structure of the main production tables but with additional metadata columns. For example: `staging_perfumes`, `staging_prices`, `staging_sellers`.
*   **Key Characteristics:**
    *   Each staging table would include columns for all data points received from sellers.
    *   Additional metadata columns would track the ingestion process (e.g., `source_identifier`, `raw_data_payload`, `validation_status`, `processing_status`, `error_details`, `import_batch_id`, `is_matched`, `matched_production_perfume_id`).
*   **Pros:**
    *   **Strong Data Integrity:** Clear separation between raw/in-process data and clean production data. Production tables are not "polluted."
    *   **Simplified Processing Logic:** Queries and operations on staging data do not need complex conditions to filter out production data, leading to cleaner and potentially more performant processing.
    *   **Schema Flexibility:** Staging table schemas can evolve to accommodate new seller data fields or temporary processing attributes without altering production schemas.
    *   **Reduced Risk:** Intensive write, update, and delete operations during cleaning and transformation occur on separate tables, minimizing risk and load on production tables.
    *   **Efficient Batch Processing:** Ideal for handling large volumes of data from multiple feeds.
    *   **Clear Manual Review Process:** Data requiring manual intervention is clearly isolated.
*   **Cons:**
    *   Requires additional database objects (more tables).
    *   Temporary data duplication until data is processed and cleared from staging.

### Option B: Flags in Main Production Tables

*   **Description:** This approach involves using the existing production tables (`perfumes`, `prices`, etc.) and adding flag columns (e.g., `is_staged`, `validation_status`, `source_id`) to differentiate new, unprocessed records from live, validated records.
*   **Pros:**
    *   Fewer database objects.
    *   Potentially simpler data "promotion" (updating flags) once processed.
*   **Cons:**
    *   **Compromised Data Integrity:** Production tables contain a mix of validated and unvalidated data, increasing the risk of exposing incorrect data or impacting application logic.
    *   **Complex Queries:** Application queries and processing logic become more complex, needing to constantly filter by status flags, which can degrade performance.
    *   **Schema Rigidity:** Production table schemas become bloated with staging-specific columns.
    *   **Higher Risk During Processing:** Cleaning and transformation operations directly on production tables are inherently riskier.
    *   **Difficult De-duplication:** Identifying and merging duplicates within the same table that also serves live data is more challenging and error-prone.

### Recommendation: Separate Staging Tables

**The recommended approach is Option A: Separate Staging Tables.**

**Justification:**

The benefits of data integrity, ease of processing, operational safety, and scalability offered by separate staging tables significantly outweigh the cons, especially for a system aiming to integrate data from diverse and potentially unreliable sources. This approach provides a robust foundation for the data ingestion pipeline, ensuring that only clean, validated, and de-duplicated data reaches the production environment. It aligns best with best practices for ETL (Extract, Transform, Load) processes.

## 3. Key Attributes for Staging Data (Assuming Separate Staging Tables)

Staging tables (e.g., `staging_perfumes`, `staging_prices`) should include columns to store the actual data from sellers, as well as metadata to manage the ingestion workflow.

### Common Metadata Attributes (for all staging tables):

*   `id`: (Primary Key) Unique identifier for the staging record.
*   `import_batch_id`: (UUID/String) Groups records from a single import run/file.
*   `source_identifier`: (String) Identifies the seller or data feed (e.g., "seller_X_api_v2", "seller_Y_daily_csv").
*   `raw_data_payload`: (JSON/TEXT) Stores the original, unaltered data chunk received from the source for auditing and re-processing.
*   `validation_status`: (ENUM/String) e.g., 'pending', 'passed', 'failed_validation', 'passed_with_warnings'.
*   `processing_status`: (ENUM/String) e.g., 'new', 'validated', 'transformed', 'matching_pending', 'matched_existing', 'matched_new_perfume', 'imported_to_production', 'error_processing', 'requires_manual_review'.
*   `error_details`: (JSON/TEXT) Stores structured error messages or codes from validation or processing steps.
*   `is_duplicate_of_staged_id`: (Nullable Foreign Key to self) If intra-staging-batch duplicates are found before full processing.
*   `matched_production_perfume_id`: (Nullable Foreign Key to `perfumes.id`) Links to the master perfume record in production if a match is found.
*   `matched_production_price_id`: (Nullable Foreign Key to `prices.id`) Links to the price record in production if applicable (e.g., for price updates).
*   `confidence_score`: (Float, Nullable) A score indicating the confidence of a match during de-duplication.
*   `imported_at`: (Timestamp) When the raw data was fetched from the source.
*   `processed_at`: (Timestamp) Timestamp of the last processing attempt on this record.
*   `created_at`, `updated_at`: (Timestamps) Standard record timestamps for the staging entry.

### Example Attributes for `staging_perfumes`:

*   All common metadata attributes listed above.
*   `seller_provided_perfume_id`: (String) Seller's unique identifier for the product.
*   `perfume_name_raw`: (String) As provided by seller.
*   `brand_name_raw`: (String) As provided by seller.
*   `concentration_raw`: (String) e.g., "Eau de Parfum", "EDT".
*   `size_raw`: (String) e.g., "100ml", "3.4oz".
*   `gender_raw`: (String) e.g., "Men's", "Pour Femme", "Unisex".
*   `description_raw`: (TEXT)
*   `notes_raw`: (JSON/TEXT) Could be structured (top, middle, base) or unstructured.
*   `image_url_raw`: (String)
*   `seller_product_url_raw`: (String)
*   `category_raw`: (String) Seller's categorization.
*   `sku_raw`: (String) Seller's SKU.
*   *... (any other relevant fields provided by sellers)*

### Example Attributes for `staging_prices`:

*   All common metadata attributes listed above.
*   `staged_perfume_identifier`: (String) A way to link this price to its corresponding record in `staging_perfumes` (e.g., using `seller_provided_perfume_id` + `source_identifier`).
*   `price_raw`: (String/Decimal) Price as provided.
*   `currency_raw`: (String) e.g., "USD", "EUR".
*   `discount_price_raw`: (String/Decimal, Nullable)
*   `availability_raw`: (String) e.g., "in stock", "out of stock", "preorder".
*   `seller_specific_price_id`: (String, Nullable) Seller's unique ID for this price entry, if any.
*   *... (any other relevant fields like sale start/end dates)*

## 4. Data Flow Outline

The conceptual flow of data through the ingestion pipeline will be as follows:

```mermaid
graph TD
    A[Raw Data from Sellers <br/> (API, CSV, XML, Web Scrape etc.)] --> B{Source-Specific Parsers/Adaptors};
    B --> C[Staging Area <br/> (e.g., staging_perfumes, staging_prices) <br/> - Data stored with metadata];
    C --> D{Validation Engine <br/> - Checks data types, formats, required fields};
    D -- Valid Data --> E{Cleaning & Transformation Engine <br/> - Standardizes values, formats, units};
    D -- Invalid/Needs Review --> F[Manual Review Queue / Error Log <br/> - Records flagged for attention];
    E --> G{De-duplication & Matching Engine <br/> - Compares against existing Staging & Production Data};
    G -- New Unique Perfume --> H[Load to Production Tables <br/> (Create new `perfumes` record, then `prices`)];
    G -- Matched Existing Perfume --> I[Update/Add Price to Production <br/> (Add new `prices` record linked to existing `perfumes` ID, or update existing price)];
    G -- Ambiguous Match --> F;
    F -- Corrected/Approved --> C; # Re-queue for processing
```

## 5. Considerations for Parsers/Adaptors

The chosen staging area design (Separate Staging Tables) directly facilitates the development and operation of modular, source-specific parsers/adaptors:

*   **Unified Target Schema:** All parsers, regardless of the source data's format (CSV, JSON, XML, etc.), will have a common goal: to transform their specific input into the standardized structure of the staging tables (e.g., `staging_perfumes`, `staging_prices`).
*   **Decoupling:** Parsers are decoupled from the complex business logic of validation (beyond basic structural checks), cleaning, transformation, de-duplication, and loading into production. Their primary responsibility is extraction and basic structural mapping.
*   **Simplified Parser Logic:** Parsers can focus on accurately extracting data from their specific source and populating the `*_raw` fields and essential identifiers in the staging tables. The `raw_data_payload` column allows for storing the original data, meaning parsers don't need to perfectly handle every nuance initially; subsequent processing steps can refine the data.
*   **Independent Development & Maintenance:** Each parser can be developed, tested, and updated independently. If a seller changes their feed format, only the corresponding parser needs modification, without impacting other parts of the ingestion pipeline.
*   **Error Isolation & Re-processing:** The `source_identifier` and `raw_data_payload` in the staging tables are crucial. If a parser introduces errors, it's easier to identify affected records. The `raw_data_payload` allows for re-processing of data from a specific source or batch if a parser's logic is fixed or improved, without needing to re-fetch from the original seller.
*   **Scalability for New Sources:** Adding a new seller/data source primarily involves developing a new parser that maps to the existing staging table structure. The rest of the pipeline (validation, cleaning, matching, loading) remains largely the same.

This staging area design provides a flexible and robust foundation for building out the individual parsers/adaptors required for each data source, which will be detailed in a subsequent task.