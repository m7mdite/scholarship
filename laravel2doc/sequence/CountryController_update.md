sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CountryController as CountryController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+CountryController: update(request, id)
    CountryController->>+V: validate(request)
    V-->>-CountryController: validated data
    CountryController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-CountryController: Model instance
    CountryController->>+Model: update(data)
    Model->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Model: Success
    Model-->>-CountryController: Updated model
    CountryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CountryController,Model: This sequence updates an existing resource
  