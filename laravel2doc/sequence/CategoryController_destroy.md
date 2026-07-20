sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CategoryController as CategoryController
    participant Category as Category
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+CategoryController: destroy(id)
    CategoryController->>+Category: find(id)
    Category->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Category: Return record
    Category-->>-CategoryController: Model instance
    CategoryController->>+Category: delete()
    Category->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Category: Success
    Category-->>-CategoryController: Success
    CategoryController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over CategoryController,Category: This sequence removes a resource
  