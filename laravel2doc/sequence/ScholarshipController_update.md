sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+ScholarshipController: update(request, id)
    ScholarshipController->>+V: validate(request)
    V-->>-ScholarshipController: validated data
    ScholarshipController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-ScholarshipController: Model instance
    ScholarshipController->>+Model: update(data)
    Model->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Model: Success
    Model-->>-ScholarshipController: Updated model
    ScholarshipController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over ScholarshipController,Model: This sequence updates an existing resource
  