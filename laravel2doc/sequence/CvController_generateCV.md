sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CvController as CvController
    participant Model as Model
    participant DB as Database
    
    C->>R: Request
    R->>+CvController: generateCV()
    Note over CvController: Process request
    alt Uses database
      CvController->>+Model: operation()
      Model->>+DB: Database query
      DB-->>-Model: Return data
      Model-->>-CvController: Return result
    else Direct response
      Note over CvController: Process without database
    end
    CvController-->>-R: Return response
    R-->>C: Response
    
    Note over CvController: Generic operation flow
  