sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ReviewController as ReviewController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+ReviewController: update(request, id)
    ReviewController->>+V: validate(request)
    V-->>-ReviewController: validated data
    ReviewController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-ReviewController: Model instance
    ReviewController->>+Model: update(data)
    Model->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Model: Success
    Model-->>-ReviewController: Updated model
    ReviewController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over ReviewController,Model: This sequence updates an existing resource
  