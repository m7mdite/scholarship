sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant Country as Country
    participant DB as Database
    
    C->>R: Request
    R->>+ScholarshipController: getByCountry()
    Note over ScholarshipController: Process request
    alt Uses database
      ScholarshipController->>+Country: operation()
      Country->>+DB: Database query
      DB-->>-Country: Return data
      Country-->>-ScholarshipController: Return result
    else Direct response
      Note over ScholarshipController: Process without database
    end
    ScholarshipController-->>-R: Return response
    R-->>C: Response
    
    Note over ScholarshipController: Generic operation flow
  