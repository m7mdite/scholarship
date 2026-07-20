sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant AuthController as AuthController
    participant Model as Model
    participant DB as Database
    
    C->>R: Request
    R->>+AuthController: logout()
    Note over AuthController: Process request
    alt Uses database
      AuthController->>+Model: operation()
      Model->>+DB: Database query
      DB-->>-Model: Return data
      Model-->>-AuthController: Return result
    else Direct response
      Note over AuthController: Process without database
    end
    AuthController-->>-R: Return response
    R-->>C: Response
    
    Note over AuthController: Generic operation flow
  