# 📌 Project Web Berbasis API
Tugas Proyek – Pengembangan Web Berbasis API

## 👥 Identitas Kelompok
**Kelompok 9**

Anggota:
- Tiara Nuriani  
- Uminati  
- Lisa Kelly  
- Winda Anugrah  
- Zadly Baan  

---

## 📖 Deskripsi Proyek
Project ini merupakan aplikasi web berbasis **RESTful API** yang dikembangkan menggunakan arsitektur **client–server**.

Project ini merupakan **pengembangan dari project sebelumnya**, yang sebelumnya masih menggunakan metode web biasa.  
Pada project ini, sistem dikembangkan ulang dengan **metode API**, sehingga backend dan frontend dipisahkan.

Backend berfungsi sebagai **API** yang mengelola autentikasi dan data, sedangkan frontend berfungsi sebagai **client** yang mengakses API melalui HTTP request.

Semua pertukaran data menggunakan **format JSON**.

---

## 🎯 Tujuan Proyek
- Mengembangkan project sebelumnya menjadi sistem berbasis API  
- Memahami konsep dan peran API dalam sistem web modern  
- Merancang dan mengimplementasikan RESTful API  
- Menerapkan CRUD data melalui endpoint API  
- Mengintegrasikan frontend dan backend  
- Menerapkan autentikasi user  
- Melakukan pengujian API menggunakan Postman  

---

## 🏗️ Arsitektur Sistem
Sistem menggunakan arsitektur **Client–Server**:
- **Backend (API)** : PHP
- **Frontend** : HTML, CSS, JavaScript
- **Database** : MySQL
- **Format Data** : JSON

---

## 📂 Struktur Folder API

Folder `api/` berisi file backend berikut:

```bash
api/
├── login.php    # Autentikasi user
├── regis.php    # Registrasi user
└── repost.php   # CRUD data (GET, POST, PUT, DELETE)


---

## 🔐 Autentikasi API
- Autentikasi API dilakukan melalui endpoint login.php.
- User wajib login terlebih dahulu sebelum mengakses data
- Autentikasi digunakan sebagai kontrol akses API
- Seluruh response API dikembalikan dalam format JSON

---

## 🔗 Daftar Endpoint API
🔑 Login
Method : POST
Endpoint : /api/login.php
Fungsi : Autentikasi user

---

## 📝 Registrasi
Method : POST
Endpoint : /api/regis.php
Fungsi : Registrasi user baru

---

## 📊 CRUD Data (RESTful API)
Seluruh proses CRUD dilakukan dalam satu file repost.php, dengan pembedaan berdasarkan HTTP Method, sehingga tetap menerapkan konsep RESTful API.
GET /api/repost.php
→ Mengambil data
POST /api/repost.php
→ Menambah data
PUT /api/repost.php
→ Mengubah data
DELETE /api/repost.php
→ Menghapus data

---

## 📦 Contoh Response API
{
  "status": true,
  "message": "Data berhasil diproses"
}

---

## 🧪 Pengujian API
 Pengujian API dilakukan menggunakan Postman, meliputi:
- Login user
- Registrasi user
- CRUD data (GET, POST, PUT, DELETE)
- Pengujian dilakukan untuk memastikan API berjalan dengan baik tanpa bergantung pada frontend.

---

## 🛠️ Tools & Teknologi
PHP
- MySQL
- Apache (XAMPP)
- Postman
- Git & GitHub


