<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ArtikelModel;
use App\Models\KegiatanModel;
use App\Models\LoginModel;
use App\Models\QuizModel;

class KegiatanController extends BaseController
{
    protected $get;
    protected $user;
    protected $kegiatan;
    public function __construct(){
        $this->get = new ArtikelModel();
        $this->user = new LoginModel();
        $this->kegiatan = new KegiatanModel();
    }
    public function index()
    {
        $session = session();
        if ($session->logged_in != TRUE) {
            return redirect()->to('/login');
        }
        $getkegiatan = $this->kegiatan->getadminkegiatan();
        $data = [
            'title' => 'Kegiatan',
            'kegiatan' => $getkegiatan,
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/dashboard/kegiatan/adminKegiatan', $data);
    }
    public function hapus_kegiatan($id){
        $kegiatan = $this->kegiatan->findCover($id);
        $row = $kegiatan->getRow();
        $cover=$row->cover;
        // If default.svg
        if ($cover != 'default.svg') {
            //Hapus gambar
            unlink('assets/images/artikel/' . $cover);
        }
        $this->kegiatan->delete($id);
		return redirect()->back();
    }
    public function tambah_data(){
        $penulis = $this->user->penulis();
        $data = [
            'title' => 'Tambah Artikel',
            'penulis' => $penulis,
            'validation' => \Config\Services::validation()
        ];

        return view('Apps/form_artikel', $data);
    }
    public function edit_kegiatan($id) {
        $edit = $this->kegiatan->getkegiatan($id);
        
        $data = [
			'title' => 'Edit Kegiatan',
			'tampil' => $edit,
            
            'validation' => \Config\Services::validation()
		];

		return view('Backend/dashboard/kegiatan/editKegiatan', $data);
    }
    public function update($id){
        //Ambil gambar
        $fileCover = $this->request->getFile('cover');
        // dd($fileCover);

        //Check gambar apakah di upload
        if ($fileCover->getError() == 4) {
            $coverName = 'default.svg';
        }else {
            //Pindah gambar
            $fileCover->move('assets/images/artikel');
            //Ambil nama gambar
            $coverName = $fileCover->getName();
        }
        if ($this->request->getVar('kategori') === 'kegiatan') {
            $this->kegiatan->save([
                'id' => $id,
                'judul' => $this->request->getVar('judul'),
                'cover' => $coverName,
                'sumber_cover' => $this->request->getVar('sumber_cover'),
                'deskripsi' => $this->request->getVar('deskripsi'),
                'kategori' => $this->request->getVar('kategori'),
                'text' => $this->request->getVar('content')
            ]);
        }else{
            $this->get->save([
                'id' => $id,
                'judul' => $this->request->getVar('judul'),
                'cover' => $coverName,
                'sumber_cover' => $this->request->getVar('sumber_cover'),
                'deskripsi' => $this->request->getVar('deskripsi'),
                'kategori' => $this->request->getVar('kategori'),
                'text' => $this->request->getVar('content')
            ]);
        }
        return redirect()->to('kegiatan-admin');
    }

}
