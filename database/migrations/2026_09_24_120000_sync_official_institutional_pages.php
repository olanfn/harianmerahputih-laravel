<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed the institutional pages from the public Harian Merah Putih
     * information pages. This is a data migration so a fresh deployment gets
     * the same editorial/legal baseline without running the development seeder.
     */
    public function up(): void
    {
        $now = now();

        $pages = [
            'redaksi' => [
                'title' => 'Redaksi',
                'summary' => 'Susunan redaksi dan identitas penerbit Harian Merah Putih.',
                'sort_order' => 1,
                'content' => <<<'HTML'
<h2>Redaksi</h2>
<p><strong>PT. KONSISTEN JAYA MEDIA</strong><br>Media harianmerahputih.id</p>
<h3>Pimpinan</h3>
<p><strong>Direktur Utama/Pemimpin Umum:</strong> <em>Belum dicantumkan pada halaman resmi.</em><br>
<strong>Direktur Keuangan/Pemimpin Perusahaan:</strong> <em>Belum dicantumkan pada halaman resmi.</em><br>
<strong>Pemimpin Redaksi/Penanggungjawab Pemberitaan:</strong> &mdash;<br>
<strong>Redaktur Pelaksana/Wakil Penanggungjawab Pemberitaan:</strong> &mdash;</p>
<h3>Dewan Redaksi</h3>
<p>Ahmad Choironudin, Nurcholis Romadona, Abraham Iksan.</p>
<h3>Redaktur</h3>
<p>Miftahul Ilmi, Rangga Putra, Simon Templar F, Agiyo Monseh M, Tukiman Sarmijan O.</p>
<h3>Fotografer</h3>
<p>Dewangga Burhanudin, Dwi Prasetyo, Olla Sugita.</p>
<h3>Reporter</h3>
<p>Martina Eka N, Edward F Tahalea, Matias Sucahyo, Gagarin Martines, Dwi Prasetyo, Wenny Ardila, Namira Gautama, Marsekal Putri.</p>
<h3>Koresponden Daerah</h3>
<ul>
    <li><strong>Banyuwangi:</strong> Agus M. Saputra</li>
    <li><strong>Jawa Tengah &amp; DIY:</strong> Tanto Gailea (Kepala), Dicke Muhdi, Idrus Ipa</li>
    <li><strong>Nusa Tenggara Timur:</strong> Efrenitus Polce</li>
    <li><strong>Maluku (Ambon):</strong> Christirold J. Hukunala, Dinnur Soamole</li>
</ul>
<h3>Marketing, Iklan &amp; Promosi</h3>
<p>Dera, Gisca Demelia.</p>
<h3>Desain, Promosi &amp; Kreatif</h3>
<p>Ahmad Choironudin (Kepala), Mayda Yudhi Nugroho, Ario Meimarna.</p>
<h3>Informasi Penerbit</h3>
<p><strong>Alamat Redaksi:</strong> <em>Belum dicantumkan pada halaman Redaksi resmi.</em><br>
<strong>Telepon:</strong> 031-<br>
<strong>Kontak Iklan:</strong> +<br>
<strong>Email:</strong> <a href="mailto:redaksiharianmerahputih@gmail.com">redaksiharianmerahputih@gmail.com</a><br>
<strong>Penerbit:</strong> PT. KONSISTEN JAYA MEDIA</p>
HTML,
            ],
            'tentang-kami' => [
                'title' => 'Tentang Kami',
                'summary' => 'Mengenal Harian Merah Putih dan komitmen kami kepada pembaca.',
                'sort_order' => 2,
                'content' => <<<'HTML'
<h2>Tentang Harian Merah Putih</h2>
<p>Harian Merah Putih adalah portal berita digital di <strong>harianmerahputih.id</strong> yang menghadirkan informasi aktual dari Jawa Timur dan berbagai daerah di Indonesia.</p>
<p>Harian Merah Putih diterbitkan oleh <strong>PT. KONSISTEN JAYA MEDIA</strong>. Kami menyajikan berita nasional, politik, hukum dan kriminal, kesehatan, ekonomi, pendidikan, olahraga, lifestyle, viral, internasional, serta foto peristiwa.</p>
<h3>Komitmen Editorial</h3>
<p>Redaksi mengutamakan akurasi, keberimbangan, verifikasi sumber, dan kepentingan publik. Setiap koreksi atau hak jawab dapat disampaikan melalui kanal resmi redaksi untuk ditinjau sesuai pedoman jurnalistik yang berlaku.</p>
<h3>Untuk Pembaca</h3>
<p>Kami terus memperbaiki kualitas liputan, penyajian digital, dan layanan informasi agar berita mudah dipahami serta dapat dipertanggungjawabkan.</p>
HTML,
            ],
            'pedoman-media-siber' => [
                'title' => 'Pedoman Media Siber',
                'summary' => 'Prinsip kerja dan tanggung jawab editorial Harian Merah Putih.',
                'sort_order' => 3,
                'content' => <<<'HTML'
<h2>Pedoman Media Siber</h2>
<p>Harian Merah Putih menggunakan Pedoman Pemberitaan Media Siber Dewan Pers sebagai acuan dalam mengelola berita, konten pengguna, koreksi, dan hak jawab.</p>
<h3>Verifikasi dan keberimbangan</h3>
<p>Berita diupayakan melalui verifikasi sumber dan konfirmasi kepada pihak terkait. Dalam keadaan mendesak untuk kepentingan publik, berita dapat diterbitkan dengan menjelaskan status verifikasinya dan diperbarui setelah informasi tambahan diperoleh.</p>
<h3>Konten buatan pengguna</h3>
<p>Materi dari pengguna harus mematuhi hukum dan etika: tidak memuat fitnah, kebencian, kekerasan, diskriminasi, pornografi, pelanggaran privasi, atau pelanggaran hak cipta. Redaksi dapat menyunting atau menurunkan konten yang melanggar.</p>
<h3>Ralat, koreksi, dan hak jawab</h3>
<p>Permohonan koreksi atau hak jawab perlu menyertakan judul/tautan berita, bagian yang dipermasalahkan, alasan, data pendukung, serta identitas dan kontak pengirim. Koreksi ditampilkan secara proporsional dan diberi penanda yang jelas.</p>
<h3>Pencabutan dan publikasi ulang</h3>
<p>Pencabutan berita dilakukan secara terbatas sesuai ketentuan Pedoman Media Siber. Publikasi ulang wajib menghormati hak cipta dan mencantumkan sumber yang relevan.</p>
<h3>Iklan dan penyelesaian sengketa</h3>
<p>Materi berbayar dibedakan dari berita dan diberi label yang sesuai. Sengketa pemberitaan yang tidak selesai melalui komunikasi redaksi dapat ditempuh melalui Dewan Pers sesuai kewenangannya.</p>
<p><small>Pedoman ini mengacu pada Pedoman Pemberitaan Media Siber Dewan Pers, 15 November 2011.</small></p>
HTML,
            ],
            'kebijakan-privasi' => [
                'title' => 'Kebijakan Privasi',
                'summary' => 'Penjelasan mengenai penggunaan dan perlindungan data pengunjung.',
                'sort_order' => 4,
                'content' => <<<'HTML'
<h2>Kebijakan Privasi</h2>
<p>Kebijakan ini menjelaskan secara umum cara harianmerahputih.id menangani informasi yang dibagikan atau tercatat ketika Anda menggunakan situs.</p>
<h3>Informasi yang dikumpulkan</h3>
<p>Kami dapat menerima informasi yang diberikan secara sukarela dan informasi teknis yang tercatat saat navigasi, seperti alamat IP, jenis browser, penyedia layanan internet, waktu akses, halaman rujukan, dan aktivitas kunjungan.</p>
<h3>File log dan cookies</h3>
<p>File log digunakan untuk menganalisis tren, mengelola situs, memahami pergerakan pengunjung, dan meningkatkan layanan. Mitra iklan atau layanan pihak ketiga dapat memakai cookies, JavaScript, atau teknologi serupa sesuai kebijakan mereka.</p>
<h3>Pihak ketiga</h3>
<p>Kebijakan ini tidak mengatur situs pihak ketiga yang ditautkan atau layanan iklan eksternal. Pengunjung disarankan membaca kebijakan masing-masing penyedia dan dapat menonaktifkan cookies melalui pengaturan browser.</p>
<h3>Ruang lingkup online dan persetujuan</h3>
<p>Kebijakan ini berlaku untuk aktivitas online di harianmerahputih.id. Dengan menggunakan situs, Anda menyetujui kebijakan privasi dan persyaratan yang berlaku.</p>
HTML,
            ],
            'hubungi-redaksi' => [
                'title' => 'Hubungi Redaksi',
                'summary' => 'Saluran komunikasi untuk koreksi, hak jawab, dan informasi redaksi.',
                'sort_order' => 5,
                'content' => <<<'HTML'
<h2>Hubungi Redaksi</h2>
<p>Gunakan kanal berikut untuk menyampaikan koreksi, hak jawab, informasi liputan, masukan, atau pertanyaan kerja sama kepada Harian Merah Putih.</p>
<h3>Kontak resmi</h3>
<ul>
    <li><strong>Penerbit:</strong> PT. KONSISTEN JAYA MEDIA</li>
    <li><strong>Alamat:</strong> Jl. Komplek Ruko Panji Makmur Blok C-02, Jl. Panjang Jiwo No. 46-48, Surabaya, Jawa Timur, Indonesia</li>
    <li><strong>Telepon/Sales:</strong> <a href="tel:+6231">+6231</a></li>
    <li><strong>Email redaksi:</strong> <a href="mailto:redaksiharianmerahputih@gmail.com">redaksiharianmerahputih@gmail.com</a></li>
    <li><strong>Email iklan:</strong> <a href="mailto:infoiklanharianmerahputih@gmail.com">infoiklanharianmerahputih@gmail.com</a></li>
</ul>
<h3>Koreksi dan hak jawab</h3>
<p>Sertakan judul atau tautan berita, bagian yang perlu ditinjau, penjelasan koreksi, data pendukung bila ada, serta nama dan kontak yang dapat diverifikasi. Redaksi akan meninjau pesan sesuai kebutuhan verifikasi dan pedoman yang berlaku.</p>
HTML,
            ],
        ];

        foreach ($pages as $slug => $page) {
            $payload = [...$page, 'updated_at' => $now];

            if (DB::table('redaction_pages')->where('slug', $slug)->exists()) {
                DB::table('redaction_pages')->where('slug', $slug)->update($payload);
            } else {
                DB::table('redaction_pages')->insert([
                    'slug' => $slug,
                    ...$payload,
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Keep institutional content on rollback; editorial pages are mutable CMS data.
    }
};
