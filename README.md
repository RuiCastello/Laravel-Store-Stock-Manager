# Laravel-Store-Stock-Manager
Simple (shoe) store stock manager. Two versions included: REST API and regular website working concurrently. 

Created in Laravel.
<br />
<br />

## Structure
Models:
> /app/Department.php  
> /app/Feedstock.php  
> /app/Relation.php  
> /app/Shoe.php  
> /app/User.php
<br />

Web controllers:
> /app/Http/Controllers/
<br />

API controllers:
> /app/Http/Controllers/API
<br />

Extra middleware:
> /app/Http/Middleware/CheckAdmin.php  
> /app/Http/Middleware/CheckAdminApi.php  
> Above entries added to:  
> /app/Http/Kernel.php
<br />

Config:  
- Barcode library added to:
> /config/app.php  
- Authentication works concurrently with both web and API modes: 
> /config/auth.php  
- DB mysql engine was set to InnoDB: 
> /config/database.php  
<br />

Migrations:
> database/migrations
<br />

Web templates (views)
> /resources/views/  
> /resources/views/feedstock  
> /resources/views/layouts  
> /resources/views/shoes
<br />

API Routes:  
- /shoes (resource)  
- /feedstocks (resource)  
- /users (resource)  
- /login (POST only)  
- /logout (POST only)  
- /me (POST only)  
<br />

WEB Routes:
- /shoe (resource)
- /feedstock (resource)
- /home (GET ONLY)
- / (GET only)
<br />



