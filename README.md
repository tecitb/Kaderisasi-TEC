# Situs Kaderisasi TEC

Backend RESTful API, dengan fungsionalitas quiz,user,coupon management 

## Instalasi:
1. `git clone`
2. Buat file environment variabel `.env` yang berisi 
```
DB_HOST=ISIKAN_HOST_DATABASE_ANDA
DB_NAME=ISIKAN_NAMA_DATABASE_ANDA
DB_USERNAME=ISIKAN_USER_DATABASE_ANDA
DB_PASSWORD=ISIKAN_PASSWORD_DATABASE_ANDA
JWT_SECRET=ISIKAN_KUNCI_JWT_RAHASIA
``` 

## Peta REST API:
1.  GET
    * `/api/users` : List semua user :white_check_mark: 
    * `/api/quiz` : List semua kuis :white_check_mark: 
    * `/api/quiz/:id` : Lihat detail kuis beserta pertanyaan dan jawaban :white_check_mark: 
    * `/api/user/:id/quiz/:id` : Lihat hasil kuis user beserta jawabannya :white_check_mark: (ADMIN ONLY)
    * `/api/user/:id/score` : Lihat skor user tiap kuis :white_check_mark: 
    * `/api/verify/:token` : Verifikasi email user :white_check_mark: 
    * `/reset/:token` : Halaman untuk reset
2. POST
    * `/api/login` : Mendapatkan akses dengan JSON Web Token :white_check_mark: (return token)
    * `/api/registration`: Buat user, cek kupon bila ada :white_check_mark:
    * `/api/quiz`: Buat quiz dengan pertanyaan berikut jawaban :white_check_mark: (ADMIN ONLY)
    * `/api/answer`: Kirim jawaban user, proses skor :white_check_mark: 
    * `/api/reset` : Kirim email untuk reset Token :white_check_mark: 
    * `/api/generateCoupon/:num`: Generate coupon (default max: 100) :white_check_mark: (ADMIN ONLY)
3. PUT
    * `/api/reset` : Reset user password dengan mengupdate ke password baru :white_check_mark: 