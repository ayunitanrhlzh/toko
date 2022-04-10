<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // kita ambil data kategori per halaman 20 data dan (paginate(20))
        // kita urutkan yang terakhir diiput yang paling atas (orderBy)
        $itemkategori = Kategori::orderBy('created_at', 'desc')->paginate(20);
        $data = array('title' => 'Kategori Produk',
                    'itemkategori' => $itemkategori);
        return view('kategori.index', $data)->with('no', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array('title' => 'Form Kategori');
        return view('kategori.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_kategori' => 'required|unique:kategori',
            'nama_kategori'=>'required',
            'slug_kategori' => 'required',
            'deskripsi_kategori' => 'required'
        ]);

        $validatedData['user_id'] = auth()->user()['id'] ?? null;
        $validatedData['slug_kategori'] = Str::slug($request->slug_kategori);
        $validatedData['status'] = 'publish';
        
        Kategori::create($validatedData);
        
        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $validatedData = Kategori::findOrFail($id);//cari berdasarkan id = $id, 
        // kalo ga ada error page not found 404
        $data = array('title' => 'Form Edit Kategori',
                    'validatedData' => $validatedData);
        return view('kategori.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kode_kategori' => 'required|unique:kategori',
            'nama_kategori'=>'required',
            'slug_kategori' => 'required',
            'deskripsi_kategori' => 'required'
        ]);

        $validatedData = Kategori::findOrFail($id);//cari berdasarkan id = $id, 
        // kalo ga ada error page not found 404
        $slug = Str::slug($request->slug_kategori);//slug kita gunakan nanti pas buka produk per kategori
        // kita validasi dulu, biar tidak ada slug yang sama
        $validasislug = Kategori::where('id', '!=', $id)//yang id-nya tidak sama dengan $id
                                ->where('slug_kategori', $slug)
                                ->first();
        if ($validasislug) {
            return back()->with('error', 'Slug sudah ada, coba yang lain');
        } else {
            $inputan = $request->all();
            $inputan['slug'] = $slug;
            $validatedData->update($inputan);
            return redirect()->route('kategori.index')->with('success', 'Data berhasil diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validatedData = Kategori::findOrFail($id);//cari berdasarkan id = $id, 
        // kalo ga ada error page not found 404
        if (count((array)$validatedData->produk) > 0) {
            // dicek dulu, kalo ada produk di dalam kategori maka proses hapus dihentikan
            return back()->with('error', 'Hapus dulu produk di dalam kategori ini, proses dihentikan');
        } else {
            if ($validatedData->delete()) {
                return back()->with('success', 'Data berhasil dihapus');
            } else {
                return back()->with('error', 'Data gagal dihapus');
            }
        }
    }

    public function uploadimage(Request $request) {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kategori_id' => 'required',
        ]);
        $itemuser = $request->user();
        $itemkategori = Kategori::where('user_id', $itemuser->id)
                                ->where('id', $request->kategori_id)
                                ->first();
        if ($itemkategori) {
            $fileupload = $request->file('image');
            $folder = 'assets/images';
            $itemgambar = (new ImageController)->upload($fileupload, $itemuser, $folder);
            $inputan['foto'] = $itemgambar->url;//ambil url file yang barusan diupload
            $itemkategori->update($inputan);
            return back()->with('success', 'Image berhasil diupload');
        } else {
            return back()->with('error', 'Kategori tidak ditemukan');
        }
    }

    public function deleteimage(Request $request, $id) {
        $itemuser = $request->user();
        $itemkategori = Kategori::where('user_id', $itemuser->id)
                                ->where('id', $id)
                                ->first();
        if ($itemkategori) {
            // kita cari dulu database berdasarkan url gambar
            $itemgambar = Image::where('url', $itemkategori->foto)->first();
            // hapus imagenya
            if ($itemgambar) {
                Storage::delete($itemgambar->url);
                $itemgambar->delete();
            }
            // baru update foto kategori
            $itemkategori->update(['foto' => null]);
            return back()->with('success', 'Data berhasil dihapus');
        } else {
            return back()->with('error', 'Data tidak ditemukan');
        }
    }
}
