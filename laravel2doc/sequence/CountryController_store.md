sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CountryController as CountryController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: POST /resource
    R->>+CountryController: store(request)
    CountryController->>+V: validate(request)
    V-->>-CountryController: validated data
    CountryController->>+Model: create(data)
    Model->>+DB: INSERT INTO table
    DB-->>-Model: Return new record
    Model-->>-CountryController: New model instance
    CountryController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over CountryController,Model: This sequence creates a new resource
  