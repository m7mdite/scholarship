sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant ReviewController as ReviewController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: GET /resource
    R->>+ReviewController: index()
    ReviewController->>+Scholarship: all() / get() / paginate()
    Scholarship->>+DB: SELECT * FROM table
    DB-->>-Scholarship: Return records
    Scholarship-->>-ReviewController: Collection of models
    ReviewController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over ReviewController,Scholarship: This sequence retrieves a list of resources
  