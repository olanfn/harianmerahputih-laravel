<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('redaction_pages', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('id');
            $table->string('title')->nullable()->after('slug');
            $table->text('summary')->nullable()->after('title');
            $table->unsignedInteger('sort_order')->default(0)->after('summary');
        });

        DB::table('redaction_pages')->where('id', 1)->update([
            'slug' => 'redaksi',
            'title' => 'Redaksi',
            'summary' => 'Informasi susunan redaksi dan pengelola Harian Merah Putih.',
            'sort_order' => 1,
        ]);

        $now = now();
        DB::table('redaction_pages')->insert([
            [
                'slug' => 'tentang-kami',
                'title' => 'Tentang Kami',
                'summary' => 'Mengenal Harian Merah Putih dan komitmen kami kepada pembaca.',
                'sort_order' => 2,
                'content' => '<h2>Tentang Harian Merah Putih</h2><p>Harian Merah Putih adalah portal berita Indonesia yang menghadirkan informasi aktual, relevan, dan mudah dipahami oleh pembaca.</p><h3>Komitmen Kami</h3><p>Kami berupaya menyajikan informasi secara akurat, berimbang, dan bertanggung jawab dengan mengutamakan kepentingan publik.</p><h3>Ruang Lingkup Pemberitaan</h3><p>Harian Merah Putih menyajikan berita nasional, politik, hukum dan kriminal, ekonomi, kesehatan, pendidikan, olahraga, gaya hidup, teknologi, serta perkembangan daerah dan internasional.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'pedoman-media-siber',
                'title' => 'Pedoman Media Siber',
                'summary' => 'Prinsip kerja dan tanggung jawab editorial Harian Merah Putih.',
                'sort_order' => 3,
                'content' => '<h2>Pedoman Media Siber</h2><p>Pedoman ini menjadi acuan pengelolaan konten jurnalistik di Harian Merah Putih.</p><h3>Verifikasi dan Keberimbangan</h3><p>Setiap informasi diupayakan melalui proses pemeriksaan sumber, konfirmasi, dan penyajian konteks yang memadai. Informasi yang belum dapat diverifikasi akan diberi penjelasan yang sesuai.</p><h3>Koreksi dan Hak Jawab</h3><p>Kami membuka ruang koreksi dan hak jawab atas pemberitaan yang dinilai tidak akurat. Koreksi dilakukan secara proporsional tanpa menghilangkan jejak perubahan yang relevan.</p><h3>Konten Pengguna</h3><p>Komentar atau materi dari pengguna tidak boleh mengandung fitnah, kebencian, kekerasan, diskriminasi, pelanggaran privasi, maupun pelanggaran hak cipta.</p><h3>Perlindungan Anak dan Privasi</h3><p>Identitas anak serta korban tindak kekerasan dilindungi sesuai kepentingan publik dan ketentuan yang berlaku.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'kebijakan-privasi',
                'title' => 'Kebijakan Privasi',
                'summary' => 'Penjelasan mengenai penggunaan dan perlindungan data pengunjung.',
                'sort_order' => 4,
                'content' => '<h2>Kebijakan Privasi</h2><p>Harian Merah Putih menghormati privasi pembaca dan berupaya mengelola data secara wajar, aman, dan transparan.</p><h3>Data yang Diproses</h3><p>Sistem dapat memproses data teknis dasar seperti alamat IP, jenis perangkat, halaman yang dibuka, waktu akses, serta data yang secara sukarela dikirimkan melalui sarana komunikasi.</p><h3>Tujuan Penggunaan</h3><p>Data digunakan untuk menjaga keamanan layanan, memahami performa situs, menangani permintaan pembaca, dan meningkatkan kualitas penyajian konten.</p><h3>Penyimpanan dan Keamanan</h3><p>Akses terhadap data dibatasi sesuai kebutuhan operasional. Kami menerapkan langkah teknis yang wajar untuk mencegah akses, perubahan, atau pengungkapan tanpa izin.</p><h3>Perubahan Kebijakan</h3><p>Kebijakan ini dapat diperbarui mengikuti perkembangan layanan dan ketentuan yang berlaku. Versi terbaru selalu ditampilkan pada halaman ini.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'hubungi-redaksi',
                'title' => 'Hubungi Redaksi',
                'summary' => 'Saluran komunikasi untuk koreksi, hak jawab, dan informasi redaksi.',
                'sort_order' => 5,
                'content' => '<h2>Hubungi Redaksi</h2><p>Pembaca dapat menghubungi Harian Merah Putih untuk menyampaikan koreksi, hak jawab, masukan, atau informasi yang relevan dengan pemberitaan.</p><h3>Koreksi dan Hak Jawab</h3><p>Sertakan judul atau tautan berita, bagian yang perlu ditinjau, penjelasan koreksi, serta identitas dan kontak yang dapat diverifikasi.</p><h3>Email Redaksi</h3><p><a href="mailto:redaksi@harianmerahputih.id">redaksi@harianmerahputih.id</a></p><h3>Catatan</h3><p>Pesan yang masuk akan ditinjau oleh tim redaksi. Waktu tanggapan dapat berbeda sesuai kebutuhan verifikasi dan tingkat urgensi.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('redaction_pages')->whereIn('slug', [
            'tentang-kami', 'pedoman-media-siber', 'kebijakan-privasi', 'hubungi-redaksi',
        ])->delete();

        Schema::table('redaction_pages', function (Blueprint $table): void {
            $table->dropUnique('redaction_pages_slug_unique');
            $table->dropColumn(['slug', 'title', 'summary', 'sort_order']);
        });
    }
};
