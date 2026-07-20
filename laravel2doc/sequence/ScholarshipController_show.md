sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ScholarshipController as ScholarshipController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: GET /resource/{id}
    R->>+ScholarshipController: show(id)
    ScholarshipController->>+Scholarship: find(id) / findOrFail(id)
    Scholarship->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Scholarship: Return record
    Scholarship-->>-ScholarshipController: Model instance
    ScholarshipController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over ScholarshipController,Scholarship: This sequence retrieves a specific resource by ID
  