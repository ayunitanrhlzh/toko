<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'App\Http\Controllers\HomepageController@index');
Route::get('/about', 'App\Http\Controllers\HomepageController@about');
Route::get('/kontak', 'App\Http\Controllers\HomepageController@kontak');
Route::get('/kategori', 'App\Http\Controllers\HomepageController@kategori');
Route::get('/kategori/{slug}', 'App\Http\Controllers\HomepageController@kategoribyslug');
Route::get('/kategori/{slug}', 'App\Http\Controllers\HomepageController@produkperkategori');
Route::get('/produk', 'App\Http\Controllers\HomepageController@produk');
Route::get('/produk/{slug}', 'App\Http\Controllers\HomepageController@produkdetail');

Route::group(['prefix' => 'admin'], function() {
  Route::get('/', 'App\Http\Controllers\DashboardController@index')->name('dashboard.index');
  // route kategori
  Route::resource('kategori', 'App\Http\Controllers\KategoriController');
  // route produk
  Route::resource('produk', 'App\Http\Controllers\ProdukController');
  // route customer
  Route::resource('customer', 'App\Http\Controllers\CustomerController');
  // route transaksi
  Route::resource('transaksi', 'App\Http\Controllers\TransaksiController');
  // profil
  Route::get('profil', 'App\Http\Controllers\UserController@index');
  // setting profil
  Route::get('setting', 'App\Http\Controllers\UserController@setting');
  // form laporan
  Route::get('laporan', 'App\Http\Controllers\LaporanController@index');
  // proses laporan
  Route::get('proseslaporan', 'App\Http\Controllers\LaporanController@proses');
  // image
  Route::get('image', 'App\Http\Controllers\ImageController@index');
  // upload image kategori
  Route::post('imagekategori', 'App\Http\Controllers\KategoriController@uploadimage');
  // hapus image kategori
  Route::delete('imagekategori/{id}', 'App\Http\Controllers\KategoriController@deleteimage');
  // simpan image
  Route::post('image', 'App\Http\Controllers\ImageController@store');
  // hapus image by id
  Route::delete('image/{id}', 'App\Http\Controllers\ImageController@destroy');
  // upload image produk
  Route::post('produkimage', 'App\Http\Controllers\ProdukController@uploadimage');
  // hapus image produk
  Route::delete('produkimage/{id}', 'App\Http\Controllers\ProdukController@deleteimage');
  // slideshow
  Route::resource('slideshow', 'App\Http\Controllers\SlideshowController');
  // produk promo
  Route::resource('promo', 'App\Http\Controllers\ProdukPromoController');
  // load async produk
  Route::get('loadprodukasync/{id}', 'App\Http\Controllers\ProdukController@loadasync');
  // wishlist
  Route::resource('wishlist', 'App\Http\Controllers\WishlistController');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', function() {
  return redirect('/admin');
});