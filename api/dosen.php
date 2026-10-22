<?php
// Set Header Response ke JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include file koneksi (naik satu level folder)
require_once '../koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ----------------------------------------------------
    // GET: Ambil Data Dosen (Semua / Berdasarkan ID)
    // Contoh URL: api/dosen.php ATAU api/dosen.php?id=1
    // ----------------------------------------------------
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT d.*, p.nama_prodi, f.nama_fakultas 
                                    FROM dosen d 
                                    LEFT JOIN prodi p ON d.id_prodi = p.id 
                                    LEFT JOIN fakultas f ON p.id_fakultas = f.id 
                                    WHERE d.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($data = $result->fetch_assoc()) {
                echo json_encode([
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $data
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status"  => "error",
                    "code"    => 404,
                    "message" => "Data dosen tidak ditemukan."
                ], JSON_PRETTY_PRINT);
            }
            $stmt->close();
        } else {
            // Ambil Semua Data Dosen
            $query = "SELECT d.*, p.nama_prodi, f.nama_fakultas 
                      FROM dosen d 
                      LEFT JOIN prodi p ON d.id_prodi = p.id 
                      LEFT JOIN fakultas f ON p.id_fakultas = f.id 
                      ORDER BY d.id ASC";
            $result = $conn->query($query);
            $dosen_list = [];

            while ($row = $result->fetch_assoc()) {
                $dosen_list[] = $row;
            }

            echo json_encode([
                "status" => "success",
                "code"   => 200,
                "total"  => count($dosen_list),
                "data"   => $dosen_list
            ], JSON_PRETTY_PRINT);
        }
        break;

    // ----------------------------------------------------
    // POST: Tambah Data Dosen Baru via API
    // Payload Body: JSON
    // ----------------------------------------------------
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        if (!empty($input['nama']) && !empty($input['id_prodi'])) {
            $nama                = $input['nama'];
            $id_prodi            = intval($input['id_prodi']);
            $nip                 = $input['nip'] ?? '';
            $nuptk               = $input['nuptk'] ?? '';
            $nomor_registrasi    = $input['nomor_registrasi'] ?? '';
            $jabatan_fungsional  = $input['jabatan_fungsional'] ?? '';
            $kepangkatan         = $input['kepangkatan'] ?? '';
            $pendidikan_terakhir = $input['pendidikan_terakhir'] ?? '';
            $rumpun_ilmu         = $input['rumpun_ilmu'] ?? '';
            $status_serdos       = $input['status_serdos'] ?? '';
            $status_pegawai      = $input['status_pegawai'] ?? '';
            $ikatan_kerja        = $input['ikatan_kerja'] ?? '';
            $status_aktivitas    = $input['status_aktivitas'] ?? 'Aktif';
            $penempatan          = $input['penempatan'] ?? 'UM Bengkulu';

            $stmt = $conn->prepare("INSERT INTO dosen (nama, nip, nuptk, nomor_registrasi, id_prodi, jabatan_fungsional, kepangkatan, pendidikan_terakhir, rumpun_ilmu, status_serdos, status_pegawai, ikatan_kerja, status_aktivitas, penempatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssisssssssss", $nama, $nip, $nuptk, $nomor_registrasi, $id_prodi, $jabatan_fungsional, $kepangkatan, $pendidikan_terakhir, $rumpun_ilmu, $status_serdos, $status_pegawai, $ikatan_kerja, $status_aktivitas, $penempatan);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    "status"  => "success",
                    "code"    => 201,
                    "message" => "Data dosen berhasil ditambahkan via API.",
                    "id"      => $stmt->insert_id
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(500);
                echo json_encode([
                    "status"  => "error",
                    "code"    => 500,
                    "message" => "Gagal menyimpan data: " . $conn->error
                ], JSON_PRETTY_PRINT);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "code"    => 400,
                "message" => "Field 'nama' dan 'id_prodi' wajib diisi."
            ], JSON_PRETTY_PRINT);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "status"  => "error",
            "code"    => 405,
            "message" => "Method HTTP tidak diizinkan."
        ], JSON_PRETTY_PRINT);
        break;
}
?>