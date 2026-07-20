sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: GET /resource
    R->>+ScholarshipController: index()
    ScholarshipController->>+Scholarship: all() / get() / paginate()
    Scholarship->>+DB: SELECT * FROM table
    DB-->>-Scholarship: Return records
    Scholarship-->>-ScholarshipController: Collection of models
    ScholarshipController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over ScholarshipController,Scholarship: This sequence retrieves a list of resources
  