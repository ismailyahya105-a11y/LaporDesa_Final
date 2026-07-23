package com.lapordesa.mobile.data

import android.content.ContentResolver
import android.net.Uri
import com.lapordesa.mobile.data.remote.*
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File

class LaporanRepository(private val api: ApiService, private val tokens: TokenStore) {
 suspend fun login(email: String, password: String): Result<Unit> = runCatching { api.login(LoginRequest(email,password)).let { if (!it.success || it.token == null) error(it.message ?: "Login gagal") else tokens.save(it.token) } }
 suspend fun logout() { runCatching { api.logout() }; tokens.clear() }
 suspend fun profile() = api.profile().user ?: error("Profil tidak ditemukan")
 suspend fun laporan() = api.laporan().data?.data?.data ?: emptyList()
 suspend fun detail(id: Long) = api.detail(id).data ?: error("Laporan tidak ditemukan")
 suspend fun create(judul:String, kategori:String, isi:String, uri: Uri?, resolver: ContentResolver) {
  val text = "text/plain".toMediaTypeOrNull(); val photo = uri?.let { val bytes=resolver.openInputStream(it)?.use { s -> s.readBytes() } ?: error("Foto tidak dapat dibaca"); MultipartBody.Part.createFormData("foto", "laporan.jpg", bytes.toRequestBody(resolver.getType(it)?.toMediaTypeOrNull())) }
  val result=api.createLaporan(judul.toRequestBody(text), kategori.toRequestBody(text), isi.toRequestBody(text), photo); if (!result.success) error(result.message ?: "Gagal mengirim laporan")
 }
}
