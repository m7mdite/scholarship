sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant SpecializationController as SpecializationController
    participant Specialization as Specialization
    participant DB as Database
    
    C->>R: GET /resource/{id}
    R->>+SpecializationController: show(id)
    SpecializationController->>+Specialization: find(id) / findOrFail(id)
    Specialization->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Specialization: Return record
    Specialization-->>-SpecializationController: Model instance
    SpecializationController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over SpecializationController,Specialization: This sequence retrieves a specific resource by ID
  