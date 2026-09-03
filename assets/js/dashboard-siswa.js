/**
 * dashboard-siswa.js
 * EduCare - Dashboard Siswa
 * Semua logika interaktif dipisahkan dari PHP
 */

// ================================================================
// 1. DATA STATIS
// ================================================================

// Data kursus bawaan (built-in) sekarang disimpan di data/courses.json
// dan dimuat secara dinamis oleh loadBuiltinCourses() di bootDashboard().
// Materi buatan guru (data/materi.json) tetap digabungkan lewat mergeGuruCourses().
const COURSES = [];

function escapeHtml(str) {
  if (!str) return "";
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// ================================================================
// PROFIL SETIAP MATA PELAJARAN
// Dipakai supaya penjelasan materi & contoh script yang tampil di
// dashboard siswa SELALU mengikuti mata pelajaran/kategori kursus
// yang bersangkutan (bukan template generik yang sama untuk semua).
// ================================================================
const SUBJECT_PROFILES = {
  web: {
    label: "Pemrograman Web",
    field: "pengembangan web",
    contoh:
      "membangun website perusahaan atau toko online yang diakses ribuan pengguna setiap hari",
    tip: "menulis kode yang rapi (clean code), memakai penamaan variabel yang jelas, dan menguji tampilan di berbagai ukuran layar",
  },
  php: {
    label: "Pemrograman PHP",
    field: "pengembangan back-end",
    contoh:
      "membuat sistem informasi sekolah, aplikasi absensi, atau website dinamis yang terhubung ke database",
    tip: "memvalidasi input dari pengguna, memakai koneksi database yang aman, dan memisahkan logika dari tampilan",
  },
  ai: {
    label: "Kecerdasan Buatan (AI)",
    field: "AI & machine learning",
    contoh:
      "sistem rekomendasi produk di e-commerce atau chatbot layanan pelanggan",
    tip: "memastikan data latih bersih, menghindari bias data, dan selalu mengevaluasi akurasi model",
  },
  data: {
    label: "Data Science",
    field: "analisis data",
    contoh:
      "menganalisis data penjualan perusahaan untuk mengambil keputusan bisnis yang lebih tepat",
    tip: "membersihkan data sebelum dianalisis, memvisualisasikan hasil dengan jelas, dan menghindari kesimpulan yang tidak didukung data",
  },
  mobile: {
    label: "Pengembangan Aplikasi Android",
    field: "pengembangan aplikasi mobile",
    contoh:
      "aplikasi mobile yang dipakai jutaan pengguna setiap hari, mulai dari e-commerce sampai transportasi online",
    tip: "menjaga aplikasi tetap ringan, menangani error dengan baik, dan menguji di berbagai jenis perangkat",
  },
  uiux: {
    label: "UI/UX Design",
    field: "desain pengalaman pengguna",
    contoh:
      "merancang aplikasi yang mudah dan nyaman digunakan bahkan oleh pengguna yang baru pertama kali mencoba",
    tip: "selalu mengutamakan kebutuhan pengguna, menjaga konsistensi visual, dan menguji desain lewat prototipe sebelum dibangun",
  },
  cloud: {
    label: "Cloud Computing",
    field: "infrastruktur cloud",
    contoh:
      "menjalankan aplikasi berskala besar tanpa perlu memiliki dan merawat server fisik sendiri",
    tip: "memperhatikan keamanan akses, mengatur skalabilitas sesuai kebutuhan, dan memantau biaya penggunaan layanan cloud",
  },
  mtk: {
    label: "Matematika",
    field: "ilmu matematika",
    contoh:
      "menghitung anggaran belanja, mengukur luas tanah, atau membaca data dalam bentuk grafik di kehidupan sehari-hari",
    tip: "memahami konsep dasarnya dulu sebelum menghafal rumus, dan selalu mengecek kembali hasil hitungan langkah demi langkah",
  },
  ipa: {
    label: "Ilmu Pengetahuan Alam (IPA)",
    field: "ilmu sains",
    contoh:
      "memahami fenomena alam di sekitar kita, mulai dari cuaca, ekosistem, sampai cara kerja tubuh manusia",
    tip: "menghubungkan teori dengan contoh nyata di sekitarmu, dan mencatat pengamatan secara sistematis",
  },
  ips: {
    label: "Ilmu Pengetahuan Sosial (IPS)",
    field: "ilmu sosial",
    contoh:
      "memahami interaksi masyarakat, sejarah bangsa, dan sistem pemerintahan di lingkungan sekitar kita",
    tip: "menghubungkan peristiwa dengan sebab-akibatnya, dan melihat suatu masalah dari berbagai sudut pandang",
  },
  bahasa: {
    label: "Bahasa",
    field: "kebahasaan",
    contoh:
      "menulis surat resmi, berkomunikasi dengan orang asing, atau menyusun presentasi yang jelas dan efektif",
    tip: "membaca dan menulis secara rutin, serta memperhatikan tata bahasa dan pemilihan kata yang tepat",
  },
  self: {
    label: "Pengembangan Diri",
    field: "pengembangan pribadi",
    contoh:
      "mengatur waktu belajar, membangun kebiasaan positif, dan meningkatkan rasa percaya diri sehari-hari",
    tip: "memulai dari kebiasaan kecil yang konsisten, dan merefleksikan progres secara rutin",
  },
};

// Menentukan profil mapel yang tepat untuk kursus, termasuk kursus
// buatan guru yang kategorinya (cat) tidak ada di daftar baku di atas
// (misalnya guru membuat kursus dengan cat custom seperti "sejarah").
function getSubjectProfile(course) {
  const cat = (course.cat || "").toLowerCase();
  const titleLc = (course.title || "").toLowerCase();

  // Deteksi kursus PHP yang ter-tag sebagai 'web' agar tidak disamakan
  // dengan materi HTML/CSS/JS murni.
  if (cat === "web" && titleLc.includes("php")) {
    return SUBJECT_PROFILES.php;
  }

  if (SUBJECT_PROFILES[cat]) {
    return SUBJECT_PROFILES[cat];
  }

  // Fallback pintar untuk kategori custom dari guru: coba tebak dari
  // judul/kategori kursus supaya materi tetap relevan, bukan generik.
  const guess = titleLc + " " + cat;
  if (/php|laravel/.test(guess)) return SUBJECT_PROFILES.php;
  if (/matematika|hitung|aljabar|geometri/.test(guess))
    return SUBJECT_PROFILES.mtk;
  if (/ipa|sains|fisika|kimia|biologi/.test(guess)) return SUBJECT_PROFILES.ipa;
  if (/ips|sejarah|geografi|sosiolog|ekonomi/.test(guess))
    return SUBJECT_PROFILES.ips;
  if (/bahasa|english|inggris|sastra/.test(guess))
    return SUBJECT_PROFILES.bahasa;
  if (/diri|karakter|motivasi|softskill|soft skill/.test(guess))
    return SUBJECT_PROFILES.self;
  if (/html|css|javascript|frontend|front-end/.test(guess))
    return SUBJECT_PROFILES.web;

  // Fallback terakhir: pakai judul kursus asli sebagai nama mapel,
  // supaya tetap terasa "milik" kursus tersebut, bukan template kosong.
  return {
    label: course.title || "Materi Umum",
    field: (course.desc ? course.desc : "materi kursus ini").toLowerCase(),
    contoh: `menerapkan pemahaman ${course.title ? '"' + course.title + '"' : "materi ini"} dalam tugas atau kegiatan sehari-hari yang relevan`,
    tip: "memahami konsep intinya terlebih dahulu, lalu berlatih dengan contoh soal atau latihan yang bervariasi",
  };
}

// ================================================================
// GENERATOR CONTOH MATEMATIKA
// Berbeda dari mapel lain, kalau materi matematika hanya dikasih
// template angka acak (mis. selalu a=12, b=5, lalu dijumlah & dikali),
// hasilnya tidak nyambung dengan judul soalnya sendiri.
// Fungsi ini membaca judul materi ("Contoh: 1/2 + 1/4 = 3/4",
// "Keliling bangun", dst) lalu membuat rumus & langkah pengerjaan
// yang benar-benar sesuai topiknya.
// ================================================================
function gcdMath(a, b) {
  a = Math.abs(a);
  b = Math.abs(b);
  return b === 0 ? a : gcdMath(b, a % b);
}

function buildMathScript(title) {
  const t = title || "";
  const tl = t.toLowerCase();
  let m;

  // 1) Operasi pecahan: a/b (+|-) c/d = e/f
  m = t.match(/(\d+)\s*\/\s*(\d+)\s*([+\-])\s*(\d+)\s*\/\s*(\d+)/);
  if (m) {
    const A = parseInt(m[1]),
      B = parseInt(m[2]),
      op = m[3],
      C = parseInt(m[4]),
      D = parseInt(m[5]);
    const kpk = (B * D) / gcdMath(B, D);
    const p1 = A * (kpk / B);
    const p2 = C * (kpk / D);
    const hasilPembilang = op === "+" ? p1 + p2 : p1 - p2;
    const g = gcdMath(hasilPembilang, kpk) || 1;
    const sederhana = `${hasilPembilang / g}/${kpk / g}`;
    return `// Materi: Operasi Pecahan - ${title}
// Rumus: samakan penyebut (KPK dari kedua penyebut), lalu ${op === "+" ? "jumlahkan" : "kurangkan"} pembilangnya

function operasiPecahan(a, b, c, d, operator) {
  const kpk = (b * d) / cariGCD(b, d);
  const pembilang1 = a * (kpk / b);
  const pembilang2 = c * (kpk / d);

  console.log('Soal: ' + a + '/' + b + ' ' + operator + ' ' + c + '/' + d);
  console.log('Langkah 1 - Cari KPK penyebut ' + b + ' dan ' + d + ' = ' + kpk);
  console.log('Langkah 2 - Samakan penyebut: ' + pembilang1 + '/' + kpk + ' ' + operator + ' ' + pembilang2 + '/' + kpk);

  const hasilPembilang = operator === '+' ? pembilang1 + pembilang2 : pembilang1 - pembilang2;
  console.log('Langkah 3 - ${op === "+" ? "Jumlahkan" : "Kurangkan"} pembilang: ' + hasilPembilang + '/' + kpk);

  return hasilPembilang + '/' + kpk;
}

function cariGCD(x, y) {
  return y === 0 ? x : cariGCD(y, x % y);
}

const hasil = operasiPecahan(${A}, ${B}, ${C}, ${D}, '${op}');
console.log('Kesimpulan: ' + hasil + ' = ${sederhana} (bentuk paling sederhana)');`;
  }

  // 2) Persamaan linear satu variabel: x (+|-) n = m
  m = t.match(/x\s*([+\-])\s*(\d+)\s*=\s*(\d+)/);
  if (m) {
    const op = m[1],
      N = parseInt(m[2]),
      R = parseInt(m[3]);
    const X = op === "+" ? R - N : R + N;
    const lawan = op === "+" ? "-" : "+";
    return `// Materi: Persamaan Linear Satu Variabel - ${title}
// Rumus: pindahkan konstanta ke ruas kanan memakai operasi kebalikannya

function selesaikanPersamaan(konstanta, ruasKanan, operator) {
  console.log('Soal: x ' + operator + ' ' + konstanta + ' = ' + ruasKanan);
  console.log('Langkah 1 - Pindahkan konstanta ke ruas kanan (operasi kebalikan: ${lawan}' + konstanta + ')');

  const x = operator === '+' ? ruasKanan - konstanta : ruasKanan + konstanta;
  console.log('Langkah 2 - Hitung: x = ' + ruasKanan + ' ${lawan} ' + konstanta + ' = ' + x);

  return x;
}

const nilaiX = selesaikanPersamaan(${N}, ${R}, '${op}');
console.log('Kesimpulan: x = ' + nilaiX);`;
  }

  // 3) Topik-topik matematika lain, dipetakan berdasarkan kata kunci judul
  //    supaya rumus & langkahnya benar-benar relevan dengan babnya.
  const topics = [
    {
      test: /pembulatan/,
      build: () => `// Materi: Pembulatan Bilangan - ${title}
// Rumus: lihat angka setelah posisi pembulatan. >= 5 dibulatkan naik, < 5 dibulatkan turun

function bulatkan(nilai, keDesimal) {
  console.log('Soal: bulatkan ' + nilai + ' ke ' + keDesimal + ' angka di belakang koma');
  const faktor = Math.pow(10, keDesimal);
  const hasil = Math.round(nilai * faktor) / faktor;
  console.log('Langkah - Kalikan dengan 10^' + keDesimal + ', bulatkan, lalu bagi kembali');
  return hasil;
}

console.log('Kesimpulan: 3.14159 dibulatkan 2 angka di belakang koma =', bulatkan(3.14159, 2));
console.log('Kesimpulan: 2.5 dibulatkan ke bilangan bulat terdekat =', bulatkan(2.5, 0));`,
    },

    {
      test: /perbandingan bilangan/,
      build: () => `// Materi: Perbandingan Bilangan - ${title}
// Rumus: bandingkan nilai dua bilangan lalu tentukan tanda <, >, atau =

function bandingkan(a, b) {
  console.log('Soal: bandingkan ' + a + ' dan ' + b);
  if (a > b) return a + ' > ' + b;
  if (a < b) return a + ' < ' + b;
  return a + ' = ' + b;
}

console.log('Kesimpulan:', bandingkan(7, 12));
console.log('Kesimpulan:', bandingkan(0.75, 3/4));`,
    },

    {
      test: /nilai tempat/,
      build: () => `// Materi: Nilai Tempat - ${title}
// Rumus: setiap digit punya nilai berdasarkan posisinya (satuan, puluhan, ratusan, ribuan, dst)

function nilaiTempat(bilangan) {
  const digit = String(bilangan).split('').reverse();
  const namaTempat = ['satuan', 'puluhan', 'ratusan', 'ribuan', 'puluh ribuan'];
  console.log('Soal: uraikan nilai tempat dari ' + bilangan);
  digit.forEach((d, i) => {
    console.log('Langkah - Digit ' + d + ' berada di posisi ' + (namaTempat[i] || 'posisi ke-' + (i + 1)) + ' = nilainya ' + (parseInt(d) * Math.pow(10, i)));
  });
}

nilaiTempat(4826);
console.log('Kesimpulan: 4826 = 4000 + 800 + 20 + 6');`,
    },

    {
      test: /urutan bilangan/,
      build: () => `// Materi: Urutan Bilangan - ${title}
// Rumus: bandingkan tiap pasang bilangan lalu susun dari terkecil ke terbesar (atau sebaliknya)

function urutkan(daftar) {
  console.log('Soal: urutkan bilangan berikut dari terkecil ke terbesar:', JSON.stringify(daftar));
  const hasil = [...daftar].sort((a, b) => a - b);
  console.log('Langkah - Bandingkan tiap pasang bilangan dan tukar posisinya jika perlu');
  return hasil;
}

console.log('Kesimpulan: hasil urutan =', JSON.stringify(urutkan([8, 3, 15, 1, 9])));`,
    },

    {
      test: /operasi hitung campuran/,
      build: () => `// Materi: Operasi Hitung Campuran - ${title}
// Rumus urutan pengerjaan (PEMDAS): kurung -> perkalian/pembagian -> penjumlahan/pengurangan

function hitungCampuran() {
  console.log('Soal: 4 + 2 x (5 - 3)');
  const dalamKurung = 5 - 3;
  console.log('Langkah 1 - Selesaikan dalam kurung: 5 - 3 = ' + dalamKurung);
  const perkalian = 2 * dalamKurung;
  console.log('Langkah 2 - Kerjakan perkalian: 2 x ' + dalamKurung + ' = ' + perkalian);
  const hasil = 4 + perkalian;
  console.log('Langkah 3 - Kerjakan penjumlahan: 4 + ' + perkalian + ' = ' + hasil);
  return hasil;
}

console.log('Kesimpulan: hasil akhir =', hitungCampuran());`,
    },

    {
      test: /pemfaktoran/,
      build: () => `// Materi: Pemfaktoran Sederhana - ${title}
// Rumus: cari faktor persekutuan dari setiap suku, lalu keluarkan sebagai faktor bersama

function faktorkan(koefisien1, koefisien2) {
  const fpb = cariFPB(koefisien1, koefisien2);
  console.log('Soal: faktorkan ' + koefisien1 + 'x + ' + koefisien2);
  console.log('Langkah - FPB dari ' + koefisien1 + ' dan ' + koefisien2 + ' = ' + fpb);
  console.log('Hasil pemfaktoran: ' + fpb + '(' + (koefisien1 / fpb) + 'x + ' + (koefisien2 / fpb) + ')');
}

function cariFPB(a, b) {
  return b === 0 ? a : cariFPB(b, a % b);
}

faktorkan(6, 9);
console.log('Kesimpulan: 6x + 9 = 3(2x + 3)');`,
    },

    {
      test: /pertidaksamaan/,
      build: () => `// Materi: Pertidaksamaan - ${title}
// Rumus: sama seperti persamaan, tapi tanda pertidaksamaan dibalik jika dikali/dibagi bilangan negatif

function selesaikanPertidaksamaan() {
  console.log('Soal: 2x + 3 > 9');
  console.log('Langkah 1 - Pindahkan konstanta: 2x > 9 - 3 -> 2x > 6');
  console.log('Langkah 2 - Bagi kedua ruas dengan 2: x > 3');
  return 'x > 3';
}

console.log('Kesimpulan:', selesaikanPertidaksamaan());`,
    },

    {
      test: /koefisien dan konstanta/,
      build: () => `// Materi: Koefisien dan Konstanta - ${title}
// Rumus: koefisien = angka pengali variabel, konstanta = angka tanpa variabel

function identifikasi(ekspresi) {
  console.log('Soal: identifikasi bagian dari ekspresi ' + ekspresi);
  console.log('Langkah - Cari angka yang menempel pada variabel (koefisien) dan angka berdiri sendiri (konstanta)');
}

identifikasi('5x + 8');
console.log('Kesimpulan: koefisien = 5 (pengali variabel x), konstanta = 8 (angka tanpa variabel)');`,
    },

    {
      test: /variabel dan persamaan|persamaan satu variabel|sifat operasi|aljabar/,
      build: () => `// Materi: Aljabar - ${title}
// Rumus dasar: apapun operasi yang dilakukan di satu ruas, lakukan juga di ruas yang lain

function selesaikanPersamaan(konstanta, ruasKanan) {
  console.log('Soal: x + ' + konstanta + ' = ' + ruasKanan);
  console.log('Langkah - Kurangi kedua ruas dengan ' + konstanta);
  const x = ruasKanan - konstanta;
  return x;
}

console.log('Kesimpulan: x =', selesaikanPersamaan(5, 12));`,
    },

    {
      test: /lingkaran/,
      build: () => `// Materi: Lingkaran - ${title}
// Rumus: Keliling = 2 x pi x r,  Luas = pi x r^2

function hitungLingkaran(jariJari) {
  const pi = 3.14;
  console.log('Soal: hitung keliling & luas lingkaran dengan jari-jari ' + jariJari + ' cm');
  const keliling = 2 * pi * jariJari;
  console.log('Langkah 1 - Keliling = 2 x pi x r = 2 x ' + pi + ' x ' + jariJari + ' = ' + keliling + ' cm');
  const luas = pi * jariJari * jariJari;
  console.log('Langkah 2 - Luas = pi x r^2 = ' + pi + ' x ' + jariJari + '^2 = ' + luas + ' cm2');
  return { keliling, luas };
}

console.log('Kesimpulan:', JSON.stringify(hitungLingkaran(7)));`,
    },

    {
      test: /persegi panjang/,
      build: () => `// Materi: Persegi Panjang - ${title}
// Rumus: Keliling = 2 x (panjang + lebar),  Luas = panjang x lebar

function hitungPersegiPanjang(panjang, lebar) {
  console.log('Soal: hitung keliling & luas persegi panjang ' + panjang + ' x ' + lebar + ' cm');
  const keliling = 2 * (panjang + lebar);
  console.log('Langkah 1 - Keliling = 2 x (p + l) = 2 x (' + panjang + ' + ' + lebar + ') = ' + keliling + ' cm');
  const luas = panjang * lebar;
  console.log('Langkah 2 - Luas = p x l = ' + panjang + ' x ' + lebar + ' = ' + luas + ' cm2');
  return { keliling, luas };
}

console.log('Kesimpulan:', JSON.stringify(hitungPersegiPanjang(8, 5)));`,
    },

    {
      test: /segitiga/,
      build: () => `// Materi: Segitiga - ${title}
// Rumus: Keliling = jumlah semua sisi,  Luas = 1/2 x alas x tinggi

function hitungSegitiga(sisiA, sisiB, sisiC, alas, tinggi) {
  console.log('Soal: hitung keliling & luas segitiga dengan sisi ' + sisiA + ', ' + sisiB + ', ' + sisiC + ' cm');
  const keliling = sisiA + sisiB + sisiC;
  console.log('Langkah 1 - Keliling = sisi A + sisi B + sisi C = ' + keliling + ' cm');
  const luas = 0.5 * alas * tinggi;
  console.log('Langkah 2 - Luas = 1/2 x alas x tinggi = 1/2 x ' + alas + ' x ' + tinggi + ' = ' + luas + ' cm2');
  return { keliling, luas };
}

console.log('Kesimpulan:', JSON.stringify(hitungSegitiga(3, 4, 5, 4, 3)));`,
    },

    {
      test: /keliling/,
      build: () => `// Materi: Keliling Bangun Datar - ${title}
// Rumus: keliling = total panjang seluruh sisi bangun tersebut

function hitungKeliling(sisiSisi) {
  console.log('Soal: hitung keliling bangun dengan sisi-sisi ' + JSON.stringify(sisiSisi));
  const keliling = sisiSisi.reduce((total, sisi) => total + sisi, 0);
  console.log('Langkah - Jumlahkan semua sisi: ' + sisiSisi.join(' + ') + ' = ' + keliling);
  return keliling;
}

console.log('Kesimpulan: keliling =', hitungKeliling([5, 5, 5, 5]), 'cm');`,
    },

    {
      test: /luas/,
      build: () => `// Materi: Luas Bangun Datar - ${title}
// Rumus umum persegi/persegi panjang: Luas = panjang x lebar

function hitungLuas(panjang, lebar) {
  console.log('Soal: hitung luas bangun ' + panjang + ' x ' + lebar + ' cm');
  const luas = panjang * lebar;
  console.log('Langkah - Luas = panjang x lebar = ' + panjang + ' x ' + lebar + ' = ' + luas + ' cm2');
  return luas;
}

console.log('Kesimpulan: luas =', hitungLuas(6, 4), 'cm2');`,
    },

    {
      test: /jenis sudut|garis, sudut/,
      build: () => `// Materi: Jenis Sudut - ${title}
// Rumus: klasifikasikan sudut berdasarkan besarnya derajat

function jenisSudut(derajat) {
  console.log('Soal: sudut ' + derajat + ' derajat termasuk jenis apa?');
  if (derajat < 90) return 'Sudut Lancip (< 90 derajat)';
  if (derajat === 90) return 'Sudut Siku-siku (= 90 derajat)';
  if (derajat < 180) return 'Sudut Tumpul (90 - 180 derajat)';
  return 'Sudut Lurus (= 180 derajat)';
}

console.log('Kesimpulan:', jenisSudut(45));
console.log('Kesimpulan:', jenisSudut(120));`,
    },

    {
      test: /konversi satuan|panjang dan jarak/,
      build: () => `// Materi: Konversi Satuan Panjang - ${title}
// Rumus: setiap turun 1 tingkat tangga satuan dikali 10, naik 1 tingkat dibagi 10 (km-hm-dam-m-dm-cm-mm)

function konversiKmKeM(km) {
  console.log('Soal: ubah ' + km + ' km menjadi meter');
  const meter = km * 1000;
  console.log('Langkah - 1 km = 1000 m, jadi ' + km + ' km x 1000 = ' + meter + ' m');
  return meter;
}

console.log('Kesimpulan:', konversiKmKeM(3), 'meter');`,
    },

    {
      test: /berat dan volume/,
      build: () => `// Materi: Berat dan Volume - ${title}
// Rumus: 1 kg = 1000 gram, 1 liter = 1000 ml

function konversiBerat(kg) {
  console.log('Soal: ubah ' + kg + ' kg menjadi gram');
  const gram = kg * 1000;
  console.log('Langkah - 1 kg = 1000 gram, jadi ' + kg + ' kg x 1000 = ' + gram + ' gram');
  return gram;
}

console.log('Kesimpulan:', konversiBerat(2.5), 'gram');`,
    },

    {
      test: /waktu dan jam/,
      build: () => `// Materi: Konversi Waktu - ${title}
// Rumus: 1 jam = 60 menit, 1 menit = 60 detik

function konversiJamKeMenit(jam) {
  console.log('Soal: ubah ' + jam + ' jam menjadi menit');
  const menit = jam * 60;
  console.log('Langkah - 1 jam = 60 menit, jadi ' + jam + ' jam x 60 = ' + menit + ' menit');
  return menit;
}

console.log('Kesimpulan:', konversiJamKeMenit(2), 'menit');`,
    },

    {
      test: /skala/,
      build: () => `// Materi: Skala - ${title}
// Rumus: Jarak sebenarnya = jarak pada peta x skala

function hitungJarakSebenarnya(jarakPeta_cm, skala) {
  console.log('Soal: jarak pada peta ' + jarakPeta_cm + ' cm dengan skala 1:' + skala);
  const jarakSebenarnya_cm = jarakPeta_cm * skala;
  const jarakKm = jarakSebenarnya_cm / 100000;
  console.log('Langkah - Jarak sebenarnya = ' + jarakPeta_cm + ' x ' + skala + ' = ' + jarakSebenarnya_cm + ' cm = ' + jarakKm + ' km');
  return jarakKm;
}

console.log('Kesimpulan: jarak sebenarnya =', hitungJarakSebenarnya(5, 500000), 'km');`,
    },

    {
      test: /estimasi/,
      build: () => `// Materi: Estimasi - ${title}
// Rumus: bulatkan tiap bilangan ke satuan terdekat yang wajar, baru hitung perkiraan hasilnya

function estimasi(a, b) {
  console.log('Soal: perkirakan hasil dari ' + a + ' + ' + b);
  const aBulat = Math.round(a / 10) * 10;
  const bBulat = Math.round(b / 10) * 10;
  console.log('Langkah - Bulatkan ke puluhan terdekat: ' + a + ' -> ' + aBulat + ', ' + b + ' -> ' + bBulat);
  return aBulat + bBulat;
}

console.log('Kesimpulan: estimasi hasil =', estimasi(48, 33));`,
    },

    {
      test: /pola bilangan|menentukan pola|deret angka/,
      build: () => `// Materi: Pola Bilangan - ${title}
// Rumus: cari selisih (beda) antar suku yang berurutan, lalu gunakan untuk memprediksi suku berikutnya

function cariPolaBerikutnya(deret) {
  console.log('Soal: lanjutkan deret ' + JSON.stringify(deret));
  const beda = deret[1] - deret[0];
  console.log('Langkah - Selisih tiap suku = ' + beda);
  const sukuBerikutnya = deret[deret.length - 1] + beda;
  return sukuBerikutnya;
}

console.log('Kesimpulan: suku berikutnya =', cariPolaBerikutnya([2, 5, 8, 11]));`,
    },

    {
      test: /logika sederhana|pikir kritis/,
      build: () => `// Materi: Logika Sederhana - ${title}
// Rumus: sebuah pernyataan bernilai benar (true) atau salah (false), lalu ditarik kesimpulannya

function ujiLogika(bilangan) {
  console.log('Soal: apakah ' + bilangan + ' adalah bilangan genap?');
  const hasil = bilangan % 2 === 0;
  console.log('Langkah - Bilangan genap jika habis dibagi 2 (sisa bagi = 0)');
  return hasil;
}

console.log('Kesimpulan:', ujiLogika(24) ? '24 adalah bilangan genap' : '24 bukan bilangan genap');`,
    },

    {
      test: /kombinasi operasi/,
      build: () => `// Materi: Kombinasi Operasi - ${title}
// Rumus urutan pengerjaan (PEMDAS): kurung -> pangkat -> perkalian/pembagian -> penjumlahan/pengurangan

function kombinasiOperasi() {
  console.log('Soal: (6 + 4) / 2 - 1');
  const kurung = 6 + 4;
  console.log('Langkah 1 - Kurung: 6 + 4 = ' + kurung);
  const bagi = kurung / 2;
  console.log('Langkah 2 - Pembagian: ' + kurung + ' / 2 = ' + bagi);
  const hasil = bagi - 1;
  console.log('Langkah 3 - Pengurangan: ' + bagi + ' - 1 = ' + hasil);
  return hasil;
}

console.log('Kesimpulan: hasil akhir =', kombinasiOperasi());`,
    },

    {
      test: /soal cerita|soal aplikasi|soal tantangan/,
      build: () => `// Materi: Soal Cerita - ${title}
// Rumus: ubah kalimat cerita menjadi operasi matematika, lalu selesaikan langkah demi langkah

function selesaikanSoalCerita() {
  console.log('Soal: Andi punya 24 permen, ia membaginya sama rata ke 4 temannya. Berapa permen tiap teman?');
  console.log('Langkah 1 - Ubah menjadi operasi pembagian: 24 / 4');
  const hasil = 24 / 4;
  console.log('Langkah 2 - Hasil pembagian = ' + hasil);
  return hasil;
}

console.log('Kesimpulan: tiap teman mendapat', selesaikanSoalCerita(), 'permen');`,
    },

    {
      test: /bilangan bulat|pecahan|desimal/,
      build: () => `// Materi: Bilangan Bulat, Pecahan, dan Desimal - ${title}
// Rumus: ketiganya adalah cara berbeda untuk menyatakan nilai yang sama

function jenisBilangan() {
  console.log('Bilangan bulat: -2, -1, 0, 1, 2 (tanpa pecahan/desimal)');
  console.log('Bilangan pecahan: 1/2 artinya 1 dibagi 2');
  console.log('Bilangan desimal: 0.5 (bentuk lain dari 1/2)');
  console.log('Langkah - Ubah pecahan ke desimal dengan membagi pembilang dengan penyebut: 1 / 2 = ' + (1 / 2));
}

jenisBilangan();
console.log('Kesimpulan: 1/2 = 0.5 (nilainya sama, bentuknya berbeda)');`,
    },
  ];

  for (const topic of topics) {
    if (topic.test.test(tl)) {
      return topic.build();
    }
  }

  // Fallback umum: tetap bertema matematika (bukan angka acak tak relevan),
  // memakai judul asli sebagai konteks soal.
  return `// Materi: ${title}
// Rumus: kerjakan operasi hitung sesuai konsep pada topik "${title}" langkah demi langkah

function selesaikanSoal(a, b) {
  console.log('Topik: ${title}');
  console.log('Langkah 1 - Pahami dulu apa yang diketahui dan apa yang ditanyakan pada soal');
  console.log('Langkah 2 - Terapkan rumus/konsep yang sesuai dengan topik ini');

  const hasil = a + b;
  console.log('Langkah 3 - Hitung hasil akhirnya = ' + hasil);
  return hasil;
}

console.log('Kesimpulan: hasil akhir =', selesaikanSoal(12, 5));`;
}

function getDynamicLessonContent(course, lesson) {
  const title = lesson.t;
  const cat = course.cat;
  const courseTitle = course.title;
  const profile = getSubjectProfile(course);
  const isGeneral = getCourseGroup(course) === "umum";

  let paragraphs = [];
  let rawScript = "";

  const effectiveCat =
    cat === "web" && (course.title || "").toLowerCase().includes("php")
      ? "php"
      : cat;

  if (effectiveCat === "php") {
    rawScript = `<?php
// Contoh PHP - ${title}
// Skenario: Mengolah data materi "${title}" secara dinamis di EduCare

$materi = [
    'judul' => '${title}',
    'kursus' => '${courseTitle}',
    'status' => 'aktif'
];

echo "Memuat materi: " . $materi['judul'] . "\\n";

// Simulasi query sederhana ke "database" (array asosiatif)
$daftarSiswa = [
    ['nama' => 'Ali', 'nilai' => 85],
    ['nama' => 'Siti', 'nilai' => 91],
    ['nama' => 'Budi', 'nilai' => 68],
];

foreach ($daftarSiswa as $siswa) {
    if ($siswa['nilai'] >= 80) {
        echo $siswa['nama'] . " lulus dengan nilai " . $siswa['nilai'] . "\\n";
    }
}

echo "Materi '" . $materi['judul'] . "' selesai diproses.";
?>`;
  } else if (cat === "web") {
    const t = title.toLowerCase();
    // --- DETEKSI TOPIK UNTUK KURSUS WEB ---
    // HTML DASAR
    if (t.includes("pengenalan html")) {
      rawScript = `// HTML DOM Simulator - Pengenalan HTML
// Skenario: Membuat struktur dokumen HTML dinamis untuk aplikasi EduCare
const docType = '<!DOCTYPE html>';
const html = document.createElement('html');
html.lang = 'id';

const head = document.createElement('head');
const titleEl = document.createElement('title');
titleEl.textContent = 'Belajar: Pengenalan HTML';
head.appendChild(titleEl);

const body = document.createElement('body');
const header = document.createElement('header');
header.innerHTML = '<h1 style="color:#ffffff">Selamat Datang di Portal HTML</h1>';

const section = document.createElement('section');
section.innerHTML = \`
  <article>
    <h2>Menguasai HTML Dasar</h2>
    <p>Ini adalah demo elemen semantik dinamis di browser.</p>
  </article>
\`;

body.appendChild(header);
body.appendChild(section);
html.appendChild(head);
html.appendChild(body);

console.log('Struktur HTML berhasil dibuat!');
console.log('Output HTML:', html.outerHTML);`;
    } else if (t.includes("struktur dasar")) {
      rawScript = `// HTML DOM - Struktur Dasar
// Skenario: Membangun elemen <html>, <head>, <body> dengan atribut
const html = document.createElement('html');
html.lang = 'id';

const head = document.createElement('head');
const meta = document.createElement('meta');
meta.charset = 'UTF-8';
head.appendChild(meta);

const title = document.createElement('title');
title.textContent = 'Struktur Dasar HTML';
head.appendChild(title);

const body = document.createElement('body');
const header = document.createElement('header');
header.innerHTML = '<h1>Struktur Dasar</h1><p>Ini adalah body dari dokumen HTML.</p>';
body.appendChild(header);

html.appendChild(head);
html.appendChild(body);

console.log('✅ Struktur HTML lengkap dengan head dan body.');
console.log(html.outerHTML);`;
    } else if (t.includes("tags & elemen") || t.includes("tags and elements")) {
      rawScript = `// HTML DOM - Tags & Elemen
// Skenario: Membuat berbagai elemen HTML (div, span, heading, paragraf)
const container = document.createElement('div');
container.style.padding = '20px';
container.style.border = '1px solid #3d7bff';
container.style.borderRadius = '8px';

const h2 = document.createElement('h2');
h2.textContent = 'Contoh Berbagai Tag';
container.appendChild(h2);

const p1 = document.createElement('p');
p1.innerHTML = 'Ini adalah <strong>paragraf</strong> dengan <em>teks miring</em>.';
container.appendChild(p1);

const ul = document.createElement('ul');
['Item satu', 'Item dua', 'Item tiga'].forEach(text => {
    const li = document.createElement('li');
    li.textContent = text;
    ul.appendChild(li);
});
container.appendChild(ul);

const span = document.createElement('span');
span.textContent = 'Ini adalah elemen span.';
span.style.color = '#8b5cf6';
container.appendChild(span);

document.body.appendChild(container);
console.log('✅ Berbagai tag HTML berhasil dibuat.');`;
    } else if (t.includes("form") && t.includes("input")) {
      rawScript = `// HTML DOM - Form & Input
// Skenario: Membuat form dengan berbagai tipe input
const form = document.createElement('form');
form.id = 'demoForm';
form.style.maxWidth = '400px';
form.style.margin = '20px auto';
form.style.padding = '20px';
form.style.border = '1px solid #3d7bff';
form.style.borderRadius = '8px';

const fields = [
    { label: 'Nama', type: 'text', name: 'nama', placeholder: 'Masukkan nama' },
    { label: 'Email', type: 'email', name: 'email', placeholder: 'email@contoh.com' },
    { label: 'Usia', type: 'number', name: 'usia', placeholder: '17' },
    { label: 'Jenis Kelamin', type: 'select', name: 'gender', options: ['Laki-laki', 'Perempuan'] },
    { label: 'Setuju', type: 'checkbox', name: 'agree' }
];

fields.forEach(f => {
    const div = document.createElement('div');
    div.style.marginBottom = '10px';

    const label = document.createElement('label');
    label.textContent = f.label + ': ';
    label.style.display = 'inline-block';
    label.style.width = '100px';
    div.appendChild(label);

    let input;
    if (f.type === 'select') {
        input = document.createElement('select');
        input.name = f.name;
        f.options.forEach(opt => {
            const option = document.createElement('option');
            option.textContent = opt;
            input.appendChild(option);
        });
    } else if (f.type === 'checkbox') {
        input = document.createElement('input');
        input.type = f.type;
        input.name = f.name;
    } else {
        input = document.createElement('input');
        input.type = f.type;
        input.name = f.name;
        input.placeholder = f.placeholder || '';
    }
    div.appendChild(input);
    form.appendChild(div);
});

const submitBtn = document.createElement('button');
submitBtn.type = 'submit';
submitBtn.textContent = 'Kirim';
submitBtn.style.padding = '8px 20px';
submitBtn.style.background = '#3d7bff';
submitBtn.style.color = 'white';
submitBtn.style.border = 'none';
submitBtn.style.borderRadius = '4px';
form.appendChild(submitBtn);

document.body.appendChild(form);
console.log('✅ Form dengan berbagai input berhasil dibuat.');
console.log('Coba isi dan submit (simulasi).');`;
    } else if (t.includes("tabel") && t.includes("list")) {
      rawScript = `// HTML DOM - Tabel & List
// Skenario: Membuat tabel data dan list (ordered & unordered)
const wrapper = document.createElement('div');
wrapper.style.padding = '20px';

// Tabel
const table = document.createElement('table');
table.style.borderCollapse = 'collapse';
table.style.width = '100%';
table.style.marginBottom = '20px';

const thead = document.createElement('thead');
const trHead = document.createElement('tr');
['Nama', 'Umur', 'Kota'].forEach(text => {
    const th = document.createElement('th');
    th.textContent = text;
    th.style.border = '1px solid #ccc';
    th.style.padding = '8px';
    th.style.background = '#3d7bff';
    th.style.color = 'white';
    trHead.appendChild(th);
});
thead.appendChild(trHead);
table.appendChild(thead);

const tbody = document.createElement('tbody');
const data = [
    ['Andi', '25', 'Jakarta'],
    ['Budi', '30', 'Bandung'],
    ['Cici', '22', 'Surabaya']
];
data.forEach(row => {
    const tr = document.createElement('tr');
    row.forEach(cell => {
        const td = document.createElement('td');
        td.textContent = cell;
        td.style.border = '1px solid #ccc';
        td.style.padding = '8px';
        tr.appendChild(td);
    });
    tbody.appendChild(tr);
});
table.appendChild(tbody);
wrapper.appendChild(table);

// List
const ol = document.createElement('ol');
ol.innerHTML = '<li>Langkah pertama</li><li>Langkah kedua</li><li>Langkah ketiga</li>';
wrapper.appendChild(ol);

const ul = document.createElement('ul');
ul.innerHTML = '<li>Poin A</li><li>Poin B</li><li>Poin C</li>';
wrapper.appendChild(ul);

document.body.appendChild(wrapper);
console.log('✅ Tabel dan list berhasil dibuat.');`;
    } else if (t.includes("link") && t.includes("media")) {
      // ======== TAMBAHAN KHUSUS UNTUK LINK & MEDIA ========
      rawScript = `// HTML DOM - Link & Media
// Skenario: Membuat elemen link, gambar, dan video secara dinamis

// Link
const link = document.createElement('a');
link.href = 'https://educare.id';
link.target = '_blank';
link.textContent = 'Kunjungi EduCare';
link.style.color = '#3d7bff';
link.style.textDecoration = 'underline';
link.style.fontSize = '1.2rem';

// Gambar
const img = document.createElement('img');
img.src = 'https://via.placeholder.com/200x100?text=EduCare';
img.alt = 'Logo EduCare';
img.style.width = '200px';
img.style.borderRadius = '8px';
img.style.margin = '10px 0';

// Video (sumber dari W3Schools)
const video = document.createElement('video');
video.src = 'https://www.w3schools.com/html/mov_bbb.mp4';
video.controls = true;
video.width = 320;
video.style.borderRadius = '8px';

// Audio (opsional)
const audio = document.createElement('audio');
audio.src = 'https://www.w3schools.com/html/horse.ogg';
audio.controls = true;
audio.style.marginTop = '10px';

// Tambahkan ke body
const container = document.createElement('div');
container.style.padding = '20px';
container.style.border = '1px solid #ccc';
container.style.borderRadius = '8px';
container.append(link, document.createElement('br'), img, document.createElement('br'), video, document.createElement('br'), audio);
document.body.appendChild(container);

console.log('✅ Elemen Link & Media berhasil ditambahkan ke halaman!');
console.log('📌 Coba klik link, lihat gambar, dan putar video.');`;
    } else if (t.includes("semantic html")) {
      rawScript = `// HTML DOM - Semantic HTML
// Skenario: Membangun struktur halaman dengan elemen semantik
const container = document.createElement('div');

// Header
const header = document.createElement('header');
header.style.background = '#3d7bff';
header.style.color = 'white';
header.style.padding = '10px';
header.innerHTML = \`
  <h1>${window.EduCareI18n?.t('site.title') || 'Website Saya'}</h1>
  <nav>
    <a href="#">${window.EduCareI18n?.t('nav.home') || 'Home'}</a> |
    <a href="#">${window.EduCareI18n?.t('nav.about') || 'About'}</a> |
    <a href="#">${window.EduCareI18n?.t('nav.contact') || 'Contact'}</a>
  </nav>\`;
container.appendChild(header);

// Main
const main = document.createElement('main');
main.style.display = 'flex';
main.style.gap = '20px';
main.style.padding = '20px';

// Article
const article = document.createElement('article');
article.style.flex = '3';
article.innerHTML = '<h2>Artikel Utama</h2><p>Ini adalah konten artikel yang menggunakan elemen <code>&lt;article&gt;</code>.</p>';
main.appendChild(article);

// Aside
const aside = document.createElement('aside');
aside.style.flex = '1';
aside.style.background = '#f0f0f0';
aside.style.padding = '10px';
aside.innerHTML = '<h3>Sidebar</h3><p>Informasi tambahan di sini.</p>';
main.appendChild(aside);

container.appendChild(main);

// Footer
const footer = document.createElement('footer');
footer.style.background = '#333';
footer.style.color = 'white';
footer.style.padding = '10px';
footer.style.textAlign = 'center';
footer.textContent = '© 2026 EduCare - Belajar Semantic HTML';
container.appendChild(footer);

document.body.appendChild(container);
console.log('✅ Halaman dengan elemen semantik (header, main, article, aside, footer) berhasil dibuat.');`;
    } else if (t.includes("atribut html")) {
      rawScript = `// HTML DOM - Atribut HTML
// Skenario: Menambahkan dan memanipulasi atribut elemen
const div = document.createElement('div');
div.id = 'mainContainer';
div.className = 'box highlight';
div.setAttribute('data-info', 'ini adalah atribut data');
div.style.padding = '20px';
div.style.border = '2px solid #3d7bff';
div.style.borderRadius = '8px';

const p = document.createElement('p');
p.textContent = 'Elemen ini memiliki berbagai atribut.';
p.setAttribute('title', 'Tooltip dari atribut title');

div.appendChild(p);
document.body.appendChild(div);

console.log('✅ Atribut yang ditambahkan:');
console.log('id:', div.id);
console.log('className:', div.className);
console.log('data-info:', div.getAttribute('data-info'));
console.log('title pada p:', p.getAttribute('title'));`;
    } else if (t.includes("validasi dasar")) {
      rawScript = `// HTML DOM - Validasi Dasar
// Skenario: Menggunakan Constraint Validation API untuk validasi form
const form = document.createElement('form');
form.id = 'validateForm';
form.style.maxWidth = '300px';
form.style.margin = '20px auto';

const input = document.createElement('input');
input.type = 'text';
input.required = true;
input.minLength = 5;
input.placeholder = 'Minimal 5 karakter';
input.style.width = '100%';
input.style.padding = '8px';

const btn = document.createElement('button');
btn.type = 'submit';
btn.textContent = 'Kirim';
btn.style.marginTop = '10px';

form.appendChild(input);
form.appendChild(btn);
document.body.appendChild(form);

form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (input.checkValidity()) {
        console.log('✅ Validasi berhasil! Nilai:', input.value);
    } else {
        console.log('❌ Validasi gagal:', input.validationMessage);
    }
});

console.log('✅ Form dengan validasi dasar siap. Coba submit dengan input kosong atau <5 karakter.');`;
    } else if (t.includes("praktik html")) {
      rawScript = `// HTML DOM - Praktik HTML
// Skenario: Menggabungkan semua elemen HTML yang telah dipelajari
const container = document.createElement('div');
container.style.padding = '20px';
container.style.border = '1px solid #3d7bff';
container.style.borderRadius = '8px';

// Heading
container.innerHTML = '<h1>Praktik HTML</h1><p>Ini adalah hasil praktik dari berbagai elemen HTML.</p>';

// Link & Gambar
const link = document.createElement('a');
link.href = '#';
link.textContent = 'Klik di sini';
link.style.display = 'block';
link.style.margin = '10px 0';
container.appendChild(link);

const img = document.createElement('img');
img.src = 'https://via.placeholder.com/150';
img.alt = 'Contoh gambar';
img.style.width = '150px';
container.appendChild(img);

// Tabel sederhana
const table = document.createElement('table');
table.style.border = '1px solid #ccc';
const tr = document.createElement('tr');
['Nama', 'Nilai'].forEach(text => {
    const th = document.createElement('th');
    th.textContent = text;
    th.style.border = '1px solid #ccc';
    th.style.padding = '5px';
    tr.appendChild(th);
});
table.appendChild(tr);
container.appendChild(table);

// Form
const form = document.createElement('form');
form.innerHTML = '<label>Nama: <input type="text" name="name"></label><br><button type="submit">Kirim</button>';
container.appendChild(form);

document.body.appendChild(container);
console.log('✅ Praktik HTML selesai. Semua elemen dasar telah diterapkan.');`;
      // CSS DASAR
    } else if (t.includes("selector css")) {
      rawScript = `// CSS - Selector CSS
// Skenario: Menambahkan style menggunakan berbagai selector
const style = document.createElement('style');
style.textContent = \`
    /* Selector elemen */
    h2 { color: #3d7bff; }
    /* Selector class */
    .highlight { background: #f0f0f0; padding: 10px; }
    /* Selector ID */
    #special { border: 2px solid #8b5cf6; border-radius: 8px; }
    /* Selector atribut */
    input[type="text"] { border: 1px solid #ccc; padding: 6px; }
    /* Selector pseudo-class */
    button:hover { background: #8b5cf6; color: white; }
\`;
document.head.appendChild(style);

// Buat elemen untuk demonstrasi
document.body.innerHTML += \`
    <h2>Ini heading dengan selector elemen</h2>
    <div class="highlight">Ini div dengan class "highlight"</div>
    <div id="special">Ini div dengan ID "special"</div>
    <input type="text" placeholder="Selector atribut">
    <button>Hover saya</button>
\`;
console.log('✅ Berbagai selector CSS telah diterapkan.');`;
    } else if (t.includes("box model")) {
      rawScript = `// CSS - Box Model
// Skenario: Mengatur margin, border, padding, dan content
const style = document.createElement('style');
style.textContent = \`
    .box-model {
        width: 200px;
        height: 100px;
        padding: 20px;
        border: 5px solid #3d7bff;
        margin: 30px auto;
        background: #f9f9f9;
        text-align: center;
        box-sizing: border-box; /* agar padding & border masuk ke dalam width */
    }
\`;
document.head.appendChild(style);

const div = document.createElement('div');
div.className = 'box-model';
div.textContent = 'Kotak dengan Box Model';
document.body.appendChild(div);

console.log('✅ Box model: margin, border, padding, content.');`;
    } else if (t.includes("warna") && t.includes("tipografi")) {
      rawScript = `// CSS - Warna & Tipografi
// Skenario: Mengatur warna dan font
const style = document.createElement('style');
style.textContent = \`
    .typography {
        color: #2c3e50;
        font-family: 'Georgia', serif;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
        background: #ecf0f1;
        padding: 20px;
        border-radius: 8px;
    }
    .highlight-text {
        color: #e74c3c;
        background: #f1c40f;
        padding: 4px 8px;
        border-radius: 4px;
    }
\`;
document.head.appendChild(style);

const div = document.createElement('div');
div.className = 'typography';
div.innerHTML = 'Ini adalah teks dengan <span class="highlight-text">warna dan tipografi</span> yang menarik.';
document.body.appendChild(div);

console.log('✅ Warna dan tipografi telah diterapkan.');`;
    } else if (t.includes("margin") && t.includes("padding")) {
      rawScript = `// CSS - Margin & Padding
// Skenario: Membedakan margin dan padding
const style = document.createElement('style');
style.textContent = \`
    .box-margin {
        margin: 20px;
        background: #3d7bff;
        color: white;
        padding: 10px;
        border-radius: 4px;
    }
    .box-padding {
        padding: 40px 20px;
        background: #8b5cf6;
        color: white;
        border-radius: 8px;
        margin: 10px 0;
    }
    .container {
        border: 2px dashed #ccc;
        padding: 10px;
    }
\`;
document.head.appendChild(style);

const container = document.createElement('div');
container.className = 'container';
container.innerHTML = \`
    <div class="box-margin">Margin 20px (jarak antar kotak)</div>
    <div class="box-padding">Padding 40px (jarak dalam kotak)</div>
\`;
document.body.appendChild(container);

console.log('✅ Margin (luar) dan Padding (dalam) telah didemonstrasikan.');`;
    } else if (t.includes("display") && t.includes("position")) {
      rawScript = `// CSS - Display & Position
// Skenario: Mengubah display (block, inline, flex) dan position (relative, absolute, fixed)
const style = document.createElement('style');
style.textContent = \`
    .display-demo {
        display: flex;
        gap: 10px;
        padding: 20px;
        background: #eee;
        border-radius: 8px;
    }
    .display-demo div {
        background: #3d7bff;
        color: white;
        padding: 10px;
        border-radius: 4px;
        flex: 1;
        text-align: center;
    }
    .relative-box {
        position: relative;
        top: 20px;
        left: 20px;
        background: #8b5cf6;
        color: white;
        padding: 10px;
        border-radius: 4px;
        width: 200px;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="display-demo">
        <div>Flex item 1</div>
        <div>Flex item 2</div>
        <div>Flex item 3</div>
    </div>
    <div class="relative-box">Relative: bergeser 20px ke bawah & kanan</div>
\`;
console.log('✅ Display flex dan position relative telah diterapkan.');`;
    } else if (t.includes("background") && t.includes("border")) {
      rawScript = `// CSS - Background & Border
// Skenario: Mengatur background dan border
const style = document.createElement('style');
style.textContent = \`
    .bg-border {
        background: linear-gradient(135deg, #3d7bff, #8b5cf6);
        color: white;
        padding: 30px;
        border: 4px solid #fff;
        border-radius: 16px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        text-align: center;
        font-size: 1.2rem;
        width: 300px;
        margin: 20px auto;
    }
\`;
document.head.appendChild(style);

const div = document.createElement('div');
div.className = 'bg-border';
div.textContent = 'Background gradien dan border elegan';
document.body.appendChild(div);

console.log('✅ Background dan border telah diatur.');`;
    } else if (t.includes("pseudo class")) {
      rawScript = `// CSS - Pseudo Class
// Skenario: Menggunakan pseudo-class :hover, :focus, :nth-child
const style = document.createElement('style');
style.textContent = \`
    .pseudo-list li {
        padding: 8px;
        transition: background 0.3s;
        list-style: none;
        border-bottom: 1px solid #ccc;
    }
    .pseudo-list li:hover {
        background: #3d7bff;
        color: white;
        cursor: pointer;
    }
    .pseudo-list li:nth-child(odd) {
        background: #f0f0f0;
    }
    .pseudo-input:focus {
        border: 2px solid #8b5cf6;
        outline: none;
        box-shadow: 0 0 10px rgba(139,92,246,0.5);
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <ul class="pseudo-list">
        <li>Item 1 (hover saya)</li>
        <li>Item 2 (hover saya)</li>
        <li>Item 3 (hover saya)</li>
    </ul>
    <input class="pseudo-input" placeholder="Fokus di sini" style="padding:8px; margin-top:10px;">
\`;
console.log('✅ Pseudo-class :hover, :nth-child, :focus diterapkan.');`;
    } else if (t.includes("transition dasar")) {
      rawScript = `// CSS - Transition Dasar
// Skenario: Memberikan efek transisi pada perubahan properti
const style = document.createElement('style');
style.textContent = \`
    .transition-box {
        width: 100px;
        height: 100px;
        background: #3d7bff;
        transition: all 0.5s ease;
        border-radius: 4px;
        margin: 20px auto;
        text-align: center;
        line-height: 100px;
        color: white;
        cursor: pointer;
    }
    .transition-box:hover {
        width: 200px;
        height: 200px;
        background: #8b5cf6;
        border-radius: 50%;
        transform: rotate(180deg);
    }
\`;
document.head.appendChild(style);

const div = document.createElement('div');
div.className = 'transition-box';
div.textContent = 'Hover saya';
document.body.appendChild(div);

console.log('✅ Transisi dasar: hover mengubah ukuran, warna, bentuk, dan rotasi.');`;
    } else if (t.includes("responsifitas")) {
      rawScript = `// CSS - Responsifitas Dasar
// Skenario: Menggunakan media query untuk tampilan responsif
const style = document.createElement('style');
style.textContent = \`
    .responsive-container {
        display: flex;
        gap: 20px;
        padding: 20px;
        background: #eee;
        border-radius: 8px;
        flex-wrap: wrap;
    }
    .responsive-item {
        background: #3d7bff;
        color: white;
        padding: 20px;
        flex: 1 1 200px;
        text-align: center;
        border-radius: 8px;
    }
    @media (max-width: 600px) {
        .responsive-container {
            flex-direction: column;
        }
        .responsive-item {
            background: #8b5cf6;
        }
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="responsive-container">
        <div class="responsive-item">Item 1</div>
        <div class="responsive-item">Item 2</div>
        <div class="responsive-item">Item 3</div>
    </div>
    <p style="text-align:center;font-size:0.9rem;">* Ubah lebar browser untuk melihat efek responsif</p>
\`;
console.log('✅ Media query untuk responsifitas dasar telah diterapkan.');`;
    } else if (t.includes("praktik css")) {
      rawScript = `// CSS - Praktik CSS
// Skenario: Menggabungkan berbagai konsep CSS
const style = document.createElement('style');
style.textContent = \`
    .praktek-css {
        max-width: 500px;
        margin: 20px auto;
        padding: 20px;
        background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
        border-radius: 16px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        text-align: center;
    }
    .praktek-css h2 {
        color: #2c3e50;
        font-family: 'Arial', sans-serif;
        border-bottom: 3px solid #3d7bff;
        display: inline-block;
        padding-bottom: 5px;
    }
    .praktek-css button {
        background: #3d7bff;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 30px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    .praktek-css button:hover {
        background: #8b5cf6;
        transform: scale(1.05);
    }
    .praktek-css .box {
        background: white;
        padding: 15px;
        margin: 15px 0;
        border-radius: 8px;
        border-left: 5px solid #3d7bff;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="praktek-css">
        <h2>Praktik CSS</h2>
        <div class="box">Ini adalah box dengan border kiri biru</div>
        <button>Klik Saya</button>
        <p style="margin-top:12px;font-size:0.8rem;color:#666;">* Gabungan selector, box model, warna, transisi, dan responsif</p>
    </div>
\`;
console.log('✅ Praktik CSS: semua konsep dasar diterapkan dalam satu komponen.');`;
      // CSS MODERN
    } else if (t.includes("flexbox dasar")) {
      rawScript = `// CSS Modern - Flexbox Dasar
// Skenario: Menerapkan Flexbox untuk tata letak
const style = document.createElement('style');
style.textContent = \`
    .flex-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: center;
        gap: 20px;
        background: #f0f0f0;
        padding: 30px;
        border-radius: 12px;
        min-height: 200px;
    }
    .flex-item {
        background: #3d7bff;
        color: white;
        padding: 20px 40px;
        border-radius: 8px;
        font-weight: bold;
        flex: 0 1 auto;
    }
    .flex-item:nth-child(2) {
        background: #8b5cf6;
        align-self: flex-end;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="flex-container">
        <div class="flex-item">Item 1</div>
        <div class="flex-item">Item 2 (align-self: flex-end)</div>
        <div class="flex-item">Item 3</div>
        <div class="flex-item">Item 4</div>
    </div>
\`;
console.log('✅ Flexbox dasar: display flex, justify-content, align-items, flex-wrap.');`;
    } else if (t.includes("css grid layout") || t.includes("grid layout")) {
      rawScript = `// CSS Modern - CSS Grid Layout
// Skenario: Membuat layout grid dengan kolom dan baris
const style = document.createElement('style');
style.textContent = \`
    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        background: #e8e8e8;
        padding: 20px;
        border-radius: 12px;
    }
    .grid-item {
        background: #3d7bff;
        color: white;
        padding: 30px 10px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
    }
    .grid-item:nth-child(1) {
        grid-column: 1 / 3;
        background: #8b5cf6;
    }
    .grid-item:nth-child(6) {
        grid-column: 2 / 4;
        background: #e74c3c;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="grid-container">
        <div class="grid-item">1 (span 2 kolom)</div>
        <div class="grid-item">2</div>
        <div class="grid-item">3</div>
        <div class="grid-item">4</div>
        <div class="grid-item">5</div>
        <div class="grid-item">6 (span 2 kolom)</div>
    </div>
\`;
console.log('✅ CSS Grid: grid-template-columns, gap, dan grid-column.');`;
    } else if (t.includes("responsive design")) {
      rawScript = `// CSS Modern - Responsive Design
// Skenario: Layout responsif dengan grid dan media query
const style = document.createElement('style');
style.textContent = \`
    .responsive-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        padding: 20px;
        background: #f5f5f5;
        border-radius: 12px;
    }
    .responsive-grid > div {
        background: #3d7bff;
        color: white;
        padding: 40px 10px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
    }
    @media (max-width: 600px) {
        .responsive-grid {
            grid-template-columns: 1fr;
        }
        .responsive-grid > div {
            background: #8b5cf6;
        }
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="responsive-grid">
        <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div>
    </div>
    <p style="text-align:center;font-size:0.8rem;">* Ubah lebar browser untuk melihat perubahan</p>
\`;
console.log('✅ Responsive Design dengan auto-fit dan media query.');`;
    } else if (t.includes("animasi css")) {
      rawScript = `// CSS Modern - Animasi CSS
// Skenario: Membuat animasi dengan @keyframes
const style = document.createElement('style');
style.textContent = \`
    @keyframes bounce {
        0%   { transform: translateY(0); }
        50%  { transform: translateY(-30px); }
        100% { transform: translateY(0); }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    .animate-box {
        width: 100px;
        height: 100px;
        background: #3d7bff;
        margin: 20px auto;
        border-radius: 12px;
        animation: bounce 1.5s infinite ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    .spin-box {
        width: 80px;
        height: 80px;
        background: #8b5cf6;
        margin: 20px auto;
        border-radius: 50%;
        animation: spin 3s linear infinite;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="animate-box">Bounce</div>
    <div class="spin-box">Spin</div>
\`;
console.log('✅ Animasi CSS: @keyframes dan properti animation.');`;
    } else if (t.includes("variable css") || t.includes("custom properties")) {
      rawScript = `// CSS Modern - Variable CSS (Custom Properties)
// Skenario: Menggunakan variabel CSS untuk tema
const style = document.createElement('style');
style.textContent = \`
    :root {
        --primary: #3d7bff;
        --secondary: #8b5cf6;
        --bg: #f0f0f0;
        --text: #2c3e50;
        --radius: 12px;
    }
    .variable-demo {
        background: var(--bg);
        color: var(--text);
        padding: 30px;
        border-radius: var(--radius);
        border: 2px solid var(--primary);
        max-width: 400px;
        margin: 20px auto;
        text-align: center;
    }
    .variable-demo h3 {
        color: var(--primary);
    }
    .variable-demo button {
        background: var(--secondary);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: var(--radius);
        cursor: pointer;
        font-weight: bold;
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="variable-demo">
        <h3>Menggunakan Variabel CSS</h3>
        <p>Warna, radius, dan background dikontrol oleh variabel.</p>
        <button>Klik Saya</button>
    </div>
\`;
console.log('✅ CSS Variables: --primary, --secondary, --bg, --text, --radius.');`;
    } else if (t.includes("dark mode")) {
      rawScript = `// CSS Modern - Dark Mode
// Skenario: Mengimplementasikan dark mode dengan prefers-color-scheme
const style = document.createElement('style');
style.textContent = \`
    .dark-mode-demo {
        padding: 30px;
        border-radius: 12px;
        max-width: 400px;
        margin: 20px auto;
        text-align: center;
        background: #fff;
        color: #222;
        border: 1px solid #ccc;
        transition: background 0.3s, color 0.3s;
    }
    @media (prefers-color-scheme: dark) {
        .dark-mode-demo {
            background: #222;
            color: #eee;
            border-color: #555;
        }
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="dark-mode-demo">
        <h3>Dark Mode Otomatis</h3>
        <p>Warna berubah sesuai preferensi sistem operasi.</p>
        <span style="font-size:0.8rem;">* Ganti tema sistem untuk melihat efek</span>
    </div>
\`;
console.log('✅ Dark Mode dengan @media (prefers-color-scheme).');`;
    } else if (t.includes("utility class")) {
      rawScript = `// CSS Modern - Utility Class
// Skenario: Membuat kelas utilitas untuk tata letak dan styling
const style = document.createElement('style');
style.textContent = \`
    .flex { display: flex; }
    .flex-center { justify-content: center; align-items: center; }
    .gap-1 { gap: 10px; }
    .gap-2 { gap: 20px; }
    .p-2 { padding: 20px; }
    .m-2 { margin: 20px; }
    .bg-primary { background: #3d7bff; color: white; }
    .bg-secondary { background: #8b5cf6; color: white; }
    .rounded { border-radius: 8px; }
    .text-center { text-align: center; }
    .shadow { box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="flex flex-center gap-2 p-2 m-2 bg-primary rounded shadow">
        <span>Utility 1</span>
        <span>Utility 2</span>
        <span>Utility 3</span>
    </div>
    <div class="flex flex-center gap-1 p-2 bg-secondary rounded text-center">
        <span>Kelas utilitas memudahkan styling</span>
    </div>
\`;
console.log('✅ Utility classes: flex, gap, padding, background, rounded, shadow.');`;
    } else if (t.includes("layout modern")) {
      rawScript = `// CSS Modern - Layout Modern
// Skenario: Menggabungkan Flexbox dan Grid untuk layout kompleks
const style = document.createElement('style');
style.textContent = \`
    .modern-layout {
        display: grid;
        grid-template-columns: 1fr 3fr 1fr;
        gap: 20px;
        padding: 20px;
        background: #f5f5f5;
        border-radius: 12px;
    }
    .modern-layout > div {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    .modern-layout .sidebar {
        background: #3d7bff;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .modern-layout .main {
        background: #fafafa;
        border: 2px solid #3d7bff;
    }
    .modern-layout .main .inner {
        display: flex;
        gap: 10px;
        justify-content: space-around;
        margin-top: 10px;
    }
    .modern-layout .main .inner span {
        background: #8b5cf6;
        color: white;
        padding: 10px;
        border-radius: 8px;
        flex: 1;
    }
    @media (max-width: 700px) {
        .modern-layout {
            grid-template-columns: 1fr;
        }
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="modern-layout">
        <div class="sidebar">Sidebar (Flex column)</div>
        <div class="main">
            <h3>Main Content</h3>
            <div class="inner">
                <span>Flex item</span>
                <span>Flex item</span>
                <span>Flex item</span>
            </div>
        </div>
        <div>Aside</div>
    </div>
\`;
console.log('✅ Layout modern: grid + flex + media query.');`;
    } else if (t.includes("pseudoelement")) {
      rawScript = `// CSS Modern - Pseudoelement
// Skenario: Menggunakan ::before dan ::after untuk dekorasi
const style = document.createElement('style');
style.textContent = \`
    .pseudo-element {
        position: relative;
        padding: 20px;
        background: #f0f0f0;
        border-radius: 12px;
        text-align: center;
        max-width: 400px;
        margin: 20px auto;
        font-size: 1.2rem;
    }
    .pseudo-element::before {
        content: "🔹 ";
        font-size: 1.5rem;
    }
    .pseudo-element::after {
        content: " 🔹";
        font-size: 1.5rem;
    }
    .pseudo-element:hover::before {
        content: "⭐ ";
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="pseudo-element">Hover saya untuk melihat perubahan</div>
\`;
console.log('✅ Pseudoelement ::before dan ::after dengan konten dinamis.');`;
    } else if (t.includes("praktik layout")) {
      rawScript = `// CSS Modern - Praktik Layout
// Skenario: Membangun layout halaman dengan kombinasi teknik modern
const style = document.createElement('style');
style.textContent = \`
    .praktik-layout {
        display: grid;
        grid-template-areas:
            "header header"
            "nav main"
            "footer footer";
        grid-template-columns: 200px 1fr;
        gap: 10px;
        padding: 20px;
        background: #eee;
        border-radius: 12px;
    }
    .praktik-layout > header {
        grid-area: header;
        background: #3d7bff;
        color: white;
        padding: 15px;
        text-align: center;
        border-radius: 8px;
    }
    .praktik-layout > nav {
        grid-area: nav;
        background: #8b5cf6;
        color: white;
        padding: 15px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .praktik-layout > main {
        grid-area: main;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .praktik-layout > footer {
        grid-area: footer;
        background: #333;
        color: white;
        padding: 10px;
        text-align: center;
        border-radius: 8px;
    }
    @media (max-width: 600px) {
        .praktik-layout {
            grid-template-areas:
                "header"
                "nav"
                "main"
                "footer";
            grid-template-columns: 1fr;
        }
    }
\`;
document.head.appendChild(style);

document.body.innerHTML += \`
    <div class="praktik-layout">
        <header>Header</header>
        <nav>Nav<br>Link 1<br>Link 2</nav>
        <main>
            <h3>Main Content</h3>
            <p>Ini adalah layout modern dengan grid areas, flex, dan media query.</p>
        </main>
        <footer>Footer</footer>
    </div>
\`;
console.log('✅ Praktik Layout: grid-area, flex, dan responsif.');`;
      // JAVASCRIPT (BAB 4)
    } else if (t.includes("js fundamentals")) {
      rawScript = `// JavaScript Fundamentals
// Skenario: Demonstrasi variabel, tipe data, dan fungsi sederhana
let nama = "EduCare";
const tahun = 2026;
var aktif = true;

function sapa(pengguna) {
    return \`Halo, \${pengguna}!\`;
}

console.log("Nama:", nama);
console.log("Tahun:", tahun);
console.log("Aktif:", aktif);
console.log(sapa("Pelajar"));

// Tipe data
let angka = 10;
let teks = "Belajar JS";
let boolean = true;
let array = [1, 2, 3];
let obj = { key: "value" };

console.log("Tipe angka:", typeof angka);
console.log("Tipe teks:", typeof teks);
console.log("Tipe boolean:", typeof boolean);
console.log("Tipe array:", typeof array);
console.log("Tipe obj:", typeof obj);`;
    } else if (t.includes("dom manipulation")) {
      rawScript = `// JavaScript - DOM Manipulation
// Skenario: Membuat, mengubah, dan menghapus elemen DOM
const container = document.createElement('div');
container.id = 'domContainer';
container.style.padding = '20px';
container.style.border = '2px solid #3d7bff';
container.style.borderRadius = '8px';

// Buat elemen baru
const p = document.createElement('p');
p.textContent = 'Ini adalah paragraf baru.';
container.appendChild(p);

// Ubah konten
const h2 = document.createElement('h2');
h2.textContent = 'DOM Manipulation';
container.prepend(h2);

// Tambahkan atribut
container.setAttribute('data-created', new Date().toISOString());

// Hapus elemen setelah 5 detik (simulasi)
setTimeout(() => {
    if (container.contains(p)) {
        container.removeChild(p);
        console.log('✅ Paragraf dihapus setelah 5 detik.');
    }
}, 5000);

document.body.appendChild(container);
console.log('✅ Elemen baru ditambahkan, paragraf akan dihapus otomatis dalam 5 detik.');`;
    } else if (t.includes("events & functions")) {
      rawScript = `// JavaScript - Events & Functions
// Skenario: Menambahkan event listener ke tombol
const btn = document.createElement('button');
btn.textContent = 'Klik Saya';
btn.style.padding = '10px 24px';
btn.style.background = '#3d7bff';
btn.style.color = 'white';
btn.style.border = 'none';
btn.style.borderRadius = '8px';
btn.style.cursor = 'pointer';

// Fungsi event handler
function handleClick() {
    alert('Tombol diklik!');
    console.log('✅ Event click terpicu.');
}

// Tambahkan event
btn.addEventListener('click', handleClick);

// Event lain (hover)
btn.addEventListener('mouseenter', () => {
    btn.style.background = '#8b5cf6';
});
btn.addEventListener('mouseleave', () => {
    btn.style.background = '#3d7bff';
});

document.body.appendChild(btn);
console.log('✅ Tombol dengan event click, mouseenter, mouseleave.');`;
    } else if (t.includes("async & fetch")) {
      rawScript = `// JavaScript - Async & Fetch
// Skenario: Mengambil data dari API dummy
async function ambilData() {
    try {
        console.log('⏳ Mengambil data dari API...');
        const response = await fetch('https://jsonplaceholder.typicode.com/posts/1');
        if (!response.ok) throw new Error('Gagal fetch');
        const data = await response.json();
        console.log('✅ Data berhasil diambil:', data);
        return data;
    } catch (error) {
        console.error('❌ Error:', error.message);
    }
}

// Panggil fungsi
ambilData();`;
    } else if (t.includes("array & object")) {
      rawScript = `// JavaScript - Array & Object
// Skenario: Menggunakan method array dan manipulasi object
const siswa = [
    { nama: 'Andi', nilai: 85 },
    { nama: 'Budi', nilai: 92 },
    { nama: 'Cici', nilai: 78 }
];

// Filter nilai >= 80
const lulus = siswa.filter(s => s.nilai >= 80);
console.log('Siswa lulus:', lulus);

// Map untuk mendapatkan nama saja
const namaSiswa = siswa.map(s => s.nama);
console.log('Nama semua siswa:', namaSiswa);

// Reduce untuk total nilai
const totalNilai = siswa.reduce((acc, s) => acc + s.nilai, 0);
console.log('Total nilai:', totalNilai);

// Object destructuring
const { nama, nilai } = siswa[0];
console.log(\`Siswa pertama: \${nama} dengan nilai \${nilai}\`);`;
    } else if (t.includes("loop & condition")) {
      rawScript = `// JavaScript - Loop & Condition
// Skenario: Menggunakan perulangan dan percabangan
const angka = [5, 12, 8, 20, 3];

// For loop
for (let i = 0; i < angka.length; i++) {
    if (angka[i] > 10) {
        console.log(\`Angka \${angka[i]} lebih besar dari 10\`);
    } else {
        console.log(\`Angka \${angka[i]} tidak lebih dari 10\`);
    }
}

// While loop
let counter = 0;
while (counter < 5) {
    console.log('Iterasi ke-', counter + 1);
    counter++;
}

// For...of
for (const num of angka) {
    console.log('Nilai:', num);
}`;
    } else if (t.includes("scope & hoisting")) {
      rawScript = `// JavaScript - Scope & Hoisting
// Skenario: Demonstrasi hoisting dan scope (var, let, const)

// Hoisting (var)
console.log(x); // undefined (hoisting)
var x = 5;

// let tidak di-hoist (akan error jika diakses sebelum deklarasi)
// console.log(y); // ReferenceError

let y = 10;
const z = 20;

function testScope() {
    var localVar = 'local';
    let blockLet = 'block';
    if (true) {
        var functionScope = 'function scope'; // var function-scoped
        let blockScope = 'block scope'; // let block-scoped
        console.log(blockScope);
    }
    console.log(functionScope); // bisa diakses
    // console.log(blockScope); // error
}

testScope();
console.log('x =', x, 'y =', y, 'z =', z);`;
    } else if (t.includes("es6 basics")) {
      rawScript = `// JavaScript - ES6 Basics
// Skenario: Menggunakan fitur ES6: arrow function, destructuring, template literal, spread

// Arrow function
const kaliDua = (a) => a * 2;
console.log('Kali dua 5 =', kaliDua(5));

// Destructuring array
const [first, second] = [10, 20];
console.log('Destructuring array:', first, second);

// Destructuring object
const user = { id: 1, name: 'EduCare' };
const { name, id } = user;
console.log(\`ID: \${id}, Nama: \${name}\`);

// Spread operator
const arr1 = [1, 2];
const arr2 = [...arr1, 3, 4];
console.log('Spread array:', arr2);

// Template literal
const greeting = \`Halo, \${name}!\`;
console.log(greeting);

// Default parameter
function sapa(nama = 'Tamu') {
    console.log(\`Halo \${nama}\`);
}
sapa();
sapa('Andi');`;
    } else if (t.includes("form handling")) {
      rawScript = `// JavaScript - Form Handling
// Skenario: Menangani submit form dan validasi
const form = document.createElement('form');
form.id = 'myForm';
form.innerHTML = \`
    <label>Nama: <input type="text" name="nama" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Kirim</button>
\`;
form.style.maxWidth = '300px';
form.style.margin = '20px auto';
form.style.padding = '20px';
form.style.border = '1px solid #ccc';
form.style.borderRadius = '8px';
document.body.appendChild(form);

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    const nama = formData.get('nama');
    const email = formData.get('email');
    if (nama && email) {
        console.log('✅ Form disubmit:', { nama, email });
        alert(\`Terima kasih, \${nama}!\`);
    } else {
        console.log('❌ Form tidak lengkap.');
    }
});
console.log('✅ Form dengan event submit dan validasi siap.');`;
    } else if (t.includes("praktik js")) {
      rawScript = `// JavaScript - Praktik JS
// Skenario: Menggabungkan semua konsep JS: variabel, fungsi, loop, array, event
const students = [
    { name: 'Ahmad', score: 80 },
    { name: 'Siti', score: 95 },
    { name: 'Budi', score: 70 }
];

// Fungsi untuk menghitung rata-rata
function average(arr) {
    const total = arr.reduce((sum, s) => sum + s.score, 0);
    return total / arr.length;
}

// Tampilkan data
console.log('Daftar siswa:', students);
console.log('Rata-rata nilai:', average(students));

// Filter lulus (>=75)
const passed = students.filter(s => s.score >= 75);
console.log('Siswa lulus:', passed);

// Event listener tombol
const btn = document.createElement('button');
btn.textContent = 'Tampilkan Lulus';
btn.onclick = () => {
    alert(\`Siswa lulus: \${passed.map(s => s.name).join(', ')}\`);
};
document.body.appendChild(btn);
console.log('✅ Praktik JS selesai.');`;
      // PROJECT MINI (BAB 5)
    } else if (t.includes("merancang landing page")) {
      rawScript = `// Project Mini - Merancang Landing Page
// Skenario: Membuat struktur dasar landing page
const landing = document.createElement('div');
landing.style.maxWidth = '800px';
landing.style.margin = '20px auto';
landing.style.padding = '20px';
landing.style.fontFamily = 'Arial, sans-serif';

landing.innerHTML = \`
    <header style="text-align:center; padding: 40px 0; background: #3d7bff; color: white; border-radius: 12px;">
        <h1>Selamat Datang di EduCare</h1>
        <p>Belajar dari nol sampai mahir</p>
    </header>
    <section style="display:flex; gap:20px; margin: 30px 0;">
        <div style="flex:1; background:#f0f0f0; padding:20px; border-radius:8px;">
            <h3>📚 Belajar</h3>
            <p>Materi lengkap dengan praktik langsung.</p>
        </div>
        <div style="flex:1; background:#f0f0f0; padding:20px; border-radius:8px;">
            <h3>🧑‍🏫 Mentor</h3>
            <p>Dibimbing oleh pengajar berpengalaman.</p>
        </div>
        <div style="flex:1; background:#f0f0f0; padding:20px; border-radius:8px;">
            <h3>🏆 Sertifikat</h3>
            <p>Dapatkan pengakuan atas kemampuanmu.</p>
        </div>
    </section>
    <footer style="text-align:center; padding:20px; background:#333; color:white; border-radius:8px;">
        © 2026 EduCare
    </footer>
\`;
document.body.appendChild(landing);
console.log('✅ Landing page sederhana berhasil dibuat.');`;
    } else if (t.includes("membuat navbar")) {
      rawScript = `// Project Mini - Membuat Navbar
// Skenario: Membuat navbar responsif dengan Flexbox
const navbar = document.createElement('nav');
navbar.style.display = 'flex';
navbar.style.justifyContent = 'space-between';
navbar.style.alignItems = 'center';
navbar.style.padding = '10px 20px';
navbar.style.background = '#3d7bff';
navbar.style.color = 'white';
navbar.style.borderRadius = '8px';

navbar.innerHTML = \`
    <div style="font-weight:bold; font-size:1.2rem;">EduCare</div>
    <div style="display:flex; gap:20px;">
    <a href="#" style="color:white; text-decoration:none;">${window.EduCareI18n?.t('nav.home') || 'Beranda'}</a>
    <a href="#" style="color:white; text-decoration:none;">${window.EduCareI18n?.t('nav.courses') || 'Kursus'}</a>
    <a href="#" style="color:white; text-decoration:none;">${window.EduCareI18n?.t('nav.about') || 'Tentang'}</a>
    <a href="#" style="color:white; text-decoration:none;">${window.EduCareI18n?.t('nav.contact') || 'Kontak'}</a>
    </div>
\`;
document.body.appendChild(navbar);
console.log('✅ Navbar sederhana dengan flex telah dibuat.');`;
    } else if (t.includes("menambahkan animasi")) {
      rawScript = `// Project Mini - Menambahkan Animasi
// Skenario: Menambahkan animasi CSS pada elemen
const style = document.createElement('style');
style.textContent = \`
    @keyframes slideIn {
        from { transform: translateX(-100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animated-box {
        animation: slideIn 1s ease-out;
        background: #8b5cf6;
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        margin: 20px auto;
        max-width: 400px;
    }
\`;
document.head.appendChild(style);

const box = document.createElement('div');
box.className = 'animated-box';
box.textContent = 'Halo! Saya muncul dengan animasi.';
document.body.appendChild(box);
console.log('✅ Animasi slideIn diterapkan pada elemen.');`;
    } else if (t.includes("integrasi js")) {
      rawScript = `// Project Mini - Integrasi JS
// Skenario: Menambahkan interaktivitas dengan JavaScript
const container = document.createElement('div');
container.style.textAlign = 'center';
container.style.margin = '20px auto';

const heading = document.createElement('h2');
heading.textContent = 'Integrasi JS';
container.appendChild(heading);

const btn = document.createElement('button');
btn.textContent = 'Klik untuk ubah warna';
btn.style.padding = '10px 24px';
btn.style.background = '#3d7bff';
btn.style.color = 'white';
btn.style.border = 'none';
btn.style.borderRadius = '8px';
btn.style.cursor = 'pointer';

btn.addEventListener('click', () => {
    const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16);
    document.body.style.background = randomColor;
});

container.appendChild(btn);
document.body.appendChild(container);
console.log('✅ Interaktivitas JS: tombol ubah warna background.');`;
    } else if (t.includes("testing dasar")) {
      rawScript = `// Project Mini - Testing Dasar
// Skenario: Simulasi unit test sederhana
function tambah(a, b) {
    return a + b;
}

function kurang(a, b) {
    return a - b;
}

// Test cases
console.log('Testing fungsi tambah:');
console.assert(tambah(2, 3) === 5, '2+3 seharusnya 5');
console.assert(tambah(-1, 1) === 0, '-1+1 seharusnya 0');

console.log('Testing fungsi kurang:');
console.assert(kurang(10, 3) === 7, '10-3 seharusnya 7');
console.assert(kurang(0, 5) === -5, '0-5 seharusnya -5');

console.log('✅ Semua test case berhasil!');`;
    } else if (t.includes("optimasi ui")) {
      rawScript = `// Project Mini - Optimasi UI
// Skenario: Menambahkan efek visual untuk meningkatkan UX
const style = document.createElement('style');
style.textContent = \`
    .optimized-btn {
        background: #3d7bff;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .optimized-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .optimized-btn:active {
        transform: scale(0.95);
    }
\`;
document.head.appendChild(style);

const btn = document.createElement('button');
btn.className = 'optimized-btn';
btn.textContent = 'Tombol dengan efek optimasi';
document.body.appendChild(btn);
console.log('✅ Tombol dengan efek hover dan active untuk UX lebih baik.');`;
    } else if (t.includes("publikasi demo")) {
      rawScript = `// Project Mini - Publikasi Demo
// Skenario: Simulasi proses deploy aplikasi
console.log('🚀 Memulai proses publikasi demo...');
console.log('📦 Mengecek file build...');
console.log('✅ Build selesai.');
console.log('🌐 Mengupload ke server...');
console.log('✅ Demo berhasil dipublikasikan di: https://educare-demo.netlify.app');
console.log('🎉 Selamat! Demo sudah online.');`;
    } else if (t.includes("review project")) {
      rawScript = `// Project Mini - Review Project
// Skenario: Mengecek checklist kelengkapan project
const checklist = [
    'Struktur HTML semantik',
    'CSS styling dengan Flexbox/Grid',
    'Interaktivitas JavaScript',
    'Responsive design',
    'Optimasi performa',
    'Kode bersih dan terstruktur'
];

console.log('📋 Review Project Mini:');
checklist.forEach((item, i) => {
    console.log(\`\${i+1}. \${item} - ✅\`);
});
console.log('✅ Semua aspek telah terpenuhi.');`;
    } else if (t.includes("presentasi hasil")) {
      rawScript = `// Project Mini - Presentasi Hasil
// Skenario: Menyiapkan poin-poin presentasi
const presentasi = [
    '1. Perkenalan project',
    '2. Tujuan dan manfaat',
    '3. Fitur utama',
    '4. Teknologi yang digunakan',
    '5. Tantangan dan solusi',
    '6. Demo langsung',
    '7. Q&A'
];

console.log('📝 Materi presentasi:');
presentasi.forEach(p => console.log(p));
console.log('✅ Siap presentasi!');`;
    } else if (t.includes("refleksi belajar")) {
      rawScript = `// Project Mini - Refleksi Belajar
// Skenario: Merefleksikan pembelajaran yang telah dilakukan
const refleksi = {
    'Apa yang sudah dipelajari': 'HTML, CSS, JS, dan project mini',
    'Kesulitan': 'Memahami konsep CSS Grid dan Async JS',
    'Strategi mengatasi': 'Praktik langsung dan mencari sumber tambahan',
    'Pencapaian': 'Berhasil membuat landing page interaktif',
    'Rencana selanjutnya': 'Mendalami React dan backend'
};

console.log('📖 Refleksi Belajar:');
for (const [key, value] of Object.entries(refleksi)) {
    console.log(\`\${key}: \${value}\`);
}
console.log('✅ Refleksi selesai.');`;
      // --- FALLBACK (jika tidak ada kondisi yang cocok) ---
    } else {
      rawScript = `// JavaScript & DOM Manipulation - ${title}
// Skenario: Mengolah data array siswa secara dinamis dan memperbarui UI
const dataSiswa = [
  { id: 1, nama: 'Danss', level: 11, xp: 5310 },
  { id: 2, nama: 'Khaerul Fakhri', level: 11, xp: 5230 },
  { id: 3, nama: 'Morren Bangkit', level: 10, xp: 4870 }
];

console.log('--- Memulai Pengolahan Data Siswa ---');
console.log('Jumlah Pelajar Aktif:', dataSiswa.length);

// Cari siswa dengan XP di atas 5000
const topPelajar = dataSiswa.filter(siswa => siswa.xp > 5000);
console.log('Siswa berprestasi (XP > 5000):');
topPelajar.forEach((s, idx) => {
  console.log(\`\${idx + 1}. \${s.nama} - \${s.xp} XP (Level \${s.level})\`);
});

// Update XP secara lokal untuk simulasi
const totalXPSemuakelas = dataSiswa.reduce((sum, s) => sum + s.xp, 0);
console.log('Total akumulasi XP komunitas:', totalXPSemuakelas);`;
    }
  } else if (cat === "ai") {
    rawScript = `// AI & Machine Learning Simulator - ${title}
// Skenario: Membuat model prediksi sederhana berbasis bobot dan bias (Regresi)
function hitungPrediksiML(inputFitur) {
  // Rumus dasar regresi: y = (weight * x) + bias
  const weight = 2.5; 
  const bias = 1.2;
  
  console.log('Mengevaluasi model dengan input fitur:', inputFitur);
  console.log('Bobot (Weight):', weight, '| Bias:', bias);
  
  const hasilPrediksi = (weight * inputFitur) + bias;
  return hasilPrediksi;
}

const inputX = 4.5;
const prediksiY = hitungPrediksiML(inputX);

console.log('------------------------------------------------');
console.log('Hasil prediksi model untuk ${title}:', prediksiY.toFixed(2));
console.log('Model AI berhasil memproses input secara real-time!');`;
  } else if (cat === "data") {
    rawScript = `// Data Science Data Cleaning & Statistics - ${title}
// Skenario: Mengisi nilai kosong (Imputation) dan menghitung nilai statistik
const dataMentah = [12.5, 15.0, null, 18.2, 14.5, null, 20.1];
console.log('Dataset mentah:', JSON.stringify(dataMentah));

// Pembersihan: Hapus nilai null untuk mencari rata-rata data valid
const dataValid = dataMentah.filter(v => v !== null);
const rataRata = dataValid.reduce((a, b) => a + b, 0) / dataValid.length;
console.log('Nilai rata-rata dari data valid:', rataRata.toFixed(2));

// Imputasi: Isi nilai null dengan nilai rata-rata
const dataBersih = dataMentah.map(v => v === null ? parseFloat(rataRata.toFixed(2)) : v);
console.log('Dataset setelah dibersihkan:', JSON.stringify(dataBersih));

// Hitung Median
const dataUrut = [...dataBersih].sort((a, b) => a - b);
const mid = Math.floor(dataUrut.length / 2);
const median = dataUrut.length % 2 !== 0 ? dataUrut[mid] : (dataUrut[mid - 1] + dataUrut[mid]) / 2;
console.log('Nilai Median dataset bersih:', median);`;
  } else if (cat === "mobile") {
    rawScript = `// Android Development Kotlin & State - ${title}
// Skenario: Simulasi siklus hidup Activity dan pembaruan Compose state
class MainViewModelSim {
  private var count = 0
  
  fun getCountValue(): Int {
    return count
  }
  
  fun onIncrementClicked() {
    count++
    println("State Berubah! Nilai counter Android: " + count)
  }
}

// Simulasi inisialisasi pada onCreate()
val viewModel = MainViewModelSim()
println("Siklus Hidup: ViewModel diinisialisasi.")
viewModel.onIncrementClicked()
viewModel.onIncrementClicked()
println("UI Update: Menampilkan nilai counter = " + viewModel.getCountValue());`;
  } else if (cat === "uiux") {
    rawScript = `// UI/UX Design System Token Configurator - ${title}
// Skenario: Mendefinisikan token untuk tipografi dan warna aplikasi EduCare
const figmaTokens = {
  colors: {
    primary: '#ffffff',
    secondary: '#a3a3a3',
    background: '#060606',
    text: '#f2f2f0'
  },
  typography: {
    fontFamily: 'Bricolage Grotesque, sans-serif',
    baseSize: '16px',
    h1: '2.5rem',
    h2: '2rem'
  },
  grid: {
    columns: 12,
    gutter: '24px',
    margin: '32px'
  }
};

console.log('--- UI/UX Token untuk ${title} ---');
console.log('Warna Aksen Dasbor:', figmaTokens.colors.primary);
console.log('Font Utama Figma:', figmaTokens.typography.fontFamily);
console.log('Konfigurasi Grid Tata Letak:', JSON.stringify(figmaTokens.grid, null, 2));`;
  } else if (cat === "cloud") {
    rawScript = `// Cloud Infrastructure as Code YAML Simulator - ${title}
// Skenario: Simulasi deployment microservice ke AWS ECS menggunakan Docker
const ecsTaskDefinition = {
  family: 'educare-web-task',
  containerDefinitions: [
    {
      name: 'web-app',
      image: 'educare/frontend:v2.0',
      cpu: 256,
      memory: 512,
      portMappings: [{ containerPort: 80, hostPort: 80 }]
    }
  ]
};

console.log('1. Membaca spesifikasi tugas ECS untuk deployment...');
console.log(JSON.stringify(ecsTaskDefinition, null, 2));
console.log('2. Menghubungkan ke API AWS Region ap-southeast-1...');
console.log('3. Melakukan provisioning kontainer...');
console.log('Hasil: Container successfully deployed to ECS Cluster!');`;
  } else if (effectiveCat === "mtk") {
    rawScript = buildMathScript(title);
  } else if (effectiveCat === "ipa") {
    rawScript = `// Simulasi Pengamatan IPA - ${title}
// Skenario: Mencatat dan menganalisis data pengamatan sederhana
const dataPengamatan = [
  { waktu: 'Pagi', suhu: 24 },
  { waktu: 'Siang', suhu: 31 },
  { waktu: 'Sore', suhu: 27 },
  { waktu: 'Malam', suhu: 22 }
];

console.log('Topik pengamatan: ${title}');
dataPengamatan.forEach(d => {
  console.log(d.waktu + ': ' + d.suhu + ' derajat Celcius');
});

const suhuRata = dataPengamatan.reduce((a, b) => a + b.suhu, 0) / dataPengamatan.length;
console.log('Rata-rata suhu dalam sehari:', suhuRata.toFixed(1), 'derajat Celcius');`;
  } else if (effectiveCat === "ips") {
    rawScript = `// Simulasi Data Sosial - ${title}
// Skenario: Mengolah data sederhana terkait kehidupan masyarakat
const dataPenduduk = [
  { wilayah: 'Desa Sukamaju', jumlah: 1200 },
  { wilayah: 'Desa Sumber Rejo', jumlah: 950 },
  { wilayah: 'Desa Mekar Sari', jumlah: 1430 }
];

console.log('Topik: ${title}');
const totalPenduduk = dataPenduduk.reduce((a, b) => a + b.jumlah, 0);
dataPenduduk.forEach(d => {
  console.log(d.wilayah + ': ' + d.jumlah + ' jiwa');
});
console.log('Total penduduk tiga desa:', totalPenduduk, 'jiwa');`;
  } else if (effectiveCat === "bahasa") {
    rawScript = `// Simulasi Analisis Bahasa - ${title}
// Skenario: Menganalisis struktur kalimat sederhana
const kalimat = "Ani membaca buku di perpustakaan sekolah";
const kataKata = kalimat.split(' ');

console.log('Topik: ${title}');
console.log('Kalimat:', kalimat);
console.log('Jumlah kata:', kataKata.length);
console.log('Kata pertama (subjek):', kataKata[0]);
console.log('Kata kedua (predikat):', kataKata[1]);`;
  } else if (effectiveCat === "self") {
    rawScript = `// Simulasi Pelacak Kebiasaan - ${title}
// Skenario: Memantau progres kebiasaan baik selama satu minggu
const kebiasaan = [
  { hari: 'Senin', selesai: true },
  { hari: 'Selasa', selesai: true },
  { hari: 'Rabu', selesai: false },
  { hari: 'Kamis', selesai: true },
  { hari: 'Jumat', selesai: true }
];

console.log('Topik: ${title}');
const jumlahSelesai = kebiasaan.filter(k => k.selesai).length;
kebiasaan.forEach(k => {
  console.log(k.hari + ': ' + (k.selesai ? 'Berhasil ✔' : 'Belum konsisten ✘'));
});
console.log('Konsistensi minggu ini:', jumlahSelesai + '/' + kebiasaan.length, 'hari');`;
  } else {
    rawScript = `// Materi: ${title}
// Kursus: ${courseTitle}
console.log("Mempelajari materi '${title}' pada kursus '${courseTitle}'.");`;
  }

  // Kursus IT (bisa praktik langsung di Coding Playground) vs kursus
  // non-IT (mtk/ipa/ips/bahasa/self/custom) yang berlatih lewat soal.
  const isPracticeCourse = getCourseGroup(course) === "it";
  const practiceMenu = isPracticeCourse ? "Coding Playground" : "Latihan Soal";
  const practiceAction = isPracticeCourse
    ? `menyalin script praktik di atas ke dalam menu <strong>${practiceMenu}</strong> untuk melihat perilakunya secara langsung, lalu lakukan modifikasi pada parameter nilai dan amati perubahan output terminalnya`
    : `mengerjakan menu <strong>${practiceMenu}</strong> dan <strong>Quiz &amp; Latihan</strong> untuk menguji pemahamanmu, lalu coba jelaskan kembali materi ini dengan kata-katamu sendiri`;

  // Generate exactly 6 detailed paragraphs, disesuaikan dengan mata pelajaran (profile)
  paragraphs.push(
    `Sesi pembelajaran mengenai <strong>${title}</strong> merupakan salah satu materi fundamental terpenting dalam rangkaian kurikulum <strong>${courseTitle}</strong> pada mata pelajaran <strong>${profile.label}</strong>. Topik ini dirancang khusus untuk membantumu memahami konsep dasar, istilah-istilah kunci, serta cara berpikir yang relevan dalam ${profile.field}. Dengan menguasai materi ini, kamu akan memiliki basis pemahaman yang kuat untuk melangkah ke bab-bab pembelajaran yang lebih menantang berikutnya.`,
  );

  paragraphs.push(
    `Secara konseptual, bahasan dari <strong>${title}</strong> dalam ${profile.field} dibangun atas pemahaman yang bertahap: mulai dari pengertian dasar, hubungan antar konsep, hingga penerapannya. Pemahaman yang menyeluruh tentang bagaimana setiap bagian materi ini saling berkaitan akan sangat membantumu ketika menghadapi soal atau situasi yang lebih kompleks. Melalui pendekatan belajar terstruktur ini, kamu diajak untuk tidak sekadar menghafal, melainkan benar-benar memahami inti dari <strong>${title}</strong>.`,
  );

  paragraphs.push(
    `Apabila ditinjau dari sisi penerapan, memahami <strong>${title}</strong> menuntut ketelitian dan latihan yang konsisten. Setiap langkah, istilah, atau aturan dalam materi ini memiliki fungsi dan urutan yang tidak boleh dilewatkan begitu saja. Kesalahan kecil dalam memahami satu bagian sering kali membuat bagian berikutnya menjadi sulit dipahami. Oleh karena itu, ${profile.tip}.`,
  );

  paragraphs.push(
    `Sebagai contoh penerapan di dunia nyata, pemahaman tentang <strong>${title}</strong> pada ${profile.field} sangat berguna untuk ${profile.contoh}. Dengan bekal teori serta latihan dari materi <strong>${title}</strong> ini, kamu akan lebih siap menghadapi situasi serupa dan mampu menjelaskan alasan di balik setiap langkah yang kamu ambil, bukan hanya sekadar mengikuti contoh.`,
  );

  paragraphs.push(
    `Selain itu, terdapat beberapa kebiasaan belajar yang baik untuk kamu terapkan secara konsisten saat mempelajari materi <strong>${profile.label}</strong> seperti ini. Guru dan mentor menyarankan pentingnya membuat catatan ringkas dengan bahasamu sendiri, mengerjakan latihan secara bertahap, dan tidak ragu bertanya ketika ada bagian yang belum jelas. Hindari melompati materi dasar karena hal ini berisiko membuat pemahaman di bab-bab selanjutnya menjadi lebih sulit.`,
  );

  paragraphs.push(
    `Untuk menutup sesi pembelajaran ini, kami sangat menekankan pentingnya berlatih secara mandiri dan berkelanjutan. Cobalah ${practiceAction}. Jangan ragu untuk memanfaatkan fitur <strong>Tanya AI Tutor</strong> (bila tersedia untuk kursus ini) bila ada istilah dalam <strong>${profile.label}</strong> yang membingungkan, sebab pemahaman terbaik lahir dari kemauan untuk terus mencoba.`,
  );

  let bodyHtml = `
        <div class="lbody" style="font-size: .95rem; line-height: 1.8; color: var(--text);">
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">📖 1. Pengantar dan Latar Belakang</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[0]}</p>
            
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">🧠 2. Konsep Teoretis dan Prinsip Utama</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[1]}</p>
            
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">🛠️ 3. Spesifikasi Teknis dan Aturan Operasional</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[2]}</p>
            
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">💡 4. Use Case dan Studi Kasus Dunia Nyata</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[3]}</p>
            ${
              isGeneral
                ? `
            <div style="padding:.9rem 1rem;border-radius:14px;background:rgba(255,255,255,.05);border:1px solid var(--border2);margin:0 0 1.25rem;">
                <strong>✅ Contoh Penerapan:</strong> ${profile.contoh}.
            </div>
            `
                : ""
            }
            
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">📌 5. Standar Industri dan Best Practices</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[4]}</p>
            
            <h3 style="color: var(--cyan); margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">🏁 6. Kesimpulan dan Langkah Eksperimen Mandiri</h3>
            <p style="margin-bottom: 1.25rem;">${paragraphs[5]}</p>
        </div>
    `;

  // ===== TAMBAHAN: Prepending komentar unik per kursus dan lesson =====
  if (rawScript) {
    rawScript = `// Kursus: ${courseTitle} - Materi: ${title}\n` + rawScript;
  }

  return {
    body: bodyHtml,
    rawScript: rawScript,
  };
}

// Mengubah total jam belajar (desimal) menjadi format jam digital
// HH:MM:SS. Masih dipakai untuk statistik jam belajar di profil/progress.
function formatDigitalClock(totalHoursDecimal) {
  const totalSeconds = Math.max(0, Math.floor((totalHoursDecimal || 0) * 3600));
  const h = Math.floor(totalSeconds / 3600);
  const m = Math.floor((totalSeconds % 3600) / 60);
  const s = totalSeconds % 60;
  const pad = (n) => String(n).padStart(2, "0");
  return `${pad(h)}:${pad(m)}:${pad(s)}`;
}

// Jam digital sungguhan: menampilkan waktu asli perangkat pengguna dalam
// format 24 jam HH:MM:SS (mis. "07:00:32"), gaya jam LED digital yang
// berjalan sungguhan pada kartu "realtime" di overview. Detik disertakan
// supaya jam terlihat benar-benar "hidup" dan mengikuti waktu sekarang
// setiap detik, bukan cuma berubah setiap pergantian menit. Tanggal
// ditampilkan dalam Bahasa Indonesia lengkap dengan tahun, contoh:
// "Jumat, 31 Juli 2026".
function getDigitalClockParts() {
  const now = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  return {
    time: `${pad(now.getHours())}:${pad(now.getMinutes())}`,
    seconds: pad(now.getSeconds()),
    dateLabel: now.toLocaleDateString("id-ID", {
      weekday: "long",
      day: "numeric",
      month: "long",
      year: "numeric",
    }),
  };
}

function startRealtimeStudyTimer() {
  if (!document.getElementById("pulse-dot-style")) {
    const style = document.createElement("style");
    style.id = "pulse-dot-style";
    style.textContent = `
            .pulse-dot {
                width: 8px;
                height: 8px;
                background-color: var(--green);
                border-radius: 50%;
                display: inline-block;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                animation: pulse-green-anim 1.5s infinite;
            }
            @keyframes pulse-green-anim {
                0% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                }
                70% {
                    transform: scale(1);
                    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
                }
                100% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
                }
            }
        `;
    document.head.appendChild(style);
  }

  // Pelacakan waktu belajar berbasis SELISIH WAKTU ASLI (timestamp),
  // bukan menambah angka tetap setiap kali interval "tick". Kalau cuma
  // menambah nilai tetap per tick, saat tab di-background browser akan
  // memperlambat/menahan interval (kadang cuma jalan sekali per menit),
  // sehingga total jam belajar jadi jauh lebih kecil dari waktu asli
  // yang sudah berlalu. Dengan menghitung selisih Date.now(), total jam
  // akan selalu akurat mengikuti waktu nyata walau tick tidak persis
  // setiap detik.
  if (!UD.lastTickAt) UD.lastTickAt = Date.now();
  let ticksSinceSave = 0;

  setInterval(() => {
    if (!UD) return;

    const now = Date.now();
    let elapsedSec = (now - (UD.lastTickAt || now)) / 1000;
    UD.lastTickAt = now;

    // Kalau tab sempat tidak aktif lama / laptop sempat tidur, jangan
    // hitung jeda itu sebagai waktu belajar — batasi maksimal 1 menit
    // per tick supaya tidak melompat jauh saat tab aktif kembali.
    if (!(elapsedSec > 0) || elapsedSec > 60) elapsedSec = 1;

    const elapsedHours = elapsedSec / 3600;
    UD.totalHours += elapsedHours;

    const dayIdx = (new Date().getDay() + 6) % 7;
    if (Array.isArray(UD.weekActivity)) {
      UD.weekActivity[dayIdx] += elapsedHours;
    }

    ticksSinceSave++;
    if (ticksSinceSave >= 5) {
      saveUD();
      ticksSinceSave = 0;
    }

    const topClockTimeEl = document.getElementById("topbarClockTime");
    const topClockDateEl = document.getElementById("topbarClockDate");
    if (topClockTimeEl || topClockDateEl) {
      const parts = getDigitalClockParts();
      if (topClockTimeEl)
        topClockTimeEl.textContent = `${parts.time}:${parts.seconds}`;
      if (topClockDateEl) topClockDateEl.textContent = parts.dateLabel;
    }

    const profileHrsEl = document.getElementById("profile-totalHours");
    if (profileHrsEl) {
      profileHrsEl.textContent = UD.totalHours.toFixed(4) + "h";
    }

    const progressHrsEl = document.getElementById("progress-totalHours");
    if (progressHrsEl) {
      progressHrsEl.textContent = UD.totalHours.toFixed(4) + "h";
    }
  }, 1000);
}

const QUIZ_DATA = {
  1: [
    {
      q: "Elemen HTML mana yang digunakan untuk membungkus konten navigasi utama?",
      opts: [
        "&lt;nav&gt;",
        "&lt;section&gt;",
        "&lt;header&gt;",
        "&lt;aside&gt;",
      ],
      ans: 0,
    },
    {
      q: "Properti CSS apa yang digunakan untuk mengubah warna teks?",
      opts: ["text-color", "color", "background-color", "font-style"],
      ans: 1,
    },
    {
      q: 'Bagaimana cara menyeleksi elemen dengan id "konten" di CSS?',
      opts: [".konten", "#konten", "konten", "*konten"],
      ans: 1,
    },
    {
      q: "Di JavaScript, mana cara yang benar untuk mendeklarasikan variabel yang nilainya bisa diubah?",
      opts: ["const x = 5;", "let x = 5;", "var x = 5;", "let/var x = 5;"],
      ans: 1,
    },
    {
      q: "Apa output dari `console.log(typeof [])` di JavaScript?",
      opts: ['"array"', '"object"', '"list"', '"undefined"'],
      ans: 1,
    },
  ],
  2: [
    {
      q: "Apa singkatan dari ML dalam konteks kecerdasan buatan?",
      opts: [
        "Machine Learning",
        "Machine Language",
        "Model Logic",
        "Modern Learning",
      ],
      ans: 0,
    },
    {
      q: "Library Python mana yang populer digunakan untuk komputasi numerik array multidimensi?",
      opts: ["Pandas", "NumPy", "Matplotlib", "Django"],
      ans: 1,
    },
    {
      q: "Algoritma ML yang mengelompokkan data tanpa label (unsupervised) disebut...",
      opts: [
        "Klasifikasi",
        "Regresi",
        "Clustering (Klasterisasi)",
        "Supervised Learning",
      ],
      ans: 2,
    },
    {
      q: 'Apa tujuan dari proses "Model Training" dalam Machine Learning?',
      opts: [
        "Mengunggah model ke internet",
        "Melatih model mengenali pola pada data latih",
        "Menghapus data kosong",
        "Menulis script Python",
      ],
      ans: 1,
    },
    {
      q: "Apa perbedaan mendasar Deep Learning (DL) dibanding Machine Learning (ML)?",
      opts: [
        "DL hanya untuk teks",
        "DL menggunakan jaringan saraf tiruan berlapis (deep neural networks)",
        "DL lebih lambat",
        "DL tidak butuh data",
      ],
      ans: 1,
    },
  ],
  3: [
    {
      q: "Apa langkah awal yang krusial sebelum melakukan analisis data?",
      opts: [
        "Membuat grafik indah",
        "Data Cleaning (Pembersihan data)",
        "Menerbitkan laporan",
        "Melakukan regresi",
      ],
      ans: 1,
    },
    {
      q: "Nilai tengah dari suatu dataset yang diurutkan disebut...",
      opts: ["Mean", "Median", "Modus", "Varians"],
      ans: 1,
    },
    {
      q: "Tipe visualisasi data yang paling cocok untuk menunjukkan tren sepanjang waktu adalah...",
      opts: [
        "Grafik Lingkaran (Pie Chart)",
        "Grafik Batang (Bar Chart)",
        "Grafik Garis (Line Chart)",
        "Scatter Plot",
      ],
      ans: 2,
    },
    {
      q: "Di Pandas (Python), objek berdimensi dua dengan kolom yang memiliki nama disebut...",
      opts: ["Series", "DataFrame", "Array", "List"],
      ans: 1,
    },
    {
      q: "Korelasi bernilai positif kuat mengindikasikan bahwa...",
      opts: [
        "Jika variabel X naik, variabel Y juga naik",
        "Jika variabel X naik, variabel Y turun",
        "Kedua variabel tidak berhubungan",
        "Nilainya selalu di bawah nol",
      ],
      ans: 0,
    },
  ],
  4: [
    {
      q: "Bahasa pemrograman modern yang direkomendasikan secara resmi oleh Google untuk Android adalah...",
      opts: ["Java", "Kotlin", "Dart", "C++"],
      ans: 1,
    },
    {
      q: "Di Android Studio, file mana yang digunakan untuk mendefinisikan dependensi project dan plugin?",
      opts: [
        "AndroidManifest.xml",
        "build.gradle (atau build.gradle.kts)",
        "MainActivity.kt",
        "colors.xml",
      ],
      ans: 1,
    },
    {
      q: "Framework UI modern berbasis deklaratif untuk membangun aplikasi Android adalah...",
      opts: ["XML Layouts", "Jetpack Compose", "Flutter", "React Native"],
      ans: 1,
    },
    {
      q: "Komponen Android Jetpack yang berfungsi menyimpan state UI agar tidak hilang saat rotasi layar adalah...",
      opts: ["LiveData", "ViewModel", "Room", "Retrofit"],
      ans: 1,
    },
    {
      q: "Library mana yang populer digunakan untuk melakukan HTTP request ke REST API di Android?",
      opts: ["Room", "Retrofit", "Gson", "Glide"],
      ans: 1,
    },
  ],
  5: [
    {
      q: "Apa kepanjangan dari UX dalam proses desain produk digital?",
      opts: [
        "User Experience",
        "User eXtension",
        "Unique Experience",
        "User Expert",
      ],
      ans: 0,
    },
    {
      q: "Fitur Figma mana yang memungkinkan tata letak elemen menyesuaikan ukuran secara otomatis?",
      opts: ["Auto Layout", "Components", "Variants", "Constraints"],
      ans: 0,
    },
    {
      q: "Representasi visual tingkat rendah (low-fidelity) dari rancangan aplikasi disebut...",
      opts: ["High-Fidelity Mockup", "Wireframe", "Prototype", "Design System"],
      ans: 1,
    },
    {
      q: "Elemen desain yang memberikan jarak kosong di sekitar konten agar mata nyaman membaca disebut...",
      opts: [
        "Contrast",
        "Whitespace (Negative Space)",
        "Alignment",
        "Typography",
      ],
      ans: 1,
    },
    {
      q: "Metode riset pengguna untuk mengamati langsung bagaimana pengguna memakai produk disebut...",
      opts: [
        "User Interview",
        "Usability Testing",
        "Survey Online",
        "A/B Testing",
      ],
      ans: 1,
    },
  ],
  6: [
    {
      q: "Di Cloud Computing, model layanan di mana pengguna menyewa mesin virtual dan jaringan disebut...",
      opts: ["SaaS", "PaaS", "IaaS", "FaaS"],
      ans: 2,
    },
    {
      q: "Layanan penyimpanan objek (Object Storage) milik AWS yang sangat populer adalah...",
      opts: ["AWS EC2", "AWS RDS", "AWS S3", "AWS Lambda"],
      ans: 2,
    },
    {
      q: "Teknologi kontainerisasi yang membungkus aplikasi beserta seluruh dependensinya adalah...",
      opts: ["Docker", "Kubernetes", "VirtualBox", "Ansible"],
      ans: 0,
    },
    {
      q: "Apa fungsi utama dari Kubernetes (K8s)?",
      opts: [
        "Menulis kode program",
        "Mengorkestrasi dan mengelola kontainer secara otomatis",
        "Mengganti fungsi sistem operasi",
        "Membuat server fisik",
      ],
      ans: 1,
    },
    {
      q: "Proses otomatisasi integrasi kode baru secara berkala ke repositori disebut...",
      opts: [
        "Continuous Deployment",
        "Continuous Integration",
        "Infrastructure as Code",
        "Cloud Computing",
      ],
      ans: 1,
    },
  ],
  7: [
    {
      q: "Berapakah hasil dari 3/4 + 1/2?",
      opts: ["4/6", "5/4", "3/8", "7/4"],
      ans: 1,
    },
    {
      q: "Jika x + 5 = 12, berapakah nilai x?",
      opts: ["5", "6", "7", "8"],
      ans: 2,
    },
    {
      q: "Bangun datar yang memiliki karakteristik semua sisi sama panjang dan keempat sudutnya siku-siku adalah...",
      opts: [
        "Persegi Panjang",
        "Persegi (Bujur Sangkar)",
        "Jajar Genjang",
        "Belah Ketupat",
      ],
      ans: 1,
    },
    {
      q: "Rumus untuk menghitung luas lingkaran adalah...",
      opts: ["π * r * r", "2 * π * r", "π * d", "1/2 * π * r"],
      ans: 0,
    },
    {
      q: "Berapakah nilai rata-rata (mean) dari angka: 6, 8, 10, 12, 14?",
      opts: ["8", "10", "12", "14"],
      ans: 1,
    },
  ],
  8: [
    {
      q: "Proses pembuatan makanan oleh tumbuhan hijau dengan bantuan cahaya matahari disebut...",
      opts: ["Respirasi", "Fotosintesis", "Transpirasi", "Evaporasi"],
      ans: 1,
    },
    {
      q: "Dalam rantai makanan, makhluk hidup yang memakan produsen langsung disebut...",
      opts: [
        "Konsumen Tingkat I",
        "Konsumen Tingkat II",
        "Dekomposer",
        "Produsen Sekunder",
      ],
      ans: 0,
    },
    {
      q: "Energi yang dimiliki oleh benda karena gerakannya disebut...",
      opts: [
        "Energi Potensial",
        "Energi Kinetik",
        "Energi Kimia",
        "Energi Termal",
      ],
      ans: 1,
    },
    {
      q: 'Planet yang dijuluki "Planet Merah" di tata surya kita adalah...',
      opts: ["Venus", "Mars", "Jupiter", "Saturnus"],
      ans: 1,
    },
    {
      q: "Fenomena naiknya air permukaan bumi karena pemanasan matahari ke atmosfer disebut...",
      opts: ["Kondensasi", "Evaporasi", "Presipitasi", "Infiltrasi"],
      ans: 1,
    },
  ],
  9: [
    {
      q: "Secara geografis, Indonesia terletak di antara dua samudra, yaitu...",
      opts: [
        "Samudra Pasifik dan Samudra Atlantik",
        "Samudra Hindia dan Samudra Pasifik",
        "Samudra Hindia dan Samudra Arktik",
        "Samudra Atlantik dan Samudra Hindia",
      ],
      ans: 1,
    },
    {
      q: "Siapakah tokoh nasional yang membacakan teks Proklamasi Kemerdekaan Indonesia?",
      opts: [
        "Mohammad Hatta",
        "Ir. Soekarno",
        "Sutan Sjahrir",
        "Jenderal Sudirman",
      ],
      ans: 1,
    },
    {
      q: "Kegiatan menggunakan barang atau jasa untuk memenuhi kebutuhan disebut...",
      opts: ["Produksi", "Konsumsi", "Distribusi", "Investasi"],
      ans: 1,
    },
    {
      q: "Norma sosial yang bersumber dari hati nurani manusia mengenai apa yang baik dan buruk disebut...",
      opts: [
        "Norma Agama",
        "Norma Kesusilaan",
        "Norma Kesopanan",
        "Norma Hukum",
      ],
      ans: 1,
    },
    {
      q: "Lembaga eksekutif tertinggi di tingkat desa dipimpin oleh...",
      opts: ["Camat", "Kepala Desa / Lurah", "Bupati", "Ketua RT"],
      ans: 1,
    },
  ],
  10: [
    {
      q: "Manakah kalimat berikut yang memenuhi kaidah subjek, predikat, dan objek (SPO) yang benar?",
      opts: [
        "Ayah membaca koran di teras.",
        "Di teras membaca koran Ayah.",
        "Membaca koran Ayah di teras.",
        "Koran dibaca Ayah teras.",
      ],
      ans: 0,
    },
    {
      q: 'Translate this greeting sentence to English: "Bagaimana kabarmu hari ini?"',
      opts: [
        "How are you today?",
        "Where are you today?",
        "Who are you today?",
        "What is your day?",
      ],
      ans: 0,
    },
    {
      q: "Kata yang digunakan untuk menghubungkan dua klausa atau kalimat disebut...",
      opts: [
        "Kata Ganti",
        "Kata Kerja",
        "Kata Hubung (Konjungsi)",
        "Kata Sifat",
      ],
      ans: 2,
    },
    {
      q: 'In English, what is the past tense form of the verb "go"?',
      opts: ["went", "gone", "goed", "going"],
      ans: 0,
    },
    {
      q: "Pikiran utama atau gagasan inti dari sebuah paragraf disebut...",
      opts: ["Judul", "Ide Pokok / Kalimat Utama", "Penjelas", "Kesimpulan"],
      ans: 1,
    },
  ],
  11: [
    {
      q: "Teknik manajemen waktu dengan bekerja fokus 25 menit lalu istirahat 5 menit disebut...",
      opts: [
        "Teknik Pareto",
        "Teknik Pomodoro",
        "Eisenhower Matrix",
        "Time Blocking",
      ],
      ans: 1,
    },
    {
      q: "Bagaimana sikap terbaik ketika menerima kritik yang membangun?",
      opts: [
        "Marah dan membalas",
        "Mendengarkan dengan tenang dan mengevaluasi diri",
        "Mengabaikan kritik tersebut",
        "Menyalahkan orang lain",
      ],
      ans: 1,
    },
    {
      q: "Kemampuan untuk memahami dan mengelola emosi diri sendiri serta orang lain disebut...",
      opts: [
        "Kecerdasan Intelektual",
        "Kecerdasan Emosional (EQ)",
        "Kecerdasan Sosial",
        "Kecerdasan Spiritual",
      ],
      ans: 1,
    },
    {
      q: "Langkah pertama yang paling efektif dalam menetapkan suatu tujuan hidup adalah...",
      opts: [
        "Menunggu motivasi datang",
        "Membuat tujuan spesifik, terukur, dan tertulis (SMART goals)",
        "Mengikuti impian orang lain",
        "Menghindari risiko",
      ],
      ans: 1,
    },
    {
      q: "Kebiasaan menunda-nunda pekerjaan penting dengan aktivitas kurang berguna disebut...",
      opts: ["Prokrastinasi", "Konsistensi", "Prioritas", "Disiplin"],
      ans: 0,
    },
  ],
};

// ================================================================
// 2. STATE GLOBAL
// ================================================================

let CU = null; // Current User
let UD = null; // User Data
let quizAnswers = {};
let quizTimer = null;
let activeCourseId = null;
let activeLessonIdx = 0;
let calYear = 0;
let calMonth = 0;

// ================================================================
// 3. LOCAL STORAGE HELPER
// ================================================================

const LS = {
  get: (k) => {
    try {
      return JSON.parse(localStorage.getItem(k));
    } catch (e) {
      return null;
    }
  },
  set: (k, v) => localStorage.setItem(k, JSON.stringify(v)),
  del: (k) => localStorage.removeItem(k),
};

// ================================================================
// 4. FUNGSI UTAMA USER DATA
// ================================================================

function newUserData(userId) {
  return {
    userId: userId,
    xp: 0,
    level: 1,
    streak: 0,
    lastLogin: null,
    totalHours: 0,
    quizzesDone: {},
    materiQuizzesDoneCount: 0,
    enrolledCourses: [],
    completedCourses: [],
    certificates: [],
    achievements: [],
    activity: [],
    notifications: [
      {
        icon: "🎉",
        title: "Selamat bergabung!",
        msg: "Selamat datang di EduCare! Mulai perjalanan belajarmu dari nol hari ini.",
        time: "Baru saja",
        read: false,
      },
    ],
    courseForums: {},
    settings: {
      emailNotif: true,
      courseNotif: true,
      reminderNotif: true,
      publicProfile: true,
      leaderboard: true,
      themeMode: "dark",
      themeAccent: "#ffffff",
    },
    weekActivity: [0, 0, 0, 0, 0, 0, 0],
  };
}

function loadUser(initialUser, initialSeed) {
  // Selalu percaya user dari sesi server saat ini (PHP session), bukan
  // cache localStorage yang bisa basi kalau ada akun lain pernah login
  // di browser yang sama. localStorage di sini cuma cache tampilan.
  CU = initialUser || LS.get("en_user");
  LS.set("en_user", CU);

  UD = LS.get("en_ud_" + CU.id);
  if (!UD) {
    UD = newUserData(CU.id);
    if (initialSeed) {
      UD.xp = initialSeed.xp ?? UD.xp;
      UD.level = initialSeed.level ?? UD.level;
      UD.streak = initialSeed.streak ?? UD.streak;
      UD.totalHours = initialSeed.totalHours ?? UD.totalHours;
      UD.materiQuizzesDoneCount = initialSeed.quizzesDoneCount ?? 0;
      UD.enrolledCourses = initialSeed.enrolledCourses ?? UD.enrolledCourses;
      UD.completedCourses = initialSeed.completedCourses ?? UD.completedCourses;
      UD.certificates = initialSeed.certificates ?? UD.certificates;
      UD.activity = initialSeed.activity ?? UD.activity;
      UD.leaderboard = initialSeed.leaderboard ?? UD.leaderboard;
      UD.notifications = (initialSeed.notifications ?? UD.notifications).map(
        (n) => ({ ...n, createdAt: n.createdAt || Date.now() }),
      );
      UD.settings = { ...UD.settings, ...(initialSeed.settings || {}) };
    }
    saveUD();
  } else if (initialSeed) {
    // Sinkronkan ulang setiap kali dashboard dibuka, supaya progres
    // materi terbaru dari server selalu tercermin di "Kursus Saya"
    // dan "Lanjut Belajar" — tanpa menghapus progres pelajaran yang
    // sudah dibuat langsung di dalam kursus.
    syncRealProgress(initialSeed);
    mergeServerNotifications(initialSeed.notifications);
    UD.leaderboard = initialSeed.leaderboard ?? UD.leaderboard;
  }

  // Jaga-jaga untuk data lama (localStorage sebelum field ini ada).
  UD.quizzesDone = UD.quizzesDone || {};
  UD.materiQuizzesDoneCount = Math.max(
    UD.materiQuizzesDoneCount || 0,
    initialSeed?.quizzesDoneCount ?? 0,
  );

  // Migrasi sekali jalan: hapus baseline "Total Belajar" palsu dari versi
  // lama (dulu dihitung dari rumus, bukan waktu nyata) supaya semua
  // pengguna mulai dari 0 jam dan selanjutnya murni bertambah dari waktu
  // nyata yang dihabiskan di dashboard.
  if (!UD._realtimeHoursFix) {
    UD.totalHours = 0;
    UD._realtimeHoursFix = true;
  }

  const today = new Date().toDateString();
  if (UD.lastLogin && UD.lastLogin !== today) {
    const yesterday = new Date(Date.now() - 86400000).toDateString();
    UD.streak = UD.lastLogin === yesterday ? UD.streak + 1 : 1;
  } else if (!UD.lastLogin) {
    UD.streak = 1;
  }
  UD.lastLogin = today;
  saveUD();
}

// Menggabungkan progres kursus nyata dari server (dibangun dari
// progress.json siswa) ke dalam data lokal, tanpa menimpa/mengurangi
// progres yang sudah lebih maju di sisi klien.
function syncRealProgress(initialSeed) {
  const serverEnrolled = initialSeed.enrolledCourses || [];

  // Server (progress.json) adalah sumber kebenaran untuk kursus guru
  // (id >= 5000, yaitu "materi" buatan guru). Kursus semacam ini TIDAK
  // pernah mendapat progres dari sisi klien (buka materi lewat
  // mark-materi.php yang langsung menulis ke server), jadi kumpulan
  // kursus guru di data lokal harus PERSIS mengikuti seed server.
  // Ini mencegah data basi dari akun lain di browser yang sama ikut
  // "keingat" sebagai materi selesai untuk akun baru (yang seed-nya
  // kosong) — masalah "Kursus Saya terisi materi padahal akun baru".
  const guruSeedIds = new Set(
    serverEnrolled.map((e) => e.id).filter((id) => id >= 5000),
  );
  UD.enrolledCourses = UD.enrolledCourses.filter(
    (e) => e.id < 5000 || guruSeedIds.has(e.id),
  );

  serverEnrolled.forEach((seedEc) => {
    const existing = UD.enrolledCourses.find((e) => e.id === seedEc.id);
    if (!existing) {
      // Kursus bawaan (id < 5000) yang belum ada di lokal tetap ditambah;
      // kursus guru sudah terfilter di atas sehingga sudah pasti belum ada.
      if (seedEc.id < 5000) UD.enrolledCourses.push(seedEc);
      return;
    }
    existing.progress = Math.max(existing.progress, seedEc.progress);
    existing.completedLessons = Array.from(
      new Set([
        ...(existing.completedLessons || []),
        ...(seedEc.completedLessons || []),
      ]),
    );
  });

  // Bersihkan daftar kursus tuntas & sertifikat yang menunjuk kursus guru
  // yang sudah tidak ada di seed (data basi / akun baru).
  UD.completedCourses = UD.completedCourses.filter(
    (id) => id < 5000 || guruSeedIds.has(id),
  );
  UD.certificates = UD.certificates.filter(
    (cert) => (cert.courseId ?? cert.id ?? 0) < 5000 || guruSeedIds.has(cert.courseId ?? cert.id ?? 0),
  );

  (initialSeed.completedCourses || []).forEach((id) => {
    if (!UD.completedCourses.includes(id)) UD.completedCourses.push(id);
  });

  (initialSeed.certificates || []).forEach((cert) => {
    if (!UD.certificates.some((c) => c.courseId === cert.courseId)) {
      UD.certificates.push(cert);
    }
  });
}

function saveUD() {
  if (UD) LS.set("en_ud_" + CU.id, UD);
}

// ================================================================
// 5. THEME
// ================================================================

function getActiveAccent(mode = null) {
  const currentMode = mode || UD?.settings?.themeMode || "light";
  const accentKey =
    currentMode === "light" ? "themeAccentLight" : "themeAccentDark";
  return (
    UD?.settings?.[accentKey] ||
    (currentMode === "light" ? "#2F5FE0" : "#4C8DFF")
  );
}

function updateThemePickerUI(mode, accent) {
  const activeMode = mode || "dark";
  const activeAccent = (accent || "#ffffff").toLowerCase();

  document.querySelectorAll("[data-theme-mode]").forEach((btn) => {
    btn.classList.toggle(
      "active",
      btn.getAttribute("data-theme-mode") === activeMode,
    );
  });

  document.querySelectorAll("[data-theme-accent]").forEach((btn) => {
    const value = (btn.getAttribute("data-theme-accent") || "").toLowerCase();
    btn.classList.toggle("active", value === activeAccent);
  });
}

function setTheme(mode, accent = null, persist = true) {
  const currentMode = mode || "light";
  const currentAccent = accent || getActiveAccent(currentMode);
  const isLight = currentMode === "light";

  document.documentElement.classList.toggle("light-mode", isLight);
  document.documentElement.style.setProperty("--cyan", currentAccent);
  document.documentElement.style.setProperty("--cd", `${currentAccent}1A`);
  document.documentElement.style.setProperty("--cg", `${currentAccent}3D`);

  const btn = document.getElementById("themeToggle");
  if (btn) {
    btn.innerHTML = `<i data-lucide="${isLight ? "sun" : "moon"}"></i>`;
    if (window.lucide) lucide.createIcons();
  }

  if (UD) {
    UD.settings.themeMode = currentMode;
    if (currentMode === "light") {
      UD.settings.themeAccentLight = currentAccent;
    } else {
      UD.settings.themeAccentDark = currentAccent;
    }
    saveUD();
  }

  if (persist) {
    LS.set("themeMode", currentMode);
    LS.set("themeAccentDark", UD?.settings?.themeAccentDark || "#4C8DFF");
    LS.set("themeAccentLight", UD?.settings?.themeAccentLight || "#2F5FE0");
  }

  updateThemePickerUI(currentMode, currentAccent);
}

function toggleTheme() {
  const nextMode =
    (UD?.settings?.themeMode || "light") === "light" ? "dark" : "light";
  setTheme(nextMode, getActiveAccent(nextMode));
}

function applyTheme(color, mode = null) {
  const targetMode = mode || UD?.settings?.themeMode || "light";
  setTheme(targetMode, color);
  showToast(dashT("siswa.chrome.settings.themeChanged", "Tema diubah!"), "ok");
}

function loadTheme() {
  const savedMode = LS.get("themeMode") || UD?.settings?.themeMode || "light";
  const savedAccent = getActiveAccent(savedMode);
  setTheme(savedMode, savedAccent, false);
}

// ================================================================
// 5b. SIDEBAR COLLAPSE (desktop, icon-only)
// ================================================================

function toggleSidebarCollapse() {
  const sb = document.getElementById("sidebar");
  if (!sb) return;
  const collapsed = sb.classList.toggle("collapsed");
  LS.set("sidebarCollapsed", collapsed);
}

function loadSidebarCollapse() {
  const sb = document.getElementById("sidebar");
  if (!sb) return;
  if (LS.get("sidebarCollapsed") === true) {
    sb.classList.add("collapsed");
  }
}

// ================================================================
// 6. ROUTING
// ================================================================

const BREADCRUMB_LABELS = {
  overview: "siswa.header.breadcrumb_overview",
  myCourses: "siswa.my_courses.title",
  materi: "siswa.materi.title",
  lesson: "siswa.sidebar.nav_continue",
  quiz: "siswa.quiz.title",
  progress: "siswa.progress.title",
  laporan: "siswa.laporan.title",
  leaderboard: "siswa.leaderboard.title",
  profile: "siswa.profile.edit",
  settings: "siswa.settings.title",
};

function dashboardText(key, fallback) {
  const translate = window.EduCareI18n?.t;
  if (!translate) return fallback;
  const value = translate(key);
  return value && value !== key ? value : fallback;
}

// dashboardText + interpolasi {var}. Contoh:
//   dashT("siswa.chrome.quiz.toast", "Quiz selesai! Skor {pct}% +{xp} XP 🏅", { pct, xp })
function dashT(key, fallback, vars) {
  let value = dashboardText(key, fallback);
  if (vars) {
    value = value.replace(/\{(\w+)\}/g, (m, v) =>
      vars[v] !== undefined ? vars[v] : m,
    );
  }
  return value;
}

function localeForLang() {
  return window.EduCareI18n?.getLang() === "en" ? "en-US" : "id-ID";
}

function updateBreadcrumb(name) {
  const el = document.getElementById("pageBreadcrumb");
  if (!el) return;
  const key = BREADCRUMB_LABELS[name] || BREADCRUMB_LABELS.overview;
  const translate = window.EduCareI18n?.t;
  const dashboardLabel = translate
    ? translate("siswa.header.breadcrumb_dashboard")
    : "Dashboard";
  const label = translate ? translate(key) : key;
  el.innerHTML = `${dashboardLabel} / <span class="cur">${label}</span>`;
}

function goDash(name) {
  document.querySelectorAll(".dp").forEach((p) => p.classList.remove("act"));
  const target = document.getElementById("dp-" + name);
  if (target) target.classList.add("act");

  document.querySelectorAll(".sbnav li a").forEach((a) => {
    const onclick = a.getAttribute("onclick") || "";
    const navKey = a.getAttribute("data-nav");
    a.classList.toggle(
      "act",
      navKey ? navKey === name : onclick.includes(`'${name}'`),
    );
  });

  updateBreadcrumb(name);

  const renders = {
    overview: renderOverview,
    myCourses: renderMyCourses,
    materi: renderMateriList,
    lesson: renderLesson,
    quiz: initQuiz,
    progress: renderProgress,
    cert: renderCerts,
    laporan: () => {},
    leaderboard: renderLB,
    notif: renderNotifs,
    profile: renderProfile,
    settings: () =>
      renderSettingsTab("sprofile", document.querySelector(".snavi")),
  };
  if (renders[name]) renders[name]();

  window.scrollTo({ top: 0, behavior: "smooth" });
}

// ================================================================
// Navigasi berbasis #hash -> panel dashboard
// ================================================================

const HASH_TO_PANEL = {
  "quiz-section": "quiz",
  "laporan-siswa": "laporan",
  "laporan-section": "laporan",
  "materi-section": "materi",
};

function panelNameFromHash() {
  const h = (window.location.hash || "").replace(/^#/, "");
  if (!h) return null;
  if (HASH_TO_PANEL[h]) return HASH_TO_PANEL[h];
  return document.getElementById("dp-" + h) ? h : null;
}

function navigateFromHash() {
  if (!CU || !UD) return;
  const panel = panelNameFromHash();
  if (panel) goDash(panel);
}

// ================================================================
// 7. HEADER / SIDEBAR
// ================================================================

function initHeader() {
  const disp = CU.fname + (CU.lname ? " " + CU.lname : "");

  const dn = document.getElementById("dashName");
  if (dn) dn.textContent = CU.fname;

  const dheadAvi = document.getElementById("dheadAvi");
  if (dheadAvi) dheadAvi.textContent = CU.fname[0].toUpperCase();

  const dheadUserName = document.getElementById("dheadUserName");
  if (dheadUserName) dheadUserName.textContent = CU.fname;

  const pdAvi = document.getElementById("pdAvi");
  if (pdAvi) pdAvi.textContent = CU.fname[0].toUpperCase();
  const pdName = document.getElementById("pdName");
  if (pdName) pdName.textContent = disp;

  const topClockTime = document.getElementById("topbarClockTime");
  const topClockDate = document.getElementById("topbarClockDate");
  if (topClockTime && topClockDate) {
    const parts = getDigitalClockParts();
    topClockTime.textContent = `${parts.time}:${parts.seconds}`;
    topClockDate.textContent = parts.dateLabel;
  }

  const unread = Array.isArray(UD.notifications)
    ? UD.notifications.filter((n) => !n.read).length
    : 0;
  const nb = document.getElementById("notifBadge");
  if (nb) {
    nb.style.display = unread > 0 ? "flex" : "none";
    nb.textContent = unread;
  }
}

// ================================================================
// PROFILE DROPDOWN (topbar avatar)
// ================================================================

function toggleProfileDropdown() {
  const dd = document.getElementById("profileDropdown");
  const btn = document.getElementById("profileMenuBtn");
  if (!dd || !btn) return;

  if (dd.classList.contains("show")) {
    closeProfileDropdown();
    return;
  }

  const rect = btn.getBoundingClientRect();
  const panelWidth = Math.min(230, window.innerWidth * 0.92);
  let left = rect.right - panelWidth;
  if (left < 12) left = 12;
  dd.style.top = rect.bottom + 8 + "px";
  dd.style.left = left + "px";

  dd.classList.add("show");
  setTimeout(
    () =>
      document.addEventListener("click", closeProfileDropdownOnClickOutside),
    0,
  );
}

function closeProfileDropdown() {
  document.getElementById("profileDropdown")?.classList.remove("show");
  document.removeEventListener("click", closeProfileDropdownOnClickOutside);
}

function closeProfileDropdownOnClickOutside(e) {
  const dd = document.getElementById("profileDropdown");
  const btn = document.getElementById("profileMenuBtn");
  if (!dd) return;
  if (dd.contains(e.target) || (btn && btn.contains(e.target))) return;
  closeProfileDropdown();
}

function levelName(l) {
  const names = dashboardText("siswa.chrome.levelNames", "");
  if (Array.isArray(names) && names[l]) return names[l];
  return (
    [
      "",
      "Pemula",
      "Explorer",
      "Pelajar",
      "Intermediate",
      "Advanced",
      "Pro",
      "Expert",
      "Master",
      "Elite",
      "Legend",
    ][l] || "Elite"
  );
}

function xpForNextLevel(l) {
  return l * 500;
}

// ================================================================
// 8. OVERVIEW
// ================================================================

function refreshDashboardSiswaUI() {
  const activePanel = document.querySelector(".dp.act");
  const activeName = activePanel?.id?.replace("dp-", "") || "overview";
  if (activeName === "overview") renderOverview();
  if (activeName === "myCourses") renderMyCourses();
  if (activeName === "materi") renderMateriList();
  if (activeName === "progress") renderProgress();
  if (activeName === "leaderboard") renderLB();
  if (activeName === "settings")
    renderSettingsTab("sprofile", document.querySelector(".snavi"));
  if (activeName === "quiz") initQuiz();
  if (typeof updateBreadcrumb === "function") updateBreadcrumb(activeName);
}

function renderOverview() {
  initHeader();

  const ec = UD.enrolledCourses;
  const hrs = UD.totalHours.toFixed(1);
  const quizzesDoneCount =
    Object.keys(UD.quizzesDone || {}).length + (UD.materiQuizzesDoneCount || 0);

  document.getElementById("dashStats").innerHTML = `
        <div class="dcard">
            <div class="dsic">📚</div>
            <div class="dsv">${ec.length}</div>
            <div class="dsl">${dashboardText("siswa.dynamic.active_courses", "Kursus Aktif")}</div>
            <div class="dsch ${ec.length > 0 ? "dup" : "tm"}">${ec.length > 0 ? "↑ " + dashboardText("siswa.dynamic.learning", "Aktif belajar") : dashboardText("siswa.dynamic.no_courses", "Belum ada kursus")}</div>
        </div>
        <div class="dcard">
            <div class="dsic">📝</div>
            <div class="dsv">${quizzesDoneCount}</div>
            <div class="dsl">${dashboardText("siswa.dynamic.quizzes_done", "Quiz Dikerjakan")}</div>
            <div class="dsch ${quizzesDoneCount > 0 ? "dup" : "tm"}">${quizzesDoneCount > 0 ? "↑ " + dashboardText("siswa.dynamic.great", "Keren!") : dashboardText("siswa.dynamic.first_quiz", "Kerjakan quiz pertamamu")}</div>
        </div>
        <div class="dcard">
            <div class="dsic">🔥</div>
            <div class="dsv">${UD.streak}</div>
            <div class="dsl">${dashboardText("siswa.dynamic.day_streak", "Streak Hari")}</div>
            <div class="dsch ${UD.streak > 0 ? "dup" : "tm"}">${UD.streak > 0 ? "🔥 " + dashboardText("siswa.dynamic.keep_going", "Pertahankan!") : dashboardText("siswa.dynamic.start_streak", "Mulai streak hari ini")}</div>
        </div>
    `;

  const oc = document.getElementById("overviewCourses");
  if (ec.length === 0) {
    oc.innerHTML = `
            <div class="es">
                <div class="ei">📚</div>
                <h3>${dashboardText("siswa.dynamic.no_courses", "Belum ada kursus")}</h3>
                <p>${dashboardText("siswa.dynamic.start_course_desc", "Mulai perjalananmu! Daftar kursus pertamamu dan mulai belajar dari nol.")}</p>
                <button class="btn bcyan bsm" onclick="goDash('materi')">📘 ${dashboardText("siswa.dynamic.view_materials", "Lihat Materi")}</button>
            </div>
        `;
  } else {
    oc.innerHTML = ec
      .slice(0, 4)
      .map((e) => {
        const c = COURSES.find((c) => c.id === e.id);
        if (!c) return "";
        const isDone = e.progress >= 100;
        return `
                <div class="cpi">
                    <div class="cph">
                        <span class="cpn">${escapeHtml(c.emoji)} ${escapeHtml(c.title)}</span>
                        <span class="cpp ${isDone ? "cpp-done" : ""}">${isDone ? "✓ " + dashboardText("siswa.dynamic.completed", "Selesai") : e.progress + "%"}</span>
                    </div>
                    <div class="cpb">
                        <div class="cpbf" style="width:${e.progress}%;background:${isDone ? "linear-gradient(90deg,var(--green),var(--cyan))" : "linear-gradient(90deg,var(--cyan),var(--violet))"}"></div>
                    </div>
                    <div class="cpm">
                        <span>${e.completedLessons.length} ${dashboardText("siswa.dynamic.modules", "modul")}</span>
                        <span onclick="enrollOrOpen(${c.id})" style="color:var(--cyan);cursor:pointer;font-weight:600">${isDone ? dashboardText("siswa.dynamic.view_course", "Lihat Kursus →") : dashboardText("siswa.dynamic.continue_learning", "Lanjutkan Belajar →")}</span>
                    </div>
                </div>
            `;
      })
      .join("");
  }

  const oa = document.getElementById("overviewActivity");
  if (!UD.activity.length) {
    oa.innerHTML = `
            <div class="es" style="padding:2rem">
                <div class="ei">⚡</div>
                <p>${dashboardText("siswa.dynamic.no_activity", "Belum ada aktivitas. Mulai belajar untuk melihat aktivitasmu di sini!")}</p>
            </div>
        `;
  } else {
    oa.innerHTML = UD.activity
      .slice(0, 5)
      .map(
        (a) => `
            <div class="ai">
                <div class="adot" style="background:var(--cyan)"></div>
                <div>
                    <div class="atext">${escapeHtml(a.text)}</div>
                    <div class="atime">${a.time}</div>
                </div>
            </div>
        `,
      )
      .join("");
  }

  const xpNeeded = xpForNextLevel(UD.level);
  const xpPct = Math.min(
    100,
    Math.round(((UD.xp % xpNeeded) / xpNeeded) * 100),
  );
  document.getElementById("xpCard").innerHTML = `
        <div style="font-size:2.5rem;font-weight:800;background:linear-gradient(135deg,var(--cyan),var(--violet));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">${UD.xp}</div>
        <div style="font-size:.78rem;color:var(--text2);font-family:var(--mono)">${dashT("siswa.chrome.overview.totalXp", "TOTAL XP")}</div>
        <div style="margin:1rem 0;padding:10px;background:var(--ad);border:1px solid rgba(245,158,11,.2);border-radius:var(--rsm)">
            <div style="font-size:1rem">⚡ ${dashT("siswa.chrome.overview.levelCurrent", "Level {level} — {name}", { level: UD.level, name: levelName(UD.level) })}</div>
            <div style="font-size:.75rem;color:var(--text2)">${dashT("siswa.chrome.overview.xpNext", "{n} XP lagi menuju Level {level}", { n: xpNeeded - (UD.xp % xpNeeded), level: UD.level + 1 })}</div>
        </div>
        <div class="cpb" style="height:8px">
            <div class="cpbf" style="width:${xpPct}%;background:linear-gradient(90deg,var(--amber),#ffffff)"></div>
        </div>
        <div style="font-size:.72rem;color:var(--text2);margin-top:4px;font-family:var(--mono)">${dashT("siswa.chrome.overview.xpFraction", "{have} / {need} XP", { have: UD.xp % xpNeeded, need: xpNeeded })}</div>
    `;

  const now = new Date();
  calYear = now.getFullYear();
  calMonth = now.getMonth();
  renderCal(document.getElementById("calendarCard"), calYear, calMonth, now);
  renderLBMini();
}

// ================================================================
// 9. CALENDAR
// ================================================================

function renderCal(el, yr, mo, now) {
  const months =
    dashboardText("siswa.chrome.overview.months", null) ||
    [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ];
  const days =
    dashboardText("siswa.chrome.overview.days", null) ||
    ["S", "S", "R", "K", "J", "S", "M"];
  const dim = new Date(yr, mo + 1, 0).getDate();
  const fd = (new Date(yr, mo, 1).getDay() + 6) % 7;
  const prev = new Date(yr, mo, 0).getDate();

  let html = `
        <div class="dch"><h3>${dashT("siswa.chrome.overview.calTitle", "📅 Kalender")}</h3></div>
        <div class="mcal">
            <div class="calh">
                <button class="caln" onclick="navCal(${yr},${mo - 1})">‹</button>
                <div class="calt">${months[mo]} ${yr}</div>
                <button class="caln" onclick="navCal(${yr},${mo + 1})">›</button>
            </div>
            <div class="cald">${days.map((d) => `<div class="caldl">${d}</div>`).join("")}
    `;

  for (let i = fd - 1; i >= 0; i--) {
    html += `<div class="calday om">${prev - i}</div>`;
  }
  for (let d = 1; d <= dim; d++) {
    const isT =
      d === now.getDate() && mo === now.getMonth() && yr === now.getFullYear();
    html += `<div class="calday ${isT ? "today" : ""}">${d}</div>`;
  }
  html += `</div></div>`;
  el.innerHTML = html;
}

function navCal(yr, mo) {
  if (mo < 0) {
    yr--;
    mo = 11;
  }
  if (mo > 11) {
    yr++;
    mo = 0;
  }
  calYear = yr;
  calMonth = mo;
  renderCal(document.getElementById("calendarCard"), yr, mo, new Date());
}

// ================================================================
// 10. LEADERBOARD MINI
// ================================================================

function renderLBMini() {
  const el = document.getElementById("lbMini");
  if (!el) return;

  const rows = Array.isArray(UD.leaderboard) ? UD.leaderboard : [];
  const meIn = rows.some((r) => r.isYou);

  let all = rows.map((r) => ({
    nm: r.name,
    init: r.avatar || (r.name ? r.name[0].toUpperCase() : "?"),
    xp: r.xp,
    grad: "var(--cyan),var(--violet)",
    isMe: !!r.isYou,
  }));

  if (!meIn) {
    all.push({
      nm: CU.fname,
      init: CU.fname[0],
      xp: UD.xp,
      grad: "var(--cyan),var(--violet)",
      isMe: true,
    });
  }
  all.sort((a, b) => b.xp - a.xp);

  el.innerHTML = all
    .slice(0, 4)
    .map((u, i) => {
      const rk = ["gd", "sv", "br", ""][i] || "";
      const rv = i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : i + 1;
      return `
            <div class="lbi ${i < 3 ? "lbi-top" : ""}" style="${u.isMe ? "background:var(--cd);border-radius:8px;padding:10px 4px" : ""}">
                <div class="lbr ${rk}">${rv}</div>
                <div class="lbav" style="background:linear-gradient(135deg,${u.grad});color:${i < 3 ? "var(--ink)" : "#fff"}">${u.init}</div>
                <div class="lbnm">${escapeHtml(u.nm)}${u.isMe ? ` <span style="font-size:.65rem;color:var(--cyan)">${dashT("siswa.chrome.lbYou", "(Kamu)")}</span>` : ""}</div>
                <div class="lbpt">${u.xp.toLocaleString()} ${dashT("siswa.chrome.xp", "XP")}</div>
            </div>
        `;
    })
    .join("");
}

// ================================================================
// 11. COURSE HELPERS
// ================================================================

function lvlMeta(level) {
  const badges = dashboardText("siswa.chrome.levelBadges", null) || [
    "🌱 Pemula",
    "🌿 Menengah",
    "🌳 Lanjutan",
  ];
  const map = {
    beginner: { lbl: badges[0], cls: "bgrn" },
    intermediate: { lbl: badges[1], cls: "bamb" },
    advanced: { lbl: badges[2], cls: "brs" },
  };
  return map[level] || { lbl: badges[0], cls: "bgrn" };
}

function getCourseGroup(c) {
  return (
    c.catGroup ||
    (["web", "ai", "data", "mobile", "uiux", "cloud"].includes(c.cat)
      ? "it"
      : "umum")
  );
}

// Menentukan indeks pelajaran yang seharusnya dilanjutkan: pelajaran
// pertama yang belum ditandai selesai. Jika semua sudah selesai,
// kembalikan pelajaran terakhir agar siswa bisa meninjau ulang.
function getResumeLessonIdx(course, ec) {
  if (!course || !ec) return 0;
  const allLessons = [];
  course.lessons.forEach((ch, ci) =>
    ch.items.forEach((_, li) => allLessons.push(`${ci}_${li}`)),
  );
  const completedKeys = ec.completedLessons || [];
  const idx = allLessons.findIndex((key) => !completedKeys.includes(key));
  return idx === -1 ? Math.max(allLessons.length - 1, 0) : idx;
}

function enrollOrOpen(courseId) {
  const c = COURSES.find((c) => c.id === courseId);
  if (!c) return;

  let ec = UD.enrolledCourses.find((e) => e.id === courseId);
  const isNewEnrollment = !ec;
  if (isNewEnrollment) {
    ec = {
      id: courseId,
      progress: 0,
      completedLessons: [],
      enrolledAt: new Date().toISOString(),
    };
    UD.enrolledCourses.push(ec);
    addXP(50, dashT("siswa.chrome.enroll.activity", 'Mendaftar kursus "{title}"', { title: c.title }));
    addActivity(dashT("siswa.chrome.enroll.activityAlt", 'Mendaftar ke kursus "{title}"', { title: c.title }), "📚");
    addNotif(
      "📚",
      dashT("siswa.chrome.enroll.notifTitle", "Kursus Baru!"),
      dashT("siswa.chrome.enroll.notifMsg", 'Kamu berhasil mendaftar ke "{title}". Selamat belajar!', { title: c.title }),
    );
    saveUD();
    showToast(dashT("siswa.chrome.enroll.toast", "Berhasil mendaftar!") + ` ${c.emoji} ${c.title}`, "ok");
  }

  // Kursus yang berasal dari Materi Pembelajaran (fromGuru) dibuka
  // dengan tampilan materi asli yang sama persis — supaya "Kursus
  // Saya"/"Lanjut Belajar" tidak menampilkan versi lain yang berbeda
  // dari "Materi Pembelajaran" dan membingungkan siswa.
  if (c.fromGuru) {
    goDash("materi");
    openMateriDetail(courseId - 5000);
    return;
  }

  activeCourseId = courseId;
  // Kursus baru mulai dari pelajaran pertama; kursus yang sudah
  // berjalan dilanjutkan dari pelajaran pertama yang belum selesai.
  activeLessonIdx = isNewEnrollment ? 0 : getResumeLessonIdx(c, ec);
  goDash("lesson");
}

// Dipanggil dari menu sidebar "Lanjut Belajar": memilih kursus yang
// paling relevan untuk dilanjutkan (kursus yang sedang berjalan dan
// belum selesai), lalu membuka materi yang sama seperti di "Materi
// Pembelajaran" (lewat enrollOrOpen) supaya tampilannya konsisten.
function continueLearning() {
  // Akun baru yang belum pernah membuka materi / mendaftar kursus apa pun
  // tidak punya "lanjutan". Cukup arahkan ke daftar Materi, jangan sampai
  // terkesan seolah-olah sudah mengklik/membaca suatu materi.
  const hasStarted =
    (Array.isArray(UD.enrolledCourses) && UD.enrolledCourses.length > 0) ||
    (Array.isArray(MATERI) && MATERI.some((m) => m.done));
  if (!hasStarted) {
    goDash("materi");
    return;
  }

  // Prioritas utama: materi pembelajaran yang BELUM SELESAI dibaca.
  // Ini yang dimaksud "Lanjut Belajar" — bukan kursus yang sudah 100%,
  // tapi materi asli buatan guru yang masih perlu dipelajari siswa.
  const unfinishedMateri = Array.isArray(MATERI)
    ? MATERI.find((m) => !m.done)
    : null;
  if (unfinishedMateri) {
    goDash("materi");
    openMateriDetail(unfinishedMateri.id);
    return;
  }

  // Semua materi sudah selesai dibaca — cek apakah masih ada kursus
  // (sistem lesson/bab) yang progresnya belum 100%.
  const ec = UD.enrolledCourses;
  if (!ec.length) {
    goDash("materi");
    return;
  }

  // Prioritaskan kursus yang sudah ada progres tapi belum 100%.
  let target = ec.find((e) => e.progress > 0 && e.progress < 100);
  // Jika tidak ada, pilih kursus yang belum selesai (terbaru didaftar).
  if (!target) target = [...ec].reverse().find((e) => e.progress < 100);

  // Kalau benar-benar semua materi & kursus sudah selesai, beri tahu
  // siswa alih-alih diam-diam membuka ulang kursus lama.
  if (!target) {
    showToast(dashT("siswa.chrome.complete.allDoneToast", "🎉 Semua materi & kursus sudah kamu selesaikan!"));
    goDash("materi");
    return;
  }

  enrollOrOpen(target.id);
}

// ================================================================
// 12. MY COURSES (Perbaikan Akhir: Struktur Rapi & Sejajar)
// ================================================================

function renderMyCourses() {
  const el = document.getElementById("myCoursesGrid");

  // "Kursus Saya" khusus menampilkan kursus yang SUDAH SELESAI (100%).
  const completedCourses = UD.enrolledCourses.filter((e) => e.progress >= 100);

  if (!completedCourses.length) {
    el.innerHTML = `
            <div class="es">
                <div class="ei">🎓</div>
                <h3>${dashT("siswa.chrome.mycourses.emptyTitle", "Belum ada kursus yang selesai")}</h3>
                <p>${dashT("siswa.chrome.mycourses.emptyDesc", "Kursus akan muncul di sini setelah kamu menyelesaikannya 100%. Yuk lanjutkan belajar dulu!")}</p>
                <button class="btn bcyan" onclick="continueLearning()">${dashT("siswa.chrome.mycourses.continue", "▶️ Lanjut Belajar")}</button>
            </div>
        `;
    return;
  }

  el.innerHTML = `
            ${completedCourses
              .map((e) => {
                const c = COURSES.find((c) => c.id === e.id);
                if (!c) return "";
                
                return `
                    <div class="course-card" onclick="enrollOrOpen(${c.id})" style="cursor:pointer">
                        
                        <div class="course-header">
                            <div class="course-icon" style="background:linear-gradient(135deg,${c.color},#0a0a0a)">${c.emoji}</div>
                            <div class="course-info">
                                <div class="course-title">${c.title}</div>
                                <div class="course-meta">
                                    <span class="course-level">${lvlMeta(c.level).lbl}</span>
                                </div>
                            </div>
                            <span class="course-status completed">${dashT("siswa.chrome.completed", "✓ Selesai")}</span>
                        </div>

                        <div class="course-progress-wrap">
                            <div class="course-progress-info">
                                <span class="course-progress-label">${dashT("siswa.chrome.mycourses.progress100", "100% selesai")}</span>
                                <span class="course-modules">${dashT("siswa.chrome.mycourses.lessonsCount", "{n} pelajaran", { n: e.completedLessons.length })}</span>
                            </div>
                        </div>

                        <div class="course-actions">
                            <button class="btn btn-review" onclick="event.stopPropagation();enrollOrOpen(${c.id})">
                                ${dashT("siswa.chrome.mycourses.review", "🔁 Tinjau Ulang")}
                            </button>
                        </div>
                    </div>
                `;
              })
              .join("")}
    `;
}

// ================================================================
// 14. LESSON
// ================================================================

// Menentukan kursus aktif jika belum ada yang dipilih secara eksplisit
// (misal: pengguna membuka menu "Lesson" atau "Quiz" langsung dari sidebar
// tanpa lebih dulu membuka salah satu kursus). Mengembalikan `true` jika
// berhasil menentukan kursus aktif, atau `false` jika pengguna belum
// mendaftar kursus apa pun.
function resolveActiveCourseId() {
  if (activeCourseId) return true;
  const ec = UD.enrolledCourses;
  if (!ec || ec.length === 0) return false;

  const target =
    ec.find((e) => e.progress > 0 && e.progress < 100) ||
    [...ec].reverse().find((e) => e.progress < 100) ||
    ec[ec.length - 1];
  activeCourseId = target.id;
  return true;
}

function renderLesson() {
  const el = document.getElementById("lessonContainer");
  if (!activeCourseId) {
    if (resolveActiveCourseId()) {
      const ec = UD.enrolledCourses;
      const target = ec.find((e) => e.id === activeCourseId);
      const cc = COURSES.find((c) => c.id === activeCourseId);
      activeLessonIdx = getResumeLessonIdx(cc, target);
    } else {
      el.innerHTML = `
                <div class="es">
                    <div class="ei">▶️</div>
                    <h3>${dashT("siswa.chrome.quiz.noActiveTitle", "Belum ada kursus aktif")}</h3>
                    <p>${dashT("siswa.chrome.lesson.noActiveDesc", "Daftar kursus dulu untuk mulai belajar!")}</p>
                    <button class="btn bcyan" onclick="goDash('materi')">${dashT("siswa.chrome.quiz.viewMateri", "📘 Lihat Materi")}</button>
                </div>
            `;
      return;
    }
  }

  const c = COURSES.find((c) => c.id === activeCourseId);
  if (!c) return;

  const ec = UD.enrolledCourses.find((e) => e.id === c.id);
  if (!ec) {
    enrollOrOpen(activeCourseId);
    return;
  }

  const allLessons = [];
  c.lessons.forEach((ch, ci) =>
    ch.items.forEach((l, li) =>
      allLessons.push({ ...l, chIdx: ci, liIdx: li, key: `${ci}_${li}` }),
    ),
  );
  const total = allLessons.length;

  // Kursus yang belum punya chapter/pelajaran (guru bisa saja menyimpan
  // materi tanpa menambahkan soal/lessons) jangan sampai membuat panel
  // lesson crash — tampilkan pesan yang jelas.
  if (!total) {
    el.innerHTML = `
            <div class="es">
                <div class="ei">📭</div>
                <h3>${dashT("siswa.chrome.lesson.noLessonsTitle", "Kursus belum punya pelajaran")}</h3>
                <p>${dashT("siswa.chrome.lesson.noLessonsDesc", 'Kursus "{title}" belum memiliki pelajaran. Silakan hubungi guru Anda atau cek materi lain.', { title: c.title })}</p>
                <button class="btn bcyan bsm" onclick="goDash('materi')">${dashT("siswa.chrome.quiz.viewMateri", "📘 Lihat Materi")}</button>
            </div>
        `;
    return;
  }

  const current = allLessons[activeLessonIdx] || allLessons[0];
  const completedKeys = ec.completedLessons || [];

  // Sidebar
  let sbHtml = `
        <div class="msbh">
            <div class="msbt">${c.emoji} ${c.title}</div>
            <div class="msbm">${dashT("siswa.chrome.lesson.summary", "{pct}% selesai · {n}/{total} pelajaran", { pct: ec.progress, n: completedKeys.length, total })}</div>
            <div class="cpb" style="margin-top:10px">
                <div class="cpbf" style="width:${ec.progress}%;background:linear-gradient(90deg,var(--cyan),var(--violet))"></div>
            </div>
        </div>
        <div class="mlist">
    `;
  let gi = 0;
  c.lessons.forEach((ch, ci) => {
    const chDone = ch.items.every((_, li) =>
      completedKeys.includes(`${ci}_${li}`),
    );
    sbHtml += `
            <div class="mchap">
                <div class="chtog" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none'">
                    <span>${ch.ch}</span>
                    <span style="font-size:.72rem;color:${chDone ? "var(--green)" : "var(--cyan)"}">${chDone ? dashT("siswa.chrome.completed", "✓ Selesai") : dashT("siswa.chrome.lesson.active", "Aktif")}</span>
                </div>
                <div class="chcon">
        `;
    ch.items.forEach((l, li) => {
      const gIdx = gi;
      const key = `${ci}_${li}`;
      const done = completedKeys.includes(key);
      const isAct = gIdx === activeLessonIdx;
      sbHtml += `
                <div class="li ${isAct ? "act" : ""} ${done && !isAct ? "comp" : ""}" onclick="activeLessonIdx=${gIdx};renderLesson()">
                    <span class="lic">${done ? "✓" : isAct ? "▶" : "○"}</span>
                    ${l.t}
                    <span class="ldur">${l.dur}</span>
                </div>
            `;
      gi++;
    });
    sbHtml += `</div></div>`;
  });
  sbHtml += `</div>`;

  // Main content
  const isGeneralCourse = getCourseGroup(c) === "umum";
  const generated = getDynamicLessonContent(c, current);
  const bodyHtml = generated.body;
  const rawScript = generated.rawScript;

  // Kursus IT (web, AI, data, mobile, uiux, cloud, dst.) menampilkan contoh
  // script/kode asli yang bisa dipraktikkan lewat kartu panel terpisah.
  // Kursus Umum (Matematika, IPA, IPS, Bahasa, Pengembangan Diri) TIDAK
  // mungkin punya contoh script pemrograman, jadi kartu panel ini
  // dihilangkan sepenuhnya untuk kursus umum — contoh penerapannya sudah
  // menyatu langsung di dalam penjelasan materi (lihat bagian "Use Case
  // dan Studi Kasus Dunia Nyata" pada isi materi di bawah).
  const scriptHtml = isGeneralCourse
    ? ""
    : `
        <div class="lvid">
            <div style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,.6);padding:4px 10px;border-radius:6px;font-size:.7rem;font-family:var(--mono)">${dashT("siswa.chrome.lesson.lessonLabel", "LESSON {cur}/{total}", { cur: activeLessonIdx + 1, total })}</div>
            <div class="pbtn" onclick="toggleScriptPanel(this)">📜</div>
            <div style="font-size:3rem;opacity:.2;margin-top:1rem;">${dashT("siswa.chrome.lesson.scriptPlaceholder", "📄 Script Langkah")}</div>
            <div id="scriptPanel" class="script-panel" style="display:none;">
                <div style="display:flex;justify-content:flex-end">
                    <button class="btn bghost bsm" onclick="toggleScriptPanel(document.querySelector('.pbtn'))">${dashT("siswa.chrome.lesson.close", "Tutup ✕")}</button>
                </div>
                <h3 style="color:var(--cyan);margin-bottom:1rem;">${dashT("siswa.chrome.lesson.scriptHeading", "📖 Script Praktik: {title}", { title: current.t })}</h3>
                <div style="font-size:.9rem;line-height:1.7;">
                    <p>${dashT("siswa.chrome.lesson.stepsIntro", "Berikut adalah langkah-langkah praktik untuk memahami materi ini:")}</p>
                    <ol style="margin:1rem 0 1rem 1.5rem;">
                        <li>${dashT("siswa.chrome.lesson.step1", "Buka <strong>Coding Playground</strong> (tombol di bawah).")}</li>
                        <li>${dashT("siswa.chrome.lesson.step2", "Salin kode contoh dan modifikasi sesuai petunjuk.")}</li>
                        <li>${dashT("siswa.chrome.lesson.step3", "Jalankan kode dan amati hasilnya.")}</li>
                        <li>${dashT("siswa.chrome.lesson.step4", "Coba variasikan nilai atau struktur kode.")}</li>
                    </ol>
                    <div class="codeblock" style="background:#0a0a0a;padding:16px;border-radius:4px;font-family:var(--mono);white-space:pre-wrap;color:#f2f2f0;border:1px solid rgba(255,255,255,.1);margin-top:12px;font-size:0.85rem;line-height:1.6;">${escapeHtml(rawScript)}</div>
                    <p style="margin-top:10px;"><strong>${dashT("siswa.chrome.lesson.tip", "Tips:")}</strong> ${dashT("siswa.chrome.lesson.tipText", "Gunakan <code>console.log()</code> untuk debug di playground. Jalankan kode di atas untuk melihat output simulasi!")}</p>
                </div>
            </div>
        </div>
    `;

  const lsnHtml = `
        <div class="lcont">
            <div class="lbc">${c.title} <span>/</span> ${c.lessons[current.chIdx]?.ch} <span>/</span> <span>${current.t}</span></div>
            <h2 class="ltit">${current.t}</h2>
            ${scriptHtml}
            <div style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap">
                <span style="font-size:.78rem;color:var(--text2)">⏱️ ${current.dur}</span>
                <span style="font-size:.78rem;color:var(--text2)">📘 ${c.title}</span>
                ${isGeneralCourse ? "" : `<button class="btn bghost bsm" style="margin-left:auto" onclick="openPlayground()">${dashT("siswa.chrome.lesson.playgroundBtn", "💻 Coding Playground")}</button>`}
                ${isGeneralCourse ? "" : `<button class="btn bghost bsm" onclick="askAI()">${dashT("siswa.chrome.lesson.aiBtn", "🤖 Tanya AI Tutor")}</button>`}
                ${isGeneralCourse ? "" : `<button class="btn bghost bsm" onclick="showToast(dashT('siswa.chrome.lesson.noteSaved', 'Catatan disimpan! 📝'),'ok')">${dashT("siswa.chrome.lesson.noteBtn", "🔖 Simpan Catatan")}</button>`}
            </div>
            <div class="lbody">
                ${bodyHtml}
            </div>
            <div class="lnav">
                <button class="btn bghost bsm" onclick="prevLesson()" ${activeLessonIdx === 0 ? 'disabled style="opacity:.4"' : ""}>${dashT("siswa.chrome.lesson.prev", "← Sebelumnya")}</button>
                <div style="display:flex;gap:.75rem">
                    ${isGeneralCourse ? "" : `<button class="btn bghost bsm" onclick="goDash('quiz')">${dashT("siswa.chrome.lesson.quizBtn", "❓ Kerjakan Quiz")}</button>`}
                    <button class="btn bcyan bsm" onclick="completeLesson()">${dashT("siswa.chrome.lesson.doneNext", "✓ Selesai & Lanjut →")}</button>
                </div>
            </div>
        </div>
    `;

  // Forum
  const forumPosts = (UD.courseForums[activeCourseId] || []).slice().reverse();
  let forumHtml = `
        <div class="forum-panel">
            <div class="forum-header">
                <h3 style="font-weight:700;font-size:1rem">${dashT("siswa.chrome.lesson.forumTitle", "💬 Forum Diskusi — {title}", { title: c.title })}</h3>
                <button class="btn bghost bsm" onclick="showNewPostForCourse(${activeCourseId})">${dashT("siswa.chrome.lesson.forumNew", "+ Buat Topik Baru")}</button>
            </div>
            <div id="courseForumList">
    `;
  if (forumPosts.length === 0) {
    forumHtml += `
            <div class="es" style="padding:2rem">
                <div class="ei">💬</div>
                <p>${dashT("siswa.chrome.lesson.forumEmpty", "Belum ada diskusi. Jadilah yang pertama bertanya atau berbagi!")}</p>
            </div>
        `;
  } else {
    forumHtml += forumPosts
      .map(
        (p, idx) => `
            <div class="forum-post">
                <div class="user">
                    <div class="user-avatar">${escapeHtml(p.init)}</div>
                    <div>
                        <div class="user-name">${escapeHtml(p.user)}</div>
                        <div class="time">${escapeHtml(p.time)}</div>
                    </div>
                </div>
                <div class="title">${escapeHtml(p.title)}</div>
                <div class="body">${escapeHtml(p.body)}</div>
                <div class="forum-actions">
                    <span onclick="likeForumPost(${activeCourseId}, ${idx})">👍 ${p.likes || 0} ${dashT("siswa.chrome.lesson.like", "Suka")}</span>
                    <span onclick="replyForumPost(${activeCourseId}, ${idx})">💬 ${dashT("siswa.chrome.lesson.reply", "Balas")}</span>
                </div>
                ${
                  p.replies && p.replies.length
                    ? `
                    <div style="margin-top:12px;padding-left:24px;border-left:2px solid var(--border);">
                        ${p.replies
                          .map(
                            (r) => `
                            <div class="forum-post" style="margin-top:8px;padding:8px 0;">
                                <div class="user">
                                    <div class="user-avatar">${escapeHtml(r.init)}</div>
                                    <div>
                                        <div class="user-name">${escapeHtml(r.user)}</div>
                                        <div class="time">${escapeHtml(r.time)}</div>
                                    </div>
                                </div>
                                <div class="body">${escapeHtml(r.body)}</div>
                            </div>
                        `,
                          )
                          .join("")}
                    </div>
                `
                    : ""
                }
            </div>
        `,
      )
      .join("");
  }
  forumHtml += `</div></div>`;

  el.innerHTML = `
        <div class="mlayout">
            <div class="msb">${sbHtml}</div>
            <div>${lsnHtml}${forumHtml}</div>
        </div>
    `;
}

// ================================================================
// 15. LESSON HELPERS
// ================================================================

function toggleScriptPanel(btn) {
  const panel = document.getElementById("scriptPanel");
  if (!panel) return;
  const lvid = panel.closest(".lvid");
  if (panel.style.display === "none") {
    panel.style.display = "block";
    if (btn) btn.textContent = "✕";
    if (lvid) lvid.classList.add("panel-open");
  } else {
    panel.style.display = "none";
    if (btn) btn.textContent = "📜";
    if (lvid) lvid.classList.remove("panel-open");
  }
}

function prevLesson() {
  if (activeLessonIdx > 0) {
    activeLessonIdx--;
    renderLesson();
  }
}

function completeLesson() {
  if (!activeCourseId) return;
  const c = COURSES.find((c) => c.id === activeCourseId);
  const ec = UD.enrolledCourses.find((e) => e.id === c.id);
  const allLessons = [];
  c.lessons.forEach((ch, ci) =>
    ch.items.forEach((_, li) => allLessons.push(`${ci}_${li}`)),
  );
  if (!allLessons.length) return;
  const key = allLessons[activeLessonIdx];

  if (!ec.completedLessons.includes(key)) {
    ec.completedLessons.push(key);
    ec.progress = Math.round(
      (ec.completedLessons.length / allLessons.length) * 100,
    );
    UD.totalHours += 0.5;
    addXP(30, dashT("siswa.chrome.complete.lessonReason", 'Menyelesaikan pelajaran di "{title}"', { title: c.title }));
    addActivity(dashT("siswa.chrome.complete.lessonReason", 'Menyelesaikan pelajaran di "{title}"', { title: c.title }), "✅");

    if (ec.completedLessons.length % 4 === 0) {
      addXP(50, dashT("siswa.chrome.complete.bonus", "Bonus chapter selesai!"));
      addNotif(
        "🎯",
        dashT("siswa.chrome.complete.chapterTitle", "Chapter Selesai!"),
        dashT("siswa.chrome.complete.chapterMsg", 'Kamu telah menyelesaikan chapter di "{title}". Lanjut terus!', { title: c.title }),
      );
    }

    if (ec.progress === 100) {
      UD.completedCourses.push(activeCourseId);
      UD.certificates.push({
        courseId: activeCourseId,
        title: c.title,
        date: new Date().toLocaleDateString(localeForLang()),
        emoji: c.emoji,
      });
      addXP(500, dashT("siswa.chrome.complete.courseReason", 'Menyelesaikan kursus "{title}"', { title: c.title }));
      addNotif(
        "🏅",
        dashT("siswa.chrome.complete.courseTitle", "Kursus Selesai!"),
        dashT("siswa.chrome.complete.courseMsg", 'Selamat! Kamu telah menyelesaikan "{title}". Sertifikatmu sudah tersedia!', { title: c.title }),
      );
      showToast(
        dashT("siswa.chrome.complete.courseDone", "🎉 Selamat! Kursus selesai! +500 XP & Sertifikat diraih!"),
        "ok",
      );
    }
    saveUD();
    initHeader();
    showToast(dashT("siswa.chrome.complete.lessonDone", "✅ Pelajaran selesai! +30 XP"), "ok");
  }

  if (activeLessonIdx < allLessons.length - 1) {
    activeLessonIdx++;
    renderLesson();
  } else {
    showToast(dashT("siswa.chrome.complete.allDone", "🎉 Kamu sudah menyelesaikan semua pelajaran!"), "ok");
  }
}

// ================================================================
// 16. CODING PLAYGROUND & AI
// ================================================================

function openPlayground() {
  const c = COURSES.find((c) => c.id === activeCourseId);
  if (!c) return;
  if (getCourseGroup(c) === "umum") {
    showToast(
      dashT("siswa.chrome.playground.onlyIT", "Coding Playground hanya tersedia untuk kursus IT/Pemrograman. Materi ini bisa dipelajari lewat ringkasan materi & Quiz."),
      "info",
    );
    return;
  }
  const allLessons = [];
  c.lessons.forEach((ch, ci) =>
    ch.items.forEach((l, li) =>
      allLessons.push({ ...l, chIdx: ci, liIdx: li, key: `${ci}_${li}` }),
    ),
  );
  const current = allLessons[activeLessonIdx] || allLessons[0];
  if (!current) {
    showToast(
      dashT("siswa.chrome.playground.noLesson", "Kursus ini belum punya pelajaran untuk dipraktikkan. Tambahkan pelajaran dari dashboard guru."),
      "info",
    );
    return;
  }
  const generated = getDynamicLessonContent(c, current);
  const code = generated.rawScript || dashT("siswa.chrome.playground.placeholder", "// Tulis kode di sini...");

  const modal = document.createElement("div");
  modal.className = "movl";
  modal.innerHTML = `
        <div class="modal" style="max-width:800px;width:90%">
            <button class="mclose" onclick="this.closest('.movl').remove()">✕</button>
            <h3 style="margin-bottom:1rem;font-weight:800">${dashT("siswa.chrome.playground.title", "💻 Coding Playground: {title}", { title: current.t })}</h3>
            <p style="font-size:.85rem;color:var(--text2);margin-bottom:1rem">${dashT("siswa.chrome.playground.hint", "Tulis atau modifikasi kode di bawah, lalu klik Jalankan.")}</p>
            <textarea id="playgroundCode" class="fi" style="font-family:var(--mono);height:300px;margin-bottom:1rem;font-size:0.85rem;line-height:1.6;background:#0a0a0a;color:#f2f2f0;border:1px solid rgba(255,255,255,.1);">${escapeHtml(code)}</textarea>
            <div style="display:flex;gap:1rem">
                <button class="btn bcyan" onclick="runPlayground()">${dashT("siswa.chrome.playground.run", "🚀 Jalankan Kode")}</button>
                <button class="btn bghost" onclick="this.closest('.movl').remove()">${dashT("siswa.chrome.playground.close", "Tutup")}</button>
            </div>
            <div id="playgroundOutput" style="background:var(--ink3);border:1px solid var(--border);border-radius:var(--rsm);padding:16px;margin-top:1rem;font-family:var(--mono);min-height:120px;white-space:pre-wrap;font-size:0.85rem;line-height:1.6;color:#f2f2f0;"></div>
        </div>
    `;
  document.body.appendChild(modal);
}

function runPlayground() {
  const code = document.getElementById("playgroundCode").value;
  const outputDiv = document.getElementById("playgroundOutput");

  const c = COURSES.find((c) => c.id === activeCourseId);
  const cat = c ? c.cat : "web";
  const isPhp =
    cat === "web" && c && (c.title || "").toLowerCase().includes("php");
  const effectiveCat = isPhp ? "php" : cat;

  if (effectiveCat !== "web") {
    const label = effectiveCat === "php" ? "PHP" : effectiveCat.toUpperCase();
    outputDiv.innerHTML = `<span style="color:var(--cyan)">[Compiling & Executing ${label} script...]</span><br/><span style="color:var(--green)">▶ Output:</span><br/>`;
    setTimeout(() => {
      let simulatedOutput = "";
      if (effectiveCat === "php") {
        simulatedOutput =
          "Memuat materi: " +
          (c ? c.title : "Materi PHP") +
          "\nAli lulus dengan nilai 85\nSiti lulus dengan nilai 91\nMateri selesai diproses.\n\nProgram exited with code 0.";
      } else if (effectiveCat === "ai") {
        simulatedOutput =
          "--- AI Model Regresi Linier ---\nUkuran Rumah: 75 m²\nHasil Prediksi Harga: $ 21,250\nModel berhasil mengevaluasi bobot dan bias secara optimal!\n\nProgram exited with code 0.";
      } else if (effectiveCat === "data") {
        simulatedOutput =
          "Dataset Asli: [\n  12.5,\n  15,\n  null,\n  18.2,\n  14.5,\n  null,\n  20.1\n]\nNilai rata-rata dari data valid: 16.08\nDataset setelah dibersihkan: [12.5, 15, 16.08, 18.2, 14.5, 16.08, 20.1]\nNilai Median dataset bersih: 16.08\n\nProgram exited with code 0.";
      } else if (effectiveCat === "mobile") {
        simulatedOutput =
          "Siklus Hidup: ViewModel diinisialisasi.\nState Android Berubah! Nilai counter Android: 1\nState Android Berubah! Nilai counter Android: 2\nUI Update: Menampilkan nilai counter = 2\n\nProgram exited with code 0.";
      } else if (effectiveCat === "uiux") {
        simulatedOutput =
          "--- UI/UX Token untuk Desain ---\nWarna Aksen Dasbor: #ffffff\nFont Utama Figma: Bricolage Grotesque, sans-serif\nAuto-layout padding horizontal (lg): 24px\n\nProgram exited with code 0.";
      } else if (effectiveCat === "cloud") {
        simulatedOutput =
          '1. Membaca spesifikasi tugas ECS untuk deployment...\n{\n  "family": "educare-web-task",\n  "containerDefinitions": [\n    {\n      "name": "web-app",\n      "image": "educare/frontend:v2.0",\n      "cpu": 256,\n      "memory": 512\n    }\n  ]\n}\n2. Menghubungkan ke API AWS Region ap-southeast-1...\n3. Melakukan provisioning kontainer...\nHasil: Container successfully deployed to ECS Cluster!\n\nProgram exited with code 0.';
      } else if (effectiveCat === "mtk") {
        simulatedOutput =
          'Diketahui: a = 12, b = 5\nLangkah 1 - Penjumlahan: a + b = 17\nLangkah 2 - Perkalian: a x b = 60\nKesimpulan: hasil akhir perhitungan = {"jumlah":17,"kali":60}\n\nProgram exited with code 0.';
      } else if (effectiveCat === "ipa") {
        simulatedOutput =
          "Pagi: 24 derajat Celcius\nSiang: 31 derajat Celcius\nSore: 27 derajat Celcius\nMalam: 22 derajat Celcius\nRata-rata suhu dalam sehari: 26.0 derajat Celcius\n\nProgram exited with code 0.";
      } else if (effectiveCat === "ips") {
        simulatedOutput =
          "Desa Sukamaju: 1200 jiwa\nDesa Sumber Rejo: 950 jiwa\nDesa Mekar Sari: 1430 jiwa\nTotal penduduk tiga desa: 3580 jiwa\n\nProgram exited with code 0.";
      } else if (effectiveCat === "bahasa") {
        simulatedOutput =
          "Kalimat: Ani membaca buku di perpustakaan sekolah\nJumlah kata: 6\nKata pertama (subjek): Ani\nKata kedua (predikat): membaca\n\nProgram exited with code 0.";
      } else if (effectiveCat === "self") {
        simulatedOutput =
          "Senin: Berhasil ✔\nSelasa: Berhasil ✔\nRabu: Belum konsisten ✘\nKamis: Berhasil ✔\nJumat: Berhasil ✔\nKonsistensi minggu ini: 4/5 hari\n\nProgram exited with code 0.";
      } else {
        simulatedOutput =
          'Mempelajari materi "' +
          (c ? c.title : "kursus ini") +
          '".\n\nProgram exited with code 0.';
      }
      outputDiv.innerHTML += simulatedOutput.replace(/\n/g, "<br/>");
    }, 800);
    return;
  }

  try {
    const oldLog = console.log;
    let output = "";
    console.log = (...args) => {
      output +=
        args
          .map((a) => (typeof a === "object" ? JSON.stringify(a, null, 2) : a))
          .join(" ") + "\n";
      oldLog(...args);
    };
    new Function(code)();
    console.log = oldLog;
    outputDiv.innerHTML = `<span style="color:var(--green)">▶ ${dashT("siswa.chrome.playground.output", "Output")}:</span><br/>${escapeHtml(output).replace(/\n/g, "<br/>") || dashT("siswa.chrome.playground.noOutput", "(tidak ada output)")}`;
  } catch (e) {
    outputDiv.innerHTML = `<span style="color:var(--rose)">⚠️ ${dashT("siswa.chrome.playground.error", "Error:")}</span> ${e.message}`;
  }
}

function askAI() {
  const q = prompt(dashT("siswa.chrome.ai.prompt", "Tanyakan sesuatu tentang materi ini (AI Tutor):"));
  if (!q) return;
  showToast(dashT("siswa.chrome.ai.thinking", "AI Tutor sedang memproses..."), "info");
  setTimeout(() => {
    showToast(
      dashT("siswa.chrome.ai.reply", "🤖 AI: Terima kasih atas pertanyaanmu! Pastikan untuk mempraktikkan kode setiap selesai membaca."),
      "info",
    );
  }, 500);
}

// ================================================================
// 17. FORUM PER KURSUS
// ================================================================

function showNewPostForCourse(courseId) {
  const title = prompt(dashT("siswa.chrome.forum.postTitle", "Judul topik diskusi:"));
  if (!title) return;
  const body = prompt(dashT("siswa.chrome.forum.postBody", "Isi diskusi:"));
  if (!body) return;
  const init = CU.fname[0];
  const user = CU.fname;
  if (!UD.courseForums[courseId]) UD.courseForums[courseId] = [];
  UD.courseForums[courseId].push({
    user,
    init,
    title,
    body,
    time: dashT("siswa.chrome.time.justNow", "Baru saja"),
    likes: 0,
    replies: [],
  });
  addXP(10, dashT("siswa.chrome.forum.postXp", "Posting di forum"));
  addActivity(dashT("siswa.chrome.forum.newTopicActivity", 'Membuat topik forum: {title}', { title }), "💬");
  saveUD();
  renderLesson();
  showToast(dashT("siswa.chrome.forum.postDone", "Topik berhasil dibuat! +10 XP"), "ok");
}

function likeForumPost(courseId, postIdx) {
  const post = (UD.courseForums[courseId] || [])[postIdx];
  if (post) {
    post.likes = (post.likes || 0) + 1;
    saveUD();
    renderLesson();
    showToast(dashT("siswa.chrome.forum.liked", "👍 Disukai!"), "ok");
  }
}

function replyForumPost(courseId, postIdx) {
  const replyBody = prompt(dashT("siswa.chrome.forum.replyPrompt", "Tulis balasanmu:"));
  if (!replyBody) return;
  const init = CU.fname[0];
  const user = CU.fname;
  const post = (UD.courseForums[courseId] || [])[postIdx];
  if (post) {
    if (!post.replies) post.replies = [];
    post.replies.push({ user, init, body: replyBody, time: dashT("siswa.chrome.time.justNow", "Baru saja") });
    saveUD();
    renderLesson();
    showToast(dashT("siswa.chrome.forum.replyDone", "Balasan ditambahkan!"), "ok");
  }
}

// ================================================================
// 18. QUIZ
// ================================================================

function guruQuizForCourse(c) {
  if (!c) return null;
  // Kursus bawaan: server sudah memetakan kategori -> materi -> quiz guru
  if (SEED_COURSE_QUIZ && SEED_COURSE_QUIZ[c.id]) {
    const m = SEED_COURSE_QUIZ[c.id];
    return {
      name: m.name,
      questions: m.questions || 0,
      done: !!m.done,
      url: m.url || "",
    };
  }
  // Kursus buatan guru: quiz yang menempel pada materi (id kursus = 5000 + materiId)
  if (c.fromGuru && typeof c.id === "number" && Array.isArray(SEED_GURU_QUIZZES)) {
    const materiId = c.id - 5000;
    for (let i = 0; i < SEED_GURU_QUIZZES.length; i++) {
      const g = SEED_GURU_QUIZZES[i];
      if (g.materiId === materiId) {
        return {
          name: g.name,
          questions: g.questions || 0,
          done: !!g.done,
          url: g.fromMateri
            ? `../belajar/quiz.php?materi_id=${g.materiId}`
            : `../belajar/quiz.php?id=${g.id}`,
        };
      }
    }
  }
  return null;
}

function initQuiz() {
  const el = document.getElementById("quizArea");
  if (!el) return; // quizArea dihapus — daftar quiz ditampilkan dari server
  quizAnswers = {};
  if (quizTimer) clearInterval(quizTimer);

  if (!resolveActiveCourseId()) {
    el.innerHTML = `
            <div class="es">
                <div class="ei">❓</div>
                <h3>${dashT("siswa.chrome.quiz.noActiveTitle", "Belum ada kursus aktif")}</h3>
                <p>${dashT("siswa.chrome.quiz.noActiveDesc", "Daftar kursus dulu untuk bisa mengerjakan quiz-nya!")}</p>
                <button class="btn bcyan" onclick="goDash('materi')">${dashT("siswa.chrome.quiz.viewMateri", "📘 Lihat Materi")}</button>
            </div>
        `;
    return;
  }

  const c = COURSES.find((c) => c.id === activeCourseId);
  if (!c) return;

  const q = QUIZ_DATA[activeCourseId];
  if (!q || !q.length) {
    // Kursus buatan guru punya quiz asli dari guru (data/quiz.json atau
    // quiz bawaan materi). Tautkan langsung ke halaman khusus alih-alih
    // menampilkan "Quiz belum tersedia" yang menyesatkan.
    const guruQuiz = guruQuizForCourse(c);
    if (guruQuiz && guruQuiz.url) {
      const linkUrl = guruQuiz.url;
      const isDone = !!guruQuiz.done;
      el.innerHTML = `
            <div class="dc" style="border-color:rgba(139,92,246,.3);background:linear-gradient(120deg,rgba(79,124,255,.12),rgba(139,123,255,.12))">
                <div class="dch">
                    <h3>${isDone ? "✅" : "🎯"} ${escapeHtml(guruQuiz.name)}</h3>
                    <span style="font-size:.7rem;color:var(--text3);font-family:var(--mono)">${dashT("siswa.chrome.quiz.guruSoal", "{n} soal · Quiz Guru", { n: guruQuiz.questions || 0 })}</span>
                </div>
                <p style="font-size:.82rem;color:var(--text2);margin:0 0 14px">${
                  isDone
                    ? dashT("siswa.chrome.quiz.guruRetake", "Quiz ini sudah kamu kerjakan. Kerjakan ulang untuk meningkatkan skor.")
                    : dashT("siswa.chrome.quiz.guruAvailable", "Quiz ini dibuat oleh guru. Kerjakan di halaman khusus untuk mengumpulkan skor.")
                }</p>
                <a class="btn bcyan bsm" href="${linkUrl}">${
                  isDone ? dashT("siswa.chrome.quiz.retake", "Kerjakan Ulang →") : dashT("siswa.chrome.quiz.start", "Mulai Kerjakan →")
                }</a>
            </div>
        `;
      return;
    }

    el.innerHTML = `
            <div class="es">
                <div class="ei">🚧</div>
                <h3>${dashT("siswa.chrome.quiz.gnotAvailableTitle", "Quiz belum tersedia")}</h3>
                <p>${dashT("siswa.chrome.quiz.gnotAvailableDesc", "Quiz untuk kursus ini belum disiapkan.")}</p>
            </div>
        `;
    return;
  }
  const quizName = c.title + " — Quiz";

  let secs = 600;

  el.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
            <div>
                <h3 style="font-weight:700;font-size:1rem">${escapeHtml(quizName)}</h3>
                <div style="font-size:.78rem;color:var(--text2);font-family:var(--mono)">${dashT("siswa.chrome.quiz.estTime", "{n} soal · estimasi 10 menit", { n: q.length })}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:1.5rem;font-weight:800;color:var(--cyan)" id="qtimer">10:00</div>
                <div style="font-size:.68rem;color:var(--text2);font-family:var(--mono)">${dashT("siswa.chrome.quiz.timer", "WAKTU")}</div>
            </div>
        </div>
        <div id="qqWrap">
            ${q
              .map(
                (qn, qi) => `
                <div class="qq">
                    <div class="qqt">${qi + 1}. ${escapeHtml(qn.q)}</div>
                    <div class="qopts">
                        ${qn.opts
                          .map(
                            (o, oi) => `
                            <div class="qopt" onclick="selQ(${qi},${oi},this)">
                                <div class="qic">${String.fromCharCode(65 + oi)}</div>
                                ${escapeHtml(o)}
                            </div>
                        `,
                          )
                          .join("")}
                    </div>
                </div>
            `,
              )
              .join("")}
        </div>
        <div style="margin-top:1.5rem;display:flex;gap:1rem">
            <button class="btn bghost" onclick="initQuiz()">${dashT("siswa.chrome.quiz.reset", "🔄 Reset")}</button>
            <button class="btn bcyan" onclick="submitQuiz()" style="flex:1">${dashT("siswa.chrome.quiz.submit", "Periksa Jawaban →")}</button>
        </div>
        <div id="quizRes" style="margin-top:1.5rem"></div>
    `;

  const timer = document.getElementById("qtimer");
  quizTimer = setInterval(() => {
    secs--;
    if (secs <= 0) {
      clearInterval(quizTimer);
      submitQuiz();
      return;
    }
    const m = Math.floor(secs / 60)
      .toString()
      .padStart(2, "0");
    const s = (secs % 60).toString().padStart(2, "0");
    if (timer) {
      timer.textContent = `${m}:${s}`;
      if (secs <= 60) timer.style.color = "var(--rose)";
    }
  }, 1000);
}

function selQ(qi, oi, el) {
  quizAnswers[qi] = oi;
  el.closest(".qopts")
    .querySelectorAll(".qopt")
    .forEach((o) => o.classList.remove("sel"));
  el.classList.add("sel");
}

function submitQuiz() {
  if (quizTimer) clearInterval(quizTimer);
  const c = COURSES.find((c) => c.id === activeCourseId);
  const q = QUIZ_DATA[activeCourseId];
  if (!c || !q) return;
  const quizName = c.title + " — Quiz";
  let score = 0;

  q.forEach((qn, qi) => {
    const opts = document
      .querySelectorAll(".qq")
      [qi]?.querySelectorAll(".qopt");
    if (!opts) return;
    opts.forEach((opt, oi) => {
      if (oi === qn.ans) opt.classList.add("cor");
      else if (oi === quizAnswers[qi]) opt.classList.add("wrg");
    });
    if (quizAnswers[qi] === qn.ans) score++;
  });

  const pct = Math.round((score / q.length) * 100);
  const xpG = score * 10;

  // Catat quiz ini sebagai sudah dikerjakan (dipakai untuk statistik
  // "Quiz Dikerjakan" di Overview). Disimpan per kursus supaya mengulang
  // quiz yang sama tidak menghitung dua kali, dan skor terbaik disimpan.
  const quizKey = "course-" + activeCourseId;
  const prevBest = UD.quizzesDone[quizKey]?.score ?? -1;
  UD.quizzesDone[quizKey] = {
    score: Math.max(prevBest, pct),
    date: new Date().toISOString(),
  };

  const el = document.getElementById("quizRes");
  if (el) {
    el.innerHTML = `
            <div class="alt ${pct >= 80 ? "aok" : "aerr"}">
                ${pct >= 80 ? "🎉" : "😅"} ${dashT("siswa.chrome.quiz.resultScore", "Skor: {score}/{total} ({pct}%) — {feedback}", { score, total: q.length, pct, feedback: pct >= 80 ? dashT("siswa.chrome.quiz.resultGreat", "Luar biasa!") : dashT("siswa.chrome.quiz.resultRetry", "Coba lagi untuk meningkatkan pemahamanmu.") })}
                <span style="margin-left:8px;font-size:.75rem;opacity:.8">${dashT("siswa.chrome.quiz.savingMsg", "Menyimpan ke server…")}</span>
            </div>
        `;
  }

  if (pct >= 80) {
    addXP(xpG, dashT("siswa.chrome.quiz.passXp", "Lulus Quiz"));
    showToast(dashT("siswa.chrome.quiz.doneToast", "Quiz selesai! Skor {pct}% +{xp} XP 🏅", { pct, xp: xpG }), "ok");
  }
  addActivity(dashT("siswa.chrome.quiz.activity", "Mengerjakan quiz: skor {pct}%", { pct }), "❓");
  saveUD();

  document.getElementById("sqfId").value = c.id;
  document.getElementById("sqfName").value = quizName;
  document.getElementById("sqfScore").value = pct;
  setTimeout(() => {
    document.getElementById("serverQuizForm").submit();
  }, 1600);
}

// ================================================================
// 19. PROGRESS
// ================================================================

function renderProgress() {
  const wd = document.getElementById("weekChart");
  const days = dashboardText("siswa.dynamic.week_days", [
    "Sen",
    "Sel",
    "Rab",
    "Kam",
    "Jum",
    "Sab",
    "Min",
  ]);
  const wa = UD.weekActivity
    .map(
      (v, i) => `
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
            <div style="flex:1;width:100%;border-radius:4px;background:var(--cd);display:flex;align-items:flex-end">
                <div style="width:100%;height:${v ? Math.max(10, (v / 5) * 100) + "%" : "5%"};border-radius:4px;background:var(--cyan)"></div>
            </div>
            <span style="font-size:.62rem;color:var(--text2);font-family:var(--mono)">${days[i]}</span>
        </div>
    `,
    )
    .join("");

  wd.innerHTML = `
        <div style="display:flex;align-items:flex-end;gap:8px;height:120px;padding-bottom:8px">${wa}</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:1.25rem;text-align:center">
            <div><div style="font-weight:800;color:var(--cyan);font-size:1.2rem">${UD.weekActivity.reduce((a, b) => a + b, 0).toFixed(1)}h</div><div style="font-size:.72rem;color:var(--text2)">${dashboardText("siswa.dynamic.this_week", "Minggu ini")}</div></div>
            <div><div id="progress-totalHours" style="font-weight:800;color:var(--violet);font-size:1.2rem">${UD.totalHours.toFixed(4)}h</div><div style="font-size:.72rem;color:var(--text2)">${dashboardText("siswa.dynamic.total", "Total")}</div></div>
            <div><div style="font-weight:800;color:var(--amber);font-size:1.2rem">${UD.enrolledCourses.length}</div><div style="font-size:.72rem;color:var(--text2)">${dashboardText("siswa.dynamic.courses", "Kursus")}</div></div>
        </div>
    `;

  const sb = document.getElementById("skillBars");
  const ec = UD.enrolledCourses;
  if (!ec.length) {
    sb.innerHTML = `<div class="es"><p>${dashboardText("siswa.dynamic.no_skill", "Belum ada skill yang dipelajari. Mulai kursus dulu!")}</p></div>`;
  } else {
    const skills = ec
      .map((e) => {
        const c = COURSES.find((c) => c.id === e.id);
        return c ? { nm: c.title, pct: e.progress, emoji: c.emoji } : null;
      })
      .filter(Boolean);
    sb.innerHTML = skills
      .map(
        (s) => `
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:5px">
                    <span>${escapeHtml(s.emoji)} ${escapeHtml(s.nm)}</span>
                    <span style="font-family:var(--mono);color:var(--cyan)">${s.pct}%</span>
                </div>
                <div class="cpb">
                    <div class="cpbf" style="width:${s.pct}%;background:linear-gradient(90deg,var(--cyan),var(--violet))"></div>
                </div>
            </div>
        `,
      )
      .join("");
  }

  const pd = document.getElementById("progressDetail");
  if (!ec.length) {
    pd.innerHTML = `<div class="es"><p>${dashboardText("siswa.dynamic.progress_detail_empty", "Daftar kursus untuk melihat detail progress.")}</p></div>`;
    return;
  }
  pd.innerHTML = ec
    .map((e) => {
      const c = COURSES.find((c) => c.id === e.id);
      if (!c) return "";
      return `
            <div class="cpi" style="border-bottom:1px solid var(--border);padding-bottom:1rem;margin-bottom:1rem">
                <div class="cph">
                    <span class="cpn">${escapeHtml(c.emoji)} ${escapeHtml(c.title)}</span>
                    <span class="cpp">${e.progress}%</span>
                </div>
                <div class="cpb">
                    <div class="cpbf" style="width:${e.progress}%;background:linear-gradient(90deg,var(--cyan),var(--violet))"></div>
                </div>
                <div class="cpm">
                    <span>${dashT("siswa.chrome.progress.enrolledAt", "Didaftar: {date}", { date: new Date(e.enrolledAt).toLocaleDateString(localeForLang()) })}</span>
                    <span>${dashT("siswa.chrome.progress.lessonsDone", "{n} pelajaran selesai", { n: e.completedLessons.length })}</span>
                </div>
            </div>
        `;
    })
    .join("");
}

// ================================================================
// 20. CERTIFICATES
// ================================================================

function renderCerts() {
  const el = document.getElementById("certGrid");
  if (!UD.certificates.length) {
    el.innerHTML = `
            <div class="es" style="grid-column:1/-1">
                <div class="ei">🏅</div>
                <h3>${dashT("siswa.chrome.certs.emptyTitle", "Belum ada sertifikat")}</h3>
                <p>${dashT("siswa.chrome.certs.emptyDesc", "Selesaikan kursus untuk mendapatkan sertifikat resmi EduCare yang diakui.")}</p>
                <button class="btn bcyan" onclick="goDash('myCourses')">${dashT("siswa.chrome.certs.viewMyCourses", "📚 Lihat Kursus Saya")}</button>
            </div>
            ${UD.enrolledCourses
              .map((e) => {
                const c = COURSES.find((c) => c.id === e.id);
                if (!c) return "";
                return `
                    <div class="dc" style="border-style:dashed;opacity:.6">
                        <div style="padding:40px;text-align:center">
                            <div style="font-size:2.5rem;margin-bottom:12px;filter:grayscale(1)">🔒</div>
                            <div style="font-weight:700;font-size:.92rem;margin-bottom:6px">${escapeHtml(c.title)}</div>
                            <div style="font-size:.78rem;color:var(--text2)">${dashT("siswa.chrome.certs.stillNeeds", "Selesaikan {n}% lagi untuk sertifikat ini", { n: 100 - e.progress })}</div>
                            <div class="cpb" style="margin-top:12px">
                                <div class="cpbf" style="width:${e.progress}%;background:linear-gradient(90deg,var(--cyan),var(--violet))"></div>
                            </div>
                        </div>
                    </div>
                `;
              })
              .join("")}
        `;
    return;
  }

  const disp = CU.fname + (CU.lname ? " " + CU.lname : "");
  el.innerHTML =
    UD.certificates
      .map(
        (cert) => `
        <div class="dc" style="border-color:rgba(0,255,219,.2)">
            <div style="background:linear-gradient(135deg,rgba(0,255,219,.08),rgba(139,92,246,.08));border:1px solid rgba(0,255,219,.15);border-radius:var(--rmd);padding:24px;text-align:center;margin-bottom:16px">
                <div style="font-size:2.5rem;margin-bottom:8px">${escapeHtml(cert.emoji)}</div>
                <div style="font-family:var(--mono);font-size:.62rem;color:var(--cyan);letter-spacing:2px;margin-bottom:8px">${dashT("siswa.chrome.certs.official", "SERTIFIKAT RESMI")}</div>
                <h3 style="font-size:1.1rem;font-weight:800">${escapeHtml(cert.title)}</h3>
                <div style="font-size:.78rem;color:var(--text2);margin-top:4px">${dashT("siswa.chrome.certs.earnedBy", "Diraih oleh <strong>{name}</strong>", { name: escapeHtml(disp) })}</div>
                <div style="font-size:.72rem;color:var(--text3);margin-top:4px;font-family:var(--mono)">${escapeHtml(cert.date)}</div>
            </div>
            <div style="display:flex;gap:.75rem">
                <button class="btn bghost bsm" style="flex:1" onclick="showToast(dashT('siswa.chrome.certs.viewToast', 'Membuka sertifikat...'),'info')">👁 ${dashT("siswa.chrome.certs.view", "Lihat")}</button>
                <button class="btn bcyan bsm" style="flex:1" onclick="showToast(dashT('siswa.chrome.certs.downloadToast', 'Mengunduh PDF...'),'ok')">⬇ ${dashT("siswa.chrome.certs.download", "Unduh PDF")}</button>
            </div>
        </div>
    `,
      )
      .join("") +
    UD.enrolledCourses
      .filter((e) => !UD.completedCourses.includes(e.id))
      .map((e) => {
        const c = COURSES.find((c) => c.id === e.id);
        if (!c) return "";
        return `
            <div class="dc" style="border-style:dashed;opacity:.6">
                <div style="padding:40px;text-align:center">
                    <div style="font-size:2.5rem;margin-bottom:12px;filter:grayscale(1)">🔒</div>
                    <div style="font-weight:700;font-size:.92rem;margin-bottom:6px">${escapeHtml(c.title)}</div>
                    <div style="font-size:.78rem;color:var(--text2)">${dashT("siswa.chrome.certs.stillNeedsShort", "Selesaikan {n}% lagi", { n: 100 - e.progress })}</div>
                    <div class="cpb" style="margin-top:12px">
                        <div class="cpbf" style="width:${e.progress}%;background:linear-gradient(90deg,var(--cyan),var(--violet))"></div>
                    </div>
                </div>
            </div>
        `;
      })
      .join("");
}

// ================================================================
// 21. LEADERBOARD
// ================================================================

function renderLB() {
  const el = document.getElementById("lbFull");
  if (!el) return;

  const rows = Array.isArray(UD.leaderboard) ? UD.leaderboard : [];
  const meIn = rows.some((r) => r.isYou);

  let all = rows.map((r) => ({
    nm: r.name,
    init: r.avatar || (r.name ? r.name[0].toUpperCase() : "?"),
    xp: r.xp,
    grad: "var(--cyan),var(--violet)",
    isMe: !!r.isYou,
  }));

  if (!meIn) {
    all.push({
      nm: CU.fname + (CU.lname ? " " + CU.lname : ""),
      init: CU.fname[0],
      xp: UD.xp,
      grad: "var(--cyan),var(--violet)",
      isMe: true,
    });
  }

  all.sort((a, b) => b.xp - a.xp);

  el.innerHTML = all
    .map((u, i) => {
      const rk = i === 0 ? "gd" : i === 1 ? "sv" : i === 2 ? "br" : "";
      const rv = i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : i + 1;
      return `
            <div class="lbi ${i < 3 ? "lbi-top" : ""}" style="${u.isMe ? "background:var(--cd);border-radius:8px;padding:10px 4px" : ""}">
                <div class="lbr ${rk}">${rv}</div>
                <div class="lbav" style="background:linear-gradient(135deg,${u.grad});color:${i < 3 ? "var(--ink)" : "#fff"}">${u.init}</div>
                <div class="lbnm">${escapeHtml(u.nm)}${u.isMe ? ` <span style="font-size:.65rem;color:var(--cyan)">${dashT("siswa.chrome.lbYou", "(Kamu)")}</span>` : ""}</div>
                <div class="lbpt">${u.xp.toLocaleString()} ${dashT("siswa.chrome.xp", "XP")}</div>
            </div>
        `;
    })
    .join("");
}

function setLbTab(type, btn) {
  document
    .querySelectorAll('[id^="lbTab"]')
    .forEach((b) => b.classList.remove("act"));
  btn.classList.add("act");
  renderLB();
}

// Menghitung label waktu relatif ("5 menit lalu", "2 jam lalu", dst) dari
// timestamp asli, supaya notifikasi tidak selamanya menampilkan "Baru
// saja" walau sudah lama dibuat.
function formatRelativeTime(ts) {
  if (!ts) return dashT("siswa.chrome.time.justNow", "Baru saja");
  const diffMs = Date.now() - ts;
  const min = Math.floor(diffMs / 60000);
  if (min < 1) return dashT("siswa.chrome.time.justNow", "Baru saja");
  if (min < 60)
    return dashT("siswa.chrome.time.minutesAgo", "{n} menit lalu", { n: min });
  const hr = Math.floor(min / 60);
  if (hr < 24)
    return dashT("siswa.chrome.time.hoursAgo", "{n} jam lalu", { n: hr });
  const day = Math.floor(hr / 24);
  if (day === 1) return dashT("siswa.chrome.time.yesterday", "Kemarin");
  if (day < 7)
    return dashT("siswa.chrome.time.daysAgo", "{n} hari lalu", { n: day });
  return new Date(ts).toLocaleDateString(localeForLang(), {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
}

function renderNotifs() {
  const el = document.getElementById("notifList");
  if (!el) return;

  if (!UD.notifications.length) {
    el.innerHTML = `
            <div class="es">
                <div class="ei">🔔</div>
                <h3>${dashT("siswa.chrome.notifs.emptyTitle", "Tidak ada notifikasi")}</h3>
                <p>${dashT("siswa.chrome.notifs.emptyDesc", "Mulai belajar untuk mendapatkan notifikasi progress!")}</p>
            </div>
        `;
    return;
  }

  el.innerHTML = UD.notifications
    .map(
      (n, idx) => `
        <div class="notif-item ${n.read ? "" : "unread"}" onclick="markNotifRead(${idx})">
            <div class="notif-item-icon">${escapeHtml(n.icon)}</div>
            <div class="notif-item-body">
                <div class="notif-item-title">${escapeHtml(n.title)}</div>
                <div class="notif-item-msg">${escapeHtml(n.msg)}</div>
                <div class="notif-item-time">${n.createdAt ? formatRelativeTime(n.createdAt) : escapeHtml(n.time)}</div>
            </div>
            ${!n.read ? '<div class="notif-item-dot"></div>' : ""}
        </div>
    `,
    )
    .join("");
}

function markNotifRead(idx) {
  if (!UD.notifications[idx] || UD.notifications[idx].read) return;
  UD.notifications[idx].read = true;
  saveUD();
  renderNotifs();
  initHeader();
}

function markAllRead() {
  UD.notifications.forEach((n) => (n.read = true));
  saveUD();
  renderNotifs();
  initHeader();
  showToast(dashT("siswa.chrome.notifs.allRead", "Semua notifikasi ditandai dibaca."), "ok");
}

function addNotif(icon, title, msg) {
  UD.notifications.unshift({
    icon,
    title,
    msg,
    createdAt: Date.now(),
    time: dashT("siswa.chrome.time.justNow", "Baru saja"),
    read: false,
  });
  saveUD();
  initHeader();
  renderNotifs();
}

// Menyatukan notifikasi dari server (mis. "Materi Baru", "Target
// Mingguan") ke daftar notifikasi lokal setiap dashboard dibuka —
// dengan dedupe berdasarkan judul+pesan supaya tidak dobel terus di
// setiap refresh halaman.
function mergeServerNotifications(seedNotifs) {
  if (!Array.isArray(seedNotifs) || !UD.notifications) return;
  seedNotifs.forEach((sn) => {
    const exists = UD.notifications.some(
      (n) => n.title === sn.title && n.msg === sn.msg,
    );
    if (!exists) {
      UD.notifications.unshift({ ...sn, createdAt: Date.now() });
    }
  });
}

function toggleNotifDropdown() {
  const dd = document.getElementById("notifDropdown");
  const btn = document.getElementById("notifBellBtn");
  if (!dd || !btn) return;

  if (dd.classList.contains("show")) {
    dd.classList.remove("show");
    document.removeEventListener("click", closeNotifDropdownOnClickOutside);
    return;
  }

  const rect = btn.getBoundingClientRect();
  const panelWidth = Math.min(340, window.innerWidth * 0.92);
  let left = rect.left;
  if (left + panelWidth > window.innerWidth - 12) {
    left = Math.max(12, window.innerWidth - panelWidth - 12);
  }
  dd.style.top = rect.bottom + 8 + "px";
  dd.style.left = left + "px";

  renderNotifs();
  dd.classList.add("show");
  setTimeout(
    () => document.addEventListener("click", closeNotifDropdownOnClickOutside),
    0,
  );
}

function closeNotifDropdownOnClickOutside(e) {
  const dd = document.getElementById("notifDropdown");
  const btn = document.getElementById("notifBellBtn");
  if (!dd) return;
  if (dd.contains(e.target) || (btn && btn.contains(e.target))) return;
  dd.classList.remove("show");
  document.removeEventListener("click", closeNotifDropdownOnClickOutside);
}

function openNotifSettings() {
  document.getElementById("notifDropdown")?.classList.remove("show");
  document.removeEventListener("click", closeNotifDropdownOnClickOutside);
  goDash("settings");
  setTab("snotif", document.querySelectorAll(".snavi")[1]);
}

function renderProfile() {
  initHeader();
  const disp = CU.fname + (CU.lname ? " " + CU.lname : "");

  const avi = document.querySelector(".profile-avatar-inner");
  if (avi) avi.textContent = (CU.fname[0] || "?").toUpperCase();
  const pn = document.querySelector(".profile-name");
  if (pn) pn.textContent = disp;
  const pr = document.querySelector(".profile-role");
  if (pr)
    pr.textContent = `@${CU.uname || "user"} · ${
      CU.status || dashT("siswa.chrome.profile.roleFallback", "Pelajar Aktif")
    }`;
  const ps = document.querySelector(".profile-stats");
  if (ps)
    ps.innerHTML = `
        <div class="profile-stat">
            <span class="profile-stat-value">${UD.totalHours.toFixed(1)}h</span>
            <span class="profile-stat-label">${dashT("siswa.chrome.profile.statHours", "Jam Belajar")}</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat-value">${UD.enrolledCourses.length}</span>
            <span class="profile-stat-label">${dashT("siswa.chrome.profile.statCourses", "Kursus")}</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat-value">${UD.xp}</span>
            <span class="profile-stat-label">${dashT("siswa.chrome.profile.statXp", "XP")}</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat-value">${UD.streak}🔥</span>
            <span class="profile-stat-label">${dashT("siswa.chrome.profile.statStreak", "Streak")}</span>
        </div>
    `;

  const achIcons = ["🌟", "🔥", "🏅", "💬", "⚡", "📚", "🚀", "🏆"];
  const achKeys = [
    "first_lesson",
    "streak_3",
    "first_cert",
    "first_forum",
    "xp_100",
    "first_enroll",
    "speed_learner",
    "perfect_quiz",
  ];
  const achDefs = dashboardText("siswa.chrome.profile.ach", [
    ["First Blood", "Selesaikan pelajaran pertama"],
    ["Streak 3 Hari", "Belajar 3 hari berturut"],
    ["Certified", "Raih sertifikat pertama"],
    ["Komunitas", "Post pertama di forum"],
    ["100 XP", "Kumpulkan 100 XP"],
    ["Enrolled", "Daftar kursus pertama"],
    ["Speed Learner", "Selesaikan kursus <30 hari"],
    ["Top Scorer", "Skor quiz 100%"],
  ]);
  const allA = achDefs.map((g, i) => ({
    icon: achIcons[i] || "🏅",
    nm: g[0],
    desc: g[1],
    key: achKeys[i] || "first_lesson",
  }));

  const earned = new Set(UD.achievements);
  if (UD.enrolledCourses.length > 0) earned.add("first_enroll");
  const totLessons = UD.enrolledCourses.reduce(
    (s, e) => s + e.completedLessons.length,
    0,
  );
  if (totLessons > 0) earned.add("first_lesson");
  if (UD.streak >= 3) earned.add("streak_3");
  if (UD.certificates.length > 0) earned.add("first_cert");
  const forumPosts = Object.values(UD.courseForums || {}).reduce(
    (a, b) => a + b.length,
    0,
  );
  if (forumPosts > 0) earned.add("first_forum");
  if (UD.xp >= 100) earned.add("xp_100");

  const ag = document.getElementById("achieveGrid");
  if (ag)
    ag.innerHTML = allA
      .map(
        (a) => `
        <div class="abadge ${earned.has(a.key) ? "" : "lck"}">
            <div class="abic">${a.icon}</div>
            <div class="abnm">${escapeHtml(a.nm)}</div>
            <div class="abds">${escapeHtml(a.desc)}</div>
            ${
              earned.has(a.key)
                ? `<div style="font-size:.65rem;color:var(--green);font-family:var(--mono);margin-top:4px">${dashT("siswa.chrome.profile.earned", "✓ DIRAIH")}</div>`
                : ""
            }
        </div>
    `,
      )
      .join("");
}

const SETTING_PANELS = {
  sprofile: () => `
        <h3>${dashT("siswa.chrome.settings.profile", "Informasi Profil")}</h3>
        <div class="fr">
            <div class="fg"><label class="fl">${dashT("siswa.chrome.settings.firstName", "Nama Depan")}</label><input class="fi" type="text" id="sF" value="${escapeHtml(CU.fname || "")}"/></div>
            <div class="fg"><label class="fl">${dashT("siswa.chrome.settings.lastName", "Nama Belakang")}</label><input class="fi" type="text" id="sL" value="${escapeHtml(CU.lname || "")}"/></div>
        </div>
        <div class="fg"><label class="fl">${dashT("siswa.chrome.settings.email", "Email")}</label><input class="fi" type="email" id="sE" value="${escapeHtml(CU.email || "")}" disabled/></div>
        <div class="fg"><label class="fl">${dashT("siswa.chrome.settings.username", "Username")}</label><input class="fi" type="text" id="sU" value="${escapeHtml(CU.uname || "")}"/></div>
        <div class="fg"><label class="fl">${dashT("siswa.chrome.settings.institution", "Institusi")}</label><input class="fi" type="text" id="sI" value="${escapeHtml(CU.inst || "")}"/></div>
        <button class="btn bcyan" onclick="saveProfile()">${dashT("siswa.chrome.settings.save", "💾 Simpan Perubahan")}</button>
    `,
  snotif: () => `
        <h3>${dashT("siswa.chrome.settings.notif", "Pengaturan Notifikasi")}</h3>
        <div class="togrow"><div class="togl"><h4>${dashT("siswa.chrome.settings.emailNotif", "Notifikasi Email")}</h4><p>${dashT("siswa.chrome.settings.emailNotifDesc", "Terima update via email")}</p></div><button class="togbtn ${UD.settings.emailNotif ? "on" : ""}" onclick="togSetting('emailNotif',this)"></button></div>
        <div class="togrow"><div class="togl"><h4>${dashT("siswa.chrome.settings.courseNotif", "Pemberitahuan Kursus")}</h4><p>${dashT("siswa.chrome.settings.courseNotifDesc", "Info kursus sesuai minatmu")}</p></div><button class="togbtn ${UD.settings.courseNotif ? "on" : ""}" onclick="togSetting('courseNotif',this)"></button></div>
        <div class="togrow"><div class="togl"><h4>${dashT("siswa.chrome.settings.reminder", "Pengingat Belajar Harian")}</h4><p>${dashT("siswa.chrome.settings.reminderDesc", "Pengingat untuk menjaga streak")}</p></div><button class="togbtn ${UD.settings.reminderNotif ? "on" : ""}" onclick="togSetting('reminderNotif',this)"></button></div>
    `,
  sprivacy: () => `
        <h3>${dashT("siswa.chrome.settings.privacy", "Pengaturan Privasi")}</h3>
        <div class="togrow"><div class="togl"><h4>${dashT("siswa.chrome.settings.publicProfile", "Profil Publik")}</h4><p>${dashT("siswa.chrome.settings.publicProfileDesc", "Orang lain dapat melihat profilmu")}</p></div><button class="togbtn ${UD.settings.publicProfile ? "on" : ""}" onclick="togSetting('publicProfile',this)"></button></div>
        <div class="togrow"><div class="togl"><h4>${dashT("siswa.chrome.settings.lbVisible", "Tampilkan di Leaderboard")}</h4><p>${dashT("siswa.chrome.settings.lbVisibleDesc", "Masuk dalam papan peringkat")}</p></div><button class="togbtn ${UD.settings.leaderboard ? "on" : ""}" onclick="togSetting('leaderboard',this)"></button></div>
    `,
  sappear: () => {
    const activeMode = UD.settings.themeMode === "light" ? "light" : "dark";
    const darkAccent = UD.settings.themeAccentDark || "#4C8DFF";
    const lightAccent = UD.settings.themeAccentLight || "#2F5FE0";

    return `
            <h3>${dashT("siswa.chrome.settings.appearance", "Tampilan & Tema")}</h3>
            <div style="margin-bottom:1.25rem">
                <label class="fl">${dashT("siswa.chrome.settings.mode", "Mode Tampilan")}</label>
                <div style="display:flex;gap:.75rem;margin-top:.5rem;flex-wrap:wrap">
                    <button class="theme-chip ${activeMode === "dark" ? "active" : ""}" type="button" data-theme-mode="dark" onclick="setTheme('dark')" title="${dashT("siswa.chrome.settings.dark", "Tema gelap")}">🌙</button>
                    <button class="theme-chip ${activeMode === "light" ? "active" : ""}" type="button" data-theme-mode="light" onclick="setTheme('light')" title="${dashT("siswa.chrome.settings.light", "Tema terang")}">☀️</button>
                </div>
            </div>
            <div style="margin-bottom:1rem">
                <label class="fl">${dashT("siswa.chrome.settings.darkColor", "Warna untuk Dark Mode")}</label>
                <div style="display:flex;gap:.75rem;margin-top:.5rem;flex-wrap:wrap">
                    <button class="theme-chip accent ${darkAccent === "#4C8DFF" ? "active" : ""}" type="button" data-theme-accent="#4C8DFF" onclick="applyTheme('#4C8DFF', 'dark')" title="${dashT("siswa.chrome.settings.colorBlue", "Biru")}" style="background:#4C8DFF"></button>
                    <button class="theme-chip accent ${darkAccent === "#8B5CF6" ? "active" : ""}" type="button" data-theme-accent="#8B5CF6" onclick="applyTheme('#8B5CF6', 'dark')" title="${dashT("siswa.chrome.settings.colorPurple", "Ungu")}" style="background:#8B5CF6"></button>
                    <button class="theme-chip accent ${darkAccent === "#10B981" ? "active" : ""}" type="button" data-theme-accent="#10B981" onclick="applyTheme('#10B981', 'dark')" title="${dashT("siswa.chrome.settings.colorGreen", "Hijau")}" style="background:#10B981"></button>
                    <button class="theme-chip accent ${darkAccent === "#F59E0B" ? "active" : ""}" type="button" data-theme-accent="#F59E0B" onclick="applyTheme('#F59E0B', 'dark')" title="${dashT("siswa.chrome.settings.colorAmber", "Amber")}" style="background:#F59E0B"></button>
                    <button class="theme-chip accent ${darkAccent === "#F43F5E" ? "active" : ""}" type="button" data-theme-accent="#F43F5E" onclick="applyTheme('#F43F5E', 'dark')" title="${dashT("siswa.chrome.settings.colorPink", "Merah Muda")}" style="background:#F43F5E"></button>
                    <button class="theme-chip accent ${darkAccent === "#22D3EE" ? "active" : ""}" type="button" data-theme-accent="#22D3EE" onclick="applyTheme('#22D3EE', 'dark')" title="${dashT("siswa.chrome.settings.colorCyan", "Cyan")}" style="background:#22D3EE"></button>
                </div>
            </div>
            <div style="margin-bottom:1rem">
                <label class="fl">${dashT("siswa.chrome.settings.lightColor", "Warna untuk Light Mode")}</label>
                <div style="display:flex;gap:.75rem;margin-top:.5rem;flex-wrap:wrap">
                    <button class="theme-chip accent ${lightAccent === "#2F5FE0" ? "active" : ""}" type="button" data-theme-accent="#2F5FE0" onclick="applyTheme('#2F5FE0', 'light')" title="${dashT("siswa.chrome.settings.colorBlue", "Biru")}" style="background:#2F5FE0"></button>
                    <button class="theme-chip accent ${lightAccent === "#7C3AED" ? "active" : ""}" type="button" data-theme-accent="#7C3AED" onclick="applyTheme('#7C3AED', 'light')" title="${dashT("siswa.chrome.settings.colorPurple", "Ungu")}" style="background:#7C3AED"></button>
                    <button class="theme-chip accent ${lightAccent === "#059669" ? "active" : ""}" type="button" data-theme-accent="#059669" onclick="applyTheme('#059669', 'light')" title="${dashT("siswa.chrome.settings.colorGreen", "Hijau")}" style="background:#059669"></button>
                    <button class="theme-chip accent ${lightAccent === "#D97706" ? "active" : ""}" type="button" data-theme-accent="#D97706" onclick="applyTheme('#D97706', 'light')" title="${dashT("siswa.chrome.settings.colorAmber", "Amber")}" style="background:#D97706"></button>
                    <button class="theme-chip accent ${lightAccent === "#DC2626" ? "active" : ""}" type="button" data-theme-accent="#DC2626" onclick="applyTheme('#DC2626', 'light')" title="${dashT("siswa.chrome.settings.colorPink", "Merah Muda")}" style="background:#DC2626"></button>
                    <button class="theme-chip accent ${lightAccent === "#0891B2" ? "active" : ""}" type="button" data-theme-accent="#0891B2" onclick="applyTheme('#0891B2', 'light')" title="${dashT("siswa.chrome.settings.colorCyan", "Cyan")}" style="background:#0891B2"></button>
                </div>
            </div>
        `;
  },
};

function renderSettingsTab(tab, navEl) {
  document.querySelectorAll(".snavi").forEach((i) => i.classList.remove("act"));
  if (navEl) navEl.classList.add("act");
  const panel = document.getElementById("settingsPanel");
  if (panel && SETTING_PANELS[tab]) panel.innerHTML = SETTING_PANELS[tab]();
}

function setTab(tab, el) {
  document.querySelectorAll(".snavi").forEach((i) => i.classList.remove("act"));
  el.classList.add("act");
  const panel = document.getElementById("settingsPanel");
  if (panel && SETTING_PANELS[tab]) panel.innerHTML = SETTING_PANELS[tab]();
}

function saveProfile() {
  const f = document.getElementById("sF")?.value.trim();
  const l = document.getElementById("sL")?.value.trim();
  const u = document.getElementById("sU")?.value.trim();
  const ins = document.getElementById("sI")?.value.trim();
  if (f) CU.fname = f;
  if (l !== undefined) CU.lname = l;
  if (u) CU.uname = u;
  if (ins !== undefined) CU.inst = ins;
  LS.set("en_user", CU);
  initHeader();
  showToast(
    dashT("siswa.chrome.settings.saved", "Profil berhasil diperbarui secara lokal! Hubungi admin untuk perubahan data resmi. ✅"),
    "ok",
  );
}

function togSetting(key, btn) {
  btn.classList.toggle("on");
  UD.settings[key] = btn.classList.contains("on");
  saveUD();
}

function addXP(amount, reason) {
  UD.xp += amount;
  while (UD.xp >= UD.level * 500 && UD.level <= 10) {
    UD.level++;
    addNotif(
      "⬆️",
      dashT("siswa.chrome.levelup.title", "Level Up!"),
      dashT("siswa.chrome.levelup.msg", "Selamat! Kamu naik ke Level {level} — {name}! XP: {xp}", { level: UD.level, name: levelName(UD.level), xp: UD.xp }),
    );
  }
  saveUD();
}

function addActivity(text, icon = "⚡") {
  const now = new Date();
  const time =
    now.toLocaleTimeString(localeForLang(), { hour: "2-digit", minute: "2-digit" }) +
    ", " +
    now.toLocaleDateString(localeForLang());
  UD.activity.unshift({ text, icon, time });
  if (UD.activity.length > 20) UD.activity.pop();
  saveUD();
}

function showToast(msg, type = "ok") {
  const el = document.getElementById("toast");
  if (!el) return;
  const ico = type === "ok" ? "✅" : type === "err" ? "❌" : "ℹ️";
  el.innerHTML = ico + " " + msg;
  el.className = "show";
  clearTimeout(window._t);
  window._t = setTimeout(() => (el.className = ""), 3500);
}

(function () {
  const c = document.getElementById("bgc");
  if (!c) return;
  const ctx = c.getContext("2d");
  let pts = [];

  function resize() {
    c.width = window.innerWidth;
    c.height = window.innerHeight;
  }

  resize();
  window.addEventListener("resize", resize);

  for (let i = 0; i < 60; i++) {
    pts.push({
      x: Math.random() * window.innerWidth,
      y: Math.random() * window.innerHeight,
      vx: (Math.random() - 0.5) * 0.3,
      vy: (Math.random() - 0.5) * 0.3,
      r: Math.random() * 1.5 + 0.5,
      col: Math.random() > 0.5 ? "rgba(0,255,219," : "rgba(139,92,246,",
      a: Math.random() * 0.4 + 0.1,
    });
  }

  function draw() {
    ctx.clearRect(0, 0, c.width, c.height);
    pts.forEach((p) => {
      p.x += p.vx;
      p.y += p.vy;
      if (p.x < 0 || p.x > c.width) p.vx *= -1;
      if (p.y < 0 || p.y > c.height) p.vy *= -1;
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = p.col + p.a + ")";
      ctx.fill();
    });

    for (let i = 0; i < pts.length; i++) {
      for (let j = i + 1; j < pts.length; j++) {
        const dx = pts[i].x - pts[j].x;
        const dy = pts[i].y - pts[j].y;
        const d = Math.sqrt(dx * dx + dy * dy);
        if (d < 120) {
          ctx.beginPath();
          ctx.moveTo(pts[i].x, pts[i].y);
          ctx.lineTo(pts[j].x, pts[j].y);
          ctx.strokeStyle = `rgba(0,255,219,${0.06 * (1 - d / 120)})`;
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }
    requestAnimationFrame(draw);
  }
  draw();
})();

(function () {
  const dot = document.getElementById("cdot");
  const ring = document.getElementById("cring");
  if (!dot || !ring) return;
  let mx = 0,
    my = 0,
    rx = 0,
    ry = 0;

  document.addEventListener("mousemove", (e) => {
    mx = e.clientX;
    my = e.clientY;
    dot.style.left = mx + "px";
    dot.style.top = my + "px";
  });

  function anim() {
    rx += (mx - rx) * 0.12;
    ry += (my - ry) * 0.12;
    ring.style.left = rx + "px";
    ring.style.top = ry + "px";
    requestAnimationFrame(anim);
  }
  anim();

  document.addEventListener("mouseover", (e) => {
    if (e.target.closest("button,a,.cc,.dc")) {
      dot.style.transform = "translate(-50%,-50%) scale(1.5)";
      ring.style.width = "40px";
      ring.style.height = "40px";
      ring.style.borderColor = "rgba(0,255,219,.7)";
    }
  });

  document.addEventListener("mouseout", (e) => {
    if (e.target.closest("button,a,.cc,.dc")) {
      dot.style.transform = "translate(-50%,-50%) scale(1)";
      ring.style.width = "30px";
      ring.style.height = "30px";
      ring.style.borderColor = "rgba(0,255,219,.4)";
    }
  });
})();

function loadBuiltinCourses(courses) {
  if (!Array.isArray(courses) || !courses.length) return;
  COURSES.length = 0;
  courses.forEach(function (c) {
    COURSES.push(c);
  });
}

function mergeGuruCourses(guruCourses) {
  if (!Array.isArray(guruCourses)) return;
  guruCourses.forEach(function (gc) {
    if (
      !COURSES.some(function (c) {
        return c.id === gc.id;
      })
    ) {
      COURSES.push(gc);
    }
  });
}

// ================================================================
// MATERI — fitur "Materi" disatukan dari folder belajar (materi.php +
// detail-materi.php) supaya siswa membuka materi asli buatan guru
// langsung dari dashboard ini, dengan tampilan yang konsisten.
// ================================================================
let MATERI = [];
let materiGroupFilter = "all";
let materiSearchTerm = "";
let SEED_GURU_QUIZZES = [];
let SEED_COURSE_QUIZ = {};
let activeMateriId = null;
let materiScrollHandler = null;
let materiLastScrollY = 0;

function loadMateri(materiList) {
  MATERI = Array.isArray(materiList) ? materiList : [];
}


function materiCardHTML(m) {
  const statusBadge = m.done
    ? `<span class="materi-status completed">${dashT("siswa.chrome.completed", "✓ Selesai")}</span>`
    : `<span class="materi-status pending">${dashT("siswa.chrome.materi.notOpened", "Belum Dibuka")}</span>`;
  const videoTag = m.video_url
    ? dashT("siswa.chrome.materi.hasVideo", "🎬 Ada Video")
    : dashT("siswa.chrome.materi.textModule", "📄 Modul Teks");

  return `
        <div class="materi-card" onclick="openMateriDetail(${m.id})">
            ${statusBadge}

            <div class="materi-header">
                <div class="materi-icon" style="background:linear-gradient(135deg,${escapeHtml(m.color || "#1e293b")},#0a0a0a)">${escapeHtml(m.emoji || "📘")}</div>
                <div class="materi-info">
                    <div class="materi-title">${escapeHtml(m.title)}</div>
                    <div class="materi-tagrow">
                        <span class="materi-tag">${escapeHtml(m.category)}</span>
                        <span class="materi-category">${escapeHtml(m.groupLabel)}</span>
                    </div>
                </div>
            </div>

            <div class="materi-desc">${escapeHtml(m.desc || "")}</div>

            <div class="materi-meta">
                <span class="materi-badge">${videoTag}</span>
                <span class="materi-date">${escapeHtml(m.dateLabel || "")}</span>
            </div>
        </div>
    `;
}

function renderMateriList() {
  const gridView = document.getElementById("materiGridView");
  const detailView = document.getElementById("materiDetailView");
  if (gridView) gridView.style.display = "";
  if (detailView) {
    detailView.style.display = "none";
    detailView.innerHTML = "";
  }
  activeMateriId = null;
  if (materiScrollHandler) {
    window.removeEventListener("scroll", materiScrollHandler);
    materiScrollHandler = null;
  }

  const el = document.getElementById("materiGrid");
  if (!el) return;

  const list = MATERI.filter((m) => {
    const matchGroup =
      materiGroupFilter === "all" || m.group === materiGroupFilter;
    const matchSearch =
      materiSearchTerm === "" ||
      (m.title || "").toLowerCase().includes(materiSearchTerm.toLowerCase());
    return matchGroup && matchSearch;
  });

  if (!list.length) {
    el.innerHTML = `
            <div class="es" style="grid-column:1 / -1">
                <div class="ei">📘</div>
                <h3>${dashT("siswa.chrome.materi.emptyTitle", "Materi tidak ditemukan")}</h3>
                <p>${dashT("siswa.chrome.materi.emptyDesc", "Belum ada materi yang cocok dengan pencarian atau kategori ini.")}</p>
            </div>
        `;
    return;
  }

  el.innerHTML = list.map(materiCardHTML).join("");
}

function setMateriGroup(group, btn) {
  materiGroupFilter = group;
  document
    .querySelectorAll("#materiGroupTabs .fbtn")
    .forEach((b) => b.classList.remove("act"));
  if (btn) btn.classList.add("act");
  renderMateriList();
}

function onMateriSearch(value) {
  materiSearchTerm = value || "";
  renderMateriList();
}

function openMateriDetail(id) {
  const m = MATERI.find((x) => x.id === id);
  if (!m) return;

  const gridView = document.getElementById("materiGridView");
  const dv = document.getElementById("materiDetailView");
  if (gridView) gridView.style.display = "none";
  if (!dv) return;
  dv.style.display = "";

  // Jangan tandai materi "selesai" saat baru dibuka. Materi baru masuk ke
  // "Kursus Saya" / badge selesai setelah siswa benar-benar membaca sampai
  // akhir (scroll ke bagian bawah konten) — cek di checkMateriScrollComplete.
  activeMateriId = id;
  materiLastScrollY = window.scrollY;
  if (!materiScrollHandler) {
    materiScrollHandler = function () {
      checkMateriScrollComplete();
    };
    window.addEventListener("scroll", materiScrollHandler, { passive: true });
  }

  const videoHtml = m.video_url
    ? `
        <div class="dc" style="margin-bottom:1.25rem">
            <div class="dch"><h3>${dashT("siswa.chrome.materi.videoTitle", "🎬 Video Penjelasan")}</h3></div>
            <div style="position:relative;aspect-ratio:16/9;border-radius:14px;overflow:hidden;background:#000">
                ${
                  m.embedUrl
                    ? `<iframe src="${m.embedUrl}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`
                    : `<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;text-align:center;padding:1.5rem">
                        <div style="font-size:2.2rem">▶️</div>
                        <div style="font-size:.8rem;color:var(--text2)">${dashT("siswa.chrome.materi.videoFallback", "Putar video eksternal melalui link berikut:")}</div>
                        <a href="${m.video_url}" target="_blank" class="btn bcyan bsm">${dashT("siswa.chrome.materi.openVideo", "Buka Link Video")}</a>
                    </div>`
                }
            </div>
        </div>
    `
    : "";

  const navHtml =
    m.prevId || m.nextId
      ? `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:1.25rem">
            ${
              m.prevId
                ? `<div class="dc" style="cursor:pointer" onclick="openMateriDetail(${m.prevId})">
                <div style="font-size:.65rem;color:var(--text2);text-transform:uppercase;letter-spacing:.05em">${dashT("siswa.chrome.materi.prev", "← Materi Sebelumnya")}</div>
                <div style="font-weight:700;font-size:.85rem;margin-top:4px">${escapeHtml(m.prevTitle)}</div>
            </div>`
                : "<div></div>"
            }
            ${
              m.nextId
                ? `<div class="dc" style="cursor:pointer;text-align:right" onclick="openMateriDetail(${m.nextId})">
                <div style="font-size:.65rem;color:var(--text2);text-transform:uppercase;letter-spacing:.05em">${dashT("siswa.chrome.materi.next", "Materi Selanjutnya →")}</div>
                <div style="font-weight:700;font-size:.85rem;margin-top:4px">${escapeHtml(m.nextTitle)}</div>
            </div>`
                : "<div></div>"
            }
        </div>
    `
      : "";

  const quizHtml = m.quizAvailable
    ? `
        <div class="dc" style="margin-top:1.25rem;background:linear-gradient(120deg,rgba(79,124,255,.14),rgba(139,123,255,.14));border-color:rgba(79,124,255,.3)">
            <h3 style="margin:0 0 6px;font-size:.95rem">${dashT("siswa.chrome.materi.quizCta", "🎯 Siap Menguji Pemahamanmu?")}</h3>
            <p style="font-size:.78rem;color:var(--text2);margin:0 0 14px">${dashT("siswa.chrome.materi.quizCtaDesc", "Kerjakan <strong>{quiz}</strong> untuk menguji materi ini dan mengumpulkan skor.", { quiz: escapeHtml(m.quizName) })}</p>
            <a class="btn bcyan bsm" href="../belajar/quiz.php?materi_id=${m.id}">${dashT("siswa.chrome.materi.takeQuiz", "Kerjakan Kuis →")}</a>
        </div>
    `
    : `
      <div class="dc" style="margin-top:1.25rem;text-align:center">
        <p style="font-size:.78rem;color:var(--text2);margin:0;font-style:italic">${dashT("siswa.materi.no_quiz_category", "Kuis untuk kategori materi ini belum tersedia. Silakan hubungi guru.")}</p>
      </div>
    `;

  const groupBadgeClass =
    m.group === "it" ? "bvl" : m.group === "mtk" ? "bamb" : "bcn";
  dv.innerHTML = `
        <button class="btn bghost bsm" onclick="closeMateriDetail()" style="margin-bottom:1.25rem">${dashT("siswa.chrome.materi.back", "← Kembali ke Materi")}</button>
        <div class="dc" style="margin-bottom:1.25rem">
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
                <span class="bdg ${groupBadgeClass}" style="font-size:.6rem">${escapeHtml(m.category)}</span>
                <span class="bdg" style="font-size:.6rem;background:var(--card2);color:var(--text);border-color:var(--border2)">${escapeHtml(m.groupLabel)}</span>
                ${m.done ? `<span class="bdg bgrn" style="font-size:.6rem">${dashT("siswa.chrome.materi.studied", "✓ Selesai Dipelajari")}</span>` : ""}
            </div>
            <h1 style="margin:0 0 8px;font-size:1.6rem">${escapeHtml(m.emoji || "📘")} ${escapeHtml(m.title)}</h1>
            <div style="font-size:.75rem;color:var(--text2);font-family:var(--mono)">${dashT("siswa.chrome.materi.published", "Diterbitkan: {date}", { date: escapeHtml(m.dateLabel || "") })}</div>
        </div>
        ${videoHtml}
        <div class="dc materi-dark-content">
            ${m.contentHtml || ""}
        </div>
        ${navHtml}
        ${quizHtml}
    `;

  if (window.lucide) lucide.createIcons();
  if (window.hljs) {
    dv.querySelectorAll(".code-block code").forEach((block) =>
      hljs.highlightElement(block),
    );
  }
  dv.querySelectorAll(".code-block__copy").forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.getAttribute("data-copy-target");
      const codeEl = document.getElementById(targetId);
      if (!codeEl) return;
      navigator.clipboard.writeText(codeEl.innerText).then(() => {
        const label = btn.querySelector("span");
        const original = label ? label.textContent : "";
        btn.classList.add("is-copied");
        if (label) label.textContent = "Copied!";
        setTimeout(() => {
          btn.classList.remove("is-copied");
          if (label) label.textContent = original;
        }, 1600);
      });
    });
  });

  window.scrollTo({ top: 0, behavior: "smooth" });
}

// Menandai materi selesai dipelajari (server + tampilan).
function markMateriDone(m) {
  if (!m || m.done) return;
  const csrf = (typeof CU !== "undefined" && CU && CU.csrf) ? CU.csrf : "";
  fetch("mark-materi.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "category=" + encodeURIComponent(m.category) + "&csrf_token=" + encodeURIComponent(csrf),
  }).catch(() => {});
  m.done = true;

  // Materi = kurus guru (id 5000 + id materi). Sinkronkan langsung ke data
  // lokal supaya kursus langsung muncul di "Kursus Saya" & statistik naik,
  // tanpa harus menunggu reload (server seed baru terbaca saat load ulang).
  const guruCourseId = 5000 + (m.id || 0);
  if (guruCourseId > 5000) {
    let ec = (UD.enrolledCourses || []).find((e) => e.id === guruCourseId);
    if (!ec) {
      ec = {
        id: guruCourseId,
        progress: 100,
        completedLessons: [],
        enrolledAt: new Date().toISOString(),
      };
      UD.enrolledCourses.push(ec);
    } else {
      ec.progress = 100;
    }
    if (!UD.completedCourses.includes(guruCourseId)) {
      UD.completedCourses.push(guruCourseId);
    }
    if (!UD.certificates.some((c) => c.courseId === guruCourseId)) {
      UD.certificates.push({
        courseId: guruCourseId,
        title: m.title || "Kursus",
        date: new Date().toLocaleDateString(localeForLang()),
        emoji: m.emoji || "🏅",
      });
    }
  }

  addXP(20, dashT("siswa.chrome.materi.openedXp", 'Membaca materi "{title}"', { title: m.title }));
  addActivity(dashT("siswa.chrome.materi.openedXp", 'Membaca materi "{title}"', { title: m.title }), "📘");
  addNotif("📘", dashT("siswa.chrome.materi.openedNotifTitle", "Materi Selesai Dipelajari"), dashT("siswa.chrome.materi.openedNotifMsg", 'Kamu telah selesai membaca materi "{title}".', { title: m.title }));
  saveUD();
  initHeader();
}

// Menandai materi selesai hanya ketika siswa benar-benar scroll ke bawah
// sampai bagian bawah konten materi terlihat. Efek scroll-to-top saat
// membuka materi (& navigasi antar halaman) diabaikan supaya materi tidak
// "ke-klick" jadi selesai padahal belum dibaca sampai akhir.
function checkMateriScrollComplete() {
  if (activeMateriId == null) return;
  const m = MATERI.find((x) => x.id === activeMateriId);
  if (!m || m.done) return;
  const dv = document.getElementById("materiDetailView");
  if (!dv || dv.style.display === "none") return;

  const y = window.scrollY;
  const scrolledDown = y > materiLastScrollY + 2;
  materiLastScrollY = y;
  if (!scrolledDown) return;

  const rect = dv.getBoundingClientRect();
  // Bagian bawah konten materi sudah terlihat di layar.
  if (rect.bottom - window.innerHeight <= 120) {
    markMateriDone(m);
  }
}

function closeMateriDetail() {
  const gridView = document.getElementById("materiGridView");
  const dv = document.getElementById("materiDetailView");
  if (dv) {
    dv.style.display = "none";
    dv.innerHTML = "";
  }
  if (gridView) gridView.style.display = "";
  activeMateriId = null;
  if (materiScrollHandler) {
    window.removeEventListener("scroll", materiScrollHandler);
    materiScrollHandler = null;
  }
}

function initApp(initialUser, initialSeed) {
  // Header (.dhead) pakai backdrop-filter untuk efek blur sticky —
  // efek sampingnya, elemen position:fixed di dalamnya (dropdown
  // notifikasi & profil) jadi ikut "terjebak" relatif ke header,
  // bukan ke viewport penuh, sehingga muncul terpotong di ujung
  // layar. Pindahkan keduanya jadi anak langsung <body> supaya
  // posisi fixed-nya dihitung terhadap layar sesungguhnya.
  ["notifDropdown", "profileDropdown"].forEach((id) => {
    const el = document.getElementById(id);
    if (el && el.parentElement !== document.body) {
      document.body.appendChild(el);
    }
  });

  loadBuiltinCourses(initialSeed && initialSeed.courses);
  mergeGuruCourses(initialSeed && initialSeed.guruCourses);
  SEED_GURU_QUIZZES = (initialSeed && initialSeed.guruQuizzes) || [];
  SEED_COURSE_QUIZ = (initialSeed && initialSeed.courseQuiz) || {};
  loadMateri(initialSeed && initialSeed.materi);
  loadUser(initialUser, initialSeed);
  loadTheme();
  loadSidebarCollapse();
  initHeader();
  renderOverview();
  navigateFromHash();
  if (window.lucide) lucide.createIcons();
  startRealtimeStudyTimer();
  window.addEventListener("scroll", () => {
    const navbar = document.getElementById("navbar");
    if (navbar) {
      navbar.style.borderBottomColor =
        window.scrollY > 50 ? "rgba(0,255,219,.12)" : "var(--border)";
    }
  });
}

function bootDashboard() {
  const initEl = document.getElementById("dashboard-init-data");
  let initialUser = null;
  let initialSeed = null;

  if (initEl) {
    try {
      const payload = JSON.parse(initEl.textContent);
      initialUser = payload.user || null;
      initialSeed = payload.seed || null;
    } catch (error) {
      console.error("Gagal membaca data dashboard:", error);
    }
  }

  if (initialUser) {
    initApp(initialUser, initialSeed);
  }
}

document.addEventListener("educare:languagechange", function () {
  if (!UD || !CU) return;
  const activePanel = document.querySelector(".dp.act");
  const activeName = activePanel?.id.replace("dp-", "") || "overview";
  updateBreadcrumb(activeName);

  const renders = {
    overview: renderOverview,
    myCourses: renderMyCourses,
    materi: renderMateriList,
    lesson: renderLesson,
    quiz: initQuiz,
    progress: renderProgress,
    leaderboard: renderLB,
    profile: renderProfile,
    settings: () => {
      const activeTab = document.querySelector(".snavi.act");
      const tab = activeTab?.getAttribute("data-settings-tab") || "sprofile";
      renderSettingsTab(tab, activeTab);
    },
  };
  if (renders[activeName]) renders[activeName]();
});

// Navigasi berbasis hash: buka panel sesuai #hash di URL (mis. #quiz, #laporan)
window.addEventListener("hashchange", navigateFromHash);

document.addEventListener("DOMContentLoaded", bootDashboard);