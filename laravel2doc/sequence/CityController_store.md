sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CityController as CityController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: POST /resource
    R->>+CityController: store(request)
    CityController->>+V: validate(request)
    V-->>-CityController: validated data
    CityController->>+Model: create(data)
    Model->>+DB: INSERT INTO table
    DB-->>-Model: Return new record
    Model-->>-CityController: New model instance
    CityController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over CityController,Model: This sequence creates a new resource
  