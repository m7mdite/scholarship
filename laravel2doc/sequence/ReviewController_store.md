sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ReviewController as ReviewController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: POST /resource
    R->>+ReviewController: store(request)
    ReviewController->>+V: validate(request)
    V-->>-ReviewController: validated data
    ReviewController->>+Model: create(data)
    Model->>+DB: INSERT INTO table
    DB-->>-Model: Return new record
    Model-->>-ReviewController: New model instance
    ReviewController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over ReviewController,Model: This sequence creates a new resource
  