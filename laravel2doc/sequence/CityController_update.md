sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CityController as CityController
    participant V as Validator
    participant Model as Model
    participant DB as Database
    
    C->>R: PUT /resource/{id}
    R->>+CityController: update(request, id)
    CityController->>+V: validate(request)
    V-->>-CityController: validated data
    CityController->>+Model: find(id)
    Model->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Model: Return record
    Model-->>-CityController: Model instance
    CityController->>+Model: update(data)
    Model->>+DB: UPDATE table SET ... WHERE id = ?
    DB-->>-Model: Success
    Model-->>-CityController: Updated model
    CityController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CityController,Model: This sequence updates an existing resource
  