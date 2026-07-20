sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant NotificationController as NotificationController
    participant Model as Model
    participant DB as Database
    
    C->>R: Request
    R->>+NotificationController: sendToAll()
    Note over NotificationController: Process request
    alt Uses database
      NotificationController->>+Model: operation()
      Model->>+DB: Database query
      DB-->>-Model: Return data
      Model-->>-NotificationController: Return result
    else Direct response
      Note over NotificationController: Process without database
    end
    NotificationController-->>-R: Return response
    R-->>C: Response
    
    Note over NotificationController: Generic operation flow
  