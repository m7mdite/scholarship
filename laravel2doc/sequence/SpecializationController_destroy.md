sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant SpecializationController as SpecializationController
    participant Model as Model
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+SpecializationController: destroy(id)
    SpecializationController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-SpecializationController: Model instance
    SpecializationController->>+Model: delete()
    Model->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Model: Success
    Model-->>-SpecializationController: Success
    SpecializationController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over SpecializationController,Model: This sequence removes a resource
  