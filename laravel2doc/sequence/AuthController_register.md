sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant AuthController as AuthController
    participant User as User
    participant DB as Database
    
    C->>R: Request
    R->>+AuthController: register()
    Note over AuthController: Process request
    alt Uses database
      AuthController->>+User: operation()
      User->>+DB: Database query
      DB-->>-User: Return data
      User-->>-AuthController: Return result
    else Direct response
      Note over AuthController: Process without database
    end
    AuthController-->>-R: Return response
    R-->>C: Response
    
    Note over AuthController: Generic operation flow
  