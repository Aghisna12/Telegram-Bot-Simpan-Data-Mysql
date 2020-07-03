function buatTabelAnggotaSql(koneksi, nama_tabel) {
  var hasil;
  if (koneksi) {
    var stmt = koneksi.createStatement()
    hasil = stmt.execute('CREATE TABLE ' + nama_tabel + ' (user_id VARCHAR(16), nama VARCHAR(255), pendidikan_terakhir VARCHAR(255), alamat VARCHAR(255), id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id));');
    stmt.close();
  }
  return hasil;
}

function cekDataAnggotaSql(koneksi, nama_tabel, user_id = "") {
  var hasil = {}
  if (koneksi) {
    var stmt = koneksi.createStatement();
    var data_anggota = stmt.executeQuery('SELECT user_id, nama, pendidikan_terakhir, alamat FROM ' + nama_tabel + ' WHERE user_id = ' + String(user_id));
    if (data_anggota.next()) {
      var banyak_kolom = data_anggota.getMetaData().getColumnCount();
      if (banyak_kolom == 4) {
        
        //nomor kolom per baris
        var kolom_user_id = 1; 
        var kolom_nama = 2;
        var kolom_pendidikan_terakhir = 3;
        var kolom_alamat = 4;
        //var kolom_id = 5;
        
        if (data_anggota.getString(kolom_user_id)) {
          hasil['user_id'] = data_anggota.getString(kolom_user_id);
        }
        if (data_anggota.getString(kolom_nama)) {
          hasil['nama'] = data_anggota.getString(kolom_nama);
        }
        if (data_anggota.getString(kolom_pendidikan_terakhir)) {
          hasil['pendidikan_terakhir'] = data_anggota.getString(kolom_pendidikan_terakhir);
        }
        if (data_anggota.getString(kolom_alamat)) {
          hasil['alamat'] = data_anggota.getString(kolom_alamat);
        }
      }
    }
    stmt.close();
  }
  return hasil;
}

function dataSemuaAnggotaSql(koneksi, nama_tabel) {
  var hasil;
  if (koneksi) {
    var stmt = koneksi.createStatement();
    hasil = stmt.executeQuery('SELECT * FROM ' + nama_tabel);
  }
  return hasil;
}

function tambahDataAnggotaSql(koneksi, nama_tabel, user_id, nama = "", pendidikan_terakhir = "", alamat = "") {
  var hasil;
  if (koneksi) {
    var stmt = koneksi.prepareStatement('INSERT INTO ' + nama_tabel + ' (user_id, nama, pendidikan_terakhir, alamat) values (?, ?, ?, ?)');
    stmt.setString(1, String(user_id));
    stmt.setString(2, nama);
    stmt.setString(3, pendidikan_terakhir);
    stmt.setString(4, alamat);
    hasil = stmt.execute();
    stmt.close();
  }
  return hasil;
}

function updateDataAnggotaSql(koneksi, nama_tabel, user_id, nama = "", pendidikan_terakhir = "", alamat = "") {
  var hasil;
  if (koneksi) {
    var stmt = koneksi.prepareStatement('UPDATE ' + nama_tabel + ' SET user_id = ?, nama = ?, pendidikan_terakhir = ?, alamat = ? WHERE user_id = ' + user_id);
    stmt.setString(1, user_id);
    stmt.setString(2, nama);
    stmt.setString(3, pendidikan_terakhir);
    stmt.setString(4, alamat);
    hasil = stmt.execute();
    stmt.close();
  }
  return hasil;
}

function getKoneksiSql() {
  //data server mysql
  var sql_server = "SERVER_HOST_MYSQL";
  var sql_server_port = "3306";
  var db_name = "MYSQL_DATABASE_NAME";
  var user_name = "USERNAME_MYSQL";
  var password = "PASSWORD_MYSQL";
  
  //koneksi utama ke server mysql
  return Jdbc.getConnection("jdbc:mysql://" + sql_server + ":" + sql_server_port + "/" + db_name, user_name, password);
}

function prossesAnggotaSql(user_id = 0, chat_id = 0, text = "") {
  
  var koneksi = getKoneksiSql();
  var nama_tabel = "anggota";
  
  //buatTabelAnggota(koneksi, nama_tabel);
  //tambahDataAnggota(koneksi, nama_tabel, "123");
  //updateDataAnggota(koneksi, nama_tabel, "1234", "Aghisna", "SMK Otomotif", "Jogja");
  //cekAnggota(koneksi, nama_tabel, "1234");
  //return;
  if (user_id != 0 && chat_id != 0 && text != "") {
    var anggota = cekDataAnggotaSql(koneksi, nama_tabel, user_id);
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
                var update_alamat = updateDataAnggotaSql(koneksi, nama_tabel, user_id, anggota.nama, anggota.pendidikan_terakhir, text);
                if (typeof update_alamat != 'undefined') {
                  var berhasil_daftar = "Terimakasih Anda Berhasil Terdaftar, Berikut data Anda;\n\n<code>";
                  berhasil_daftar += "Id         : " + anggota.user_id + "\n";
                  berhasil_daftar += "Nama       : " + anggota.nama + "\n";
                  berhasil_daftar += "Pendidikan : " + anggota.pendidikan_terakhir + "\n";
                  berhasil_daftar += "Alamat     : " + text + "</code>";
                  sendMessage(chat_id, berhasil_daftar);
                } else {
                  sendMessage(chat_id, "Maaf, Tidak dapat update data Alamat ke database.\nCobalah beberapa saat lagi.");
                }
              }
            }
          } else {
            //pendidikan_terakhir kosong
            if (text != "/daftar") {
              var update_pendidikan_terakhir = updateDataAnggotaSql(koneksi, nama_tabel, user_id, anggota.nama, text);
              if (typeof update_pendidikan_terakhir != 'undefined') {
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
            var update_nama = updateDataAnggotaSql(koneksi, nama_tabel, user_id, text);
            if (typeof update_nama != 'undefined') {
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
        var tambah_user_id = tambahDataAnggotaSql(koneksi, nama_tabel, user_id);
        if (typeof tambah_user_id != 'undefined') {
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
  //tutup koneksi
  koneksi.close();
}

function test_anggota_sql() {
  prossesAnggotaSql("USER_ID", "CHAT_ID", "/daftar");
}
