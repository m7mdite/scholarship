sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CityController as CityController
    participant Model as Model
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+CityController: destroy(id)
    CityController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-CityController: Model instance
    CityController->>+Model: delete()
    Model->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Model: Success
    Model-->>-CityController: Success
    CityController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over CityController,Model: This sequence removes a resource
  