sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CountryController as CountryController
    participant Country as Country
    participant DB as Database
    
    C->>R: GET /resource
    R->>+CountryController: index()
    CountryController->>+Country: all() / get() / paginate()
    Country->>+DB: SELECT * FROM table
    DB-->>-Country: Return records
    Country-->>-CountryController: Collection of models
    CountryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CountryController,Country: This sequence retrieves a list of resources
  