sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CityController as CityController
    participant City as City
    participant DB as Database
    
    C->>R: GET /resource
    R->>+CityController: index()
    CityController->>+City: all() / get() / paginate()
    City->>+DB: SELECT * FROM table
    DB-->>-City: Return records
    City-->>-CityController: Collection of models
    CityController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CityController,City: This sequence retrieves a list of resources
  