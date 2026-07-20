sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant SpecializationController as SpecializationController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+SpecializationController: update(request, id)
    SpecializationController->>+V: validate(request)
    V-->>-SpecializationController: validated data
    SpecializationController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-SpecializationController: Model instance
    SpecializationController->>+Model: update(data)
    Model->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Model: Success
    Model-->>-SpecializationController: Updated model
    SpecializationController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over SpecializationController,Model: This sequence updates an existing resource
  