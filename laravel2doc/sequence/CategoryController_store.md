sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CategoryController as CategoryController
    participant V as Validator
    participant Category as Category
    participant DB as Database
    
    C->>R: POST /resource
    R->>+CategoryController: store(request)
    CategoryController->>+V: validate(request)
    V-->>-CategoryController: validated data
    CategoryController->>+Category: create(data)
    Category->>+DB: INSERT INTO table
    DB-->>-Category: Return new record
    Category-->>-CategoryController: New model instance
    CategoryController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over CategoryController,Category: This sequence creates a new resource
  