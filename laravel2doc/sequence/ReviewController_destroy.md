sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ReviewController as ReviewController
    participant Model as Model
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+ReviewController: destroy(id)
    ReviewController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-ReviewController: Model instance
    ReviewController->>+Model: delete()
    Model->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Model: Success
    Model-->>-ReviewController: Success
    ReviewController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over ReviewController,Model: This sequence removes a resource
  