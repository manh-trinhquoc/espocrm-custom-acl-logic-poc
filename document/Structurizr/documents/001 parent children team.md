## Parent - children team

- Thử triển khai logic team cha - con trên espocrm
- Nguyên tắc:
1. user sẽ được thao tác với các bản ghi thuộc team con tương tự như các bản ghi thuộc team mình

## Hướng triển khai chung
### Hướng 1
- mở quyền thao tác với bản ghi là all và dùng hook, custom code ở backend để chặn các thao tác không được phép
- team sẽ có thêm 1 trường là parent. 
- tạo role có quyền thao tác với mọi record của entity và gán cho User
- Khai báo mandatory filter để lọc ở list. Sử dụng tối đa query builder để tăng hiệu suất
- selectDefs/ENTITY.json
```json
{
    "accessControlFilterClassNameMap": {
        "mandatory": "Espo\\Modules\\CustomAclLogicPoc\\ParentChildTeam\\Select\\AccessControlFilters\\Mandatory"
    }
}
```

- Khai báo custom access checker để chặn truy cập detail. Trong khai báo này dùng tự dùng onwership checker để check quyền own, team. Access checker phải custom cho từng entity, logic cũng phải fix trước role nào được quyền gì, việc build lại ScopeData từ các file cấu hình và roles trong db khá phức tạp

- aclDefs/ENTITY.json
```json
{
    "accessCheckerClassName": "Espo\\Modules\\CustomAclLogicPoc\\ParentChildTeam\\ENTITY\\AccessChecker"
}
``` 

- Không thể custom trực tiếp được ownershipCheckerClassName do nguyên nhân
    - Onwership checker sẽ bị bỏ qua nếu table có giá trị:  Table::LEVEL_ALL, Table::LEVEL_YES, Table::LEVEL_NO. 
    - Ownership checker sẽ check isOwn nếu table có giá trị: Table::LEVEL_OWN || $level === Table::LEVEL_TEAM
    - Ownership checker sẽ check inTeam nếu table có giá trị: Table::LEVEL_TEAM

- Thử tìm hiểu accesscontrolfilterresolverclassname https://docs.espocrm.com/development/metadata/select-defs/#accesscontrolfilterresolverclassname




## Hướng custom code acl ở backend
- Tham khảo select defs: https://docs.espocrm.com/development/metadata/select-defs/
- tham khảo whereClause để lọc: https://docs.espocrm.com/development/orm/
- Tham khảo ownership checker https://docs.espocrm.com/development/metadata/acl-defs/
- file: 
    - ` Espo\Core\Acl\AccessChecker\ScopeChecker `;

## Demo quyền đọc contact
- Team 1 là bố của Team 1 1. Tương ứng là User 1 và User 1 1.
- User 1 và User 1 1 có quyền tạo và đọc bản ghi cùng team.
- Contact 1 và Contact 1 1 được gán User 1 và User 1 1 tương ứng.
- Contact 2 và Contact 2 2 được gán Team 1 và Team 1 1 tương ứng
- User 1 sẽ xem được toàn 4 contact
- User 1 1 sẽ chỉ xem được Contact 1 1 và Contact 2 2.
- Phải khai báo thêm selectDefs và acfDefs của Contact
    
    
    