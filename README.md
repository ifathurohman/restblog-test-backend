# Tech - Backend Assessment Test

* * *

### User

| Methods | Endpoint | Description | Authentication  |
| --- | --- | --- | --- |
| 'POST' | '/api/register' | User Registration| No |
| 'POST' | '/api/login' | User Login | Yes |
| 'POST' | '/api/reset_password' | User Reset Password | Yes |

### Article

| Methods | Endpoint | Description | Authentication  |
| --- | --- | --- | --- |
| 'GET' | '/api/article/get' | List Article | Yes |
| 'POST' | '/api/article/create' | Create Article | Yes |
| 'PUT' | '/api/article/update' | Update Article | Yes |
| 'DELETE' | '/api/article/(:num)/delete' | Delete Article | Yes |

### Category

| Methods | Endpoint | Description | Authentication  |
| --- | --- | --- | --- |
| 'GET' | '/api/category/get' | List Category | Yes |
| 'POST' | '/api/category/create' | Create Category | Yes |
| 'PUT' | '/api/category/update' | Update Category | Yes |
| 'DELETE' | '/api/category/(:num)/delete' | Delete Category | Yes |

### Comments

| Methods | Endpoint | Description | Authentication  |
| --- | --- | --- | --- |
| 'GET' | '/api/comments/get' | List Comments | Yes |
| 'POST' | '/api/comments/create' | Create Comments | Yes |
| 'PUT' | '/api/comments/update' | Update Comments | Yes |
| 'DELETE' | '/api/comments/(:num)/delete' | Delete Comments | Yes |

### Dashboard

| Methods | Endpoint | Description | Authentication  |
| --- | --- | --- | --- |
| 'GET' | '/api/dashboard' | List Dashboard | | Yes |


