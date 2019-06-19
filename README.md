# Target API Service

## 1.0 - How to Use
All responses will have the form

```json
{
    "data": "Array holding the content of the response",
    "message": "Description of what happened"
}
```

Subsequent response definitions will only detail the expected value of the `data fields`

### Requests Type

** Definition **

`GET`

** Response **

- `200 OK` on success

```json
[
    {
        "fieldName": "fieldValue"
    },
    {
        "fieldName": "fieldValue"
    },
    {
        "fieldName": "fieldValue"
    },
]
```

** Definition **

`POST`

** Response **

- `200 OK` on success

```json
[
    {
        "fieldName": "fieldValue"
    }
]
```