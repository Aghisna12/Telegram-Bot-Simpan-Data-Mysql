<?php

/*
Bahasa Kode : PHP
Required : mysql.php
Penulis : @Aghisna12
Tanggal : jum'at 3-juli-2020 (8:23am)

Terimakasih : Allah SWT
*/

//simpan Bot token
define('BOT_TOKEN', 'TOKEN BOT TELEGRAM');
//simpan url server koneksi php mysql
define('SERVER_MYSQL', 'PHP SERVER KONEKSI KE MYSQL DATABASE');//bisa coba pake ini http://62.171.137.204/~tersakit/mysql.php?


//kirim pesan text
function sendMessage($chat_id, $text, $parse = 'HTML')
{
    $api_telegram = "https://api.telegram.org/bot" . BOT_TOKEN . "/";
    $query = array(
        'method' => 'sendMessage',
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $parse
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_telegram);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//curl method get
function curlGet($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//ambil data dari server php yg terhubung ke sql
function ambilDataAnggotaPHP($user_id)
{
    $hasil = array();
    if ($user_id)
    {
        $method_php = "ambil_data";
        $query = array(
            "method" => $method_php,
            "user_id" => $user_id
        );
        $respon = curlGet(SERVER_MYSQL . http_build_query($query));
        if ($respon)
        {
            $data_anggota = json_decode($respon);
            if (isset($data_anggota))
            {
                if (isset($data_anggota
                    ->data
                    ->query
                    ->user_id) && $data_anggota
                    ->data
                    ->query->user_id != "")
                {
                    $hasil['user_id'] = $data_anggota
                        ->data
                        ->query->user_id;
                }
                if (isset($data_anggota
                    ->data
                    ->query
                    ->nama) && $data_anggota
                    ->data
                    ->query->nama != "")
                {
                    $hasil['nama'] = $data_anggota
                        ->data
                        ->query->nama;
                }
                if (isset($data_anggota
                    ->data
                    ->query
                    ->pendidikan_terakhir) && $data_anggota
                    ->data
                    ->query->pendidikan_terakhir != "")
                {
                    $hasil['pendidikan_terakhir'] = $data_anggota
                        ->data
                        ->query->pendidikan_terakhir;
                }
                if (isset($data_anggota
                    ->data
                    ->query
                    ->alamat) && $data_anggota
                    ->data
                    ->query->alamat != "")
                {
                    $hasil['alamat'] = $data_anggota
                        ->data
                        ->query->alamat;
                }
            }
        }
    }
    return $hasil;
}

//tambah data ke server php yg terhubung ke sql
function tambahDataAnggotaPHP($user_id, $nama = "", $pendidikan_terakhir = "", $alamat = "")
{
    $hasil = false;
    if (isset($user_id))
    {
        $method_php = "tambah_data";
        $query = array(
            "method" => $method_php,
            "user_id" => $user_id,
            "nama" => $nama,
            "pendidikan_terakhir" => $pendidikan_terakhir,
            "alamat" => $alamat
        );
        $respon = curlGet(SERVER_MYSQL . http_build_query($query));
        if ($respon)
        {
            $json_data = json_decode($respon);
            if (isset($json_data
                ->data
                ->status))
            {
                $hasil = $json_data
                    ->data->status == "ok";
            }
        }
    }
    return $hasil;
}

//hapus data dari server php yg terhubung ke sql
function hapusDataAnggotaPHP($user_id)
{
    $hasil = false;
    if (isset($user_id))
    {
        $method_php = "hapus_data";
        $query = array(
            "method" => $method_php,
            "user_id" => $user_id
        );
        $respon = curlGet(SERVER_MYSQL . http_build_query($query));
        if ($respon)
        {
            $json_data = json_decode($respon);
            if (isset($json_data
                ->data
                ->status))
            {
                $hasil = $json_data
                    ->data->status == "ok";
            }
        }
    }
    return $hasil;
}

//update data dari server php yg terhubung ke sql
function updateDataAnggotaPHP($user_id, $nama = "", $pendidikan_terakhir = "", $alamat = "")
{
    $hasil = false;
    if (isset($user_id))
    {
        $method_php = "update_data";
        $query = array(
            "method" => $method_php,
            "user_id" => $user_id,
            "nama" => $nama,
            "pendidikan_terakhir" => $pendidikan_terakhir,
            "alamat" => $alamat
        );
        $respon = curlGet(SERVER_MYSQL . http_build_query($query));
        if (isset($respon))
        {
            $json_data = json_decode($respon);
            if (isset($json_data
                ->data
                ->status))
            {
                $hasil = $json_data
                    ->data->status == "ok";
            }
        }
    }
    return $hasil;
}

//fungsi utama proses pesan dari user
function prossesAnggotaPHP($user_id = "", $chat_id = "", $text = "")
{
    if ($user_id != "" && $chat_id != "" && $text != "")
    {
        if ($text == "/hapus")
        {
            if (hapusDataAnggotaPHP($user_id))
            {
                sendMessage($chat_id, "User Id Anda : <code>" . $user_id . "</code>\nData Anda berhasil dihapus.");
            }
            else
            {
                sendMessage($chat_id, "User Id Anda : <code>" . $user_id . "</code>\nData Anda gagal dihapus / sudah terhapus.");
            }
        }
        else
        {
            $anggota = ambilDataAnggotaPHP($user_id);
            if (isset($anggota["user_id"]))
            {
                if (isset($anggota["nama"]))
                {
                    if (isset($anggota["pendidikan_terakhir"]))
                    {
                        if (isset($anggota["alamat"]))
                        {
                            $berhasil_daftar = "Anda Sudah Terdaftar, Berikut data Anda;\n\n<code>";
                            $berhasil_daftar .= "Id         : " . $anggota["user_id"] . "\n";
                            $berhasil_daftar .= "Nama       : " . $anggota["nama"] . "\n";
                            $berhasil_daftar .= "Pendidikan : " . $anggota["pendidikan_terakhir"] . "\n";
                            $berhasil_daftar .= "Alamat     : " . $anggota["alamat"] . "</code>";
                            sendMessage($chat_id, $berhasil_daftar);
                        }
                        else
                        {
                            //alamat kosong
                            if ($text != "/daftar")
                            {
                                if (updateDataAnggotaPHP($user_id, $anggota["nama"], $anggota["pendidikan_terakhir"], $text))
                                {
                                    $berhasil_daftar = "Terimakasih, Anda Berhasil Terdaftar, Berikut data Anda;\n\n<code>";
                                    $berhasil_daftar .= "Id         : " . $anggota["user_id"] . "\n";
                                    $berhasil_daftar .= "Nama       : " . $anggota["nama"] . "\n";
                                    $berhasil_daftar .= "Pendidikan : " . $anggota["pendidikan_terakhir"] . "\n";
                                    $berhasil_daftar .= "Alamat     : " . $text . "</code>";
                                    sendMessage($chat_id, $berhasil_daftar);
                                }
                                else
                                {
                                    sendMessage($chat_id, "Anda sudah terdaftar, Tetapi Data Alamat Anda masih kosong\n\nSilakan tulis Alamat Anda");
                                }
                            }
                        }
                    }
                    else
                    {
                        //pendidikan_terakhir kosong
                        if ($text != "/daftar")
                        {
                            if (updateDataAnggotaPHP($user_id, $anggota["nama"], $text))
                            {
                                sendMessage($chat_id, "Silakan tulis Alamat Anda.");
                            }
                            else
                            {
                                sendMessage($chat_id, "Maaf, Tidak dapat update data Pendidikan Terakhir ke database.\nCobalah beberapa saat lagi.");
                            }
                        }
                        else
                        {
                            sendMessage($chat_id, "Anda sudah terdaftar, Tetapi Data Pendidikan Terakhir Anda masih kosong\n\nSilakan tulis Pendidikan Terakhir Anda");
                        }
                    }
                }
                else
                {
                    //nama kosong
                    if ($text != "/daftar")
                    {
                        if (updateDataAnggotaPHP($user_id, $text))
                        {
                            sendMessage($chat_id, "Silakan tulis Pendidikan Terakhir Anda");
                        }
                        else
                        {
                            sendMessage($chat_id, "Maaf, Tidak dapat update data Nama ke database.\nCobalah beberapa saat lagi.");
                        }
                    }
                    else
                    {
                        sendMessage($chat_id, "Anda sudah terdaftar, Tetapi Data Nama Anda masih kosong\n\nSilakan tulis Nama Anda");
                    }
                }
            }
            else
            {
                //belum terdaftar
                if (tambahDataAnggotaPHP($user_id))
                {
                    sendMessage($chat_id, "Silakan tulis Nama Anda");
                }
                else
                {
                    sendMessage($chat_id, "Maaf, Tidak dapat terhubung ke database.\nCobalah beberapa saat lagi.");
                }
            }
        }
    }
}

//input data dari bot tele
$data = file_get_contents('php://input');
if (isset($data))
{
    $update = json_decode($data, true);
    if (isset($update['message']))
    {
        $msg = $update['message'];
        $user_id = $msg['from']['id'];
        $chat_id = $msg['chat']['id'];
        $text = $msg['text'];

        if ($text != '')
        {
            prossesAnggotaPHP($user_id, $chat_id, $text);
        }
    }
}

?>
