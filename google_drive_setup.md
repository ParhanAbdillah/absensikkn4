# Panduan Integrasi Google Drive API untuk Laporan KKN

Aplikasi Anda kini sudah terintegrasi dengan Google Drive API. Ketika seorang mahasiswa mengunggah foto dokumentasi kegiatan, aplikasi akan otomatis:
1. Membuat folder khusus dengan nama mahasiswa (misal: `Citra Lestari`) di Google Drive (jika belum ada).
2. Mengunggah foto tersebut ke dalam folder tersebut.
3. Mengubah hak akses file menjadi publik (read-only) agar link bisa diakses langsung via link download/share.
4. Menyimpan link Google Drive tersebut ke database dan menampilkannya di Excel.

Jika Google Drive belum dikonfigurasi, sistem akan otomatis melakukan **fallback** (menyimpan foto di storage lokal seperti sebelumnya) tanpa menyebabkan error.

---

## Langkah 1: Membuat Kredensial di Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat project baru (misal: `Absensi KKN`).
3. Buka menu **APIs & Services > Library**, cari **Google Drive API**, lalu klik **Enable**.
4. Buka menu **APIs & Services > Credentials**.
5. Klik **+ Create Credentials** di bagian atas, pilih **Service Account**.
6. Isi nama service account (misal: `kkn-drive-uploader`), klik **Create and Continue**, lalu klik **Done**.
7. Klik pada alamat email Service Account yang baru dibuat tersebut untuk membuka detailnya.
8. Masuk ke tab **Keys**, klik **Add Key > Create new key**.
9. Pilih tipe **JSON**, lalu klik **Create**. File kredensial JSON akan otomatis terunduh ke komputer Anda.

---

## Langkah 2: Konfigurasi di Folder Google Drive Anda

1. Buat sebuah folder utama di Google Drive Anda (misal: `Laporan KKN 2025`).
2. Salin **Folder ID** dari URL folder tersebut di browser.
   * Contoh URL: `https://drive.google.com/drive/folders/1a2b3c4d5e6f...`
   * Maka Folder ID adalah: `1a2b3c4d5e6f...`
3. Bagikan (Share) folder utama tersebut dengan email Service Account yang Anda buat di **Langkah 1** (misal: `kkn-drive-uploader@...gserviceaccount.com`) dengan peran **Editor**. Ini wajib agar aplikasi bisa membuat sub-folder dan menulis file di sana.

---

## Langkah 3: Konfigurasi File `.env` Project Laravel

1. Simpan file kredensial JSON hasil unduhan dari **Langkah 1** ke dalam folder project Anda. Disarankan menyimpannya di `storage/app/google-credentials.json` (jangan masukkan ke Git demi keamanan).
2. Tambahkan baris berikut di akhir file `.env` Anda:

```ini
# Google Drive API Configuration
GOOGLE_DRIVE_PARENT_FOLDER_ID="1a2b3c4d5e6f..." # Ganti dengan Folder ID utama Anda
GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON="storage/app/google-credentials.json"
```

*Catatan: Jika Anda tidak ingin menyimpan file JSON di local server, Anda bisa menyalin isi teks JSON tersebut dan menyimpannya langsung sebagai satu baris string di `.env` dengan variabel:*
```ini
GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_STRING='{"type": "service_account", "project_id": ...}'
```
