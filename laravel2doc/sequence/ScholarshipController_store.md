sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: POST /resource
    R->>+ScholarshipController: store(request)
    ScholarshipController->>+V: validate(request)
    V-->>-ScholarshipController: validated data
    ScholarshipController->>+Model: create(data)
    Model->>+DB: INSERT INTO table
    DB-->>-Model: Return new record
    Model-->>-ScholarshipController: New model instance
    ScholarshipController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over ScholarshipController,Model: This sequence creates a new resource
  