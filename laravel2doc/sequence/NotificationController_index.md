sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant NotificationController as NotificationController
    participant User as User
    participant DB as Database
    
    C->>R: GET /resource
    R->>+NotificationController: index()
    NotificationController->>+User: all() / get() / paginate()
    User->>+DB: SELECT * FROM table
    DB-->>-User: Return records
    User-->>-NotificationController: Collection of models
    NotificationController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over NotificationController,User: This sequence retrieves a list of resources
  