sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant Model as Model
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+ScholarshipController: destroy(id)
    ScholarshipController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-ScholarshipController: Model instance
    ScholarshipController->>+Model: delete()
    Model->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Model: Success
    Model-->>-ScholarshipController: Success
    ScholarshipController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over ScholarshipController,Model: This sequence removes a resource
  