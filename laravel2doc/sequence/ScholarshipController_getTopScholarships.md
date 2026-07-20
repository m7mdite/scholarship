sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: Request
    R->>+ScholarshipController: getTopScholarships()
    Note over ScholarshipController: Process request
    alt Uses database
      ScholarshipController->>+Scholarship: operation()
      Scholarship->>+DB: Database query
      DB-->>-Scholarship: Return data
      Scholarship-->>-ScholarshipController: Return result
    else Direct response
      Note over ScholarshipController: Process without database
    end
    ScholarshipController-->>-R: Return response
    R-->>C: Response
    
    Note over ScholarshipController: Generic operation flow
  