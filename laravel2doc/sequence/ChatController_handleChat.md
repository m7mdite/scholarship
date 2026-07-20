sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ChatController as ChatController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: Request
    R->>+ChatController: handleChat()
    Note over ChatController: Process request
    alt Uses database
      ChatController->>+Scholarship: operation()
      Scholarship->>+DB: Database query
      DB-->>-Scholarship: Return data
      Scholarship-->>-ChatController: Return result
    else Direct response
      Note over ChatController: Process without database
    end
    ChatController-->>-R: Return response
    R-->>C: Response
    
    Note over ChatController: Generic operation flow
  