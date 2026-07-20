sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CategoryController as CategoryController
    participant V as Validator
    participant Category as Category
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+CategoryController: update(request, id)
    CategoryController->>+V: validate(request)
    V-->>-CategoryController: validated data
    CategoryController->>+Category: find(id)
    Category->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Category: Return record
    Category-->>-CategoryController: Model instance
    CategoryController->>+Category: update(data)
    Category->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Category: Success
    Category-->>-CategoryController: Updated model
    CategoryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CategoryController,Category: This sequence updates an existing resource
  