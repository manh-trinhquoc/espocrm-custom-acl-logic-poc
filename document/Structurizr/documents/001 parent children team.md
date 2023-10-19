## Parent - children team

- Thử triển khai logic team cha - con trên espocrm
- Nguyên tắc:
1. user sẽ được thao tác với các bản ghi thuộc team con tương tự như các bản ghi thuộc team mình

- Hướng triển khai
    - mở quyền thao tác với bản ghi là all và dùng hook, custom code ở backend để chặn các thao tác không được phép
    - team sẽ có thêm 1 trường là parent. 
