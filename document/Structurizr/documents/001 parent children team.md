## Parent - children team

- Thử triển khai logic team cha - con trên espocrm
- Nguyên tắc:
1. user sẽ được thao tác với các bản ghi thuộc team con tương tự như các bản ghi thuộc team mình

## Hướng triển khai chung
- mở quyền thao tác với bản ghi là all và dùng hook, custom code ở backend để chặn các thao tác không được phép
- team sẽ có thêm 1 trường là parent. 
- tạo role có quyền thao tác với mọi record của entity và gán cho User
- User sẽ có thêm 1 trường là fullAccessRoles lưu các role full access ở trên
- Viết hàm merge acl custom với đầu vào là các role loại trừ các role full access
- Nếu kết quả là team thì check tiếp logic record có thuộc team con hay không

## Demo quyền đọc contact
- Team 1 là bố của Team 1 1. Tương ứng là User 1 và User 1 1.
- User 1 và User 1 1 có quyền tạo và đọc bản ghi cùng team.
- Contact 1 và Contact 1 1 được gán User 1 và User 1 1 tương ứng.
- Contact 2 và Contact 2 2 được gán Team 1 và Team 1 1 tương ứng
- User 1 sẽ xem được toàn bộ 4 Contact
- User 1 1 sẽ chỉ xem được Contact 1 1 và Contact 2 2.
