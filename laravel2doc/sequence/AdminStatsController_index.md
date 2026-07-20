sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant AdminStatsController as AdminStatsController
    participant Category as Category
    participant DB as Database
    
    C->>R: GET /resource
    R->>+AdminStatsController: index()
    AdminStatsController->>+Category: all() / get() / paginate()
    Category->>+DB: SELECT * FROM table
    DB-->>-Category: Return records
    Category-->>-AdminStatsController: Collection of models
    AdminStatsController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over AdminStatsController,Category: This sequence retrieves a list of resources
  