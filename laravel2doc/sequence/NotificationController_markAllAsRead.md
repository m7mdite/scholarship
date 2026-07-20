sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant NotificationController as NotificationController
    participant User as User
    participant DB as Database
    
    C->>R: Request
    R->>+NotificationController: markAllAsRead()
    Note over NotificationController: Process request
    alt Uses database
      NotificationController->>+User: operation()
      User->>+DB: Database query
      DB-->>-User: Return data
      User-->>-NotificationController: Return result
    else Direct response
      Note over NotificationController: Process without database
    end
    NotificationController-->>-R: Return response
    R-->>C: Response
    
    Note over NotificationController: Generic operation flow
  