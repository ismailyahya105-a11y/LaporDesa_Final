package com.lapordesa.mobile.data.remote

import com.google.gson.annotations.SerializedName

data class LoginRequest(val email: String, val password: String)
data class LoginResponse(val success: Boolean, val token: String?, val user: User?, val message: String? = null)
data class ApiResponse<T>(val success: Boolean, val message: String? = null, val data: T? = null)
data class ProfileResponse(val success: Boolean, val user: User?)
data class User(val id: Long, val name: String, val email: String, val role: String, @SerializedName("created_at") val createdAt: String? = null)
data class Laporan(val id: Long, val judul: String, val kategori: Kategori?, @SerializedName("isi_laporan") val isiLaporan: String, val foto: String?, @SerializedName("foto_url") val fotoUrl: String?, val status: String, val tanggal: String?, val pelapor: User?)
data class PaginatedLaporan(val data: List<Laporan> = emptyList())
data class LaporanIndexPayload(val data: PaginatedLaporan)
