sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CategoryController as CategoryController
    participant Category as Category
    participant DB as Database
    
    C->>R: GET /resource
    R->>+CategoryController: index()
    CategoryController->>+Category: all() / get() / paginate()
    Category->>+DB: SELECT * FROM table
    DB-->>-Category: Return records
    Category-->>-CategoryController: Collection of models
    CategoryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CategoryController,Category: This sequence retrieves a list of resources
  