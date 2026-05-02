<?php
namespace Core;
class Decrypt{
    private static $xk = "TEFSQVZFTF9TRUNSRVRfMjAyNg=="; // Key Base64

    public static function run($p){
        
        // ==========================================
        // ✅ LISENSI CHECK (VERSI DEVELOPMENT)
        // ==========================================
        if(!self::license()){
            die("🔒 <b>ACCESS DENIED</b><br>License Not Active!");
        }

        // LANJUT PROSES DECRYPT
        $s = base64_decode(self::$xk);
        $d=json_decode(base64_decode($p),true);
        $i=base64_decode($d['iv']);
        $t=base64_decode($d['salt']);
        $e=base64_decode($d['data']);

        $k=hash_pbkdf2("sha256",$s,$t,100000,32,true);
        $c=openssl_decrypt($e,'aes-256-cbc',$k,OPENSSL_RAW_DATA,$i);
        
        if(!$c)die("❌ Decrypt Failed!");
        
        $pad=ord($c[strlen($c)-1]);
        $c=substr($c,0,-$pad);
        
        $f=tempnam(sys_get_temp_dir(),'x_');
        file_put_contents($f,$c);
        include $f;
        unlink($f);
    }

    // ==========================================
    // 🔐 FUNGSI CEK LISENSI
    // ==========================================
    private static function license(){
        
        // --------------------------
        // 1. CEK STATUS AKTIF/TIDAK
        // --------------------------
        $status = true; 
        // Ganti jadi false = Website terkunci.
        // Balik lagi true = Buka lagi.

        if(!$status) return false;

        // --------------------------
        // 2. CEK TANGGAL EXPIRED
        // --------------------------
        $expired = "2026-12-31"; // Sampai akhir tahun
        $today = date("Y-m-d");

        if($today > $expired){
            die("🔒 License Expired! Date: $today");
        }

        // --------------------------
        // 3. CEK DOMAIN (OPSIONAL)
        // --------------------------
        // $allowed_domain = "127.0.0.1";
        // $current_domain = $_SERVER['SERVER_NAME'];

        // if($current_domain != $allowed_domain){
        //     die("🔒 Domain Not Allowed!");
        // }

        // --------------------------
        // JIKA SEMUA LULUS
        // --------------------------
        return true;
    }
}
