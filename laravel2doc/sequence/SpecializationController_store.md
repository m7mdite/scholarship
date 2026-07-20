sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant SpecializationController as SpecializationController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: POST /resource
    R->>+SpecializationController: store(request)
    SpecializationController->>+V: validate(request)
    V-->>-SpecializationController: validated data
    SpecializationController->>+Model: create(data)
    Model->>+DB: INSERT INTO table
    DB-->>-Model: Return new record
    Model-->>-SpecializationController: New model instance
    SpecializationController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over SpecializationController,Model: This sequence creates a new resource
  