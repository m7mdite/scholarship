sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CategoryController as CategoryController
    participant Category as Category
    participant DB as Database
    
    C->>R: GET /resource/{id}
    R->>+CategoryController: show(id)
    CategoryController->>+Category: find(id) / findOrFail(id)
    Category->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Category: Return record
    Category-->>-CategoryController: Model instance
    CategoryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CategoryController,Category: This sequence retrieves a specific resource by ID
  