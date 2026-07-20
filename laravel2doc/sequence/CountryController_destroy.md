sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CountryController as CountryController
    participant Model as Model
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+CountryController: destroy(id)
    CountryController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-CountryController: Model instance
    CountryController->>+Model: delete()
    Model->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Model: Success
    Model-->>-CountryController: Success
    CountryController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over CountryController,Model: This sequence removes a resource
  