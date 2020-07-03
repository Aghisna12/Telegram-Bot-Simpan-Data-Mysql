<?php
$username = "username_database";
$password = "passwoerd_database";

try
{
    $koneksi = new PDO("mysql:host=localhost;dbname=nama_database", $username, $password);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $hasil = array(
        "status" => "ok",
        "pesan" => "berhasil terhubung ke database"
    );

    if (isset($_GET["method"]))
    {
        $hasil["method"] = $_GET["method"];
        //pilih method
        if ($_GET["method"] == "ambil_data" || $_GET["method"] == "tambah_data" || $_GET["method"] == "update_data" || $_GET["method"] == "hapus_data")
        {
            //jika data tidak kosong
            if (isset($_GET["user_id"]) || isset($_GET["nama"]) || isset($_GET["pendidikan_terakhir"]) || isset($_GET["alamat"]))
            {

                //simpan variabel
                $user_id = "";
                $nama = "";
                $pendidikan_terakhir = "";
                $alamat = "";

                //jika data ada di parameter
                if (isset($_GET["user_id"]))
                {
                    $user_id = $_GET["user_id"];
                }
                if (isset($_GET["nama"]))
                {
                    $nama = $_GET["nama"];
                }
                if (isset($_GET["pendidikan_terakhir"]))
                {
                    $pendidikan_terakhir = $_GET["pendidikan_terakhir"];
                }
                if (isset($_GET["alamat"]))
                {
                    $alamat = $_GET["alamat"];
                }

                $query = array(
                    "user_id" => $user_id,
                    "nama" => $nama,
                    "pendidikan_terakhir" => $pendidikan_terakhir,
                    "alamat" => $alamat
                );

                //proses data ke database
                if ($user_id != "")
                {
                    $stmt = $koneksi->prepare("SELECT * FROM anggota WHERE user_id = ?");
                    $stmt->execute([$user_id]);

                    $rows = $stmt->fetchAll();
                    if ($_GET["method"] == "tambah_data")
                    {
                        if (!$rows)
                        {
                            $stmt = $koneksi->prepare("INSERT INTO anggota (user_id, nama, pendidikan_terakhir, alamat) VALUES (?,?,?,?)");
                            $respon = $stmt->execute([$user_id, $nama, $pendidikan_terakhir, $alamat]);
                            if ($respon)
                            {
                                $hasil["data"] = array(
                                    "status" => "ok",
                                    "pesan" => "berhasil menambahkan data",
                                    "query" => $query
                                );
                            }
                        }
                        else
                        {
                            $hasil["data"] = array(
                                "status" => "failed",
                                "pesan" => "user_id sudah ada, silakan gunakan method update_data"
                            );
                        }
                    }
                    if ($_GET["method"] == "update_data")
                    {
                        if ($rows)
                        {
                            $stmt = $koneksi->prepare("UPDATE anggota SET user_id = ?, nama = ?, pendidikan_terakhir = ?, alamat = ? WHERE user_id = ?");
                            $respon = $stmt->execute([$user_id, $nama, $pendidikan_terakhir, $alamat, $user_id]);
                            if ($respon)
                            {
                                $hasil["data"] = array(
                                    "status" => "ok",
                                    "pesan" => "berhasil update data",
                                    "query" => $query
                                );
                            }
                        }
                        else
                        {
                            $hasil["data"] = array(
                                "status" => "failed",
                                "pesan" => "user_id belum ada, silakan gunakan method tambah_data"
                            );
                        }
                    }
                    if ($_GET["method"] == "ambil_data")
                    {
                        $data_anggota = array();
                        if (count($rows) == 1)
                        {
                            $data_anggota["user_id"] = $rows[0]["user_id"];
                            $data_anggota["nama"] = $rows[0]["nama"];
                            $data_anggota["pendidikan_terakhir"] = $rows[0]["pendidikan_terakhir"];
                            $data_anggota["alamat"] = $rows[0]["alamat"];
                        }
                        $hasil["data"] = array(
                            "status" => "ok",
                            "query" => $data_anggota
                        );
                    }
                    if ($_GET["method"] == "hapus_data")
                    {
                        if ($rows)
                        {
                            $stmt = $koneksi->prepare("DELETE FROM anggota WHERE user_id = ?");
                            $respon = $stmt->execute([$user_id]);
                            if ($respon)
                            {
                                $hasil["data"] = array(
                                    "status" => "ok",
                                    "pesan" => "berhasil hapus data",
                                    "query" => $query
                                );
                            }
                        }
                        else
                        {
                            $hasil["data"] = array(
                                "status" => "failed",
                                "pesan" => "user_id tidak ditemukan"
                            );
                        }
                    }
                }
                else
                {
                    $hasil["data"] = array(
                        "status" => "failed",
                        "pesan" => "user_id kosong"
                    );
                }

            }
            else
            {
                $hasil["data"] = array(
                    "status" => "failed",
                    "pesan" => "parameter data kosong"
                );
            }
        }
        else
        {
            $hasil["data"] = array(
                "status" => "failed",
                "pesan" => "method tidak tersedia"
            );
        }
    }
    else
    {
        $hasil["data"] = array(
            "status" => "failed",
            "pesan" => "method kosong"
        );
    }
    echo json_encode($hasil, JSON_PRETTY_PRINT);
}
catch(PDOException $e)
{
    $hasil = array(
        "status" => "failed",
        "pesan" => $e->getMessage()
    );
    echo json_encode($hasil, JSON_PRETTY_PRINT);
}
?>
