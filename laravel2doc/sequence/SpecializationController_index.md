sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant SpecializationController as SpecializationController
    participant Specialization as Specialization
    participant DB as Database
    
    C->>R: GET /resource
    R->>+SpecializationController: index()
    SpecializationController->>+Specialization: all() / get() / paginate()
    Specialization->>+DB: SELECT * FROM table
    DB-->>-Specialization: Return records
    Specialization-->>-SpecializationController: Collection of models
    SpecializationController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over SpecializationController,Specialization: This sequence retrieves a list of resources
  