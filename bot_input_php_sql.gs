var server_mysql_php = "URL SERVER mysql.php";//buat belajar bisa pake http://62.171.137.204/~tersakit/mysql.php?

function ambilDataAnggotaPHP(user_id) {
  var hasil = {}
  if (user_id) {
    var method_php = "ambil_data";
    var respon = UrlFetchApp.fetch(server_mysql_php + "method=" + method_php + "&user_id=" + user_id);
    if (respon) {
      var data_anggota = JSON.parse(respon);
      if (data_anggota && data_anggota.data) {
        if (data_anggota.data.query.user_id) {
          hasil['user_id'] = data_anggota.data.query.user_id;
        }
        if (data_anggota.data.query.nama) {
          hasil['nama'] = data_anggota.data.query.nama;
        }
        if (data_anggota.data.query.pendidikan_terakhir) {
          hasil['pendidikan_terakhir'] = data_anggota.data.query.pendidikan_terakhir;
        }
        if (data_anggota.data.query.alamat) {
          hasil['alamat'] = data_anggota.data.query.alamat;
        }
      }
    }
  }
  return hasil;
}

function tambahDataAnggotaPHP(user_id, nama = "", pendidikan_terakhir = "", alamat = "") {
  var hasil = false;
  if (user_id) {
    var method_php = "tambah_data";
    var respon = UrlFetchApp.fetch(server_mysql_php + "method=" + method_php + "&user_id=" + user_id + "&nama=" + nama + "&pendidikan_terakhir=" + pendidikan_terakhir + "&alamat=" + alamat);
    if (respon) {
      var json_data = JSON.parse(respon);
      if (json_data.data && json_data.data.status == "ok") {
        hasil = true;
      }
    }
  }
  return hasil;
}

function updateDataAnggotaPHP(user_id, nama = "", pendidikan_terakhir = "", alamat = "") {
  var hasil = false;
  if (user_id) {
    var method_php = "update_data";
    var respon = UrlFetchApp.fetch(server_mysql_php + "method=" + method_php + "&user_id=" + user_id + "&nama=" + nama + "&pendidikan_terakhir=" + pendidikan_terakhir + "&alamat=" + alamat);
    if (respon) {
      var json_data = JSON.parse(respon);
      if (json_data.data && json_data.data.status == "ok") {
        hasil = true;
      }
    }
  }
  return hasil;
}

function prossesAnggotaPHP(user_id = 0, chat_id = 0, text = "") {
  if (user_id != 0 && chat_id != 0 && text != "") {
    var anggota = ambilDataAnggotaPHP(user_id);
    if (anggota) {
      if (anggota.user_id) {
        if (anggota.nama) {
          if (anggota.pendidikan_terakhir) {
            if (anggota.alamat) {
              var berhasil_daftar = "Anda Sudah Terdaftar, Berikut data Anda;\n\n<code>";
              berhasil_daftar += "Id         : " + anggota.user_id + "\n";
              berhasil_daftar += "Nama       : " + anggota.nama + "\n";
              berhasil_daftar += "Pendidikan : " + anggota.pendidikan_terakhir + "\n";
              berhasil_daftar += "Alamat     : " + anggota.alamat + "</code>";
              sendMessage(chat_id, berhasil_daftar);
            } else {
              //alamat kosong
              if (text != "/daftar") {
                if (updateDataAnggotaPHP(user_id, anggota.nama, anggota.pendidikan_terakhir, text)) {
                  var berhasil_daftar = "Terimakasih, Anda Berhasil Terdaftar, Berikut data Anda;\n\n<code>";
                  berhasil_daftar += "Id         : " + anggota.user_id + "\n";
                  berhasil_daftar += "Nama       : " + anggota.nama + "\n";
                  berhasil_daftar += "Pendidikan : " + anggota.pendidikan_terakhir + "\n";
                  berhasil_daftar += "Alamat     : " + text + "</code>";
                  sendMessage(chat_id, berhasil_daftar);
                } else {
                  sendMessage(chat_id, "Anda sudah terdaftar, Tetapi Data Alamat Anda masih kosong\n\nSilakan tulis Alamat Anda");
                }
              }
            }
          } else {
            //pendidikan_terakhir kosong
            if (text != "/daftar") {
              if (updateDataAnggotaPHP(user_id, anggota.nama, text)) {
                sendMessage(chat_id, "Silakan tulis Alamat Anda.");
              } else {
                sendMessage(chat_id, "Maaf, Tidak dapat update data Pendidikan Terakhir ke database.\nCobalah beberapa saat lagi.");
              }
            } else {
              sendMessage(chat_id, "Anda sudah terdaftar, Tetapi Data Pendidikan Terakhir Anda masih kosong\n\nSilakan tulis Pendidikan Terakhir Anda");
            }
          }
        } else {
          //nama kosong
          if (text != "/daftar") {
            if (updateDataAnggotaPHP(user_id, text)) {
              sendMessage(chat_id, "Silakan tulis Pendidikan Terakhir Anda");
            } else {
              sendMessage(chat_id, "Maaf, Tidak dapat update data Nama ke database.\nCobalah beberapa saat lagi.");
            }
          } else {
            sendMessage(chat_id, "Anda sudah terdaftar, Tetapi Data Nama Anda masih kosong\n\nSilakan tulis Nama Anda");
          }
        }
      } else {
        //belum terdaftar
        if (tambahDataAnggotaPHP(user_id)) {
          sendMessage(chat_id, "Silakan tulis Nama Anda");
        } else {
          sendMessage(chat_id, "Maaf, Tidak dapat terhubung ke database.\nCobalah beberapa saat lagi.");
        }
      }
    } else {
      //data anggota kosong / gagal konek ke database
      sendMessage(chat_id, "Maaf, Tidak dapat terhubung ke database.\nCobalah beberapa saat lagi.");
    }
  }
}

function test_anggota_php() {
  prossesAnggotaPHP(user_id, chat_id, "/daftar");
}
