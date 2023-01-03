# Error Code

>code:前3碼功能, 後2碼流水號

| action         | code      | reason                 |
| -------------- | --------- | ---------------------- |
| auth.login     | **10001** | wrong credentials      |
| user.store     | **10101** | fields validation fail |
| post.store     | **10201** | fields validation fail |
| comment.store  | **10301** | fields validation fail |
| comment.update | **10302** | fields validation fail |
| relationship.store   | **10401** | Already requested. |
| relationship.store   | **10402** | Child Not found. |
| relationship.store   | **10403** | Attach user relationship error. |
| relationship.update  | **10404** | User relationship type is wrong. |
| relationship.update  | **10405** | Update user relationship error. |
| relationship.destroy | **10406** | Delete user relationship error. |
| relationship.index   | **10407** | User relationship type is wrong. |
| relationship.store   | **10408** | Can not request self. |
