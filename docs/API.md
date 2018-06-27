# API docs

## User

### Login

Location : `/api/login`  
Method : POST  
Auth : None

Data :

| Parameter | Keterangan        |
| --------- | ----------------- |
| email     | Email user        |
| password  | Password user     |

Return :

| Parameter | Keterangan        |
| --------- | ----------------- |
| token     | JSON web token    |
| id        | User id           |

### Register

Location : `/api/registration`  
Method : POST  
Auth : None

Data :

| Parameter | Keterangan              |
| --------- | ----------------------- |
| name      | Nama user               |
| email     | Email user              |
| password  | Password user           |
| coupon    | Coupon user             |
| interests | Interests user          |
| nickname  | Panggilan user          |
| about_me  | Detail user             |
| line_id   | ID LINE user            |
| instagram | Username Instagram user |
| mobile    | Nomor HP user           |
| address   | Alamat user             |

Return :

| Parameter | Keterangan     |
| --------- | -------------- |
| token     | JSON web token |
| id        | User id        |

### Request Password Reset

Location : `/api/reset`  
Method : POST
Auth : None

Data :

| Parameter | Ketarangan                                     |
| --------- | ---------------------------------------------- |
| email     | email dari user yang ingin mereset passwordnya |

### Get All User

Location : `/api/users`  
Method : GET  
Auth : Admin

Parameter :

| Parameter | Keterangan                                                   |
| --------- | ------------------------------------------------------------ |
| sort      | Sorting yang diinginkan :<br />noTEC_asc -> sorting berdasarkan no tec secara ascending<br />noTEC_desc -> sorting berdasarkan no tec secara descending<br />nama_asc -> sorting berdasarkan nama secara ascending<br />nama_desc -> sorting berdasarkan nama secara descending |

Return (array):

| Parameter | Keterangan        |
| --------- | ----------------- |
| id        | User id           |
| name      | Nama lengkap user |
| email     | Email user        |
| created_at| Waktu dibuat      |
| updated_at| Waktu diupdate    |
| isAdmin   | =1 jika admin     |

### Get Spesific User by ID

Location : `/api/user/:id`  
Method : GET  
Auth : User(self) or Admin

Return :

| Parameter | Keterangan               |
| --------- | ------------------------ |
| id        | User id                  |
| name      | Nama lengkap user        |
| email     | Email user               |
| created_at| Waktu dibuat             |
| updated_at| Waktu diupdate           |
| lunas     | Status pelunasan         |
| verified  | Status verifikasi email  |
| isAdmin            | =1 jika admin|
| interests | Interests user              |
| nickname  | Panggilan user              |
| about_me  | Detail user                 |
| line_id   | ID LINE user                |
| instagram | Username Instagram user     |
| mobile    | Nomor HP user               |
| tec_regno | No registrasi TEC           |
| address   | Alamat user                 |

### Get User Score

Location : `/api/user/:id/score`
Method : GET
Auth : User

Return (array):

| Parameter | Keterangan                  |
| --------- | --------------------------- |
| user_id   | User id                     |
| nama      | Nama user                   |
| title     | Judul quiz                  |
| quiz_id   | Quiz id                     |
| score     | Score user di quiz tersebut |



## Quiz

### Get All Quiz

Location : `/api/quiz`  
Method : GET  
Auth : User

Return (array) :

| Parameter | Keterangan               |
| --------- | ------------------------ |
| id        | Quiz id                  |
| title     | Judul quiz               |
| terjawab  | =1 jika sudah terjawab   |
| score     | Score quiz               |

### Get Single Quiz

Location : `/api/quiz/:id`  
Method : GET  
Auth : User

Return (array):

| Parameter | Keterangan                          |
| --------- | ----------------------------------- |
| title     | Judul quiz                          |
| id        | Id pertanyaan                       |
| type      | Tipe pertanyaan ("pilgan"/"isian")  |
| question  | Pertanyaan                          |
| created_at| Waktu pertanyaan dibuat             |
| option    | Pilihan jawaban (jika type=="pilgan")|

### Submit Answer

Location : `/api/answer`  
Method : POST  
Auth : User

Data :

| Parameter | Keterangan                          |
| --------- | ----------------------------------- |
| quiz_id   | Id quiz                             |
| answers   | Array berisi jawaban tiap soal      |

Isi answers :

| Parameter | Keterangan                          |
| --------- | ----------------------------------- |
| qa_id     | Id pertanyaan                       |
| answer    | Jawaban soal                        |

### Add Quiz

Location : `/api/quiz`  
Method : POST
Auth : Admin

Data :

| Parameter         | Keterangan                          |
| ----------------- | ----------------------------------- |
| title             | Judul quiz                          |
| question_answer   | Array berisi jawaban dan pertanyaan |

Isi question_answer :

| Parameter | Keterangan                          |
| --------- | ----------------------------------- |
| type      | Tipe pertanyaan ("pilgan"/"isian")  |
| question  | Pertanyaan                          |
| answer    | Jawaban pertanyaan                  |
| decoy     | Pilihan jawaban(jika type=="pilgan")|

### Get All User Score

Location : `/api/quiz/:id/score`
Method : GET
Auth : Admin

Return (array):

| Parameter | Keterangan |
| --------- | ---------- |
| user_id   | Id user    |
| name      | Nama user  |
| score     | Nilai      |



## Coupon

### Generate Coupons

Location : `/api/generateCoupon/:jumlah`  
Method : POST  
Auth : Admin

Data : -

### Get Coupons

Location : `/api/getCoupon/:jumlah`  
Method : GET  
Auth : Admin

Return (array):

| Parameter | Keterangan                        |
| --------- | --------------------------------- |
| coupon    | Kode coupon sejumlah yang diminta |



