# Situs Kaderisasi TEC

Objectives:
* Backend RESTful API
* Single Page Application

Instalasi:
1. `git clone`
2. Buat file environment variabel `.env` yang berisi 
```
DB_HOST=ISIKAN_HOST_DATABASE_ANDA
DB_NAME=ISIKAN_NAMA_DATABASE_ANDA
DB_USERNAME=ISIKAN_USER_DATABASE_ANDA
DB_PASSWORD=ISIKAN_PASSWORD_DATABASE_ANDA
JWT_SECRET=ISIKAN_KUNCI_JWT_RAHASIA
``` 

Peta REST API  (`/api`):
1.  GET
    * `/login` : Mendapatkan akses dengan JSON Web Token :white_check_mark: 
    * `/users` : List semua user
    * `/quiz` : List semua kuis :white_check_mark: 
    * `/quiz/:id` : Lihat detail kuis beserta pertanyaan dan jawaban :white_check_mark: 
    * `/user/:id/quiz/:id` : Lihat hasil kuis user beserta jawabannya :white_check_mark:
    * `/user/:id/score` : Lihat skor user tiap kuis
    * `/verify/:token` : Verifikasi email user
    * `/reset/:token` : Reset user password
2. POST
    * `/user`: Buat user, cek kupon bila ada :white_check_mark:
    * `/quiz`: Buat quiz dengan pertanyaan berikut jawaban
    * `/answer`: Kirim jawaban user, proses skor